<div class="wrap app-exp-container-wrapper">
    <form method="post" action="options.php">
        <br>

        <?php settings_fields('aeci_options'); ?>
        <?php do_settings_sections('aeci-options'); ?>
        <hr>
        <?php do_settings_sections('aepn-options'); ?>
        <?php submit_button(); ?>
    </form>
</div>