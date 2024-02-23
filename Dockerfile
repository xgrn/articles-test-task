FROM php:8.2-cli as testing

RUN apt-get update \
    && apt-get install -y \
      zlib1g-dev \
      libjpeg-dev \
      libwebp-dev \
      libpng-dev \
      libfreetype-dev \
      libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd \
    && docker-php-ext-install zip

COPY bin /usr/src/aat/bin
COPY data /usr/src/aat/data
COPY public /usr/src/aat/public
COPY src /usr/src/aat/src
COPY tests /usr/src/aat/tests
COPY composer.json /usr/src/aat/composer.json
COPY composer.lock /usr/src/aat/composer.lock
COPY phpunit.xml.dist /usr/src/aat/phpunit.xml.dist

WORKDIR /usr/src/aat

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'edb40769019ccf227279e3bdd1f5b2e9950eb000c3233ee85148944e555d97be3ea4f40c3c2fe73b22f875385f6a5155') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && php composer.phar install

RUN php vendor/bin/phpunit

FROM php:8.2-fpm as aat_app

RUN apt-get update \
    && apt-get install -y \
      zlib1g-dev \
      libjpeg-dev \
      libwebp-dev \
      libpng-dev \
      libfreetype-dev \
      libzip-dev \
      nginx \
      supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd \
    && docker-php-ext-install zip

COPY conf/nginx.conf /etc/nginx/nginx.conf
COPY conf/supervisor.conf /etc/supervisor/supervisord.conf

COPY bin /var/www/aat/bin
COPY data /var/www/aat/data
COPY public /var/www/aat/public
COPY src /var/www/aat/src
COPY templates /var/www/aat/templates
COPY composer.json /var/www/aat/composer.json
COPY composer.lock /var/www/aat/composer.lock

WORKDIR /var/www/aat

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'edb40769019ccf227279e3bdd1f5b2e9950eb000c3233ee85148944e555d97be3ea4f40c3c2fe73b22f875385f6a5155') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && php composer.phar install --no-dev \
    && rm composer.phar

RUN chown -R www-data:www-data /var/www

COPY entry.sh ./entry.sh
RUN chmod 0770 entry.sh

CMD ["./entry.sh"]

EXPOSE 80

HEALTHCHECK --timeout=10s CMD curl --silent --fail http://localhost:80 || exit 1