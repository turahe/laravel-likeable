#!/bin/sh
set -e

composer-install.sh
exec ./vendor/bin/phpunit --testdox
