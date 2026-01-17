## Соберите и запустите
docker-compose up -d

## Запустите миграции
docker-compose exec app php artisan migrate

## Генерация ключа (если не сгенерирован)
docker-compose exec app php artisan key:generate

## Запустите тесты 
docker-compose exec app php vendor/bin/pest

## Повторных запуск миграций и наполнение бд тестовыми данными
docker-compose exec app php artisan migrate:fresh --seed

## Сгенерировать доку для swagger, если её нет
docker-compose exec app php artisan l5-swagger:generate

## Посмотреть документацию по api
http://localhost/api/documentation

## Доступ к апи: http://localhost/api/products
