#!/bin/bash
set -ex
apt update -y
DEBIAN_FRONTEND=noninteractive apt install -y php-cli zip unzip
hhvm --version
php --version

(
  cd $(mktemp -d)
  curl https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
)

if (hhvm --version | grep -q -- -dev); then
  # Doesn't exist in master, but keep it here so that we can test release
  # branches on nightlies too
  rm -f composer.lock
fi
composer install
hh_client
HH_FORCE_IS_DEV=0 ./vendor/bin/hacktest.hack tests/
