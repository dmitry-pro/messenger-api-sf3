 Тестовое задание на позицию PHP Backend Developer
==================================================

Описание тестового задания см. в файле [DESCRIPTION.md](DESCRIPTION.md)

# Технологии

Фрэймворк: Symfony 3.4.  
СУБД: MySQL 5.5+ + Doctrine 2  
Тесты: Codeception  
Документация API: Swagger (NelmioApiDocBundle)  
Code Style: Symfony3

REST-инфраструктура построена на базе FosRestBundle.  
Для сериализации данных использован JmsSerializer.

# Инструкции

## Установка приложения через **_docker-compose_** (рекомендуется)

При первом запуске:  
`docker-compose build`  
`docker-compose up -d`  
`docker exec -it messenger_api_pod sh -c "cd /var/www/application/bin && ./project-install"`  

Если установка уже проводилась:
`docker-compose up -d` 

Всё! Веб-интерфейс доступен по адресу [http://localhost:8000/](http://localhost:8000/)  

Доступ по SSH:  
`docker exec -it messenger_api_pod sh -c "cd /var/www/application && sh"`  
Запуск тестов:  
`docker exec -it messenger_api_pod sh -c "cd /var/www/application && ./vendor/bin/codecept run"`  

## Авторизация для API

Приложение использует упрощённую версию механизма stateless-аутентификации для API. Токен авторизации должен присутствовать в каждом запросе. Подсистема ролей отсутствует, это подразумевает, что у пользователей мессенджера одинаковые права - условию задачи это не противоречит.  
Все открытые API-эндпоинты защищены. Запрос без валидного токена вернёт 401 ошибку. 404 ответы для несуществующих сущностей и адресов также предусмотрены.   
Чтобы авторизовать тестового юзера в Swagger UI, нужно:
 - открыть страницу Swagger UI [http://localhost:8000/api/doc/messenger/v1]()
 - нажать на кнопку "Authorize"
 - в поле `value` ввести `Bearer 12345` (токен тестового юзера `panda`)
 - нажать кнопку "Authorize" 

Теперь можно тестировать API с помощью Swagger UI. Текущим юзером для созданных сообщений будет юзер `panda`.  

## Known Issues
 - У Codeception не самая лучшая интеграция с Symfony, поэтому тесты вместо фикстур используют SQL-дамп.  
 - Модели ответов в Swagger упрощены до минимума, коды ответов ошибок были также не документированы. Все сообщения ошибок API покрыты тестами.
 - Система авторизации создана в учебных целях и не пригодна для продакшена, система ролей и защиты URL по маске были откинуты в пользу контроля кода.
 - В структуре таблиц используется связь _many-to-many_ для диалогов/пользователей, это означает, что структура БД рассчитана на диалоги с произвольным количеством участников, но API умеет только отсылать личные сообщения. Для каждой пары диалог создаётся один раз, и затем он переиспользуется при каждой отправке сообщений между этими двумя пользователями.
 - Из-за составного ключа во вспомогательной таблице _many-to-many_ мы лишены возможности создавать диалог между "собой и собой" в этой версии схемы БД. При попытке создания такого диалога API вернёт 400 ошибку.
 - Передача пароля к БД в параметре командной строки (упрощение)


# Лицензия

[MIT](https://opensource.org/licenses/MIT).

---
