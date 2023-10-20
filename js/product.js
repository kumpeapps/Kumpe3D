// const changeQty = document.querySelector("#qty");
// const priceLabel = document.querySelector("#priceLabel");
// const totalPriceLabel = document.querySelector("#totalPriceLabel");
// const addToCartButton = document.querySelector("#addToCartButton");
// const titleCrumb = document.querySelector("#titleCrumb");
// const titleLabel = document.querySelector("#titleLabel");
// const descriptionLabel = document.querySelector("#descriptionLabel");
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

refresh();

function getColorValue() {
    const ele = document.getElementsByName('radioColor');
    for (i = 0; i < ele.length; i++) {
        if (ele[i].checked)
            return ele[i].value;
    }
    return "000";
};

function buildColorOptions() {
    const base_sku = product.sku_parts.base_sku;
    const colorOptions = GET(apiUrl + "/filament?sku=" + base_sku + "&filter=" + product['filament_filter']).response;
    const colorOptionsBlock = document.getElementById("colorOptions");
    removeAllChildNodes(colorOptionsBlock);
    colorOptions.forEach(build);
    function build(element, _, _) {
        const div = document.createElement("div");
        div.setAttribute("class", "radio-value image-radio border border-secondary rounded");
        const input = document.createElement("input");
        input.setAttribute("class", "form-check-input radio-value");
        input.setAttribute("type", "radio");
        input.setAttribute("name", "radioColor");
        input.setAttribute("id", "radioColor");
        input.setAttribute("value", element['swatch_id']);
        input.setAttribute("aria-label", "...");
        input.setAttribute("onchange", "changedColor()");
        const span1 = document.createElement("span");
        const span2 = document.createElement("span");
        span1.innerHTML = element['type'] + " " + element['swatch_id'];
        span2.innerHTML = element['color_name'];
        const status = document.createElement("span");
        status.setAttribute("class", "badge mb-2 " + element['badge']);
        status.innerHTML = element['status'];
        const img = document.createElement("img");
        img.setAttribute("class", "rounded");
        img.setAttribute("src", "https://images.kumpeapps.com/filament?swatch=" + element['swatch_id'] + "_" + base_sku);
        if (element['status'] == "Backordered" || element['status'] == "Discontinued") {
            input.disabled = true;
        };
        div.appendChild(input);
        div.appendChild(span1);
        div.appendChild(span2);
        div.appendChild(status);
        div.appendChild(img);
        colorOptionsBlock.appendChild(div);
    };
        var rad = document.colorOptions.radioColor;
        for (var i = 0; i < rad.length; i++) {
            rad[i].addEventListener('change', changedColor);
        }
};

function changedColor() {
    const skuLabel = document.querySelector("#skuLabel");
    const color_id = getColorValue();
    const base_sku = product.sku_parts.base_sku;
    const sku = base_sku + '-' + color_id;
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
    document.getElementById("categoryLabel").innerHTML = product.categories
    document.getElementById("tagsLabel").innerHTML = product.tags
    changedQty();
    updateShoppingCartModal();
    buildColorOptions();
};