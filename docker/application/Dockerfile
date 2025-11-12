# FROM --platform=linux/amd64 php:8.2-fpm
# bookworm (Debian 12)
FROM php:8.2-fpm-bookworm

RUN set -eux; \
	apt-get update; \
	apt-get install -y --no-install-recommends gettext-base apache2-utils zip unzip cron nginx supervisor memcached nano sendmail wget systemd curl git openssl \
    ghostscript poppler-utils imagemagick g++ libsodium-dev iputils-ping libpq-dev zlib1g-dev libmcrypt-dev libzip-dev libicu-dev  libxslt-dev libpng-dev libssh2-1-dev \
    libssl-dev libcurl4-openssl-dev libmagickwand-dev libmagickcore-dev libc-client-dev libmemcached-dev libfreetype6-dev libjpeg62-turbo-dev libxml2-dev libxft-dev libfontconfig1-dev  \
    wait-for-it build-essential chrpath libkrb5-dev acl neofetch; \
	rm -rf /var/lib/apt/lists/*; \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; \
    docker-php-ext-configure intl; \
    docker-php-ext-configure imap --with-kerberos --with-imap-ssl; \
    docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j$(nproc) pdo pdo_pgsql zip xsl intl gd imap

# Install Redis
RUN pecl install --onlyreqdeps --force redis; \
    rm -rf /tmp/pear; \
    docker-php-ext-enable redis

COPY docker-entrypoint.sh /usr/local/bin/
COPY config/conf/nginx.conf /etc/nginx/conf.d/nginx.conf
COPY config/conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY config/ini/php.ini /usr/local/etc/php/conf.d/php.ini
COPY config/ini/opcache-recommended.ini /usr/local/etc/php/conf.d/opcache-recommended.ini
COPY config/ini/error-logging.ini /usr/local/etc/php/conf.d/error-logging.ini
COPY config/ini/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

RUN pecl install xdebug-3.2.1; \
    docker-php-ext-enable xdebug; \
    wget https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64; \
    chmod +x mhsendmail_linux_amd64; \
    mv mhsendmail_linux_amd64 /usr/local/bin/mhsendmail

ENV XDEBUG_CONFIG 1

CMD ["docker-entrypoint.sh"]
