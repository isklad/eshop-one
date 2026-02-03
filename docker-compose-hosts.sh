#!/bin/bash

set -eo pipefail

DOCKER_COMPOSE_FILE="./docker-compose.yml"
DOCKER_DEV_DOMAIN="docker.isklad.eu"
DOCKER_COMPOSE=""
HOSTS_FILE="/etc/hosts"
WSL=false


detect_os() {
  OS="$(uname -s)"
  case "${OS}" in
    Linux)  ;;
    Darwin) ;;
    *)
      echo "Unsupported operating system: ${OS}"
      exit 1
      ;;
  esac
}

pre_checks() {
  if ! command -v docker >/dev/null 2>&1; then
    echo "Error: Docker is not installed."
    exit 1
  fi

  if docker compose version >/dev/null 2>&1; then
    DOCKER_COMPOSE="docker compose"
  elif command -v docker-compose >/dev/null 2>&1 && docker-compose version >/dev/null 2>&1; then
    DOCKER_COMPOSE="docker-compose"
  else
    echo "Error: Neither 'docker compose' nor 'docker-compose' is installed."
    exit 1
  fi

  if [[ ! -f "${DOCKER_COMPOSE_FILE}" ]]; then
    echo "Error: ${DOCKER_COMPOSE_FILE} not found."
    exit 1
  fi

  # WSL check (Linux only)
  if [[ "${OS}" == "Linux" ]]; then
    grep -qi microsoft /proc/sys/kernel/osrelease 2>/dev/null && WSL=true || true
  fi
}

get_repo_name() {
  REPO_NAME=$(basename "$(git rev-parse --show-toplevel)")
  if [[ -z "${REPO_NAME}" ]]; then
    echo "Error: Unable to determine repository name."
    return 1
  fi
}

set_hosts_mappings() {
  get_repo_name

  local SERVICE HOSTNAME
  source .env

  HOSTS_MAPPINGS="/tmp/docker_compose_hosts_mappings_${REPO_NAME}"
  SERVICES=$(${DOCKER_COMPOSE} -f "${DOCKER_COMPOSE_FILE}" -p "${REPO_NAME}" ps --services)

  for SERVICE in ${SERVICES}; do
    CONTAINER_NAME="${SERVICE}"

    # Check if container is running
    if ! ${DOCKER_COMPOSE} -f "${DOCKER_COMPOSE_FILE}" -p "${REPO_NAME}" ps | grep "${CONTAINER_NAME}" | grep "Up" >/dev/null; then
      continue
    fi

    CONTAINER_ISKLAD_ALIASES=$(docker inspect -f '{{range .NetworkSettings.Networks}}{{range .Aliases}}{{.}}{{"\n"}}{{end}}{{end}}' "${CONTAINER_NAME}" | grep "${DOCKER_DEV_DOMAIN}$" || true)
    CONTAINER_VIRT_HOST_ENV_PRESENT=$(docker exec ${CONTAINER_NAME} env | grep -c VIRTUAL_HOST || true)

    if [[ -n "${CONTAINER_ISKLAD_ALIASES}" ]]; then
      for HOSTNAME in ${CONTAINER_ISKLAD_ALIASES}; do
        if [[ "${CONTAINER_VIRT_HOST_ENV_PRESENT}" -eq 1 ]]; then
          VIRTUAL_HOSTS+="${HOSTNAME} "
        else
          HOSTNAMES+="${HOSTNAME} "
        fi
      done
    fi
  done

  if [[ -n "${HOSTNAMES}" || -n "${VIRTUAL_HOSTS}" ]]; then
    if [[ -n "${HOSTNAMES}" ]]; then
      if [[ -z "${LOCAL_IP}" ]]; then
        echo "Containers aliases detected: ${HOSTNAMES}"
        echo "but LOCAL_IP variable not set!"
        echo ""
      fi
    fi

    echo "Add below lines into your hosts file:"
    if [[ -n "${VIRTUAL_HOSTS}" ]]; then
      echo "127.0.0.1 ${VIRTUAL_HOSTS}"
    fi
    if [[ -n "${HOSTNAMES}" ]]; then
      if [[ -n "${LOCAL_IP}" ]]; then
        echo "${LOCAL_IP} ${HOSTNAMES}"
      fi
    fi
    echo "::1 $([[ -n ${VIRTUAL_HOSTS} ]] && echo "${VIRTUAL_HOSTS}")$([[ -n ${HOSTNAMES} ]] && echo "${HOSTNAMES}")"
  fi
}


detect_os
pre_checks
set_hosts_mappings