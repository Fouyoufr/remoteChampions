FROM ubuntu:latest
LABEL maintainer=Fouyoufr

RUN apt-get update
RUN apt-get install -y nginx
RUN apt-get install -y git

RUN DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server

RUN apt-get install -y php-fpm
RUN apt-get install -y php-mysql

RUN echo 'server {' > /etc/nginx/sites-available/default
RUN echo 'listen 80 default_server;' >> /etc/nginx/sites-available/default
RUN echo 'listen 443 default_server ssl;' >> /etc/nginx/sites-available/default
RUN echo 'listen[::]:80 default_server;' >> /etc/nginx/sites-available/default
RUN echo 'root /var/www/html;' >> /etc/nginx/sites-available/default
RUN echo 'serverçname _;' >> /etc/nginx/sites-available/default
RUN echo 'index index.php;' >> /etc/nginx/sites-available/default
RUN echo 'location ~ \.php$ {'>> /etc/nginx/sites-available/default
RUN echo '        include snippets/fastcgi-php.conf;' >> /etc/nginx/sites-available/default
RUN echo '        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;' >> /etc/nginx/sites-available/default
RUN echo '     }' >> /etc/nginx/sites-available/default
RUN echo '}' >> /etc/nginx/sites-available/default
RUN echo '<?php' > /var/www/html/index.php
RUN echo 'echo "Hello Remote Champions!<br/>";' >> /var/www/html/index.php
RUN echo 'phpinfo();' >> /var/www/html/index.php
RUN echo '?>' >> /var/www/html/index.php
RUN git clone https://github.com/Fouyoufr/remoteChampions.git
RUN cp -a /remoteChampions/setup/. /var/www/html
RUN cp -a /remoteChampions/updates/img/. /var/www/html/img 

RUN echo '<?php' > /var/www/html/config.inc
RUN echo 'function sql_get($sqlQuery) {' >> /var/www/html/config.inc
RUN echo '  global $sqlConn;' >> /var/www/html/config.inc
RUN echo '  $sqlConn=mysqli_connect('127.0.0.1:3306','root','','remoteChampions');' >> /var/www/html/config.inc
RUN echo '  if(!$sqlConn ) {die("Could not connect: ".mysqli_error());}' >> /var/www/html/config.inc
RUN echo '  $sqlResult=mysqli_query($sqlConn,$sqlQuery);' >> /var/www/html/config.inc
RUN echo '  return $sqlResult;}' >> /var/www/html/config.inc
RUN echo '$adminPassword="bf6e04f9d6d1d7dba9fc604d0b1692f33d7de9a34f0038018f51725fe74358de";' >> /var/www/html/config.inc
RUN echo '$publicPass="";' >> /var/www/html/config.inc
RUN echo '?>' >> /var/www/html/config.inc

#RUN service mysql start
#RUN ["/bin/bash", "-c", "mysql <<< 'CREATE DATABASE remoteChampions'"]

RUN echo 'service mysql start' > /dockercmd
RUN echo 'service nginx start' >> /dockercmd
RUN chmod +x /dockercmd

EXPOSE 80 443
CMD /dockercmd
