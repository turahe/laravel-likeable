#!/bin/sh
set -e

PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
export COMPOSER_VENDOR_DIR="/app/.build/vendor-php-${PHP_VERSION}"

mkdir -p "${COMPOSER_VENDOR_DIR}"

if [ ! -f "${COMPOSER_VENDOR_DIR}/bin/phpunit" ]; then
	rm -f composer.lock
	composer config platform.php "${PHP_VERSION}.0"
	composer update --prefer-dist --no-interaction
fi

rm -rf vendor
ln -sfn "${COMPOSER_VENDOR_DIR}" /app/vendor
