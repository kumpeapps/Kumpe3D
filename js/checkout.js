let fundingSource;
let cart;
onload();

paypal.Buttons({
    style: {
        disableMaxWidth: true,
        shape: "pill",
        label: "pay"
    },

    onClick: (data) => {
        // fundingSource = "venmo"
        fundingSource = data.fundingSource;
        console.info("Funding Source: " + fundingSource);
    },
    createOrder: function (data, actions) {
        const debugEnabled = getCookie("debug")
        const checkoutData = getCheckoutData();
        if (debugEnabled) {
            console.debug(checkoutData);
        }
        let itemsArray = [];
        for (product in checkoutData['cart']) {
            const productdata = checkoutData['cart'][product];
            itemsArray.push({
                name: productdata['title'],
                quantity: productdata['quantity'],
                sku: productdata['sku'],
                unit_amount: {
                    currency_code: "USD",
                    value: productdata['price']
                }
            })
        };
        return actions.order.create({

            purchase_units: [{
                amount: {
                    value: checkoutData.total,
                    currency_code: "USD",
                    description: "Kumpe3D by KumpeApps LLC",
                    soft_descriptor: "Kumpe3D",
                    breakdown: {
                        item_total: {
                            value: checkoutData.subtotal,
                            currency_code: "USD"
                        },
                        shipping: {
                            value: checkoutData.shippingCost,
                            currency_code: "USD"
                        },
                        tax_total: {
                            value: checkoutData.taxes,
                            currency_code: "USD"
                        }
                    }
                },
                items: itemsArray,
                shipping: {
                    type: "SHIPPING",
                    name: {
                        full_name: checkoutData.firstName + " " + checkoutData.lastName
                    },
                    address: {
                        address_line_1: checkoutData.address,
                        address_line_2: checkoutData.address2,
                        admin_area_2: checkoutData.city,
                        admin_area_1: checkoutData.state,
                        postal_code: checkoutData.zip,
                        country_code: checkoutData.country
                    }
                }
            }],
            payment_source: {
                paypal: {
                    email_address: checkoutData.emailAddress,
                    name: {
                        given_name: checkoutData.firstName,
                        surname: checkoutData.lastName
                    },
                    address: {
                        address_line_1: checkoutData.address,
                        address_line_2: checkoutData.address2,
                        admin_area_2: checkoutData.city,
                        admin_area_1: checkoutData.state,
                        postal_code: checkoutData.zip,
                        country_code: checkoutData.country
                    },
                    experience_context: {
                        brand_name: "Kumpe3D",
                        shipping_preference: "SET_PROVIDED_ADDRESS",
                        payment_method_preference: "IMMEDIATE_PAYMENT_REQUIRED"
                    }
                }
            }
        })
    },

    onApprove: function (data, actions) {
        // Payment Approved
        return actions.order.capture().then(function (details) {
            debugEnabled = getCookie("debug");
            if (debugEnabled) {
                console.debug(details);
            }
            const transactionID = details['id'];
            const purchaseUnits = details['purchase_units'];
            const payments = purchaseUnits[0]['payments'];
            const captureID = payments['captures'][0]['id'];
            let checkoutData = getCheckoutData();
            const client_ip = GET("https://api.ipify.org", false);
            const browser = navigator.userAgent;
            checkoutData.ppTransactionID = transactionID;
            checkoutData.ppCaptureID = captureID;
            checkoutData.paymentMethod = fundingSource;
            checkoutData.client_ip = client_ip;
            checkoutData.browser = browser;
            if (!debugEnabled) {
                orderSuccess();
            }
            post_body = {checkout_data: checkoutData, session_id: sessionID}
            checkout_response = putJSON(apiUrl + "/checkout")
            if (debugEnabled) {
                console.debug(checkout_response)
            }
        });
    },

    onError(err) {
        console.error(err);
        Swal.fire(
            'PayPal Error',
            'An error occurred while processing your PayPal payment. Please try again.',
            'error'
        );
    }
}).render('#paypal-button-container');

function setListeners() {
    document.getElementById("firstNameInput").addEventListener("change", function () {
        validateFName();
    });
    document.getElementById("lastNameInput").addEventListener("change", function () {
        validateLName();
    });
    document.getElementById("streetAddressInput").addEventListener("change", function () {
        validateAddress();
    });
    document.getElementById("zipCodeInput").addEventListener("change", function () {
        validateZipCode();
    });
    document.getElementById("phoneInput").addEventListener("change", function () {
        validatePhone();
    });
    document.getElementById("emailInput").addEventListener("change", function () {
        validateEmail();
    }); document.getElementById("firstNameInput").addEventListener("keyup", function () {
        validateFName();
    });
    document.getElementById("lastNameInput").addEventListener("keyup", function () {
        validateLName();
    });
    document.getElementById("streetAddressInput").addEventListener("keyup", function () {
        validateAddress();
    });
    document.getElementById("zipCodeInput").addEventListener("keyup", function () {
        validateZipCode();
    });
    document.getElementById("phoneInput").addEventListener("keyup", function () {
        validatePhone();
    });
    document.getElementById("emailInput").addEventListener("keyup", function () {
        validateEmail();
    });
};

