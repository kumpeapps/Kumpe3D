const sessionID = getCookie("PHPSESSID");

updateShoppingCartModal();
updateBanner();

function buildShoppingCartModalList() {
    const spinHandle = loadingOverlay().activate();
    const ul = document.getElementById('shoppingCartModal');
    const subtotalLabel = document.getElementById('subtotalLabel');
    removeAllChildNodes(ul);
    cart = GET("https://api.preprod.kumpe3d.com/cart?user_id=0&session_id=" + sessionID).response;
    let subtotal = cart.subtotal;
    if (subtotal === null) {
        subtotal = 0;
    }
    subtotalLabel.innerHTML = '$' + subtotal;
    cart.list.forEach(renderShoppingCartModalList);
    const addToCartButton = document.querySelector("#cart_badge");
    const shoppingCartBadge = document.querySelector("#shopping_cart_badge");
    addToCartButton.innerHTML = cart.list.length;
    shoppingCartBadge.innerHTML = cart.list.length;

    function renderShoppingCartModalList(element, _, _) {
        const img_url = element["img_url"];
        const title = element["productTitle"] + "<br>(" + element['colorTitle'] + ")";
        const qty = element["quantity"];
        const original_price = element["originalPrice"];
        let price = '$' + (element["totalPrice"]);
        if (element["totalPrice"] != original_price) {
            price = price + ' <del>$' + (element['originalTotal']) + '</del>';
        }
        const sku = element["sku"];
        let li = document.createElement('li');
        let div1 = document.createElement('div');
        div1.setAttribute('class', 'cart-widget');
        let div11 = document.createElement('div');
        div11.setAttribute('class', 'dz-media me-4');
        let img111 = document.createElement('img');
        img111.setAttribute('src', img_url);
        img111.setAttribute('width', '80');
        img111.setAttribute('height', '80');
        let div12 = document.createElement('div');
        div12.setAttribute('class', 'cart-content');
        let title121 = document.createElement('h6');
        title121.setAttribute('class', 'title');
        title121.innerHTML = title + "<br>" + sku;
        let div122 = document.createElement('div');
        div122.setAttribute('class', 'd-flex align-items-center');
        let div1221 = document.createElement('div');
        div1221.setAttribute('class', 'btn-quantity light quantity-sm me-3');
        let input12211 = document.createElement('input');
        input12211.setAttribute('type', 'number');
        input12211.setAttribute('min', 0);
        input12211.setAttribute('onkeypress', "return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57")
        input12211.setAttribute('onchange', "cartQtyChange(event)");
        input12211.setAttribute('onkeyup', "cartQtyChange(event)");
        input12211.setAttribute('value', qty);
        input12211.setAttribute('id', sku);
        input12211.setAttribute('name', 'disable-demo-vertical2');
        // input12211.setAttribute('readonly', true);
        let text1222 = document.createElement('h6');
        text1222.setAttribute('class', 'dz-price text-primary mb-0');
        text1222.innerHTML = price;
        let button13 = document.createElement('button');
        button13.setAttribute('class', 'dz-close');
        let ticlose = document.createElement('i');
        ticlose.setAttribute('class', 'ti-close');
        button13.setAttribute('onclick', "deleteItem('" + sku + "')");
        button13.appendChild(ticlose);
        div1221.appendChild(input12211);
        div122.appendChild(div1221);
        div122.appendChild(text1222);
        div12.appendChild(title121);
        div12.appendChild(div122);
        div11.appendChild(img111);
        div1.appendChild(div11);
        div1.appendChild(div12);
        div1.appendChild(button13);
        li.appendChild(div1);

        ul.appendChild(li);
    }
    loadingOverlay().cancel(spinHandle);
};

function removeAllChildNodes(parent) {
    while (parent.firstChild) {
        parent.removeChild(parent.firstChild);
    }
};

function deleteItem(sku) {
    const spinHandle = loadingOverlay().activate();
    const data = {"sku": sku}
    deleteJSON(apiUrl + "/cart?user_id=0&session_id=" + sessionID, data, false);
    refresh();
    loadingOverlay().cancel(spinHandle);
};

function clearCart() {
    cartLS.destroy();
};

function updateShoppingCartModal() {
    buildShoppingCartModalList();
};

function cartQtyChange(event) {
    const spinHandle = loadingOverlay().activate();
    const sku = event.srcElement.id;
    const customization = "";
    const qty = parseInt(event.srcElement.value);
    const data = {
        "sku": sku,
        "quantity": qty,
        "customization": customization
    }
    putJSON(apiUrl + "/cart?user_id=0&session_id=" + sessionID, data);
    refresh();
    if (qty < 1) {
        deleteItem(sku);
    }
    loadingOverlay().cancel(spinHandle)
};

function updateBanner() {
    if (siteParams['storeNoticebanner'] !== '') {
        const siteBanner = document.getElementById("notificationBanner");
        siteBanner.setAttribute("class", siteParams['storeNoticebannerClass']);
        siteBanner.innerHTML = siteParams['storeNoticebanner'];
    }
};