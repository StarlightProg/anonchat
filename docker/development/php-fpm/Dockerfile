FROM php:8.1-fpm

# RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

# COPY ./conf.d /usr/local/etc/php/conf.d

# WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \ 
    nodejs \
    npm \
    default-mysql-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && pecl install redis \
    && docker-php-ext-enable redis

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
# RUN useradd -G www-data,root -u $uid -d /home/$user $user
# RUN mkdir -p /home/$user/.composer && \
#     chown -R $user:$user /home/$user

COPY ./docker/development/php-fpm/entrypoint /usr/local/bin/entrypoint

RUN chmod a+x /usr/local/bin/*

WORKDIR /var/www

ENTRYPOINT ["/usr/local/bin/entrypoint"]

CMD ["php-fpm"]
