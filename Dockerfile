FROM php:8.5-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    default-mysql-client \
    libpq-dev \
    supervisor \
    nginx \
    ca-certificates \
    gnupg

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js 20
RUN mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list && \
    apt-get update && \
    apt-get install -y nodejs && \
    npm --version

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Build frontend assets
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configure PHP
RUN echo "short_open_tag = Off" >> /usr/local/etc/php/php.ini

# Configure Supervisor
RUN echo "[supervisord]" >> /etc/supervisor/supervisord.conf && \
    echo "nodaemon=true" >> /etc/supervisor/supervisord.conf && \
    echo "" >> /etc/supervisor/supervisord.conf && \
    echo "[program:php]" >> /etc/supervisor/supervisord.conf && \
    echo "command=/usr/local/sbin/php-fpm" >> /etc/supervisor/supervisord.conf && \
    echo "autostart=true" >> /etc/supervisor/supervisord.conf && \
    echo "autorestart=true" >> /etc/supervisor/supervisord.conf && \
    echo "" >> /etc/supervisor/supervisord.conf && \
    echo "[program:nginx]" >> /etc/supervisor/supervisord.conf && \
    echo "command=/usr/sbin/nginx -g 'daemon off;'" >> /etc/supervisor/supervisord.conf && \
    echo "autostart=true" >> /etc/supervisor/supervisord.conf && \
    echo "autorestart=true" >> /etc/supervisor/supervisord.conf

# Copy nginx configuration
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copy entrypoint script
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expose port 80
EXPOSE 80

# Start entrypoint script
ENTRYPOINT ["/entrypoint.sh"]