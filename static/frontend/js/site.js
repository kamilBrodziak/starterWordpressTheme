$( function() {
    // let parallax = new ParallaxBgPicture($('.parallaxBg'));
    // parallax.toggleParallax();
    // let mobileNav = new MobileNav($("#hdrNavbarMobileButton"), $("#hdrNavbar"), $("#siteHeader"));
    // mobileNav.addHeaderFade();
    // let emailFormHandler = new EmailFormHandler($('#ftrContactForm'), "sendUserEmail","ftrContactFormSuccessInfo" );
    // emailFormHandler.submitEvent();
    $('.productTease').each((i, el) => {
        new ProductTease($(el), '');
    })

});

function ajaxCall(data) {
    jQuery.ajax({
        url: ajaxWoocommerce.ajaxUrl,
        type: 'POST',
        data: data.data,
        beforeSend: data.beforeSend,
        error: data.error,
        success: data.success
    });
}

class ProductTease {
    constructor(el, cart, items = null) {
        this.el = el;
        this.cart = cart;
        this.img = el.find('.imgContainer img');
        this.priceContainer = el.find('.productTeasePriceContainer');
        this.form = el.find('.productTeaseForm');
        this.readyForSubmit = true;
        this.productID = this.el.data('id');
        this.formButton = this.form.find('.productTeaseFormSubmit');
        this.type = this.el.data('type');
        const quantity = this.form.find('.productTeaseQuantity');
        this.quantityInput = quantity.length ? quantity : null;
        if(this.type === 'variable') {
            const _this = this;
            this.defaults = {
                price: this.priceContainer.html(),
                imgSrc: this.img.attr('src'),
                sources: [],

            }
            this.el.find('.imgContainer source').each((i, el) => {
                const source = $(el);
                _this.defaults.sources.push([source, source.attr('srcset')])
            })
            this.variationID = null;
            this.selectsContainer = this.form.find('.customSelectContainer');
            this.selects = [];
            this.readyForSubmit = false;
            this.selectsContainer.find('.customSelect').each((i, el) => {
                const select = $(el);
                const selectObj = {
                    isSelected: false,
                    disabled: i !== 0,
                    ind: i,
                    el: select,
                    itemsContainer: select.find('.customSelectItemsContainer'),
                    selected: select.find('.customSelectSelected')
                };
                if(i === 0) {
                    selectObj.items = [];
                    selectObj.itemsContainer.find('.customSelectItem').each((i, el) => {
                        selectObj.items.push($(el));
                    })
                } else {
                    _this.disableSelect(selectObj);
                }
                _this.selects.push(selectObj);
            });

            this.variations = (items) ? items : this.selectsContainer.data('items');
            this.chooseOptionText = this.selectsContainer.data('choose_option_text');
            this.addSelectFunctionality();
        }
        this.addForm();
    }

    addSelectFunctionality() {
        const _this = this;
        const hideClass = 'hide';
        this.selects.forEach((el, i) => {
            const select = el.el;
            const itemsContainer = el.itemsContainer;
            select.on('click', () => {
                const isClosed = itemsContainer.hasClass(hideClass);
                if(!el.disabled) {
                    if(isClosed) {
                        _this.showSelect(itemsContainer, hideClass);
                    } else {
                        _this.hideSelect(itemsContainer, hideClass);
                    }
                }
            });

            if(i === 0) {
                _this.addSelectItemsFunctionality(el);
            }
        })
    }

    enableSelect(select) {
        select.el.removeClass('disabled');
        select.disabled = false;
    }

    disableSelect(select) {
        const _this = this;
        const itemsContainer = select.itemsContainer;
        const selected = select.selected;
        select.disabled = true;
        itemsContainer.empty();
        selected.html(_this.chooseOptionText);
        select.el.addClass('disabled');
    }

    addSelectItemsFunctionality(select) {
        const items = select.items;
        const _this = this;
        const selected = select.selected;
        items.forEach((el, i) => {
            el.on('click', () => {
                selected.html(el.text());
                let variations = _this.variations;
                for(let i = 0; i <= select.ind; ++i) {
                    const selectedText = _this.selects[i].selected.text().replaceAll(' ', '_');
                    variations = variations[selectedText];
                }
                if(select.ind !== _this.selects.length - 1) {
                    const items = Object.keys(variations);
                    const nextSelect = _this.selects[select.ind + 1];
                    for(let i = select.ind + 1; i < _this.selects.length; ++i) {
                        _this.disableSelect(_this.selects[i]);
                    }
                    _this.generateSelectItems(nextSelect, items);
                    _this.enableSelect(nextSelect);
                    _this.addSelectItemsFunctionality(nextSelect);
                    _this.disableForm();
                } else {
                    _this.enableForm(variations);
                }

            })
        })
    }

