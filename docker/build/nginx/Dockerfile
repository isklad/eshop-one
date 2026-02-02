ARG NGINX_IMAGE=1.27.4-bookworm_10

FROM registry.digitalocean.com/isklad/nginx:${NGINX_IMAGE}

ARG GIT_COMMIT
ARG GIT_BRANCH
ARG APP_VERSION

ENV GIT_COMMIT=${GIT_COMMIT} \
    GIT_BRANCH=${GIT_BRANCH} \
    APP_VERSION=${APP_VERSION}

LABEL description="nginx container" \
      container_git_commit=${GIT_COMMIT} \
      container_git_branch=${GIT_BRANCH} \
      app_version=${APP_VERSION}

COPY --link --chown=33:33 docker/build/nginx/02_cors_map.snippet /etc/nginx/snippets/
COPY --link --chown=33:33 public /var/www/public
