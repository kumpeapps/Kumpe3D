const sessionID = getCookie("PHPSESSID");
const spinHandle = loadingOverlay();
refreshSiteParams();
updateShoppingCartModal();
updateBanner();
buildHeaderCatalogs();
buildHeaderCategories();

function buildShoppingCartModalList() {
    loadingOverlay().activate(spinHandle)
    const ul = document.getElementById('shoppingCartModal');
    const subtotalLabel = document.getElementById('subtotalLabel');
    removeAllChildNodes(ul);
    const user = getCookie("user_id")
    cart = GET(apiUrl + "/cart?user_id=" + user + "&session_id=" + sessionID).response;
    let subtotal = cart.subtotal;
    if (subtotal === null) {
        subtotal = 0;
    }
    subtotalLabel.innerHTML = '$' + subtotal;
    cart.list.forEach(renderShoppingCartModalList);
    const showCartButton = document.querySelector("#cart_badge");
    const shoppingCartBadge = document.querySelector("#shopping_cart_badge");
    showCartButton.innerHTML = cart.list.length;
    shoppingCartBadge.innerHTML = cart.list.length;

    function renderShoppingCartModalList(element, _, _) {
        const img_url = element["img_url"];
        const customization = element['customization'];
        let title = element["productTitle"] + "<br>(" + element['colorTitle'] + ")";
        if (element['colorTitle'] == null) {
            title = element["productTitle"];
        }
        if (customization !== "") {
            title = title + "<br>Customization: " + customization;
        }
        const qty = element["quantity"];
        const original_price = element["originalTotal"];
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
};

function removeAllChildNodes(parent) {
    while (parent.firstChild) {
        parent.removeChild(parent.firstChild);
    }
};

function deleteItem(sku) {
    loadingOverlay().activate(spinHandle);
    const data = {"sku": sku};
    const user = getCookie("user_id")
    deleteJSON(apiUrl + "/cart?user_id=" + user + "&session_id=" + sessionID, data, false);
    refresh();
};

function clearCart() {
    cartLS.destroy();
};

function updateShoppingCartModal() {
    buildShoppingCartModalList();
};

function cartQtyChange(event) {
    loadingOverlay().activate(spinHandle)
    const sku = event.srcElement.id;
    const customization = "";
    const qty = parseInt(event.srcElement.value);
    const data = {
        "sku": sku,
        "quantity": qty,
        "customization": customization
    }
    const user = getCookie("user_id")
    putJSON(apiUrl + "/cart?user_id=" + user + "&session_id=" + sessionID, data);
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

function buildHeaderCategories() {
    const shopCategories = document.getElementById("shopCategories");
    const categories = GET(apiUrl + "/products/categories?header=true").response;
    removeAllChildNodes(shopCategories);
    categories.forEach(build);
    function build(element, _, _) {
        const categoryOption = document.createElement("li");
        const categoryLink = document.createElement("a");
        categoryLink.setAttribute("href", "shop?category=" + element.category);
        categoryLink.innerHTML = element.name;
        categoryOption.appendChild(categoryLink);
        shopCategories.appendChild(categoryOption);
    }
};

function buildHeaderCatalogs() {
    const shopCatalogs = document.getElementById("shopCatalogs");
    const categories = GET(apiUrl + "/products/catalogs?ignore_catalog=%").response;
    removeAllChildNodes(shopCatalogs);
    categories.forEach(build);
    function build(element, _, _) {
        const catalogOption = document.createElement("li");
        const catalogLink = document.createElement("a");
        catalogLink.setAttribute("href", "shop?catalog=" + element.catalog);
        catalogLink.innerHTML = element.name;
        catalogOption.appendChild(catalogLink);
        shopCatalogs.appendChild(catalogOption);
    }
};