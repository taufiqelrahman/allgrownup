FROM php:fpm

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Create app directory
WORKDIR /usr/src/wigu/api

# Install dependencies
RUN apt-get update && apt-get install -y openssl zip unzip git libonig-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql zip exif pcntl
RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
RUN docker-php-ext-install gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /usr/src/wigu/api

# Copy existing application directory permissions
COPY --chown=www:www . /usr/src/wigu/api

# Change current user to www
USER www

RUN composer install
CMD php artisan serve
EXPOSE 8000
