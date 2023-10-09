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
    if (isOnSale()){
        totalPrice = discountPrice * qty;
        const originalTotal = originalPrice * qty;
        priceLabel.innerHTML = '$' + discountPrice + ' <del>$' + originalPrice + '</del>';
        const newTotalPriceLabel = '$' + totalPrice + ' <del>$' + originalTotal + '</del>';
        totalPriceLabel.innerHTML = newTotalPriceLabel;
    } else if (qty < wholesaleQty) {
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
    changedQty();
    updateShoppingCartModal();
};

function onload() {
    if (env == "dev") {
    Swal.fire(
        'PreProd Server',
        'This is a PreProd/Development server. Orders submitted via this site will not be charged or filled.',
        'info'
    );
}
    refresh();
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