let product;
let querySKU;
let addToCartCount = 1;
refresh();

function load() {
    const titleCrumb = document.querySelector("#titleCrumb");
    const titleLabel = document.querySelector("#titleLabel");
    const descriptionLabel = document.querySelector("#descriptionLabel");
    querySKU = urlParams.get('sku')

    product = GET(apiUrl + "/product?sku=" + querySKU).response

    titleCrumb.innerHTML = product.title
    titleLabel.innerHTML = product.title
    descriptionLabel.innerHTML = product.description
    const changeQty = document.querySelector("#productQuantity");
    const addToCartButton = document.querySelector("#addToCartButton1");
    changeQty.addEventListener("change", function () {
        changedQty();
    });

    changeQty.addEventListener("keyup", function () {
        changedQty();
    });
    addToCartButton.addEventListener("click", function () {
        addToCart(this);
    }, once = true);
};

function getColorValue() {
    const ele = document.getElementsByName('radioColor');
    for (i = 0; i < ele.length; i++) {
        if (ele[i].checked)
            return ele[i].value;
    }
    return "000";
};

function buildColorOptions() {
    const colorOptionsBlock = document.getElementById("colorOptions");
    const base_sku = product.sku_parts.base_sku;
    const colorID = product.sku_parts.color;
    removeAllChildNodes(colorOptionsBlock);
    if (colorID != "NNN") {
        const colorOptions = GET(apiUrl + "/filament?sku=" + base_sku + "&filter=" + product['filament_filter'] + "&swatch_filter=" + colorID).response;
        colorOptions.forEach(build);
    } else {
        const nnn = {
            "type": "",
            "color_name": "N/A"
        }
    }
    
    function build(element, _, _) {
        const div = document.createElement("div");
        div.setAttribute("class", "radio-value border border-secondary rounded");
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
        if (getCookie("qr_images")) {
            img.setAttribute("src", "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" + base_sku + "-" + element['swatch_id']);
        } else {
            img.setAttribute("src", "https://images.kumpeapps.com/filament?swatch=" + element['swatch_id'] + "_" + base_sku);
        }
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
    const ele = document.getElementsByName('radioColor');
    if (ele.length === 1) {
        ele[0].setAttribute("checked", true);
    }
};

function changedColor() {
    const skuLabel = document.querySelector("#skuLabel");
    const color_id = getColorValue();
    const base_sku = product.sku_parts.base_sku;
    const sku = base_sku + '-' + color_id;
    skuLabel.innerHTML = sku;
};

function changedQty() {
    const priceLabel = document.querySelector("#priceLabel");
    const totalPriceLabel = document.querySelector("#totalPriceLabel");
    const qty = document.getElementById('productQuantity').value;
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
    load();
    document.getElementById("categoryLabel").innerHTML = product.categories
    document.getElementById("tagsLabel").innerHTML = product.tags
    changedQty();
    updateShoppingCartModal();
    buildColorOptions();
    loadingOverlay().cancel(spinHandle);
};

function addToCart(element) {
    const addToCartButtonTag = "addToCartButton" + addToCartCount;
    if (element.id == addToCartButtonTag) {
        removeAllChildNodes(document.getElementById("addToCartContainer"));
        addingToCart = true;
        const sku = skuLabel.innerHTML;
        const productQuantity = document.getElementById('productQuantity').value;
        if (!isColorSet()) {
            Swal.fire(
                'Error!',
                'Please select a color',
                'error'
            );
        } else {
            const data = {
                "sku": sku,
                "quantity": productQuantity,
                "customization": ""
            };
            postJSON(apiUrl + "/cart?user_id=0&session_id=" + sessionID, data);

            // document.getElementById("cartButton").click();
        }
        updateShoppingCartModal();
        addToCartCount += 1;
        const addToCartButtonTagNew = "addToCartButton" + addToCartCount;
        const button = document.createElement("a");
        button.setAttribute("class", "btn btn-secondary w-100");
        button.setAttribute("id", addToCartButtonTagNew);
        button.innerHTML = "ADD TO CART";
        document.getElementById("addToCartContainer").appendChild(button);
        const addToCartButton = document.querySelector("#" + addToCartButtonTagNew);
        addToCartButton.addEventListener("click", function () {
            addToCart(this);
        }, once = true);
        document.getElementById("cartButton").click();
    }
};