function orderSuccess() {
    showPayPal(false);
    refresh();
    Swal.fire(
        'Success',
        'Your order has been submitted!',
        'success'
    ).then(function(){
        location.reload();
    });
};

function devData() {
    const firstName = document.getElementById("firstNameInput");
    const lastName = document.getElementById("lastNameInput");
    const companyName = document.getElementById("companyName");
    const address = document.getElementById("streetAddressInput");
    const zip = document.getElementById("zipCodeInput");
    const email = document.getElementById("emailInput");
    const phone = document.getElementById("phoneInput");
    firstName.value = "Justin";
    lastName.value = "Doe";
    address.value = "700 W Walnut St";
    zip.value = "72756";
    phone.value = "5555555555";
    email.value = "jakumpe@dev.kumpes.com";
    companyName.value = "KumpeApps Dev"
    validateAddress();
    validateEmail();
    validateFName();
    validateLName();
    validatePhone();
    validateZipCode();
};

function getCheckoutData() {
    const customerID = getCookie("user_id");
    const firstName = document.getElementById("firstNameInput").value;
    const lastName = document.getElementById("lastNameInput").value;
    const address = document.getElementById("streetAddressInput").value;
    const address2 = document.getElementById("streetAddress2Input").value;
    const city = document.getElementById("cityInput").value;
    let state = document.getElementById("stateInput").value;
    const zip = document.getElementById("zipCodeInput").value;
    const country = document.getElementById("countrySelect").value;
    const companyName = document.getElementById("companyName").value;
    const orderNotes = document.getElementById("orderNotes").value;
    const emailAddress = document.getElementById("emailInput").value;
    if (state === 'Arkansas') {
        state = 'AR';
    }
    const addressInfo = {
        "fName": firstName,
        "lName": lastName,
        "company": companyName,
        "address": address,
        "address2": address2,
        "city": city,
        "state": state,
        "zip": zip,
        "country": country,
        "comments": orderNotes,
        "email": emailAddress
    };
    const checkoutData = postJSON(apiUrl + "/checkout?user_id=" + customerID + "&session_id=" + sessionID, addressInfo).response;
    const cart = checkoutData.cart.list;
    const subtotal = checkoutData.cart.subtotal;
    const taxes = checkoutData.taxTotal;
    const total = checkoutData.grandTotal;
    const checkout = {
        customerID: customerID,
        firstName: firstName,
        lastName: lastName,
        companyName: companyName,
        address: address,
        address2: address2,
        city: city,
        state: state,
        zip: zip,
        country: country,
        shippingCost: checkoutData.shippingCost,
        cart: cart,
        subtotal: subtotal,
        taxes: taxes,
        discount: 0.00,
        total: total,
        orderNotes: orderNotes,
        emailAddress: emailAddress,
        taxData: checkoutData.taxes
    };
    return checkout;
}

