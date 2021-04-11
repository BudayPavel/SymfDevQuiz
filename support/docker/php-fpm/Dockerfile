FROM php:7.2-fpm-stretch
ARG TIMEZONE
ARG DOCKER_ENV

ENV DOCKER_ENV=${DOCKER_ENV}

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
    && date

RUN apt-get update && apt-get install -y \
        curl \
        bash \
        libtool \
        imagemagick-c++ \
        libmcrypt-dev \
        libxml2-dev \
        bzip2 \
        autoconf \
        libaio-dev \
        git \
        libstdc++ \
        libxslt-dev \
        make \
        ffmpeg \
        apt-utils \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        locales \
        locales-all \
        libzip-dev \
        zip \
        p7zip-full \
        unzip \
        wget \
        librabbitmq-dev \
        libssh-dev \
        supervisor \
    && docker-php-ext-install sockets \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && docker-php-ext-install -j$(nproc) xsl\
    && docker-php-ext-install -j$(nproc) iconv\
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd\
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install zip \
        bcmath \
        gd \
        pcntl \
        opcache \
        pdo pdo_mysql

# set correct locale
RUN { \
        echo 'en_US.UTF-8 UTF-8'; \
        echo 'ru_RU ISO-8859-5'; \
        echo 'ru_RU.CP1251 CP1251'; \
        echo 'ru_RU.KOI8-R KOI8-R'; \
        echo 'ru_RU.UTF-8 UTF-8'; \
    } > /etc/locale.gen && \
    locale-gen

ENV LANG=en_US.UTF-8 \
    LC_ALL=en_US.UTF-8 \
    LANGUAGE=en_US.UTF-8

COPY ./conf/php.ini /usr/local/etc/php/php.ini

# Install Composer
ENV COMPOSER_VERSION 1.10.10
RUN set -eux; \
  curl --silent --fail --location --retry 3 --output /tmp/installer.php --url https://raw.githubusercontent.com/composer/getcomposer.org/cb19f2aa3aeaa2006c0cd69a7ef011eb31463067/web/installer; \
  php -r " \
    \$signature = '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5'; \
    \$hash = hash('sha384', file_get_contents('/tmp/installer.php')); \
    if (!hash_equals(\$signature, \$hash)) { \
      unlink('/tmp/installer.php'); \
      echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
      exit(1); \
    }"; \
  php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION}; \
  composer --ansi --version --no-interaction; \
  rm -f /tmp/installer.php; \
  find /tmp -type d -exec chmod -v 1777 {} +

ADD ./conf/symfony.pool.conf /usr/local/etc/php/php-fpm.d/
ADD ./conf/www.conf /usr/local/etc/php-fpm.d/

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    locale-gen

WORKDIR /var/www/app
