#!/bin/bash

echo "Запускаю nginx & php-fpm"
nginx && php-fpm7.4
tail -f /var/log/nginx/error.log