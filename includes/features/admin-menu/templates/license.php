<div class="wrap app-exp-container-wrapper ">
    <h2><?php echo __('license key', 'app-expert')?></h2>
    <p><?php echo __('you can copy this license key to use it in appexperts wizard while building a new app, Add the key in general details step after adding the website URL. This is to ensure that the owner of the website is the one who is creating an application for it using appexperts.', 'app-expert')?></p>
    <?php
    $generated_license_keys = json_decode(get_option('license_key'),true);
    if (empty($generated_license_keys)){
        $license_key = sha1( wp_rand() );
        $generated_license_keys = [
                array(
                    'key' => $license_key,
                    'generated_at' => current_time('c')
                )
        ];
        add_option( 'license_key', json_encode($generated_license_keys) );
    }
    ?>
    <?php foreach ($generated_license_keys as $index => $license_key) : ?>
            <div>
                <input id="license_key" type="text" value=<?php echo $license_key['key'] ; ?> size="40" readonly="readonly"> <button type="button" class="button-secondary copy-key" data-tip="<?php esc_attr_e( 'Copied!', 'woocommerce' ); ?>"><?php esc_html_e( 'Copy', 'woocommerce' ); ?></button>
                <div class="alert" style="display:none;">
                    <div class="message">
                        <?php esc_attr_e( 'Copied!', 'woocommerce' ); ?>
                    </div>
                </div>
            </div>
    <?php endforeach;?>
</div>

