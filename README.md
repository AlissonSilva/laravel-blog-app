# Blog laravel and flutter

## Banco de dados

Definir o tipo de banco e o nome do banco de dados no arquivo .env na pasta server.

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blogapp
DB_USERNAME=root
DB_PASSWORD=

## Server

Instalar Laravel
composer global require laravel/installer

php artisan key:generate

Executar as migration

php artisan migrate

Start serve com ip do servidor

php artisan serve --host=192.168.1.99

## App

Definir o ip do server dentro \app\lib\constant.dart na linha 4.

const baseURL = 'http://192.168.1.99:8000/api'

Executar o app

flutter run
