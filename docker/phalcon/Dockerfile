FROM phalconphp/php-fpm:7

RUN curl -s https://packagecloud.io/install/repositories/phalcon/nightly/script.deb.sh | sudo bash
RUN apt-get update
RUN apt-get install -y php7.0-phalcon

WORKDIR /root
RUN rm -rf /usr/bin/phalcon
RUN git clone git://github.com/phalcon/phalcon-devtools.git /root/phalcon-devtools
RUN ln -s /root/phalcon-devtools/phalcon.php /usr/bin/phalcon
RUN chmod ugo+x /usr/bin/phalcon

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

RUN openssl genrsa -out private.key 1024
RUN openssl rsa -in private.key -pubout -out public.key

RUN echo "xdebug.remote_autostart=on" >> /etc/php/7.0/fpm/conf.d/20-xdebug.ini
#RUN echo "extension=mongo.so" >> /etc/php/7.0/fpm/php.ini
RUN echo "nameserver 8.8.8.8" >> /etc/resolv.conf
RUN cp /usr/share/zoneinfo/Europe/Moscow /etc/localtime

WORKDIR /app