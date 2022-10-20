const rp        = require('request-promise');
const puppeteer = require('puppeteer');
const fs = require('fs');
const randomUseragent = require('random-useragent');
const UserAgent = require("user-agents");

module.exports = async (url, proxy) => {

    // Return anonymized version of original URL - looks like http://127.0.0.1:16383
    // function sleep(ms) {
    //     return new Promise(resolve => setTimeout(resolve, ms));
    // }
    //
    // for (let i = 0; i < 5; i++) {
    //     console.log(`Waiting ${i} seconds...`);
    //     await sleep(i * 1000);
    // }
    const baseUrl = url;
    // const baseUrl = 'https://2ip.ru/';

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
            // `--proxy-server=socks5://bpro2xy.site:11429`,
            // `--proxy-server=http://mproxy.site:11429`,
            "--user-agent=" + userAgent + ""
        ]
    });

    const username = 'ldXwkC';
    const password = '9iQhKzAatkQt';
    const page = await browser.newPage();
    // await page.authenticate({
    //     username: username,
    //     password: password,
    // });

    // console.log(""+userAgent);
    console.log(proxy);
    // console.log(baseUrl);
    await page.setRequestInterception(true);
    page.on('request', (request) => {
        if (['image', 'stylesheet', 'font', 'script'].indexOf(request.resourceType()) !== -1) {
            request.abort();
        } else {
            request.continue();
        }
    });
    await page.goto(baseUrl);

    const html = await page.content();
    fs.writeFile('helloworld.html', html, function (err) {
        if (err) return console.log(err);
        console.log('Hello World > helloworld.txt');
    });

    await page.evaluate(`
      let ads = document.querySelectorAll('div[class*="items-ads"]');
      if (ads) {
        for(var i=0; i< ads.length; i++){
            ads[i].remove();
        }
      }
    `);
    const html2 = await page.content();
    fs.writeFile('helloworld2.html', html2, function (err) {
        if (err) return console.log(err);
        console.log('Hello World > helloworld.txt');
    });
    // await page.evaluate(`
    //   let sel = document.querySelector("${selector1}");
    //   if (sel) {
    //     sel.remove()
    //   }
    // `);
    // await page.evaluate(`
    //   let sel1 = document.querySelector("${selector2}");
    //   if (sel1) {
    //     sel1.remove()
    //   }
    // `);
    // await page.evaluate(`
    //   let sel2 = document.querySelector("${selector3}");
    //   if (sel2) {
    //     sel2.remove()
    //   }
    // `);
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
            return {
                name,
                price,
                description,
                time,
                url
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