const siteParams = GET(apiUrl + "/site-params").response;
const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);