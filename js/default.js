let siteParams = GET(apiUrl + "/site-params").response;
const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);

if (urlParams.get("referral") != "") {
    setCookie("referral_code", urlParams.get("referral"), 365);
}
const referral_code = getCookie("referral_code");

function refreshSiteParams() {
    siteParams = GET(apiUrl + "/site-params").response;
};