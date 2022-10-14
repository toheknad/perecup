const config    = require('../../../config');
const puppeteer = require('puppeteer');
const {json} = require("express");
const fs = require("fs");

const ABOUT_HOUSE = require('../config/houseParams');
const ABOUT_APARTAMENTS = require('../config/apartamentParams');
const matchParamsHelper = require('../../helper/matchParamsHelper');

/**
 * https://www.avito.ru/stavropol/kvartiry/prodam/novostroyka-ASgBAQICAUSSA8YQAUDmBxSOUg?cd=1&rn=25928&s=104
 * Отвечает за квартиры новостройки
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

    let apartament = [];
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
            return null;
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
    apartament = {
        options: {
            type: 'novostroyka',
            category: 'prodam',
            url: url
        },
        title,
        images,
        price,
        description,
        address,
        aboutHouse: matchParamsHelper(ABOUT_HOUSE, params),
        aboutApartments: matchParamsHelper(ABOUT_APARTAMENTS, params)
    };

    await browser.close();
    return apartament
}
