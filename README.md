## Comando para levantar Proyecto

Estando en la carpeta del proyecto ejecutar:
    *corroborar que el puerto 80 y el 3306 no esten usados

- ./vendor/bin/sail up -d
- ./vendor/bin/sail php artisan migrate

El archivo `API-Year-Benefits.postman_collection.json` es el archivo de la colleccion a importar en postman.

## Test Unitario

- ./vendor/bin/sail php artisan test
