## Установка и запуск приложения на локальной машине (старый способ) 
`composer install`  
`bin/console doctrine:database:create`  
`bin/console doctrine:schema:update --force`  
Загружаем фикстуры пользователей (нужно для корректной работы со Swagger UI):  
`bin/console doctrine:fixtures:load -e dev --no-interaction`  

Создаём тестовую БД (необходимо перед запуском функциональных тестов API):  
`bin/console doc:database:create -e test`  
`bin/console doctrine:sch:update --force -e test`  

## Запуск тестового сервера  
`bin/console server:run http://localhost:8000`

## Тесты

Запустить тесты Codeception:  
`./vendor/bin/codecept run`  