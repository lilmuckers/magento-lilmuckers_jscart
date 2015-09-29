jQuery.support.cors = true;

var JsCart = function(apiKey, url, options) {
    this.apiKey = apiKey;
    this.url = url;
    this.options = options || {};
    this.jq = jQuery;
    this.jq.support.cors = true;
    
    //setup the ajax defaults
    var http    = location.protocol;
    var slashes = http.concat("//");
    var host    = slashes.concat(window.location.hostname);
    this.jq.ajaxSetup({
        context: this,
        type: 'POST',
        headers: {
            ApiKey: this.apiKey,
            ApiMaster: host
        },
        xhrFields: {
            withCredentials: true
        }
    });
    
    this.callbacks = {
        cart: this.cart.bind(this),
        error: this.error.bind(this)
    }
    
    this._loadSid();
    
    this.paths = {
        connect: '/liljscart/connection/connect',
        session: '/liljscart/connection/session',
        add: '/liljscart/cart/add',
        del: '/liljscart/cart/del',
        clear: '/liljscart/cart/clear',
        saleable: '/liljscart/catalog/saleable'
    }
    
    this.jq(window).on("jscart:cartUpdate", this.cartUpdate.bind(this));
    
    this.templates = {
        cart: '<table><colgroup><col width="1" /><col /><col width="1" />' + 
        '<col width="1" /><col width="1" /></colgroup><thead><tr><th /><th>Item</th>' +
        '<th>Qty</th><th>Subtotal</th><th /></tr></thead><tbody><#= itemhtml #>' +
        '</tbody><thead><tr><td colspan="3" style="text-align:right; padding-right:10px;">Subtotal</td><td><#= cur_subtotal #>' +
        '</td></tr><tr><td colspan="3" style="text-align:right; padding-right:10px;">Total</td><td><#= cur_total #></td></tr>' +
        '<tr><td colspan="4"><a href="<#= checkout_link #>">Checkout</a></td></tr>' + 
        '</thead></table>',
        items: '<tr data-item="<#= id #>"><td><img src="<#= thumbnail #>" /></td>' +
        '<td><#= name #><br /><#= description #></td><td><#= qty #>' +
        '</td><td><a href="#" title="Remove item" class="btn-remove">' +
        'Remove item</a></td></tr>'
    }
    
    this.htmlHooks = {
        cart: '#cartContainer',
        removeBtn: '.btn-remove'
    }
    
    //template caching
    this._tmplCache = {};
}

JsCart.prototype.cart = function(error, data, response) {
    if(error) {
        this.callbacks.error(error);
    }
    
    if (!data) {
        return false;
    }
    
    var itemHtml = '';
    for (var i = 0; i < data.items.length; i++) {
        var item = data.items[i];
        itemHtml = itemHtml + this.parseTemplate(this.templates.items, item);
    }
    data.itemhtml = itemHtml;
    
    var cartHtml = this.parseTemplate(this.templates.cart, data);
    this.jq(this.htmlHooks.cart).html(cartHtml);
    
    //assign the remove hooks
    this.jq(this.htmlHooks.cart + ' ' + this.htmlHooks.removeBtn)
        .click(function(e) {
            var btn = this.jq(e.currentTarget);
            var itemId = btn.parentsUntil('tr').parent().data('item');
            this.remFromCart(itemId);
        }.bind(this));
}

JsCart.prototype.error = function(error) {
    alert(error);
}

JsCart.prototype.regCallback = function(name, func, context) {
    if (!context) {
        context = this;
    }
    this.callbacks[name] = func.bind(context);
}

JsCart.prototype.connect = function() {
    var jsSrc = '//cdn.rawgit.com/jpillora/xdomain/0.7.3/dist/xdomain.min.js';
    this.jq.getScript(jsSrc, function(){
        var parser = document.createElement('a');
        parser.href = this._url(this.paths.connect);
        var url = parser.protocol+'//'+parser.host.split(':')[0];
        console.log(url);
        var slaves = {};
        if (parser.pathname.charAt(0) == '/') {
            slaves[url] = parser.pathname;
        } else {
            slaves[url] = '/' + parser.pathname;
        }
        
        xdomain.slaves(slaves);
        
        this._send(
            this.paths.session,
            {},
            function(transport){
                console.log(transport);
                this.sid = transport.responseJSON.data.sid;
                this._store('sid', this.sid);
                this._store('sid_date', new Date());
                this._store('sid_lt', transport.responseJSON.data.lt*1000);
            }.bind(this)
        );
    }.bind(this));
    
    this.jq(document).ready(function(){
        this.callbacks.cart(false, this._get('cart'));
    }.bind(this));
}

