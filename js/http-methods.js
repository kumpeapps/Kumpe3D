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
    data = JSON.stringify(data);
    Httpreq.open("POST", yourUrl, false);
    // Httpreq.setRequestHeader("Content-Type", "application/json");
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

function sendJSON(yourUrl, data){
			
    let result = document.querySelector('.result');
    let name = document.querySelector('#name');
    let email = document.querySelector('#email');
    
    // Creating a XHR object
    let xhr = new XMLHttpRequest();
    let url = "submit.php";

    // open a connection
    xhr.open("POST", url, true);

    // Set the request header i.e. which type of content you are sending
    xhr.setRequestHeader("Content-Type", "application/json");

    // Create a state change callback
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {

            // Print received data from server
            result.innerHTML = this.responseText;

        }
    };

    // Converting JSON data to string
    var data = JSON.stringify({ "name": name.value, "email": email.value });

    // Sending data with the request
    xhr.send(data);
}