    generateSelectItems(select, items) {
        const selectItemsContainer = select.itemsContainer;
        selectItemsContainer.empty();
        select.items = [];
        items.forEach((el, i) => {
            const div = $('<div class="customSelectItem" />');
            div.html(el.replaceAll('_', ' '));
            select.items.push(div);
            selectItemsContainer.append(div);
        })
    }

    enableForm(variation) {
        if(this.type === 'variable') {
            if(this.img.attr('src') !== variation.img.src) {
                this.defaults.sources.forEach((el, i) => {
                    el[0].attr('srcset', '');
                });
                this.img.attr('src', variation.img.src);
                this.img.attr('srcset', variation.img.src);
            }
            const regularPrice = $('<span class="productTeaseOldPrice"/>')
            regularPrice.html(variation.prices.regular);
            this.priceContainer.empty();
            this.priceContainer.append(regularPrice);
            if(variation.prices.sale) {
                const salePrice = $('<span class="productTeaseNewPrice" />');
                salePrice.html(variation.prices.sale)
                this.priceContainer.append(salePrice);
            }
            this.quantityInput.val(1);

            if(variation.stock.backorders && variation.stock.backorders === false && variation.stock.quantity) {
                this.quantityInput.attr('max', variation.stock.quantity)
            }
            this.form.attr('action', variation.url.addToCart + "&quantity=" + this.quantityInput.val());
        }
        this.formButton.attr('disabled', false);
    }

    disableForm() {
        const _this = this;
        if(this.type === 'variable') {
            this.defaults.sources.forEach((el, i) => {
                el[0].attr('srcset', el[1]);
            });
            this.img.attr('src', this.defaults.imgSrc);
            this.form.attr('action', '');
        }
        this.priceContainer.html(this.defaults.price);
        this.formButton.attr('disabled', true);
    }

    showSelect(el, hideClass) {
        el.removeClass(hideClass);
    }

    hideSelect(el, hideClass) {
        el.addClass(hideClass);
    }

    addForm() {
        const _this = this;
        this.form.on('submit', (e) => {
            e.preventDefault();
            const ajax = {
                data: {
                    productID: _this.productID,
                    quantity: (_this.quantityInput) ? _this.quantityInput.val() : 1,
                    action: 'addProductToCart'
                },
                beforeSend: () => {
                    _this.el.addClass('loadingScreen');
                },
                error: (response) => {
                    console.log(response);
                    _this.el.removeClass('loadingScreen');
                },
                success: (response) => {
                    _this.el.removeClass('loadingScreen');
                    let resultClass = response === 'false' ? 'errorScreen' : 'successScreen';
                    if(response === 'false') {
                        console.log('Error occured');
                    } else {
                        _this.cart.html(response);
                    }
                    _this.el.addClass(resultClass);
                    window.setTimeout(() => {
                        _this.el.removeClass(resultClass);
                    }, 2000)
                }
            }

            if(_this.type === 'variable') {
                ajax.data.variationID = _this.variationID;
            }
        })
    }
}

class Select {
    constructor(container) {
        this.container = container;
        this.selects = [];
        container.find('.customSelect').each((i, el) => {
            const select = {
                'id': i,
                'el': $(el),
                'selected': false
            }
            this.selects.push(select);
        })
        this.items = container.data('items');
        this.chooseOptionText = container.data('choose_option_text');
        const _this = this;
        console.log(this.items);
        this.selects.each((i, el) => {
            _this.addSelectFunctionality(el);
        })
    }

    addSelectFunctionality(select) {
        const options = [], _this = this;
        select.find('.customSelectItem').each((i, el) => {
           options.push($(el));
        });
        const optionsContainer = $(select.find('.customSelectItemsContainer'));
        options.forEach((el) => {
            el.el.on('click', () => {
                let allSelected = true;
                _this.selects.some((el) => {
                    return !el.selected;
                });

                if(allSelected) {
                }
            })
        })
    }

}


class EmailFormHandler {
    constructor(form, phpHandlerFuncName, successInfoId) {
        this.form = form;
        this.phpHandlerFuncName = phpHandlerFuncName;
        this.successInfoId = successInfoId;
    }

    submitEvent() {
        let self = this;
        self.form.on('submit', function (e) {
            e.preventDefault();
            let form = self.form,
                name = form.find('#ftrContactFormName').val(),
                email = form.find('#ftrContactFormEmail').val(),
                message = form.find('#ftrContactFormMessage').val(),
                ajaxUrl = form.data('url');
            self.sendEmail(name, email, message, ajaxUrl);
        });
    }

