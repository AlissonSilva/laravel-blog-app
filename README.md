# Blog laravel and flutter

## Banco de dados

Definir o tipo de banco e o nome do banco de dados no arquivo .env na pasta server.

DB_CONNECTION=mysql<br>
DB_HOST=127.0.0.1<br>
DB_PORT=3306<br>
DB_DATABASE=blogapp<br>
DB_USERNAME=root<br>
DB_PASSWORD=<br>

## Server

Instalar Laravel <br>
composer global require laravel/installer

php artisan key:generate

Executar as migration

php artisan migrate

Start serve com ip do servidor

php artisan serve --host=192.168.1.99

### Config Swagger no servidor

composer require "darkaonline/l5-swagger"

php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"

php artisan migrate:fresh --seed

Sempre que realizar uma alteração no Swagger, é necessário realizar o comando l5-swagger:generate, pois assim vai publicar as alterações.

php artisan l5-swagger:generate

Acesse a página /api/documentation ou verificar o 'routes' em '/config/l5-swagger.php'

 'routes' => [
       'api' => 'api/documentation',
  ],

http://192.168.1.99:8000/api/documentation

## App

Definir o ip do server dentro \app\lib\constant.dart na linha 4.

const baseURL = 'http://192.168.1.99:8000/api'

Executar o app

flutter run
