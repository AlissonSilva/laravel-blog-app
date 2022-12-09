# Blog laravel and flutter

## Server

Instalar Laravel
composer global require laravel/installer

Executar as migration

php artisan migrate

Start serve com ip do servidor

php artisan serve --host=192.168.1.99

## App

Definir o ip do server dentro \app\lib\constant.dart na linha 4.

const baseURL = 'http://192.168.1.99:8000/api'

Executar o app

flutter run