JsCart.prototype.clearCart = function() {
    this._send(
        this.paths.clear, 
        {}, 
        function(transport){
            this.jq(window).trigger(
                "jscart:cartUpdate", 
                [
                    transport.responseJSON.error, 
                    transport.responseJSON.data, 
                    transport
                ]
            );
        }.bind(this)
    );
}

JsCart.prototype.isSaleable = function(sku, callback) {
    this._send(
        this.paths.add, 
        { sku: sku }, 
        function(transport){
            callback(transport.responseJSON.data);
        }.bind(this)
    );
}

JsCart.prototype.addToCart = function(sku, qty) {
    this._send(
        this.paths.add, 
        { product: sku, qty: qty }, 
        function(transport){
            this.jq(window).trigger(
                "jscart:cartUpdate", 
                [
                    transport.responseJSON.error, 
                    transport.responseJSON.data, 
                    transport
                ]
            );
        }.bind(this)
    );
}
JsCart.prototype.remFromCart = function(itemId) {
    this._send(
        this.paths.del,
        { itemId: itemId },
        function(transport) {
            this.jq(window).trigger(
                "jscart:cartUpdate", 
                [
                    transport.responseJSON.error, 
                    transport.responseJSON.data, 
                    transport
                ]
            );
        }.bind(this)
    );
}

JsCart.prototype.cartUpdate = function(e, err, data, transport) {
    if (!err || !data) {
        this._store('cart', data);
    } else {
        data = this._get('cart');
    }
    this.callbacks.cart(err, data, transport);
}

JsCart.prototype._send = function(url, data, callback) {
    this.jq.ajax({
        url: this._url(url),
        data : data,
        dataType: 'json',
        crossDomain: true,
        complete : callback
    });
    
}

JsCart.prototype._url = function(url) {
    if (this.sid) {
        return this.url+url+'?SID='+this.sid;
    }
    return this.url+url;
}

JsCart.prototype._store = function(key, data) {
    if (!this.supportLocalStorage()) {
        return false;
    }
    var key = 'mage.liljscart.'+key;
    localStorage[key] = JSON.stringify(data);
    return true;
}

JsCart.prototype._get = function(key) {
    if (!this.supportLocalStorage()) {
        return false;
    }
    var key = 'mage.liljscart.'+key;
    if (localStorage[key]) {
        return JSON.parse(localStorage[key]);
    }
    return false;
}

JsCart.prototype.supportLocalStorage = function() {
    try {
        return 'localStorage' in window && window['localStorage'] !== null;
    } catch (e) {
        return false;
    }
}


JsCart.prototype._loadSid = function() {
    var sid = this._get('sid');
    var sidDate = this._get('sid_date');
    var sidLt = this._get('sid_lt');
    
    if (!sid || !sidDate || !sidLt) {
        this._store('cart', false);
        this.sid = false;
        return false;
    }
    
    if ((new Date()) - (new Date(sidDate)) >= sidLt) {
        this._store('cart', false);
        this.sid = false;
        return false;
    }
    
    this.sid = sid;
    return sid;
}

JsCart.prototype.parseTemplate = function(str, data) {
    var err = "";
    try {
        var func = this._tmplCache[str];
        if (!func) {
            var strFunc =
            "var p=[],print=function(){p.push.apply(p,arguments);};" +
                        "with(obj){p.push('" +
                str.replace(/[\r\t\n]/g, " ")
                   .replace(/'(?=[^#]*#>)/g, "\t")
                   .split("'").join("\\'")
                   .split("\t").join("'")
                   .replace(/<#=(.+?)#>/g, "',$1,'")
                   .split("<#").join("');")
                   .split("#>").join("p.push('")
                   + "');}return p.join('');";

            func = new Function("obj", strFunc);
            this._tmplCache[str] = func;
        }
        return func(data);
    } catch (e) { err = e.message; }
    return "< # ERROR: " + err.htmlEncode() + " # >";
}


