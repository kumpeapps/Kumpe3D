const changeQty = document.querySelector("#qty");
const priceLabel = document.querySelector("#priceLabel");
const totalPriceLabel = document.querySelector("#totalPriceLabel");
const skuLabel = document.querySelector("#skuLabel");
const addToCartButton = document.querySelector("#addToCartButton");
const titleCrumb = document.querySelector("#titleCrumb");
const titleLabel = document.querySelector("#titleLabel");
const descriptionLabel = document.querySelector("#descriptionLabel");
if (env == 'dev') {
    Swal.fire(
        'Pre-Prod Server!',
        'You are viewing the Pre-Production/Dev server. Orders submitted via this site will not be filled or charged. Only PayPal sandbox accounts/credit cards will work.',
        'warning'
    );
};
const querySKU = urlParams.get('sku')
let product = GET(apiUrl + "/product?sku=" + querySKU).response
titleCrumb.innerHTML = product.title
titleLabel.innerHTML = product.title
descriptionLabel.innerHTML = product.description
document.getElementById("categoryLabel").innerHTML = product.categories
document.getElementById("tagsLabel").innerHTML = product.tags
refresh();

function getColorValue() {
    const ele = document.getElementsByName('radioColor');
    for (i = 0; i < ele.length; i++) {
        if (ele[i].checked)
            return ele[i].value;
    }
    return "000";
};
function getColorValue() {
    const ele = document.getElementsByName('radioColor');
    for (i = 0; i < ele.length; i++) {
        if (ele[i].checked)
            ele[i].addEventListener("change", changedColor());
    }
};

function changedColor() {
    const color_id = getColorValue();
    const base_sku = product.sku_parts.base_sku;
    const qty = document.getElementById('qty').value;
    let sku = base_sku + '-' + color_id;
    skuLabel.innerHTML = sku;
};

changeQty.addEventListener("change", function () {
    changedQty();
});

changeQty.addEventListener("keyup", function () {
    changedQty();
});

addToCartButton.addEventListener("click", function () {
    addToCart();
});

function changedQty() {
    const qty = document.getElementById('qty').value;
    const isOnSaleBadge = document.getElementById('isOnSaleBadge');
    const productPrice = GET(apiUrl + "/product-price?sku=" + querySKU + "&quantity=" + qty).response
    if (Boolean(productPrice.isOnSale)) {
        isOnSaleBadge.removeAttribute("hidden")
    }
    let totalPrice = productPrice.price
    // Update total price on qty change (and give wholesale price if >=10)
    if (productPrice.price !== productPrice.originalPrice) {
        totalPrice = productPrice.price * qty;
        const originalTotal = productPrice.originalPrice * qty;
        priceLabel.innerHTML = '$' + productPrice.price + ' <del>$' + productPrice.originalPrice + '</del>';
        const newTotalPriceLabel = '$' + totalPrice + ' <del>$' + originalTotal + '</del>';
        totalPriceLabel.innerHTML = newTotalPriceLabel;
    } else {
        totalPrice = productPrice.price * qty;
        priceLabel.textContent = '$' + productPrice.price;
        const newTotalPriceLabel = '$' + totalPrice;
        totalPriceLabel.textContent = newTotalPriceLabel;
    }
    changedColor();
};

function isColorSet() {
    const ele = document.getElementsByName('radioColor');
    for (i = 0; i < ele.length; i++) {
        if (ele[i].checked)
            return true;
    }
    return false;
};

function refresh() {
    changedQty();
    updateShoppingCartModal();
};