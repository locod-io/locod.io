# This dockerfile is used to run the tests by the gitlab runner.
FROM phpqa/php-fpm-for-symfony:4.1.0-on-php-7.2-fpm

RUN apt-get update \
    && apt-get -y --no-install-recommends install iproute2 \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
