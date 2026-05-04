FROM php:8.2-apache

# Extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Activer mod_rewrite
RUN a2enmod rewrite

# Copier le projet
COPY . /var/www/html/

# DocumentRoot vers la racine du projet
RUN sed -ri -e 's!/var/www/html!/var/www/html!g' /etc/apache2/sites-available/*.conf

# Page d'accueil par défaut
RUN echo "DirectoryIndex acceuil.php index.php index.html" >> /etc/apache2/apache2.conf

# Permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80
