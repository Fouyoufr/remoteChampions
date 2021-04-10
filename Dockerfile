FROM ubuntu:latest
LABEL maintainer=Fouyoufr

RUN apt-get update
RUN apt-get install -y nginx
RUN apt-get install -y git

RUN debconf-set-selections <<< 'mysql-server mysql-server/root_password password remotechampions'
RUN debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password remotechampions'
RUN apt-get -y install mysql-server

RUN apt-get -y php-fpm
RUN apt-get -y php-mysql
RUN mkdir /var/wwww/remotechampions

RUN echo 'server {' > /etc/nginx/sites-available/remotechampions
RUN echo 'listen 80;' >> /etc/nginx/sites-available/remotechampions
RUN echo 'root /var/www/remotechampions;' >> /etc/nginx/sites-available/remotechampions
RUN echo 'index index.php;' >> /etc/nginx/sites-available/remotechampions
RUN echo 'location ~ \.php$ {' >> /etc/nginx/sites-available/remotechampions
RUN echo '        include snippets/fastcgi-php.conf;' >> /etc/nginx/sites-available/remotechampions
RUN echo '        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;' >> /etc/nginx/sites-available/remotechampions
RUN echo '     }' >> /etc/nginx/sites-available/remotechampions
RUN echo '}' >> /etc/nginx/sites-available/remotechampions
RUN ln -s /etc/nginx/sites-available/remotechampions /etc/nginx/sites-enabled/
RUN echo '<?php' > /var/www/remotechampions/index.php
RUN echo 'echo "Hello Remote Champions!<br/>";' >> /var/www/remotechampions/index.php
RUN echo 'phpinfo();' >> /var/www/remotechampions/index.php
RUN echo '?>' >> /var/www/remotechampions/index.php

#COPY ./src src
#COPY ./settings.json settings.json
#COPY ./public public

#EXPOSE 8000

#CMD ["python", "src/main.py"]
