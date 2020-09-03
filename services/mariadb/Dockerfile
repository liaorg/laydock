FROM alpine:latest

ARG TZ
ARG MARIADB_VERSION
ARG CONTAINER_PACKAGE_URL


RUN sed -i "s/dl-cdn.alpinelinux.org/${CONTAINER_PACKAGE_URL}/g" /etc/apk/repositories

RUN apk --no-cache add tzdata \
    && cp "/usr/share/zoneinfo/$TZ" /etc/localtime \
    && echo "$TZ" > /etc/timezone

RUN apk update\
    && apk upgrade \
    && apk add --no-cache mariadb mariadb-client \
    && rm -f /var/cache/apk/* 
# Create needed directories
RUN mkdir -p /var/lib/mysql \
    && mkdir -p /data \
    && mkdir -p /run/mysqld \
    && mkdir /etc/mysql/conf.d \
    && mkdir /var/log/mysql

COPY ./my.cnf /etc/mysql/my.cnf
COPY ./initdb.sh /initdb.sh

# Set permissions
RUN chown -R mysql:mysql /var/lib/mysql \
    && chown -R mysql:mysql /run/mysqld \
    && chown -R mysql:mysql /var/log/mysql \
    && chown -R mysql:mysql /data \
    && chmod 777 /tmp

RUN chmod 775 /initdb.sh
# RUN /initdb.sh


### Networking
EXPOSE 3306

WORKDIR /www
# ENTRYPOINT ["/usr/bin/mysqld_safe", "--datadir='/data'", "--console"]
ENTRYPOINT ["/initdb.sh"]

# /usr/bin/mysqld --defaults-file=/etc/mysql/my.cnf --innodb-flush-method=fsync --console
# CMD ["/usr/bin/mysqld", "--defaults-file=/etc/mysql/my.cnf", "--innodb-flush-method=fsync"]
# CMD ["/usr/bin/mysqld"]
CMD ["/bin/sh"]


