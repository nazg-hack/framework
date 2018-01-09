#!/bin/bash
set -ex
hhvm --version
curl https://getcomposer.org/installer | hhvm -d hhvm.jit=0 --php -- /dev/stdin --install-dir=/usr/local/bin --filename=composer

cd /var/source
hhvm -d hhvm.php7.all=1 -d hhvm.jit=0 -d hhvm.hack.lang.auto_typecheck=0 /usr/local/bin/composer install

hhvm -d hhvm.php7.all=1 -d hhvm.jit=0 -d hhvm.hack.lang.auto_typecheck=0 vendor/bin/phpunit
