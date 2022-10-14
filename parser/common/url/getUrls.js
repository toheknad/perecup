const rp        = require('request-promise');
const puppeteer = require('puppeteer');
const fs = require('fs');
const randomUseragent = require('random-useragent');
const UserAgent = require("user-agents");


module.exports = async (url, proxy) => {
    const baseUrl = url;
    console.log(proxy);
    const UserAgent = require("user-agents");
    const userAgent = new UserAgent({
        deviceCategory: "desktop",
        platform: "Linux x86_64",
    });
    const browser = await puppeteer.launch({
        headless: true,
        args: [
            "--disable-gpu",
            "--disable-dev-shm-usage",
            "--disable-setuid-sandbox",
            "--no-sandbox",
            // '--proxy-server='+proxy,
            "--user-agent=" + userAgent + ""
        ]
    });

    const page = await browser.newPage();
    console.log(""+userAgent);
    await page.setRequestInterception(true);
    page.on('request', (request) => {
        if (['image', 'stylesheet', 'font', 'script'].indexOf(request.resourceType()) !== -1) {
            request.abort();
        } else {
            request.continue();
        }
    });
    await page.goto(baseUrl);

    // await page.screenshot({path: 'buddy-screenshot.png',  fullPage: true });
    // await page.waitForSelector('span[data-marker="pagination-button/next"]');
    //количество страниц
    const nextButton = await page.$('span[data-marker="pagination-button/next"]');
    const prev = await page.evaluateHandle(el => el.previousElementSibling, nextButton);
    let countPages = await (await prev.getProperty('innerHTML')).jsonValue();
    countPages = parseInt(countPages);

    let data = {};
    for (let i = 1; i <= 2; i++) {
        try {
            await page.goto(baseUrl+"&p="+i);
            data[i] = await page.$$eval('div[itemtype="http://schema.org/Product"]', elements => {
                return elements.map(el => {
                    // let root = el;
                    let url = el.querySelector('a[itemprop="url"]').getAttribute('href');
                    // console.log(el.querySelector('div[data-marker="item-address"] div span').textContent);
                    // console.log(el.querySelector('.geo-georeferences-SEtee span').innerText);
                    let city = el.querySelector('[class^=geo-georeferences]');
                    if (city) {
                        city = city.textContent;
                    } else {
                        city = el.querySelector('[class^=geo-address]').textContent;
                    }
                    return {
                        url: url,
                        city: city
                    };
                })
            });
        } catch (err) {
            console.error(err.message);
        }
    }
    await browser.close();
    return data
};