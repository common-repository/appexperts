<?php

/**
 * Template Name: APPExpert WPForms Template
 * This template will only display the content you entered in the page editor
 */
?>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
    <div id="loader-wrapper">
        <div id="loader"></div>
    </div>
    <div id="main-content" style="visibility:hidden;">
        <?php
         if(isset($_GET['id'])){
            echo do_shortcode('[wpforms id="'.$_GET['id'].'"]');

        }
        ?>
    </div>
<?php wp_footer();?>
