

/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */

/*
-------------------------------------- GENERAL FUNCTIONS -------------------------------------------
 */

/**
 * Change string to title case.
 * @param str
 * @returns {string}
 */
function titleCase(str) {
    str = str.toLowerCase().split(' ');
    for (let i = 0; i < str.length; i++) {
        str[i] = str[i].charAt(0).toUpperCase() + str[i].slice(1);
    }
    return str.join(' ');
}

/**
 * Decode string into html
 * @param inputStr
 * @returns {string}
 */
function decodeToHTML(inputStr) {
    const textarea = document.createElement("textarea");
    textarea.innerHTML = inputStr;
    return textarea.value;
}

/**
 * Get price in the correct format, or return a default statement when price is not a number.
 * @param price
 * @param callForPricing
 */
function getPrice(price, callForPricing = false) {
    price = parseFloat(price);
    if (!isNaN(price) && price > 0 && price !== null) {
        return CURRENCY + (Math.round(price * 100) / 100).toFixed(PRICE_DECIMALS);
    } else {
        if (callForPricing === true) {
            return 'Call for pricing';
        } else {
            return null;
        }
    }
}

/*
-------------------------------------- COOKIES -------------------------------------------
 */

/**
 * Get or set a cookie either for the user or catalog side, depending on which controller is active.
 * @param getOrSet
 * @param cookieSuffix
 * @param cookieValue
 * @param daysToExpire
 */
function cookieManagement(getOrSet, cookieSuffix, cookieValue = null, daysToExpire = 14) {
    let prefix = '';

    if(CONTROLLER) {
        prefix = CONTROLLER + '_';
    } else {
        return null;
    }

    if (getOrSet === 'get') {
        return getCookie(prefix + cookieSuffix);
    } else if (getOrSet === 'set' && cookieValue != null) {
        setCookie(prefix + cookieSuffix, cookieValue, daysToExpire);
        return null;
    } else {
        return null;
    }
}

/**
 * Set a cookie.
 * @param key
 * @param value
 * @param expireInDays
 */
function setCookie(key, value, expireInDays) {
    const d = new Date();
    d.setTime(d.getTime() + (expireInDays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = key + "=" + value + ";" + expires + ";path=/";
}

/**
 * Get a cookie.
 * @param key
 * @returns {string}
 */
function getCookie(key) {
    let name = key + "=";
    let cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        let c = cookies[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

/**
 * Show all cookies
 * @returns {string}
 */
function showAllCookies() {
    let cookies = document.cookie.split(';');
    let aString = '';
    for (let i = 1 ; i <= cookies.length; i++) {
        aString += i + ' ' + cookies[i-1] + "\n";
    }
    return aString;
}

/**
 * Get microtime for calculating page load.
 * @returns {number}
 */
function microtime() {
    let now;

    if(typeof performance !== 'undefined' && performance.now) {
        now = (performance.now() + performance.timing.navigationStart) / 1000;
    }
    else {
        now = (Date.now ? Date.now() : new Date().getTime()) / 1000;
    }

    return (now);
}

/**
 * Calculate page load time.
 */
function pageLoadTime() {
    const startTime = parseFloat($('#page-load-time').data('start-time'));
    if (!isNaN(startTime)) {
        const endTime = microtime();
        $('#page-load-time').text((endTime - startTime).toFixed(4));
    }
}