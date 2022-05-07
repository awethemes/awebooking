#!/usr/bin/env bash

CURRENT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

PHP_SCOPER_TARGET="$CURRENT_DIR/php-scoper"

if [ ! -f "$PHP_SCOPER_TARGET" ] || [ "$1" == "-f" ]
then
	curl -L -o "$PHP_SCOPER_TARGET" https://github.com/humbug/php-scoper/releases/download/0.17.2/php-scoper.phar;
	chmod +x "$PHP_SCOPER_TARGET"
else
    echo "PHP-Scoper already download, pass -f to force an update";
fi
