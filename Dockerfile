# PHP Images can be found at https://hub.docker.com/_/php/
FROM php:7.4.10-alpine

# The application will be copied in /home/application and the original document root will be replaced in the apache configuration
VOLUME /home/application/
WORKDIR /home/application/

# Custom Document Root
ENV APACHE_DOCUMENT_ROOT /home/application/public

# Package Installation
RUN set -ex \
    && apk add --update apache2 bash expect g++ git icu-dev libzip-dev npm \
    php7-apache2 php7-mbstring php7-session php7-json php7-pdo php7-openssl \
    php7-tokenizer php7-pdo php7-pdo_mysql php7-xml php7-simplexml php7-fileinfo \
    php7-gd libxml2-dev php7-xml php7-dom php7-xmlreader php7-xmlwriter openssh libpng-dev

# Concatenated RUN commands
RUN set -ex \
    && chmod -R 777 /home/application \
    && chown -R www-data:www-data /home/application \
    && mkdir -p /run/apache2 \
    && sed -i '/LoadModule rewrite_module/s/^#//g' /etc/apache2/httpd.conf \
    && sed -i '/LoadModule session_module/s/^#//g' /etc/apache2/httpd.conf \
    && sed -ri -e 's!/var/www/localhost/htdocs!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/httpd.conf \
    && sed -i 's/AllowOverride\ None/AllowOverride\ All/g' /etc/apache2/httpd.conf \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure zip --with-libzip=/usr/include \
    && docker-php-ext-install intl mbstring opcache pdo_mysql zip gd fileinfo xml \
    && rm  -rf /tmp/* /var/cache/apk/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Launch the httpd in foreground
CMD rm -rf /run/apache2/* || true && /usr/sbin/httpd -DFOREGROUND
