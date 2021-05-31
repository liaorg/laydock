ARG PHP_VERSION
FROM ${PHP_VERSION}

ARG TZ
ARG PHP_EXTENSIONS
ARG CONTAINER_PACKAGE_URL
ARG PHP_RUNING

COPY ./etc /tmp/etc
COPY ./extensions /tmp/extensions

# Fix: https://github.com/docker-library/php/issues/240
# apk add gnu-libiconv libstdc++ --no-cache --repository http://${CONTAINER_PACKAGE_URL}/alpine/edge/community/ --allow-untrusted
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php \
    COMPOSER_HOME=/tmp/composer

RUN sed -i "s/dl-cdn.alpinelinux.org/${CONTAINER_PACKAGE_URL}/g" /etc/apk/repositories \
    && apk update \
    && apk upgrade \
    && apk --no-cache add tzdata \
    && cp "/usr/share/zoneinfo/$TZ" /etc/localtime \
    && echo "$TZ" > /etc/timezone \
    && cd /tmp/extensions \
    && chmod +x install.sh \
    && sh install.sh \
    && cp install-php-extensions /usr/local/bin/ \
    && chmod uga+x /usr/local/bin/install-php-extensions \
    && apk add gnu-libiconv libstdc++ --no-cache --repository http://${CONTAINER_PACKAGE_URL}/alpine/edge/community/ --allow-untrusted \
    && curl -o /usr/bin/composer https://mirrors.aliyun.com/composer/composer.phar \
    && chmod +x /usr/bin/composer \
    && composer config -g repo.packagist composer https://mirrors.aliyun.com/composer \
    && apk --no-cache add shadow && usermod -u 1000 www-data && groupmod -g 1000 www-data \
    && rm -rf /tmp/extensions \
    && rm -rf /tmp/etc \
    && rm -rf /var/cache/apk/*

WORKDIR /www