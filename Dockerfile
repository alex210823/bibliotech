FROM php:8.2-apache

# Installa l'estensione mysqli
RUN docker-php-ext-install mysqli

# Abilita l'estensione
RUN docker-php-ext-enable mysqli

# Riavvia Apache
RUN service apache2 restart || true
