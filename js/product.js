const changeQty = document.querySelector("#qty");
const priceLabel = document.querySelector("#priceLabel");
const totalPriceLabel = document.querySelector("#totalPriceLabel");
const skuLabel = document.querySelector("#skuLabel");
const addToCartButton = document.querySelector("#addToCartButton");
if (env == 'dev') {
    Swal.fire(
        'Pre-Prod Server!',
        'You are viewing the Pre-Production/Dev server. Orders submitted via this site will not be filled or charged. Only PayPal sandbox accounts/credit cards will work.',
        'warning'
    );
};
const querySKU = urlParams.get('sku')
let product = GET(apiUrl + "/product?sku=" + querySKU).response
refresh();

function getColorValue() {
    const ele = document.getElementsByName('radioColor');
    for (i = 0; i < ele.length; i++) {
        if (ele[i].checked)
            return ele[i].value;
    }
    return "000";
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
    const productPrice = GET(apiUrl + "/product-price?sku=" + querySKU + "&quantity=" + qty).response
    let totalPrice = productPrice.price
    // Update total price on qty change (and give wholesale price if >=10)
    if (productPrice.isOnSale) {
        totalPrice = productPrice.discountPrice * qty;
        const originalTotal = productPrice.originalPrice * qty;
        priceLabel.innerHTML = '$' + productPrice.discountPrice + ' <del>$' + productPrice.originalPrice + '</del>';
        const newTotalPriceLabel = '$' + totalPrice + ' <del>$' + originalTotal + '</del>';
        totalPriceLabel.innerHTML = newTotalPriceLabel;
    } else if (qty < productPrice.wholesaleQty) {
        totalPrice = productPrice.price * qty;
        priceLabel.textContent = '$' + productPrice.price;
        const newTotalPriceLabel = '$' + totalPrice;
        totalPriceLabel.textContent = newTotalPriceLabel;
    } else {
        totalPrice = productPrice.wholesale_price * qty;
        const originalTotal = productPrice.price * qty;
        priceLabel.innerHTML = '$' + productPrice.wholesale_price + ' <del>$' + productPrice.price + '</del>';
        const newTotalPriceLabel = '$' + totalPrice + ' <del>$' + originalTotal + '</del>';
        totalPriceLabel.innerHTML = newTotalPriceLabel;
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