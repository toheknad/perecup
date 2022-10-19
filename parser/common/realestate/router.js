// const urls  = require('./getUrls');
// const tasks  = require('./tasks.json');
const request = require('request');
const config = require('../../config');

/**
 * AVITO
 */

const cars = require('../../avito/cars');


const amqp = require("amqplib/callback_api");
const urls = require("../url/getUrls");
const fs = require("fs");


console.log('TEST213');
amqp.connect(config.rabbitMQUrl, (err, conn) => {
    if (err) {
        console.log(`Er22ror ${err}`);
    }
    conn.createChannel((error, ch) => {
        if (error) {
            console.log('TEST')
        }
        ch.assertQueue(config.urlQueueParse, { durable: true });
        ch.prefetch(1);
        ch.consume(config.urlQueueParse, async (msg) => {
            let task = JSON.parse(msg.content.toString());
            let result = {};
            if (task.source === 'avito') {

                let fullUrl = task.url;

                // console.log(fullUrl);
                result = await cars(fullUrl, task.proxy);
            }
            let isFirstCheck;
            if (task.isFirstCheck === true) {
                isFirstCheck = true;
            } else {
                isFirstCheck = false;
            }
            for (let i = 0; i < result.length; i++) {
                // let e = {test:'123123'};
                // let opts = { headers: { 'type': 'json'}};
                ch.sendToQueue(config.urlQueueParseChecked, Buffer.from(JSON.stringify(result[i])), {
                    headers:{
                        content_type:	'application/json',
                        idUser: task.userId,
                        isFirstCheck: isFirstCheck
                    },
                    timestamp:	1665161929
                });
            }

            // нужно создать очередь вручную в браузере, чтобы все было ок
            ch.ack(msg);
        }, { noAck: false });
    });
});




