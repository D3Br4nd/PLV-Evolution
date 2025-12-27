FROM dunglas/frankenphp:php8.4-alpine

RUN install-php-extensions \
    pdo_pgsql \
    redis \
    bcmath \
    intl \
    zip

# Copy application files (optional if using bind mount in dev, but good practice)
# COPY . /app

# Entrypoint is already handled by base image
