#!/bin/bash
php artisan optimize:clear
php artisan migrate --force
php artisan optimize
