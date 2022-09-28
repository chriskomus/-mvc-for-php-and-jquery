

/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */

// Site wide settings provided as data attributes.
const siteInfo = $('#site-info');
const ROOT_URL = siteInfo.data('root-url');
let container = $('#sortable-wrapper');

// controller and parameters
const CONTROLLER = $(container).data('api');
const PARAMETER = $(container).data('parameter');

// Most data on the site (ie: index.php of each view) is populated by JSON data.
// jsonAllData contains all data required for the page
// jsonControllerData is the original state of the controller specific data
// jsonColumns is the list of columns displayed on the page
// currentData is updated after every filter or sort change
let jsonAllData = [];
let jsonControllerData = [];
let jsonColumns = [];
let currentData = [];

// API call urls and promises will go in these arrays
let urls = [];
let promises = [];

// If a controller has been set, get the API urls for the columns and the data
if (CONTROLLER) {
    urls = [
        ['columns', encodeURI(ROOT_URL + '/api/columns/' + CONTROLLER)],
        ['mainData', encodeURI(ROOT_URL + '/api/' + CONTROLLER + '/' + PARAMETER)]
    ];
}

// Check if extraUrls has been defined in a controller .js file. This will contain additional API call(s).
// Add it to the end of the urls that will be called.
if (typeof extraUrls !== 'undefined') {
    urls = [...urls, ...extraUrls];
}

// return a new promise for an API call containing the fetched data
const apiPromise = (url) => {
    return new Promise((resolve, reject) => {
        resolve(fetch(encodeURI(url)).then(result => result.json()));
    })
}

// For each url in the array, push a new promise to the array of promises.
// Create a new object in the jsonAllData array that contains the name of the API call. This will later have the
// results added to in once Promise.all in main.js has been fulfilled.
if (urls.length > 0) {
    urls.map((url) => {
        promises.push(apiPromise(url[1]));
        jsonAllData.push({'name': url[0],'results': []});
    })
}