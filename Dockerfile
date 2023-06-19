FROM ubuntu:20.04 as intermediate

# по умолчанию московское время
RUN ln -snf /usr/share/zoneinfo/"Europe/Moscow" /etc/localtime && echo "Europe/Moscow" > /etc/timezone

#ENV DEBIAN_FRONTEND=noninteractive TERM=linux

#RUN sed -i 's/archive.ubuntu.com/ru.archive.ubuntu.com/g' /etc/apt/sources.list

# установка пакетов
RUN apt-get update
RUN apt-get install -y nginx
RUN apt-get install -y php7.4
RUN apt-get install -y php7.4-cli
RUN apt-get install -y php7.4-common
RUN apt-get install -y php7.4-curl
RUN apt-get install -y php7.4-mysql
RUN apt-get install -y php7.4-fpm
RUN apt-get install -y php-simplexml


COPY conf/nginx.conf /etc/nginx/sites-available/default
COPY conf/entrypoint.sh /entrypoint.sh
RUN chmod a+x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]