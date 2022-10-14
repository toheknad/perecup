const config    = require('../../../config');
const puppeteer = require('puppeteer');
const {json} = require("express");
const matchParamsHelper = require("../../helper/matchParamsHelper");
const ABOUT_HOUSE = require("../config/houseParams");
const ABOUT_APARTAMENTS = require("../config/apartamentParams");
const ABOUT_RULES = require("../config/rulesParams");

/**
 * https://www.avito.ru/stavropol/kvartiry/sdam/na_dlitelnyy_srok-ASgBAgICAkSSA8gQ8AeQUg
 * Отвечает за квартиры аренду на длительный срок
 */
module.exports = async (url) => {

    const browser = await puppeteer.launch({
        headless: true,
        args: [
            "--disable-gpu",
            "--disable-dev-shm-usage",
            "--disable-setuid-sandbox",
            "--no-sandbox",
        ]
    });
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

    let params;

    let title;
    let images;
    let price;
    let description;
    let address;
    try {
        await page.goto(url);
        console.log('Страница загружена');
        if (await page.$('.item-closed-warning__content') !== null ) {
            return null
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
    let apartament = {
        options: {
            type: 'naDlitelnyySrok',
            category: 'sdam',
            url: url
        },
        title,
        images,
        price,
        description,
        address,
        aboutHouse: matchParamsHelper(ABOUT_HOUSE, params),
        aboutApartments: matchParamsHelper(ABOUT_APARTAMENTS, params),
        aboutRules: matchParamsHelper(ABOUT_RULES, params)
    };
    await browser.close();
    return apartament
}

async function autoScroll(page){
    await page.evaluate(async () => {
        await new Promise((resolve, reject) => {
            var totalHeight = 0;
            var distance = 100;
            var timer = setInterval(() => {
                var scrollHeight = document.body.scrollHeight;
                window.scrollBy(0, distance);
                totalHeight += distance;

                if(totalHeight >= scrollHeight){
                    clearInterval(timer);
                    resolve();
                }
            }, 200);
        });
    });
}