FROM nginx:1.15.8
ARG TIMEZONE

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
    && date

RUN apt-get update && apt-get install -y \
        nginx \
        locales \
        locales-all

RUN apt-get clean && rm -rf /tmp/*

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

ADD ./conf/nginx.conf /etc/nginx/
ADD ./conf/symfony.conf /etc/nginx/conf.d/

RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf
RUN rm /etc/nginx/conf.d/default.conf

RUN usermod -u 1000 www-data

CMD ["nginx"]

EXPOSE 80
