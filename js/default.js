let siteParams = GET(apiUrl + "/site-params").response;
const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);

function refreshSiteParams() {
    siteParams = GET(apiUrl + "/site-params").response;
};