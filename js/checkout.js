let fundingSource;
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
        console.log(fundingSource);
    },
    createOrder: function (data, actions) {
        const checkoutData = getCheckoutData();
        let itemsArray = [];
        for (product in checkoutData['cart']) {
            const productdata = checkoutData['cart'][product];
            itemsArray.push({
                name: productdata['name'],
                quantity: productdata['quantity'],
                sku: productdata['id'],
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
            const purchaseUnits = details['purchase_units'];
            const payments = purchaseUnits[0]['payments'];
            const transactionID = payments['captures'][0]['id'];
            let checkoutData = getCheckoutData();
            let orderID = "unavailable.";
            checkoutData.ppTransactionID = transactionID;
            checkoutData.paymentMethod = fundingSource;
            checkoutData.statusID = 3;
            orderSuccess();
            fetch("submit_order.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    checkout_data: checkoutData,
                    session_id: sessionID
                })
            });
        });
    },

    onError(err) {
        console.log(err);
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
    });document.getElementById("firstNameInput").addEventListener("keyup", function () {
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
    cartLS.destroy();
    onload();
    Swal.fire(
        'Success',
        'Your order has been submitted!',
        'success'
    );
    onload();
    showPayPal(false);
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
    address.value = "123 Easy St";
    zip.value = "72103";
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
    const customerID = 0;
    const firstName = document.getElementById("firstNameInput");
    const lastName = document.getElementById("lastNameInput");
    const address = document.getElementById("streetAddressInput");
    const address2 = document.getElementById("streetAddress2Input");
    const city = document.getElementById("citySelect");
    const state = document.getElementById("stateSelect");
    const zip = document.getElementById("zipCodeInput");
    const country = document.getElementById("countrySelect");
    const shipping = document.getElementById("shippingCost");
    const companyName = document.getElementById("companyName");
    const orderNotes = document.getElementById("orderNotes");
    const emailAddress = document.getElementById("emailInput");
    const cart = cartLS.list();
    const subtotal = cartLS.total();
    const taxes = 0;
    const total = subtotal + taxes + parseFloat(shipping.value);
    const checkout = {
        customerID: customerID,
        firstName: firstName.value,
        lastName: lastName.value,
        companyName: companyName.value,
        address: address.value,
        address2: address2.value,
        city: city.value,
        state: state.value,
        zip: zip.value,
        country: country.value,
        shippingCost: parseFloat(shipping.value),
        cart: cart,
        subtotal: subtotal,
        taxes: taxes,
        discount: 0.00,
        total: total,
        orderNotes: orderNotes.value,
        emailAddress: emailAddress.value
    };
    return checkout;
}

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
    console.warn("Arkansas taxes required");
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
    updateShoppingCartModal();
    buildCheckout();
    buildCountries();
};