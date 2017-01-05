FROM php:7.0-alpine

## ENV configuration for svgo
ENV NPM_CONFIG_LOGLEVEL info
ENV NODE_VERSION 6.9.2

RUN     apk update \
    &&  apk upgrade \
    &&  apk add --no-cache \
        --repository "http://dl-4.alpinelinux.org/alpine/edge/testing" \
        git \
        autoconf \
        automake \
        build-base \
        libtool \
        nasm

## Add PDO and MySQL
RUN docker-php-ext-install pdo pdo_mysql

## Add pngquant
ARG INSTALL_PNGQUANT=false
RUN if [ ${INSTALL_PNGQUANT} = true ]; then \
    apk add --no-cache \
    --repository "http://dl-4.alpinelinux.org/alpine/edge/testing" \
    pngquant \
;fi


## Add mozjpeg
ARG INSTALL_MOZJPEG=false
RUN if [ ${INSTALL_MOZJPEG} = true ]; then \

        git clone git://github.com/mozilla/mozjpeg.git source \
    &&  cd source \
    &&  autoreconf -fiv \
    &&  ./configure --prefix=/usr \
    &&  make install \
    &&  rm -rf /source \

;fi


## Add gifsicle
ARG INSTALL_GIFSICLE=false
RUN if [ ${INSTALL_GIFSICLE} = true ]; then \

        git clone https://github.com/kohler/gifsicle source \
    &&  cd source \
    &&  autoreconf -i \
    &&  ./configure --disable-gifdiff \
    &&  make install \
    &&  rm -rf /source \

;fi

## Add svgo
ARG INSTALL_SVGO=false
RUN if [ ${INSTALL_SVGO} = true ]; then \

        apk add --no-cache \
        libstdc++ \
    &&  apk add --no-cache --virtual .build-deps \
        binutils-gold \
        curl \
        g++ \
        gcc \
        gnupg \
        libgcc \
        linux-headers \
        make \
        python \
  && for key in \
    9554F04D7259F04124DE6B476D5A82AC7E37093B \
    94AE36675C464D64BAFA68DD7434390BDBE9B9C5 \
    0034A06D9D9B0064CE8ADF6BF1747F4AD2306D93 \
    FD3A5288F042B6850C66B31F09FE44734EB7990E \
    71DCFD284A79C3B38668286BC97EC7A07EDE3FC1 \
    DD8F2338BAE7501E3DD5AC78C273792F7D83545D \
    B9AE9905FFD7803F25714661B63B535A4C206CA9 \
    C4F0DFFF4E8C1A8236409D08E73BC641CC11F4C8 \
  ; do \
    gpg --keyserver ha.pool.sks-keyservers.net --recv-keys "$key"; \
  done \
    && curl -SLO "https://nodejs.org/dist/v$NODE_VERSION/node-v$NODE_VERSION.tar.xz" \
    && curl -SLO "https://nodejs.org/dist/v$NODE_VERSION/SHASUMS256.txt.asc" \
    && gpg --batch --decrypt --output SHASUMS256.txt SHASUMS256.txt.asc \
    && grep " node-v$NODE_VERSION.tar.xz\$" SHASUMS256.txt | sha256sum -c - \
    && tar -xf "node-v$NODE_VERSION.tar.xz" \
    && cd "node-v$NODE_VERSION" \
    && ./configure \
    && make -j$(getconf _NPROCESSORS_ONLN) \
    && make install \
    && apk del .build-deps \
    && cd .. \
    && rm -Rf "node-v$NODE_VERSION" \
    && rm "node-v$NODE_VERSION.tar.xz" SHASUMS256.txt.asc SHASUMS256.txt \
    && npm install -g svgo \

;fi

## Add composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    mv composer.phar  /bin/composer && \
    php -r "unlink('composer-setup.php');"

RUN apk del autoconf \
            automake \
            build-base \
            libtool \
            nasm

WORKDIR /image-minify/web

CMD ["php", "-S", "0.0.0.0:8080"]

EXPOSE 8080
