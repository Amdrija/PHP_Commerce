FROM nginx
COPY . /var/www/demo_projekat/
COPY ./site.conf /etc/nginx/conf.d/default.conf