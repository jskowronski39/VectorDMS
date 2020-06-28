FROM phpdockerio/php74-fpm:latest
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

RUN \
    curl -sL https://deb.nodesource.com/setup_12.x | bash \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list

# Install selected extensions and other stuff
RUN \
    apt-get update \
    && apt-get -y --no-install-recommends install  php7.4-mysql php-xdebug php7.4-intl nodejs yarn \
    && apt remove -y cmdtest \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
