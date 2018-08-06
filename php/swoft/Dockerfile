FROM php:7.2-fpm

ADD sources.list /etc/apt/sources.list

# Timezone
RUN /bin/cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime \
    && echo 'Asia/Shanghai' > /etc/timezone

# Libs
RUN apt-get update && apt-get install -y \
        curl \
        wget \
        git \
        zip \
        cron \
        openssl \
        git \
        gnupg2 \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libicu-dev \
        libz-dev \
        libssl-dev \
        libnghttp2-dev \
        libpcre3-dev \
        && apt-get clean \
        && apt-get autoremove \
        && rm -r /var/lib/apt/lists/* \
        && docker-php-ext-install \
          bcmath \
          intl \
          mbstring \
          mysqli \
          pcntl \
          pdo_mysql \
          zip \
          gd \
          exif

#####################################
# 安装 composer 并设置中国源
#####################################
# 从国内镜像安装composer，并配置成默认从国内镜像安装composer包
#RUN php -r "readfile('http://install.phpcomposer.com/installer');" | php -- --install-dir=/bin/ --filename=composer
RUN wget https://dl.laravel-china.org/composer.phar -O /usr/local/bin/composer
RUN chmod a+x /usr/local/bin/composer
RUN composer config -g repo.packagist composer https://packagist.laravel-china.org

# memcached
RUN apt-get update \
	&& apt-get install -y \
	libmemcached-dev \
	libmemcached11

# Install PHPUnit
RUN curl -OL https://phar.phpunit.de/phpunit.phar \
	&& chmod 755 phpunit.phar \
	&& mv phpunit.phar /usr/local/bin/ \
	&& ln -s /usr/local/bin/phpunit.phar /usr/local/bin/phpunit

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis && pecl clear-cache

# Hiredis
RUN wget https://github.com/redis/hiredis/archive/v0.13.3.tar.gz -O hiredis.tar.gz \
    && mkdir -p hiredis \
    && tar -xf hiredis.tar.gz -C hiredis --strip-components=1 \
    && rm hiredis.tar.gz \
    && ( \
        cd hiredis \
        && make -j$(nproc) \
        && make install \
        && ldconfig \
    ) \
    && rm -r hiredis

# Swoole extension
RUN wget https://github.com/swoole/swoole-src/archive/v4.0.0.tar.gz -O swoole.tar.gz \
    && mkdir -p swoole \
    && tar -xf swoole.tar.gz -C swoole --strip-components=1 \
    && rm swoole.tar.gz \
    && ( \
        cd swoole \
        && phpize \
        && ./configure --enable-async-redis --enable-mysqlnd --enable-coroutine --enable-openssl --enable-http2 \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r swoole \
    && docker-php-ext-enable swoole

ADD . /app/swoft

WORKDIR /app/swoft

RUN composer install --no-dev \
    && composer dump-autoload -o \
    && composer clearcache

#EXPOSE 80

CMD ["php", "/app/swoft/bin/swoft", "start"]