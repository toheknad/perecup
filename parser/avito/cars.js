const rp        = require('request-promise');
const puppeteer = require('puppeteer');
const fs = require('fs');
const randomUseragent = require('random-useragent');
const UserAgent = require("user-agents");


module.exports = async (url) => {

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    for (let i = 0; i < 5; i++) {
        console.log(`Waiting ${i} seconds...`);
        await sleep(i * 1000);
    }
    console.log('Done');
    const baseUrl = url;

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
    console.log(baseUrl);
    await page.setRequestInterception(true);
    page.on('request', (request) => {
        if (['image', 'stylesheet', 'font', 'script'].indexOf(request.resourceType()) !== -1) {
            request.abort();
        } else {
            request.continue();
        }
    });
    await page.goto(baseUrl);

    await page.screenshot({path: 'buddy-screenshot.png',  fullPage: true });
    // await page.waitForSelector('span[data-marker="pagination-button/next"]');
    //количество страниц
    console.log('2323');


    let data = await page.$$eval('div[itemtype="http://schema.org/Product"]', elements => {
        return elements.map(el => {
            let name = el.querySelector('a[data-marker="item-title"] h3').textContent;
            let url = el.querySelector('a[data-marker="item-title"]').getAttribute('href');
            let price = el.querySelector('meta[itemprop="price"]').getAttribute('content');
            let description = el.querySelector('div[data-marker="item-specific-params"]').textContent;
            let time = el.querySelector('div[data-marker="item-date"]').textContent;
            return {
                name,
                price,
                description,
                time,
                url
            };
        })
    });

    for (let i = 0; i < data.length; i++) {
        data[i].baseUrl = url;
    }

    console.log(data);
    await browser.close();
    console.log('DONE!');
    return data;
};