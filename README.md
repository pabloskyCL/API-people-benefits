## Comando para levantar Proyecto

Estando en la carpeta del proyecto ejecutar:
    *corroborar que el puerto 80 y el 3306 no esten usados
    *copiar .env.example y nombrar la copia con .env

- `docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs`

- `./vendor/bin/sail up -d`
- `./vendor/bin/sail php artisan key:generate`
- `./vendor/bin/sail php artisan migrate`

El archivo `API-Year-Benefits.postman_collection.json` es el archivo de la colleccion a importar en postman.

## Test Unitario

- `./vendor/bin/sail php artisan test`
