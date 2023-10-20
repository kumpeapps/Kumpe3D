function GET(yourUrl, return_json = true) {
    var Httpreq = new XMLHttpRequest(); // a new request
    Httpreq.open("GET", yourUrl, false);
    Httpreq.send(null);
    response = Httpreq.responseText;
    if (return_json) {
        return JSON.parse(response);
    } else {
        return response;
    }
};

function POST(yourUrl, data, return_json = true) {
    var Httpreq = new XMLHttpRequest(); // a new request
    Httpreq.open("POST", yourUrl, false);
    Httpreq.send(data);
    response = Httpreq.responseText;
    if (return_json) {
        return JSON.parse(response);
    } else {
        return response;
    }
};

function postJSON(yourUrl, data, return_json = true) {
    var Httpreq = new XMLHttpRequest(); // a new request
    JSON.stringify(data);
    Httpreq.open("POST", yourUrl, false);
    Httpreq.send(data);
    response = Httpreq.responseText;
    if (return_json) {
        return JSON.parse(response);
    } else {
        return response;
    }
};

const getJSON = async url => {
    const response = await fetch(url);
    if (!response.ok) // check if response worked (no 404 errors etc...)
        throw new Error(response.statusText);

    if (response.status === 204) // check if 204 (No Content)
        return null;
    else
        return response.json();
};