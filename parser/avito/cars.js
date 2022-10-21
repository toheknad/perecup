const rp        = require('request-promise');
const puppeteer = require('puppeteer-extra');
// const puppeteer = require('puppeteer');
const fs = require('fs');
const randomUseragent = require('random-useragent');
const UserAgent = require("user-agents");

const StealthPlugin = require('puppeteer-extra-plugin-stealth')
puppeteer.use(StealthPlugin())


module.exports = async (url, proxy) => {

    // Return anonymized version of original URL - looks like http://127.0.0.1:16383
    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    for (let i = 0; i < 3; i++) {
        console.log(`Waiting ${i} seconds...`);
        await sleep(i * 1000);
    }
    const baseUrl = url;
    // const baseUrl = 'https://2ip.ru/';
    // const baseUrl = 'https://arh.antoinevastel.com/bots/areyouheadless';
    // const baseUrl = 'https://bot.sannysoft.com';;


    const userAgent = new UserAgent({ platform: 'Win32' });
    let httpProxy = 'http://'+proxy;
    const browser = await puppeteer.launch({
        headless: true,
        args: [
            "--disable-gpu",
            "--disable-dev-shm-usage",
            "--disable-setuid-sandbox",
            "--no-sandbox",
            // "--user-agent=" + userAgent + "",
            // "--proxy-server=socks4://176.123.56.58:3629",
            // "--proxy-server="+httpProxy,
            // "--proxy-server=http://188.143.169.29:30153",
        ]
    });

    const username = 'iparchitect_17211_21_10_22';
    const password = 'f6i3rABhAFb6KRTdn8';
    const page = await browser.newPage();

    // await page.authenticate({
    //     username: username,
    //     password: password,
    // });

    console.log(""+userAgent);
    console.log(proxy);
    // console.log(baseUrl);
    await page.setRequestInterception(true);
    page.on('request', (request) => {
        if (['image', 'font', 'stylesheet', 'script'].indexOf(request.resourceType()) !== -1) {
            request.abort();
        } else {
            request.continue();
        }
    });
    await page.goto(baseUrl);


    await page.evaluate(`
      let ads = document.querySelectorAll('div[class*="items-ads"]');
      if (ads) {
        for(var i=0; i< ads.length; i++){
            ads[i].remove();
        }
      }
    `);
    // await page.screenshot({path: 'buddy-screenshot.png',  fullPage: true });
    // await page.waitForSelector('span[data-marker="pagination-button/next"]');
    //количество страниц≠

    let data = await page.$$eval('div[data-marker="catalog-serp"] div[itemtype="http://schema.org/Product"]', elements => {
        return elements.map(el => {
            if (!el.parentElement.getAttribute('data-marker')) {
                return {};
            }
            // return el.children[0].getAttribute('class');
            let name = el.querySelector('a[data-marker="item-title"] h3').textContent;
            let url = el.querySelector('a[data-marker="item-title"]').getAttribute('href');
            let price = el.querySelector('meta[itemprop="price"]').getAttribute('content');
            let description = el.querySelector('div[data-marker="item-specific-params"]').textContent;
            let time = el.querySelector('div[data-marker="item-date"]').textContent;
            let city = el.querySelector('div[class*="geo-georeferences"] span span').textContent;
            return {
                name,
                price,
                description,
                time,
                url,
                city
            };
        })
    });
    // console.log(data);
    let filtered = data.filter(function(value, index, arr){
        return value.name;
    });
    for (let i = 0; i < filtered.length; i++) {
        filtered[i].baseUrl = url;
    }
    if (filtered.length > 0) {
        console.log('YES');
    } else {
        console.log('NO')
    }

    await browser.close();
    // console.log('DONE!');
    return filtered;
};