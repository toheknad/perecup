module.exports = {
    backendApi: 'http://localhost:8080/',
    urlQueueParse: 'parse_url', // таски для парсинга по регионам с бэка
    urlQueueParseChecked: 'parse_url_checked', // таски для парсинга по регионам с бэка
    // urlQueueCheck: 'parse_url_check', // таски с урлами объявлений с парсера
    // realestateParseQueue: 'realestate_parse', // таски с урлами для парсинга с бэка
    // realestateParseDoneQueue: 'realestate_done', // таски с готовыми объявлениями для бэка
    rabbitMQUrl: "amqp://admin:admin@rabbitmq:5672"
}