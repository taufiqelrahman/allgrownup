FROM lorisleiva/laravel-docker

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Create app directory
WORKDIR /usr/src/wigu/api

# Install dependencies
# RUN apt-get update && apt-get install -y openssl zip unzip git libonig-dev sudo

# Clear cache
# RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
# RUN docker-php-ext-install pdo_mysql pcntl

# Install composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
# RUN groupadd -g 1000 www
# RUN useradd -u 1000 -ms /bin/bash -g www www | chpasswd && adduser www sudo

# Copy existing application directory contents
COPY . /usr/src/wigu/api
# RUN mkdir -p /usr/src/wigu/api/vendor
# RUN sudo chmod -R 755 /usr/src/wigu/api/vendor
# RUN chown -R www:www /usr/src/wigu/api/

# Copy existing application directory permissions
# COPY --chown=www:www . /usr/src/wigu/api

# Change current user to www
# USER www

RUN composer install
# prefer to run install passport manually
# RUN php artisan passport:install
# RUN env >> .env
CMD php artisan serve --host=0.0.0.0 --port=5120
EXPOSE 5120
