FROM php:7.2-fpm

ADD sources.list /etc/apt/sources.list

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
        && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
        && docker-php-ext-install \
          bcmath \
          intl \
          mbstring \
          mysqli \
          pcntl \
          pdo_mysql \
          zip \
          gd \
          exif \
          sysvmsg

#####################################
# 安装 composer 并设置中国源
#####################################
# 从国内镜像安装composer，并配置成默认从国内镜像安装composer包
#RUN php -r "readfile('http://install.phpcomposer.com/installer');" | php -- --install-dir=/bin/ --filename=composer
RUN wget https://dl.laravel-china.org/composer.phar -O /usr/local/bin/composer
RUN chmod a+x /usr/local/bin/composer
RUN composer config -g repo.packagist composer https://packagist.laravel-china.org

ENV TZ=Asia/Shanghai
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# memcached
RUN apt-get update \
	&& apt-get install -y \
	libmemcached-dev \
	libmemcached11 \
    && pecl install memcached \
    # 启用扩展
    && docker-php-ext-enable memcached

#RUN pecl install memcache && docker-php-ext-enable memcache
# Install PHPUnit
RUN curl -OL https://phar.phpunit.de/phpunit.phar \
	&& chmod 755 phpunit.phar \
	&& mv phpunit.phar /usr/local/bin/ \
	&& ln -s /usr/local/bin/phpunit.phar /usr/local/bin/phpunit

#####################################
# 安装 Redis 扩展
#####################################
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

RUN wget https://github.com/websupport-sk/pecl-memcache/archive/NON_BLOCKING_IO_php7.zip \
    && mkdir -p memcache \
    && unzip NON_BLOCKING_IO_php7.zip \
    && rm NON_BLOCKING_IO_php7.zip \
    && cd pecl-memcache-NON_BLOCKING_IO_php7 \
    && /usr/local/bin/phpize \
    && ./configure --with-php-config=/usr/local/bin/php-config && make && make install \
    && docker-php-ext-enable memcache

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

# 安装nodejs
# Install Node.js
#RUN curl -sL https://deb.nodesource.com/setup_8.x | bash - && \
#  apt-get install -y nodejs
#RUN curl -o- -L https://yarnpkg.com/install.sh | bash

COPY ./php.ini /usr/local/etc/php/
COPY ./www.conf /usr/local/etc/php/
COPY ./www.conf /usr/local/etc/php-fpm.d/

#ADD . /app/swoft

#WORKDIR /app/swoft

RUN apt-get purge -y g++ \
    && apt-get autoremove -y \
    #&& rm -r /var/lib/apt/lists/* \
    && rm -rf /tmp/*

#RUN composer install --no-dev \
#    && composer dump-autoload -o \
#    && composer clearcache

#EXPOSE 80

#CMD ["php", "/app/swoft/bin/swoft", "start"]