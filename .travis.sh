#!/bin/bash
set -ex
hhvm --version
curl https://getcomposer.org/installer | hhvm -d hhvm.jit=0 --php -- /dev/stdin --install-dir=/usr/local/bin --filename=composer

cd /var/source
hhvm -c php7.ini /usr/local/bin/composer install

hh_server --check $(pwd)
hhvm -c php7.ini vendor/bin/phpunit
