FROM ubuntu:latest
LABEL maintainer=Fouyoufr

RUN apt-get update

RUN apt-get install -y nginx
RUN apt-get install -y php7.4 php7.4-fpm
RUN apt-get install -y php7.4-mysql php7.4-curl php7.4-json

RUN openssl req -new -newkey rsa:4096 -days 365 -nodes -x509 \
    -subj "/C=FR/ST=Denial/L=PAIRS/O=Self-signed certificate/CN=127.0.0.1" \
    -keyout /etc/nginx/conf.d/remotechampions.key  -out /etc/nginx/conf.d/remotechampions.crt

#RUN echo $'server {\n\
#    listen 443 http2 ssl;\n\
#    listen [::]:443 http2 ssl;\n\
#    server_name 127.0.0.1;\n\
#    ssl_certificate /etc/nginx/conf.d/remotechampions.crt;\n\
#    ssl_certificate_key /etc/nginx/conf.d/remotechampions.key;\n\
#}' > /etc/nginx/conf.d/ssl.conf

#RUN echo 'return 301 https://$host$request_uri/;' > /etc/nginx/default.d/ssl-redirect.conf

RUN echo $'server {\n\
listen 80 default_server;\n\
listen 443 ssl;\n\
server_name _;\n\
ssl_certificate /etc/nginx/conf.d/remotechampions.crt;\n\
ssl_certificate_key /etc/nginx/conf.d/remotechampions.key;\n\
root /var/www/html;\n\
server_name _;\n\
index index.php;\n\
location ~ .php$ {\n\
  try_files $uri =404;\n\
  fastcgi_pass 127.0.0.1:9000;\n\
  fastcgi_index index.php;\n\
  fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n\
  include fastcgi_params;\n\
  }\n\
}' > /etc/nginx/sites-available/default

RUN apt-get install -y git
RUN git clone https://github.com/Fouyoufr/remoteChampions.git
RUN cp -a /remoteChampions/setup/. /var/www/html
RUN cp -a /remoteChampions/updates/img/. /var/www/html/img 

RUN echo $'<?php\n\
function sql_get($sqlQuery) {\n\
  global $sqlConn;\n\
  $sqlConn=mysqli_connect("127.0.0.1:3306","root","","remoteChampions");\n\
  if(!$sqlConn ) {die("Could not connect: ".mysqli_error());}\n\
  $sqlResult=mysqli_query($sqlConn,$sqlQuery);\n\
  return $sqlResult;}\n\
$adminPassword="bf6e04f9d6d1d7dba9fc604d0b1692f33d7de9a34f0038018f51725fe74358de";\n\
$publicPass="";\n\
?>' > /var/www/html/config.inc

RUN DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server
RUN apt-get install -y php-mysql
#RUN service mysql start
#RUN ["/bin/bash", "-c", "mysql <<< 'CREATE DATABASE remoteChampions'"]

RUN echo $'service mysql start\n\
service php7.4-fpm start\n\
service nginx start' > /dockercmd
RUN chmod +x /dockercmd

EXPOSE 80 443
CMD /dockercmd
