<?php

/**
 * Template Name: Clean Page
 * This template will only display the content you entered in the page editor
 */


?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body class="cleanpage">
    <div id="loader-wrapper">
        <div id="loader"></div>
    </div>
    <div id="main-content" style="visibility:hidden;">
        <?php
        while (have_posts()) : the_post();
            the_content();
        endwhile;
        ?>
    </div>
    <?php wp_footer(); ?>


    <script type="text/javascript">
        jQuery(document).ready(function() {
            <?php if(isset($_GET['ae_display_lang'])){
                echo "Cookies.set('ae_locale', '{$_GET['ae_display_lang']}');";
            }?>
            // Handle login redirection
            var loginClass = '.showlogin';

            jQuery(".woocommerce-form-login").remove();
            jQuery(loginClass).on("click", function(e) {
                e.preventDefault();
                messageHandler.postMessage("login");
            });

            let set_label_classes = function(input){
                if(input.val().length){
                    input.parent().parent().find("label").addClass("active ae-has-value");
                }else{
                    input.parent().parent().find("label").removeClass("active ae-has-value");
                }
            }
            jQuery(document).on("focus",".input-text", function(e){
                e.stopPropagation();
                jQuery(this).parent().parent().find("label").addClass("active").removeClass('ae-has-value');
            });

            jQuery(document).on("blur",".input-text", function(e){
                set_label_classes( jQuery(this));
            });
            jQuery(document).on("change",".input-text", function(e){
                    set_label_classes( jQuery(this));
            });
            jQuery(".input-text").each(function (){
                set_label_classes( jQuery(this));
            });
        });
    </script>
</body>

</html>