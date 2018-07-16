FROM php:5.6.27-fpm

ADD sources.list /etc/apt/sources.list

RUN apt-get upgrade && apt-get update && apt-get install -y \
        curl \
        wget \
        git \
        zip \
        cron \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libicu-dev \
                --no-install-recommends

RUN docker-php-ext-install  mcrypt  intl mysqli  mbstring pdo_mysql exif \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd\
	&& docker-php-ext-install bcmath pcntl zip

#####################################
# 安装 Redis 扩展
#####################################
RUN pecl install redis && docker-php-ext-enable redis && pecl clear-cache

RUN pecl install -o -f xdebug \
    && rm -rf /tmp/pear

ENV TZ=Asia/Shanghai
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

COPY ./php.ini /usr/local/etc/php/
COPY ./www.conf /usr/local/etc/php/
COPY ./www.conf /usr/local/etc/php-fpm.d/

RUN apt-get purge -y g++ \
    && apt-get autoremove -y \
    && rm -r /var/lib/apt/lists/* \
    && rm -rf /tmp/*

#RUN usermod -u 1000 www-data

#EXPOSE 9000
#CMD ["php-fpm"]
