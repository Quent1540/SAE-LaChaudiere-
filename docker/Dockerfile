FROM php:8.4-cli

RUN apt-get update && \
    apt-get install -y cron openssl curl && \
    rm -rf /var/lib/apt/lists/*

RUN curl -sSLf -o /usr/local/bin/install-php-extensions \
    https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions gettext iconv intl tidy zip sockets \
                            pgsql mysqli pdo_mysql pdo_pgsql xdebug redis @composer

EXPOSE 80

COPY php.ini /usr/local/etc/php/
