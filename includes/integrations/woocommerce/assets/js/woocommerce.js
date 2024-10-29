jQuery(document).ready(function () {
    // Handle login redirection
    var loginClass = '.showlogin';

    jQuery(".woocommerce-form-login").remove();
    jQuery(loginClass).on("click", function (e) {
        e.preventDefault();
        messageHandler.postMessage("login");
    });

    let set_label_classes = function (input) {
        if (input.val().length) {
            input.parent().parent().find("label").addClass("active ae-has-value");
        } else {
            input.parent().parent().find("label").removeClass("active ae-has-value");
        }
    }
    jQuery(document).on("focus", ".input-text", function (e) {
        e.stopPropagation();
        jQuery(this).parent().parent().find("label").addClass("active").removeClass('ae-has-value');
    });

    jQuery(document).on("blur", ".input-text", function (e) {
        set_label_classes(jQuery(this));
    });
    jQuery(document).on("change", ".input-text", function (e) {
        set_label_classes(jQuery(this));
    });
    jQuery(".input-text").each(function () {
        set_label_classes(jQuery(this));
    });
});