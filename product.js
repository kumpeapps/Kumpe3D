const changeQty = document.querySelector("#qty");
const priceLabel = document.querySelector("#priceLabel");
const totalPriceLabel = document.querySelector("#totalPriceLabel");
const skuLabel = document.querySelector("#skuLabel");
const addToCartButton = document.querySelector("#addToCartButton");


changeQty.addEventListener("change", function() {
    changedQty();
});

changeQty.addEventListener("keyup", function() {
    changedQty();
});

addToCartButton.addEventListener("click", function() {
    addToCart();
});

function changedQty() {
    const qty = document.getElementById('qty').value;
    let totalPrice = price
    // Update total price on qty change (and give wholesale price if >=10)
    if (qty < 10) {
        totalPrice = price * qty;
        priceLabel.textContent = '$' + price;
        const newTotalPriceLabel = '$' + totalPrice;
        totalPriceLabel.textContent = newTotalPriceLabel;
    } else {
        totalPrice = wholesale_price * qty;
        const originalTotal = price * qty;
        priceLabel.innerHTML = '$' + wholesale_price + ' <del>$' + price + '</del>';
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
    updateShoppingCartModal();
}