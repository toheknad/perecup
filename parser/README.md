#Парсеры 

#Установка
После того как были запущены докер-контейнеры выполняем команды ниже
docker-compose exec parser-nodejs npm ci
docker-compose exec parser-nodejs chmod -R o+rwx node_modules/puppeteer/.local-chromium
Все готово к работе

#Воркеры:
docker-compose exec parser-nodejs node common/url/router  - воркер, который принимает урл и отправляет в очередь 
урлы объявлений
docker-compose exec parser-nodejs node common/realestate/router - парсинг отдельной страницы с последующим 
возвратом в очередь для бэка