FROM ubuntu:latest
LABEL maintainer=Fouyoufr

RUN apt-get update

RUN apt-get install -y nano

RUN apt-get install -y nginx
RUN apt-get install -y php7.4-fpm
RUN apt-get install -y php7.4-mysql php7.4-curl php7.4-json php7.4-xml php7.4-zip
RUN sed -i 's/display_errors = Off/display_errors = On/g' /etc/php/7.4/fpm/php.ini

RUN openssl req -new -newkey rsa:4096 -days 3650 -nodes -x509 \
    -subj "/C=FR/ST=Denial/L=PAIRS/O=Self-signed certificate/CN=localhost" \
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

#RUN DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server
#RUN apt-get install -y php-mysql
#RUN echo '[mysqld]\n\
#skip-grant-tables\n\
#user = mysql\n\
#bind-address = 0.0.0.0\n\
#mysqlx-bind-address = 127.0.0.1\n\
#key_buffer_size = 16M\n\
#myisam-recover-options = BACKUP\n\
#log_error = /var/log/mysql/error.log\n\
#max_binlog_size = 100M'> /etc/mysql/mysql.conf.d/mysqld.cnf

RUN apt-get install -y git
RUN git clone https://github.com/Fouyoufr/remoteChampions.git
RUN cp -a /remoteChampions/setup/. /var/www/html
RUN cp -a /remoteChampions/updates/img/. /var/www/html/img
RUN mkdir /var/www/html/dockerSetup
RUN cp /remoteChampions/updates/aide.md /var/www/html/dockerSetup
RUN chmod -R 777 /var/www/html

RUN echo '#!/bin/sh\n\
service php7.4-fpm start\n\
service nginx start\n\
/bin/bash' > /dockercmd.sh
RUN chmod +x /dockercmd.sh

EXPOSE 80
EXPOSE 443
CMD /dockercmd.sh