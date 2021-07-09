FROM php:8.0-apache

LABEL org.opencontainers.image.authors="Guillaume ELAMBERT"
WORKDIR /var/www/html

EXPOSE 80
COPY . .
RUN chmod 777 ./system/db