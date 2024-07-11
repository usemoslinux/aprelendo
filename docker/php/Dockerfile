FROM php:fpm-alpine

RUN apk add --no-cache autoconf gcc g++ linux-headers make python3 py3-pip && \
    apk add python3-dev

# Set working directory
WORKDIR /opt/venv

# Create and activate virtual environment
ENV VIRT_ENV=/opt/venv
RUN python3 -m venv $VIRT_ENV
ENV PATH="$VIRT_ENV/bin:$PATH"

# Upgrade pip and setuptools within the virtual environment
RUN source bin/activate && pip install --upgrade pip setuptools && deactivate

# Install youtube_transcript_api within the virtual environment
RUN source bin/activate && pip install youtube_transcript_api && deactivate

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set the working directory
WORKDIR /var/www/aprelendo/

# Copy the composer.json and composer.lock files to the working directory
COPY composer.json composer.lock /var/www/aprelendo/

# Install the PHP dependencies
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application code
COPY --chown=www-data:www-data . /var/www/aprelendo/

# Ensure correct permissions again after copy
RUN chown -R www-data:www-data /var/www/aprelendo/

# Run the autoloader
RUN composer dump-autoload --optimize 
