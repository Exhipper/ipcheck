FROM php:8.3-apache

# Install required extensions
RUN apt-get update && apt-get install -y \
    curl \
    && docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module (optional but useful)
RUN a2enmod rewrite

# Copy website files
COPY index.php /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
