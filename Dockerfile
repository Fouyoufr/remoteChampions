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
RUN echo 'listen[::]:80 default_server; >> /etc/nginx/sites-available/default
RUN echo 'root /var/www/html;' >> /etc/nginx/sites-available/default
RUN echo 'serverçname _; >> /etc/nginx/sites-available/default
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

#COPY ./src src
#COPY ./settings.json settings.json
#COPY ./public public

#EXPOSE 8000

#CMD ["python", "src/main.py"]
