<?php $server_key = get_option('server_key');?>
<div class="wrap app-exp-container-wrapper">
    <?php if(!empty($server_key)): ?>
        <input type="text" value="<?php echo $server_key; ?>" readonly>
        <label for="server-key" class="status-connected"><span class="connected-icon">✓</span><?php echo __('Connected','app-expert')?></label>
        <form method="post" action="options.php">
            <br>
                <?php settings_fields('notification_options'); ?>
                <?php do_settings_sections('notification-options'); ?>
            <hr>
                <?php submit_button(); ?>
        </form>
    <?php else: ?>
        <div class="alert alert-warning text-center mt-5" role="alert">
            <?php echo __('you should subscribe to the pro plan and make sure you added your firebase keys on integration tab.', 'app-expert')?>
        </div>
    <?php endif; ?>
</div>