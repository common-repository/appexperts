<?php

/**
 * Template Name: Thank You
 * This template will only display the content of the woocommerce thank you page
 */

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order = null;
if (function_exists('wc_get_order')) {
    $order = wc_get_order($order_id);
}

?>
<!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body class="cleanpage">
    <div class="appexperts-thankyou">

        <div>
            <?php if (!empty($order)) : ?>
                <div>
                    <h3>
                        <?php echo apply_filters('woocommerce_thankyou_order_received_text', __('Thank you. Your order has been received.', 'app-expert'), $order); ?>
                    </h3>
                </div>
                <ul class="centerFlex woocommerce-thankyou-order-details order_details" style="list-style: none;text-align: center; padding: 20px;">
                    <li class="order">
                        <?php _e('Order Number:', 'app-expert'); ?>
                        <strong>#<?php echo $order->get_order_number(); ?></strong>
                    </li>
                    <li class="date">
                        <?php _e('Date:', 'app-expert'); ?>
                        <strong><?php echo date_i18n(get_option('date_format'), strtotime($order->get_date_created())); ?></strong>
                    </li>
                    <li class="total">
                        <?php _e('Total:', 'app-expert'); ?>
                        <strong><?php echo $order->get_formatted_order_total(); ?></strong>
                    </li>
                    <?php if ($order->get_payment_method_title()) : ?>
                        <li class="method">
                            <?php _e('Payment Method:', 'app-expert'); ?>
                            <strong><?php echo $order->get_payment_method_title(); ?></strong>
                        </li>
                    <?php endif; ?>
                </ul>

                <button class="back-to-app" onclick="javascript:backToApp();">
                    <strong><?php _e('Continue Shopping', 'app-expert'); ?></strong>
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="clear"></div>
    <?php wp_footer(); ?>

    <script type="text/javascript">
        function backToApp() {
            messageHandler.postMessage("back-from-checkout");
        }
        jQuery(document).ready(function() {
            <?php if(isset($_GET['ae_display_lang'])){
                echo "Cookies.set('ae_locale', '{$_GET['ae_display_lang']}');";
            }?>
        });
    </script>
</body>

</html>