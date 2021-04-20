FROM ubuntu:latest
LABEL maintainer=Fouyoufr

RUN apt-get update

RUN apt-get install -y nginx
RUN apt-get install -y php7.4-fpm
RUN apt-get install -y php7.4-mysql php7.4-curl php7.4-json php7.4-xml php7.4-zip
RUN sed -i 's/display_errors = Off/display_errors = On/g' /etc/php/7.4/fpm/php.ini

RUN openssl req -new -newkey rsa:4096 -days 3650 -nodes -x509 \
    -subj "/C=FR/ST=Denial/L=PAIRS/O=Self-signed certificate/CN=127.0.0.1" \
    -keyout /etc/nginx/conf.d/remotechampions.key  -out /etc/nginx/conf.d/remotechampions.crt

RUN echo 'server {\n\
listen 80 default_server;\n\
listen 443 ssl;\n\
server_name _;\n\
ssl_certificate /etc/nginx/conf.d/remotechampions.crt;\n\
ssl_certificate_key /etc/nginx/conf.d/remotechampions.key;\n\
root /var/www/html;\n\
index index.php;\n\
location / {\n\
  try_files $uri $uri/ =404;\n\
  }\n\
location ~ .php$ {\n\
  include snippets/fastcgi-php.conf;\n\
  fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;\n\
  }\n\
}' > /etc/nginx/sites-available/default

RUN apt-get install -y git
RUN git clone https://github.com/Fouyoufr/remoteChampions.git
RUN cp -a /remoteChampions/setup/. /var/www/html
RUN cp -a /remoteChampions/updates/img/. /var/www/html/img
RUN mkdir /var/www/html/dockerSetup
RUN cp /remoteChampions/updates/* /var/www/html/dockerSetup 2>null
RUN cp /remoteChampions/setup/*.* /var/www/html/dockerSetup
RUN chmod -R 777 /var/www/html

RUN echo '<?php\n\
function sql_get($sqlQuery) {\n\
  global $sqlConn;\n\
  $sqlConn=@mysqli_connect("","root","","remoteChampions");\n\
  if(!$sqlConn) {\n\
    $sqlConn=mysqli_connect("","root","");\n\
    @mysqli_query($sqlConn,"CREATE DATABASE `remoteChampions`");\n\
    $sqlConn=mysqli_connect("","root","","remoteChampions");\n\
    if(!$sqlConn ) die("Could not connect: ".mysqli_connect_error());}\n\
  $sqlResult=mysqli_query($sqlConn,$sqlQuery);\n\
  return $sqlResult;}\n\
$adminPassword="8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918";\n\
$publicPass="";\n\
?>' > /var/www/html/config.inc

RUN DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server
RUN apt-get install -y php-mysql
RUN echo '[mysqld]\n\
skip-grant-tables\n\
user = mysql\n\
bind-address = 0.0.0.0\n\
mysqlx-bind-address = 127.0.0.1\n\
key_buffer_size = 16M\n\
myisam-recover-options = BACKUP\n\
log_error = /var/log/mysql/error.log\n\
max_binlog_size = 100M'> /etc/mysql/mysql.conf.d/mysqld.cnf

RUN apt-get install -y nano

RUN echo '#!/bin/sh\n\
service mysql start\n\
service php7.4-fpm start\n\
service nginx start\n\
/bin/bash' > /dockercmd.sh
RUN chmod +x /dockercmd.sh

EXPOSE 80 443
CMD /dockercmd.sh