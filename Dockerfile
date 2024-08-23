FROM php:8.2-apache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libgmp-dev \
    git 

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite headers

RUN a2enmod ssl

RUN a2enmod proxy_wstunnel

# RUN a2enmod mod_proxy

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN docker-php-ext-install \
    gmp \
    bcmath \
    pdo_mysql \
    pdo \
    zip \
    iconv \
    fileinfo

RUN docker-php-ext-configure pcntl --enable-pcntl \ 
    && docker-php-ext-install pcntl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -G www-data,root -u 1000 -d /home/devuser devuser
RUN mkdir -p /home/devuser/.composer && \
    chown -R devuser:devuser /home/devuser

EXPOSE 80 9003

COPY . .

ENTRYPOINT [ "apache2-foreground" ]
# CMD ["apache2-foreground"]