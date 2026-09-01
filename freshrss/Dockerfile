ARG BUILD_FROM=ghcr.io/hassio-addons/base:21.0.3
# hadolint ignore=DL3006
FROM ${BUILD_FROM}

# Set shell
SHELL ["/bin/bash", "-o", "pipefail", "-c"]

# Setup base
ARG FRESHRSS_VERSION="1.29.1"
RUN \
    apk add --no-cache \
        nginx=1.30.4-r1 \
        php84-ctype=8.4.25-r0 \
        php84-curl=8.4.25-r0 \
        php84-dom=8.4.25-r0 \
        php84-fileinfo=8.4.25-r0 \
        php84-fpm=8.4.25-r0 \
        php84-gmp=8.4.25-r0 \
        php84-iconv=8.4.25-r0 \
        php84-intl=8.4.25-r0 \
        php84-mbstring=8.4.25-r0 \
        php84-opcache=8.4.25-r0 \
        php84-openssl=8.4.25-r0 \
        php84-pdo=8.4.25-r0 \
        php84-pdo_sqlite=8.4.25-r0 \
        php84-session=8.4.25-r0 \
        php84-simplexml=8.4.25-r0 \
        php84-xml=8.4.25-r0 \
        php84-xmlreader=8.4.25-r0 \
        php84-xmlwriter=8.4.25-r0 \
        php84-zip=8.4.25-r0 \
        php84=8.4.25-r0 \
    \
    && ln -s /usr/bin/php84 /usr/bin/php \
    \
    && mkdir -p /var/www/freshrss \
    && curl -J -L -o /tmp/freshrss.tar.gz \
        "https://github.com/FreshRSS/FreshRSS/archive/refs/tags/${FRESHRSS_VERSION}.tar.gz" \
    && tar zxf /tmp/freshrss.tar.gz -C \
        /var/www/freshrss --strip-components=1 \
    \
    \
    # Ingress renders the app in an iframe, which FreshRSS' Content-Security-
    # Policy of frame-ancestors 'none' does not survive. Its `csp.frame-ancestors`
    # setting only reaches the four controllers that declare a policy of their
    # own; every other page, the settings among them, falls back to this default
    # and would be refused by the browser. 'self' is the narrowest value that
    # lets the frame render, since Ingress serves the app from below the root of
    # the Home Assistant origin, and it still refuses framing from anywhere else
    # on the published port. The grep is what makes an upstream change to this
    # line fail the build rather than quietly ship a blank panel.
    && grep -q "'frame-ancestors' => \"'none'\"," \
        /var/www/freshrss/lib/Minz/ActionController.php \
    && sed -i "s/'frame-ancestors' => \"'none'\",/'frame-ancestors' => \"'self'\",/" \
        /var/www/freshrss/lib/Minz/ActionController.php \
    \
    && find /var/www/ -type f -name "*.md" -depth -exec rm -f {} \; \
    && rm -f -r \
        /etc/nginx \
        /tmp/* \
        /var/www/freshrss/.github \
        /var/www/freshrss/Docker \
        /var/www/freshrss/Makefile \
        /var/www/freshrss/composer.json \
        /var/www/freshrss/composer.lock \
        /var/www/freshrss/data \
        /var/www/freshrss/docs \
        /var/www/freshrss/eslint.config.js \
        /var/www/freshrss/extensions \
        /var/www/freshrss/package-lock.json \
        /var/www/freshrss/package.json \
        /var/www/freshrss/phpcs.xml \
        /var/www/freshrss/phpstan-next.neon \
        /var/www/freshrss/phpstan.dist.neon \
        /var/www/freshrss/tests

# Copy root filesystem
COPY rootfs /

# Build arguments
ARG BUILD_ARCH
ARG BUILD_DATE
ARG BUILD_DESCRIPTION
ARG BUILD_NAME
ARG BUILD_REF
ARG BUILD_REPOSITORY
ARG BUILD_VERSION

# Labels
LABEL \
    io.hass.name="${BUILD_NAME}" \
    io.hass.description="${BUILD_DESCRIPTION}" \
    io.hass.arch="${BUILD_ARCH}" \
    io.hass.type="addon" \
    io.hass.version=${BUILD_VERSION} \
    maintainer="Franck Nijhof <opensource@frenck.dev>" \
    org.opencontainers.image.title="${BUILD_NAME}" \
    org.opencontainers.image.description="${BUILD_DESCRIPTION}" \
    org.opencontainers.image.vendor="Home Assistant Community Apps" \
    org.opencontainers.image.authors="Franck Nijhof <opensource@frenck.dev>" \
    org.opencontainers.image.licenses="MIT" \
    org.opencontainers.image.url="https://frenck.dev/home-assistant-apps" \
    org.opencontainers.image.source="https://github.com/${BUILD_REPOSITORY}" \
    org.opencontainers.image.documentation="https://github.com/${BUILD_REPOSITORY}/blob/main/README.md" \
    org.opencontainers.image.created=${BUILD_DATE} \
    org.opencontainers.image.revision=${BUILD_REF} \
    org.opencontainers.image.version=${BUILD_VERSION}
