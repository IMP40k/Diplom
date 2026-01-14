# Базовый образ с Apache и PHP
FROM php:8.2-apache

# Устанавливаем расширения PHP для MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Копируем файлы сайта в контейнер
COPY . /var/www/html/

# Даём права Apache
RUN chown -R www-data:www-data /var/www/html

# Открываем порт 80
EXPOSE 80
