const rp        = require('request-promise');
const puppeteer = require('puppeteer-extra');
// const puppeteer = require('puppeteer');
const fs = require('fs');
const randomUseragent = require('random-useragent');
const UserAgent = require("user-agents");

const StealthPlugin = require('puppeteer-extra-plugin-stealth')
puppeteer.use(StealthPlugin())


module.exports = async (url, proxy, sleepSeconds, browser) => {

    // Return anonymized version of original URL - looks like http://127.0.0.1:16383
    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    const baseUrl = url;
    // const baseUrl = 'https://www.avito.ru/stavropol/avtomobili/hyundai/miniven/avtomat-ASgBAQICAUTgtg2imzECQOa2DRTGtyjwtg0U7rco?cd=1&f=ASgBAQECA0TyCrCKAYYUyOYB4LYNopsxAkDmtg0Uxrco8LYNFO63KAFFxpoMGHsiZnJvbSI6MCwidG8iOjEwMDAwMDAwfQ&moreExpensive=1&radius=300&s=104';
    // const baseUrl = 'https://m.avito.ru/stavropol/avtomobili/s_probegom/hyundai/accent/avtomat-ASgBAQICA0SGFMjmAeC2DaKbMeK2DaSbMQFA8LYNFO63KA?cd=1&f=ASgBAQECBETyCrCKAYYUyOYB4LYNopsx4rYNpJsxAUDwtg0U7rcoAkX4AhZ7ImZyb20iOjg5OCwidG8iOm51bGx9xpoMFnsiZnJvbSI6MCwidG8iOjIwMDAwMH0&radius=300';
    // const baseUrl = 'https://2ip.ru/';
    // const baseUrl = 'https://arh.antoinevastel.com/bots/areyouheadless';
    // const baseUrl = 'https://bot.sannysoft.com';;


    // const browser = await puppeteer.launch({
    //     headless: true,
    //     args: [
    //         "--disable-gpu",
    //         "--disable-dev-shm-usage",
    //         "--disable-setuid-sandbox",
    //         "--no-sandbox",
    //         // "--user-agent=" + userAgent + "",
    //         // "--proxy-server=socks4://176.123.56.58:3629",
    //         // "--proxy-server="+httpProxy,
    //         "--proxy-server=http://"+proxy.ip,
    //         // "--proxy-server=http://83.217.7.249:11223",
    //     ]
    // });

    const page = await browser.newPage();
    //
    await page.authenticate({
        username: proxy.login,
        password: proxy.password,
        // username: 'w2Sa86Rcpo',
        // password: 'sXT5wUtBWx',
    });

    console.log(proxy.ip);
    await page.setRequestInterception(true);
    var i = 0;
    page.on('request', (request) => {
        // if (['image', 'font', 'stylesheet', 'script', 'gif', 'png', 'svg+xml', 'webp', 'javascript', 'xhr'].indexOf(request.resourceType()) !== -1) {
        //     request.abort();
        // } else {
        //     request.continue();
        // }
        if (i > 1) {
            request.abort();
        } else {
            request.continue();
        }
        i++;
    });
    try {
        await page.goto(baseUrl);
        // console.log('YES');
        // return [];
        // await page.waitForSelector('span[data-marker="pagination-button/next"]');
    } catch(e) {
        console.log(e);
        // await browser.close();
        // await page.screenshot({path: '11before.png',  fullPage: true });
        await page.close()
        return [];
    }
    // слип для проверки удаления объявлений из блока дороже чем вы указали
    for (let i = 0; i < sleepSeconds; i++) {
        console.log(`Waiting ${i} seconds...`);
        await sleep(i * 1000);
    }

    console.log(baseUrl);
    // await page.screenshot({path: 'before.png',  fullPage: true });
    // удаление дороже чем у вас
    // const sfasf = await page.content();
    // fs.writeFile('tset223.html', sfasf, function (err) {
    //     if (err) return console.log(err);
    //     console.log('Hello World > helloworld.txt');
    // });

    await page.evaluate(`
      let extra = document.querySelectorAll('div[class*="items-extra"]');
        if (extra) {
            for(var i=0; i< extra.length; i++){
                let test = true;
    
                while(test) {
                    let sib = extra[i].nextSibling
                    if (sib) {
                        sib.remove();
                    } else {
                        test = false;
                    }
                }
            }
        }
    `);

    // // удаление объявления в других городах
    // await page.evaluate(`
    //   let extra = document.querySelector('div[class*="items-extra"]');
    //   if (extra) {
    //     let test = true;
    //      while(test) {
    //         let sib = extra.nextSibling
    //         if (sib) {
    //             sib.remove();
    //         } else {
    //             test = false;
    //         }
    //      }
    //   }
    // `);

    await page.evaluate(`
      let ads = document.querySelectorAll('div[class*="items-ads"]');
      if (ads) {
        for(var i=0; i< ads.length; i++){
            ads[i].remove();
        }
      }
    `);

    // await page.screenshot({path: 'after.png',  fullPage: true });
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
            let city = '';
            if (el.querySelector('div[class*="geo-georeferences"] span span')) {
                city = el.querySelector('div[class*="geo-georeferences"] span span').textContent;
            } else {
                city = el.querySelector('span[class*="geo-address"] span').textContent
            }
            let image = '';
            if (el.querySelector('li[class*="photo-slider-list-item"]')) {
                image = el.querySelector('li[class*="photo-slider-list-item"]').getAttribute('data-marker');
                image = image.replace('slider-image/image-', '');
            }

            return {
                name,
                price,
                description,
                time,
                url,
                city,
                image
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
        // await page.screenshot({path: 'before.png',  fullPage: true });
    }
    // await page.screenshot({path: 'before.png',  fullPage: true });
    // await browser.close();
    await page.close()
    // console.log('DONE!');
    return filtered;
};