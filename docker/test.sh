#!/bin/sh
set -e

composer-install.sh
composer test
