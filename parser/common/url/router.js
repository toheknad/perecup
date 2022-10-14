const urls  = require('./getUrls');
const tasks  = require('./tasks.json');
const request = require('request');
const config = require('../../config');
const amqp = require('amqplib/callback_api');
const fs = require("fs");



amqp.connect(config.rabbitMQUrl,  (err, conn) => {
    if (err) {
        console.log(`Error ${err}`);
    }
    conn.createChannel((error, ch) => {
        if (error) {
            console.log(`Error ${err}`);
        }
        ch.prefetch(1);
        ch.assertQueue(config.urlQueueParse, {durable: true});
        ch.consume(config.urlQueueParse, async (msg) => {
            let task = JSON.parse(msg.content.toString());
            let result;
            if (task.source === 'avito') {
                const baseUrl =  "https://www.avito.ru"
                // тут получаем таск для парсинга ссылки ПО КРАЮ
                let fullUrl = task.url;
                console.log(fullUrl);
                result = await urls(fullUrl, task.proxy);

                let nextTask = [];
                // тут происходит добавление ссылок в очередь, чтобы проверить нужно ли нам проверять
                // ссылки, вдруг уже все есть у нас в БД
                for (const [key, page] of Object.entries(result)) {
                    for (const [k, url] of Object.entries(page)) {
                        let newTask = {
                            realEstateName: task.realEstateName,
                            realEstateType: task.realEstateType,
                            terms: task.terms,
                            url: baseUrl + url.url,
                            city: url.city,
                            source: 'avito'
                        }

                        ch.sendToQueue(config.urlQueueCheck, Buffer.from(JSON.stringify(newTask)));
                    }
                }
            }

            fs.writeFile(Math.random(1000) + ".json", JSON.stringify(result), (err) => {
                if (err)
                    console.log(err);
                else {
                    console.log('OK')
                }
            });
            ch.ack(msg);
        }, {noAck: false});
    });
});

test = async () => {
    for (let i = 0; i <= 5; i++) {
        await sleep(1000);
        console.log(i);
    }
}

function sleep(ms) {
    return new Promise((resolve) => {
        setTimeout(resolve, ms);
    });
}




