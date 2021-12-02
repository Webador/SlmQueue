#!/usr/bin/sh

rm -rf lib
rm -f composer.lock

# copy code to `lib` directory. This prevents Composer from symlinking the root directory,
# which would cause PHPUnit to crash
rsync -av --progress ../../../ lib/ --exclude=".*"  --exclude="vendor/"
composer install
