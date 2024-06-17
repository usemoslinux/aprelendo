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

