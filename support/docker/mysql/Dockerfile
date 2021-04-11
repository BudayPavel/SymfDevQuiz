FROM mysql:5.7

# add custom config
COPY ./conf/my.cnf /etc/mysql/my.cnf

RUN apt-get update \
    && apt-get -y install nano