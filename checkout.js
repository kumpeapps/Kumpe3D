paypal.Buttons({
    style: {
      disableMaxWidth: true ,
      shape: "pill",
      label: "pay"
    },
  }).render('#paypal-button-container');

function buildCheckout() {
    const itemsDiv = document.getElementById('checkout_items');
    const subtotalLabel = document.getElementById('cart_subtotal');
    const totalLabel = document.getElementById('cart_total');
    removeAllChildNodes(itemsDiv);
    subtotalLabel.innerHTML = '$' + cartLS.total();
    totalLabel.innerHTML = '$' + (cartLS.total() + 10)
    cart = cartLS.list();
    cart.forEach(renderCheckoutList);
    
    function renderCheckoutList(element, _, _) {
        const img_url = element["image_url"];
        const title = element["name"];
        const qty = element["quantity"];
        const original_price = element["original_price"];
        let price = '$' + (element["price"] * qty);
        if (element["price"] != original_price) {
            price = price + ' <del>$' + (original_price * qty) + '</del>';
        }
        const sku = element["id"];
        let div1 = document.createElement("div");
        div1.setAttribute("class", "cart-item style-1");
        let div11 = document.createElement("div");
        div11.setAttribute("class", "dz-media");
        let img111 = document.createElement("img");
        img111.setAttribute("src", img_url);
        div11.appendChild(img111);
        div1.appendChild(div11);
        let div12 = document.createElement("div");
        div12.setAttribute("class", "dz-content");
        let title121 = document.createElement("h6");
        title121.setAttribute("class", "title mb-0");
        title121.innerHTML = title;
        div12.appendChild(title121);
        let span122 = document.createElement("span");
        span122.setAttribute("class", "price")
        span122.innerHTML = price;
        div12.appendChild(span122);
        div1.appendChild(div12);
        itemsDiv.appendChild(div1);
    };
};

function onload() {
    refresh();
};

function refresh() {
    updateShoppingCartModal();
    buildCheckout();
    buildCountries();
};

function buildCountries() {
    const countrySelect = document.getElementById('countrySelect');
    countryList = countries;
    removeAllChildNodes(countrySelect);
    countryList.forEach(renderCountrySelect);
    function renderCountrySelect(element, _, _) {
        const countryName = element[3];
        const countryAbbrv = element[2];
        let option = document.createElement("option");
        option.setAttribute("value", countryAbbrv);
        option.innerHTML = countryName;
        if (countryAbbrv === 'US') {
            option.setAttribute("selected", true);
        }
        countrySelect.appendChild(option);
    };
    buildStates();
};

function buildStates() {
    const stateSelect = document.getElementById('stateSelect');
    const countrySelect = document.getElementById('countrySelect');
    const selectedCountry = countrySelect.value;
    removeAllChildNodes(stateSelect);
    states.forEach(renderStateSelect);
    function renderStateSelect(element, _, _) {
        const stateName = element[3];
        const countryAbbrv = element[4];
        if (countryAbbrv === selectedCountry) {
            let option = document.createElement("option");
            option.setAttribute("value", stateName);
            option.innerHTML = stateName;
            stateSelect.appendChild(option);
        }
    };
    buildCities();
};

// FIXME: Cities not updated when state is updated.
function buildCities() {
    let citySelect = document.getElementById('citySelect');
    const stateSelect = document.getElementById('stateSelect');
    const selectedState = stateSelect.value
    removeAllChildNodes(citySelect);
    if (selectedState === "Alabama") {
        cities.forEach(renderCitySelect);
    }
    function renderCitySelect(element, _, _) {
        const cityName = element[3];
        const stateName = element[6];
        if (stateName === selectedState) {
            let option = document.createElement("option");
            option.setAttribute("value", cityName);
            option.innerHTML = cityName;
            citySelect.appendChild(option);
        }
    };
};
