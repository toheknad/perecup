const puppeteer = require('puppeteer');
const {json} = require("express");
const fs = require("fs");

const ABOUT_HOUSE = require('../config/houseParams');
const RULES = require('../config/rulesParams');
const matchParamsHelper = require('../../helper/matchParamsHelper');

/**
 * https://www.avito.ru/stavropol/kvartiry/prodam/novostroyka-ASgBAQICAUSSA8YQAUDmBxSOUg?cd=1&rn=25928&s=104
 * Отвечает за дома
 */
(async () => {

    const baseUrl = "https://www.avito.ru";
    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    await page.setRequestInterception(true);
    page.on('request', (request) => {
        if (['image', 'stylesheet', 'font', 'script'].indexOf(request.resourceType()) !== -1) {
            request.abort();
        } else {
            request.continue();
        }
    });
    await page.setViewport({ width: 1280, height: 800 })

    // let jsonData = require('../taunhaus-sdam.json');

    let apartament = [];
    let params;
    for (const [key, task] of Object.entries(jsonData)) {
        for (const [k, url] of Object.entries(task)) {

            let title;
            let images;
            let price;
            let description;
            let address;
            try {
                await page.goto(baseUrl + url.url);
                console.log('Страница загружена');
                if (await page.$('.item-closed-warning__content') !== null ) {
                    continue;
                }
                await page.waitForSelector('.item-address__string')
                title = await page.$eval('.title-info-title-text', (el) => el.textContent)
                images = await page.$$eval('.js-gallery-img-frame', elements => {
                    return elements.map(el => {
                        let imageUrl = el.getAttribute('data-url');
                        return {
                            url: imageUrl
                        };
                    })
                })

                price = await page.$eval('.js-item-price', (el) => el.getAttribute('content'))
                description = await page.$eval('div[itemprop="description"]', (el) => el.textContent)
                address = await page.$eval('.item-address__string', (el) => el.textContent)


                params = await page.$$eval('.item-params-label', elements => {
                    return elements.map(el => {
                        return {
                            type: el.textContent,
                            value: el.parentNode.innerText
                        }
                    })
                })


            } catch (err) {
                console.error(err.message);
            }
            apartament.push({
                options: {
                    type: 'taunhaus',
                    category: 'sdam'
                },
                title,
                images,
                price,
                description,
                address,
                aboutHouse: matchParamsHelper(ABOUT_HOUSE, params),
                rules: matchParamsHelper(RULES, params)
            });
            console.log('Успешно: ' + url.url);
        }
    }
    fs.writeFile("./taunhaus-sdam-result.json", JSON.stringify(apartament), (err) => {
        if (err)
            console.log(err);
        else {
            console.log("File written successfully\n");
            console.log("The written has the following contents:");
        }
    });
    await browser.close();
})()

async function sleep(millis) {
    console.log('sleep');
    return new Promise(resolve => setTimeout(resolve, millis));
}