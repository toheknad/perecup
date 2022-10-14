#Генерация JWT
bin/console lexik:jwt:generate-keypair

# Посмотреть очереди 
docker-compose exec rabbitmq rabbitmqctl list_queues 

#Удалить сообщения из указаной очереди parse_url
docker-compose exec rabbitmq rabbitmqctl purge_queue parse_url 

# Создание пользователя админки
bin/console admin:add_user your_user_name

#Вход в админку 
http://localhost:8080/adm360

#Для загрузки прокси нужно, чтобы лежал файл с ними в папке files/ в корне проекта
bin/console proxy:load




###>>> Комнады под крон

# Раз в минуту, отправка данных для собора ссылок объявлений
bin/console parser:send-parse-url

###<<< Комнады под крон


###>>> Команды под супервизор

# Слушатель сообщений с урлами на проверку
bin/console messenger:consume -vv check_url

###<<< Команды под супервизор