    sendEmail(name, email, message, ajaxUrl) {
        let self = this;
        self.form.find("#ftrContactFormSubmit").prop("disabled", true);
        $.ajax({
            url: ajaxUrl,
            type: 'post',
            data: {
                name: name,
                email: email,
                message: message,
                action: self.phpHandlerFuncName
            },
            error: function (response) {
                console.log(response);
                self.form.find("#ftrContactFormSubmit").prop("disabled", false)
            },
            success: function (response) {
                console.log(response);
                console.log(message);
                self.displaySuccessMessage();
                self.form.find("#ftrContactFormSubmit").prop("disabled", false)
            }
        });
    }

    displaySuccessMessage() {
        let successInfoElement = this.form.find("#" + this.successInfoId);
        successInfoElement.css("display", "flex");
        setTimeout(function() {
            successInfoElement.css("display", "none");
        }, 2000);
    }
}

class ParallaxBgPicture {
    constructor(parallaxBgDOM) {
        this.parallaxBgDOM = parallaxBgDOM;
        this.parallaxBgInitialPosY = 0;
        this.adjustParallaxWhenBrowserResize();
    }

    setParallaxBgPosY() {
        this.parallaxBgInitialPosY = parseFloat($("header").css('height'));
    }

    adjustParallaxWhenBrowserResize() {
        let self = this;
        $(window).resize(function() {
            self.setParallaxBgPosY();
            self.scrollParallax(parseFloat($(window).scrollTop()));
        });
    }

    toggleParallax() {
        let self = this;
        this.setParallaxBgPosY();
        this.scrollParallax($(window).scrollTop());
        $(window).scroll(function() {
            self.scrollParallax(this.scrollY);
        });
    }

    scrollParallax(scrollY) {
        let newBgPosY = this.parallaxBgInitialPosY - scrollY > 0 ? this.parallaxBgInitialPosY - scrollY : 0;
        this.parallaxBgDOM.css("background-position-y", newBgPosY);
    }
}


class MobileNav {
    constructor(mobileNavButton, navbar, header) {
        this.mobileNavButton = mobileNavButton;
        this.navbar = navbar;
        this.header = header;
        this.lastScrollTop = header.offset().top;
        this.enableMobileNavbarButton();
        this.closeNavByClickEvent();
        this.closeNavWhenResizeEvent();
        this.closeNavWhenScrollEvent();
        this.changeNavMode();
    }

    closeNavByClickEvent() {
        let self = this;
        $(document).mouseup(function(e) {
            if (self.mobileNavButton.hasClass("changeMobileNavButtonState") && !self.mobileNavButton.is(e.target) &&
                !self.navbar.is(e.target) && self.navbar.has(e.target).length === 0) {
                    self.closeNav();
            }
        });
    }

    closeNavWhenResizeEvent() {
        let self = this;
        $(window).resize(function() {
            self.changeNavMode();
        });
    }

    closeNavWhenScrollEvent() {
        let self = this;
        $(window).scroll(function() {
            if(self.mobileNavButton.css("display") !== "none") {
                self.closeNav();
            }
        });
    }

    changeNavMode() {
        let self = this, doneCallback, promise;
        this.mobileNavButton.removeClass("changeMobileNavButtonState");
        if(this.mobileNavButton.css("display") === "none") {
            promise = () => {self.navbar.removeClass("hdrNavbarMobileTransition")};
            doneCallback = () => {self.navbar.css("width","100%")};
        } else {
            promise = () => {self.navbar.css("width", 0)};
            doneCallback = () => {self.navbar.addClass("hdrNavbarMobileTransition")};
        }
        $.when(promise()).then(doneCallback);
    }

    enableMobileNavbarButton() {
        let self = this;
        this.mobileNavButton.on("click", function() {
            if(parseFloat(self.navbar.css("width")) > 0) {
                self.closeNav();
            } else {
                self.openNav();
            }
        });
    }

    headerFade() {
        let scrollTop = $(window).scrollTop();
        if (scrollTop <= parseFloat(this.header.css("height"))) {
            this.header.removeClass(['stickyFade', 'stickyTransition']);
        } else if (this.lastScrollTop >= scrollTop || scrollTop <= parseFloat(this.header.css("height"))) {
            this.header.removeClass('stickyFade');
            this.header.addClass('stickyTransition');
        } else if(scrollTop > parseFloat(this.header.css("height")) ){
            this.header.addClass(['stickyFade', 'stickyTransition']);
        }
        this.lastScrollTop = scrollTop;
    };

    addHeaderFade() {
        this.headerFade();
        let self = this;
        $(window).scroll(function() {
            self.headerFade();
        });
    }

    closeNav() {
        this.navbar.css("width", 0);
        this.mobileNavButton.removeClass("changeMobileNavButtonState");
    }

    openNav() {
        this.mobileNavButton.addClass("changeMobileNavButtonState");
        this.navbar.css("width", "70%");
    }
}
