let Crawler = require("simplecrawler");
let cheerio = require ("cheerio");

process.env["NODE_TLS_REJECT_UNAUTHORIZED"] = 0;

// initiate Crawler
let crawler = new Crawler('https://www.nabu.de/umwelt-und-ressourcen/index.html');
crawler.maxDepth = 2;

let result = [];

crawler.on("fetchcomplete", function(queueItem, responseBuffer, response) {


    console.log(queueItem.url);

    var $ = cheerio.load(responseBuffer.toString("utf8"));

    console.log(responseBuffer.toString("utf8"));

    $('.slide').each(function (i, elem) {
        //console.log($(this).html());
    });

});



crawler.discoverResources = function(buffer, queueItem) {

    var $ = cheerio.load(buffer.toString("utf8"));

    return $(".slide a[href]").map(function () {
        return $(this).attr("href");
    }).get();

};



crawler.start();