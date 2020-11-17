$( function() {
    // let parallax = new ParallaxBgPicture($('.parallaxBg'));
    // parallax.toggleParallax();
    // let mobileNav = new MobileNav($("#hdrNavbarMobileButton"), $("#hdrNavbar"), $("#siteHeader"));
    // mobileNav.addHeaderFade();
    // let emailFormHandler = new EmailFormHandler($('#ftrContactForm'), "sendUserEmail","ftrContactFormSuccessInfo" );
    // emailFormHandler.submitEvent();
    $('.customSelectContainer').each((i, el) => {
        new Select($(el));
    })
});

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
                    let
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
