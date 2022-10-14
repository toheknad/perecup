const express = require('express');
const app = express();
const port = 3000

const urls  = require('./avito/apartament/getUrls');


// GET method route
/**
 * PARAMS pageStart - с какой странице начинают парситься ссылки(включительно)
 * PARAMS pageEnd - по какую страницу(включительно)
 */
app.get('/apartment/novostroyka/urls', function (req, res) {
    let pageStart = req.query.pageStart;
    let pageEnd = req.query.pageEnd;
    let url = "https://www.avito.ru/stavropol/kvartiry/prodam/novostroyka-ASgBAQICAUSSA8YQAUDmBxSOUg?s=104"
    let type = 'novostroyka'
    urls(pageStart, pageEnd, url, type).then(e => {
        res.json(e);
    })
});

app.get('/apartment/na_dlitelnyy_srok/urls', function (req, res) {
    let pageStart = req.query.pageStart;
    let pageEnd = req.query.pageEnd;
    let url = "https://www.avito.ru/stavropol/kvartiry/sdam/na_dlitelnyy_srok-ASgBAgICAkSSA8gQ8AeQUg?cd=1&rn=25934&s=104"
    let type = 'na_dlitelnyy_srok'
    urls(pageStart, pageEnd, url, type).then(e => {
        res.json(e);
    })
});

app.get('/apartment/posutochno/urls', function (req, res) {
    let pageStart = req.query.pageStart;
    let pageEnd = req.query.pageEnd;
    let url = "https://www.avito.ru/stavropol/kvartiry/sdam/posutochno/-ASgBAgICAkSSA8gQ8AeSUg?cd=1&rn=25935&s=104"
    let type = 'posutochno'
    urls(pageStart, pageEnd, url, type).then(e => {
        res.json(e);
    })
});

app.get('/apartment/page-count', function (req, res) {
});

app.listen(port, () => {
    console.log(`Example app listening on port ${port}`)
})