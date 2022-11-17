const request = require('request');
const config = require('../../config');

/**
 * AVITO
 */

const cars = require('../../avito/cars');


const amqp = require("amqplib/callback_api");
const fs = require("fs");
const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth')
puppeteer.use(StealthPlugin())

const redis = require("redis");
const moment = require('moment');


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
            // console.log(task);
            let result = {};
            if (task.source === 'avito') {
                for (let i = 0; i < task.links.length; i++) {

                    let fullUrl = task.links[i].url;


                    result = await cars(fullUrl, task.proxy.data, task.links[i].sleepSeconds);

                    let isFirstCheck;
                    if (task.links[i].isFirstCheck === true) {
                        isFirstCheck = true;
                        console.log('TRUE');
                    } else {
                        isFirstCheck = false;
                        console.log('FALSE');
                    }
                    for (let c = 0; c < result.length; c++) {
                        // let e = {test:'123123'};
                        // let opts = { headers: { 'type': 'json'}};
                        ch.sendToQueue(config.urlQueueParseChecked, Buffer.from(JSON.stringify(result[c])), {
                            headers: {
                                content_type: 'application/json',
                                idUser: task.links[i].userId,
                                isFirstCheck: isFirstCheck
                            },
                            timestamp: 1665161929
                        });
                    }
                }
            }
            let redisClient;

            let proxy = task.proxy
            proxy.lastUsingTime = new Date();
            var moment = require('moment');

            var dateFormat = 'YYYY-MM-DD HH:mm:ss';

            var seconds = proxy.holdSeconds;
            proxy.holdPassTime = moment(proxy.lastUsingTime).add('seconds', seconds).format(dateFormat);
            redisClient = await redis.createClient({
                url: 'redis://redis:6379'
            });
            await redisClient.on("error", (error) => console.error(`Error : ${error}`));
            await redisClient.connect();
            await redisClient.lPush('hold_proxies', JSON.stringify(proxy));

            // нужно создать очередь вручную в браузере, чтобы все было ок
            ch.ack(msg);
        }, { noAck: false });
    });
});




