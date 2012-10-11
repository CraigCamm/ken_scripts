#!/bin/bash

if [ "$1" = "-a" ] ; then
    DIR="$( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
    $DIR/php-cli.php
else
    exec /usr/bin/php "$@"
fi
