FROM php:7.2.17-fpm-alpine3.9

ENV LANG=C.UTF-8

ENV SWOOLE_VERSION=4.3.1
ENV XDEBUG_VERSION=2.7.1

RUN  echo > /etc/apk/repositories \
&& sed -i '1i\http://mirrors.ustc.edu.cn/alpine/v3.9/main/' /etc/apk/repositories \
&& sed -i '1i\http://mirrors.ustc.edu.cn/alpine/v3.9/community/' /etc/apk/repositories \
&& sed -i '1i\http://mirrors.aliyun.com/alpine/v3.9/main/' /etc/apk/repositories \
&& sed -i '1i\http://mirrors.aliyun.com/alpine/v3.9/community/' /etc/apk/repositories \
&& apk update --no-cache \
    && apk add --no-cache vim jq gmp-dev openssl-dev openssh-client \
       linux-headers bash $PHPIZE_DEPS libbsd freetype-dev \
    && ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/local/include/ \
    && docker-php-ext-install gmp gd zip sockets pdo_mysql opcache pcntl \
    && printf "yes\n yes\n yes\n yes\n no\n " |  pecl install  swoole-${SWOOLE_VERSION} \
    && docker-php-ext-enable swoole \
    && pecl install xdebug-${XDEBUG_VERSION} \
#    && docker-php-ext-enable xdebug \
    && curl -L https://github.com/composer/composer/releases/download/1.8.5/composer.phar  -o /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer \
    && apk del bash $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/cache/apk/* \
           /usr/share/doc/* /name/usr/share/man/* /usr/share/info/*

WORKDIR /data/www
