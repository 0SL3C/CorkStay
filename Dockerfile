# Use an official PHP runtime with Apache
FROM php:8.2-apache

# Set the working directory inside the container
WORKDIR /var/www/html

# Remove the default Apache configuration
RUN rm -f /etc/apache2/sites-available/000-default.conf

# Copy your custom Apache virtual host configuration
COPY corkstay-httpd.conf /etc/apache2/sites-available/corkstay-httpd.conf

# Enable the virtual host
RUN a2ensite corkstay-httpd.conf

# Set the document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/swd-corkstay

# Copy the application code into the container first
COPY . /var/www/html/swd-corkstay/

# Update the Apache configuration with the correct document root
RUN sed -i "s|/var/www/html|${APACHE_DOCUMENT_ROOT}|g" /etc/apache2/sites-available/corkstay-httpd.conf /etc/apache2/apache2.conf

# Enable required Apache modules
RUN a2enmod rewrite headers expires

# Install necessary PHP extensions and dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip

# Set environment variables (if needed)
ENV APP_ENV=production

# Expose port 80 for the Apache web server
EXPOSE 80