function buildCheckout() {
    const user = getCookie("user_id");
    const firstName = document.getElementById("firstNameInput").value;
    const lastName = document.getElementById("lastNameInput").value;
    const address = document.getElementById("streetAddressInput").value;
    const address2 = document.getElementById("streetAddress2Input").value;
    const city = document.getElementById("cityInput").value;
    let state = document.getElementById("stateInput").value;
    const zip = document.getElementById("zipCodeInput").value;
    const country = document.getElementById("countrySelect").value;
    const companyName = document.getElementById("companyName").value;
    const orderNotes = document.getElementById("orderNotes").value;
    const emailAddress = document.getElementById("emailInput").value;
    if (state === 'Arkansas') {
        state = 'AR';
    }
    const addressInfo = {
        "fName": firstName,
        "lName": lastName,
        "company": companyName,
        "address": address,
        "address2": address2,
        "city": city,
        "state": state,
        "zip": zip,
        "country": country,
        "comments": orderNotes,
        "email": emailAddress
    };
    const checkoutData = postJSON(apiUrl + "/checkout?user_id=" + user + "&session_id=" + sessionID, addressInfo).response;
    const cart = checkoutData.cart.list;
    const itemsDiv = document.getElementById('checkout_items');
    const subtotalLabel = document.getElementById('cart_subtotal');
    const totalLabel = document.getElementById('cart_total');
    removeAllChildNodes(itemsDiv);
    subtotalLabel.innerHTML = '$' + checkoutData.cart.subtotal;
    totalLabel.innerHTML = '$' + checkoutData.grandTotal;
    cart.forEach(renderCheckoutList);

    function renderCheckoutList(element, _, _) {
        const img_url = element["img_url"];
        const title = element["title"];
        const original_price = element["originalTotal"];
        let price = '$' + (element["totalPrice"]);
        if (element["totalPrice"] != original_price) {
            price = price + ' <del>$' + (original_price) + '</del>';
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
    setListeners();
    refresh();
    isValidCheck();
    if (env == 'dev') {
        Swal.fire(
            'PreProd Server!',
            'You are viewing the Pre-Production/Dev server. Orders submitted via this site will not be filled or charged. Only PayPal sandbox accounts/credit cards will work.',
            'warning'
        );
    };
};

function validateFName() {
    const fieldID = "firstNameInput";
    const field = document.getElementById(fieldID).value;
    const valid = validator.isAlpha(field, ["en-US"], { ignore: " -" });
    fieldValidated(fieldID, valid);
};

function validateLName() {
    const fieldID = "lastNameInput";
    const field = document.getElementById(fieldID).value;
    const valid = validator.isAlpha(field, ["en-US"], { ignore: " -" });
    fieldValidated(fieldID, valid);
};

function validateAddress() {
    const fieldID = "streetAddressInput";
    const field = document.getElementById(fieldID).value;
    const valid = validator.isAlphanumeric(field, ["en-US"], { ignore: " -" });
    fieldValidated(fieldID, valid);
};

function validatePhone() {
    const fieldID = "phoneInput";
    const field = document.getElementById(fieldID).value;
    const valid = validator.isMobilePhone(field);
    fieldValidated(fieldID, valid);
};

function validateEmail() {
    const fieldID = "emailInput";
    const field = document.getElementById(fieldID).value;
    const valid = validator.isEmail(field, { host_blacklist: ["yopmail.com"] });
    fieldValidated(fieldID, valid);
};

function validateZipCode() {
    const fieldID = "zipCodeInput";
    const country = document.getElementById("countrySelect").value;
    const field = document.getElementById(fieldID).value;
    const valid = validator.isPostalCode(field, country);
    if (valid) {
        zipData = GET(apiUrl + "/zipcode?single_record=1&zip=" + field).response;
        document.getElementById("stateInput").value = zipData.state_id;
        document.getElementById("cityInput").value = zipData.city;
        document.getElementById("cityContainer").removeAttribute("hidden");
        document.getElementById("stateContainer").removeAttribute("hidden");
    }
    fieldValidated(fieldID, valid);
};

function fieldValidated(fieldID, valid = true) {
    const field = document.getElementById(fieldID);
    isValid[fieldID] = valid;
    if (valid) {
        field.setAttribute("class", "form-control is-valid");
    } else {
        field.setAttribute("class", "form-control is-invalid");
    }
    isValidCheck();
};

function getArkansasTaxes() {
    const address = document.getElementById("streetAddressInput").value;
    const city = document.getElementById("cityInput").value;
    const state = document.getElementById("stateInput").value;
    const zip = document.getElementById("zipCodeInput").value;
    const taxes = document.getElementById("taxes");
    const taxTotalLabel = document.getElementById("totalTax");
    const subtotal = cart.subtotal;
    const tax_data = GET(apiUrl + "/taxes?address=" + address + "&city=" + city + "&state=" + state + "&zip=" + zip + "&subtotal=" + subtotal).response;
    let taxLabel = "";
    let taxTotal = 0;
    if (tax_data.is_state_taxable) {
        taxLabel = taxLabel + tax_data.taxable_state + ": $" + tax_data.state_tax + "<br>";
        taxTotal = taxTotal + tax_data.state_tax;
    }
    if (tax_data.is_county_taxable) {
        taxLabel = taxLabel + tax_data.taxable_county + " County: $" + tax_data.county_tax + "<br>";
        taxTotal = taxTotal + tax_data.county_tax;
    }
    if (tax_data.is_city_taxable) {
        taxLabel = taxLabel + tax_data.taxable_city + ": $" + tax_data.city_tax + "<br>";
        taxTotal = taxTotal + tax_data.city_tax;
    }
    taxes.innerHTML = taxLabel;
    taxTotalLabel.innerHTML = "Tax Total: $" + taxTotal;
    refresh();
};

function isValidCheck() {
    const isValidArray = Object.values(isValid);
    const isAllValid = allTrue(isValidArray);
    const state = document.getElementById("stateInput").value;
    if (state === 'AR' || state === 'Arkansas') {
        getArkansasTaxes();
    }
    showPayPal(isAllValid);
};

// Show/Hide PayPal buttons
function showPayPal(show = true) {
    const paypalContainer = document.getElementById("paypal-button-container");
    const paymentBlockedNotice = document.getElementById("paymentBlockedNotice");
    if (show) {
        paypalContainer.removeAttribute("hidden");
        paymentBlockedNotice.setAttribute("hidden", true);
    } else {
        paymentBlockedNotice.removeAttribute("hidden");
        paypalContainer.setAttribute("hidden", true);
    }
};

function refresh() {
    user = getCookie("user_id");
    cart = GET(apiUrl + "/cart?user_id=" + user + "&session_id=" + sessionID).response;
    updateShoppingCartModal();
    buildCheckout();
};