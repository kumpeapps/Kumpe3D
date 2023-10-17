const changeQty = document.querySelector("#qty");
const priceLabel = document.querySelector("#priceLabel");
const totalPriceLabel = document.querySelector("#totalPriceLabel");
const skuLabel = document.querySelector("#skuLabel");
const addToCartButton = document.querySelector("#addToCartButton");
// refresh();
if (env == 'dev') {
    Swal.fire(
        'Pre-Prod Server!',
        'You are viewing the Pre-Production/Dev server. Orders submitted via this site will not be filled or charged. Only PayPal sandbox accounts/credit cards will work.',
        'warning'
    );
};
const querySKU = urlParams.get('sku')
let product = GET(apiUrl + "/product?sku=" + querySKU)


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
    if (isOnSale()) {
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

function getColorValue() {
    const ele = document.getElementsByName('radioColor');
    for (i = 0; i < ele.length; i++) {
        if (ele[i].checked)
            return ele[i].value;
    }
    return "000";
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

function isOnSale() {
    const currentDate = new Date();
    const currentTime = currentDate.getTime()
    const discountStartTime = discountStart.getTime()
    const discountEndTime = discountEnd.getTime()
    if (currentTime >= discountStartTime && currentTime <= discountEndTime) {
        // Item is on sale
        price = discountPrice;
        return true;
    } else {
        // Item is not on sale
        price = originalPrice;
        return false;
    }
};