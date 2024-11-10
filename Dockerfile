# Используем официальный PHP образ с Apache
FROM php:8.0-apache

# Устанавливаем необходимые библиотеки
RUN apt-get update && \
    apt-get install -y libcurl4-openssl-dev pkg-config && \
    docker-php-ext-install curl

# Копируем файлы из папки src в директорию Apache в контейнере
COPY public/ /var/www/html/

# Копирование конфигурации Apache
COPY apache-config/000-default.conf /etc/apache2/sites-available/000-default.conf

# Устанавливаем права доступа на директорию
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Открываем порт 80
EXPOSE 80

