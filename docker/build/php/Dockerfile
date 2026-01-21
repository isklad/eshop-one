ARG PHP_IMAGE=8.4.6-fpm-bookworm_5

FROM ghcr.io/isklad/php-fpm:${PHP_IMAGE}

ARG GIT_COMMIT
ARG GIT_BRANCH
ARG APP_VERSION

ENV GIT_COMMIT=${GIT_COMMIT} \
    GIT_BRANCH=${GIT_BRANCH} \
    APP_VERSION=${APP_VERSION}

LABEL description="php container" \
      container_git_commit=${GIT_COMMIT} \
      container_git_branch=${GIT_BRANCH} \
      app_version=${APP_VERSION}

COPY --link --chown=33:33 . /var/www/
