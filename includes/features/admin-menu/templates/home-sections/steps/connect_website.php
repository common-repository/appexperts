<div>
    <h5 class="ps-2"><?= __("Application testing", 'app-expert')?></h5>
    <p  class="ps-2"><strong><?= __("To obtain a successful connection, you need to make sure that:", 'app-expert')?></strong></p>
    <ol>
        <li><?= __("APPExperts plugin is installed and active on your website", 'app-expert')?></li>
        <li><?= __("Your provided website base URL is correct and added in a valid format (https://www.example.com)", 'app-expert')?></li>
        <?php $url= admin_url("admin.php?page=app_expert_license"); ?>
        <li><?= sprintf(__("you copied license key from AppExperts plugin on your WordPress website and pasted it next to your URL in AppExperts builder. (license key can be found <a href='%s'>Here</a>)", 'app-expert'),$url)?></li>
        <li><?= __("If you’ve selected an E-commerce type, make sure that you have the WooCommerce plugin installed and enabled on your connected website.", 'app-expert')?></li>
    </ol>
</div>
<div>
    <h5 class="ps-2"><?= __("Add consumer and secret key", 'app-expert')?></h5>
    <p  class="ps-2"><strong><?= __("Note: This step is related to the eCommerce app type only.", 'app-expert')?></strong></p>
    <ol>
        <li><?= __("The Woocommerce plugin must be installed on your website for this step to work.", 'app-expert')?></li>
        <li><?= __("To allow the mobile app to read e-commerce data from your website, you must add a consumer key and secret key.", 'app-expert')?></li>
    </ol>
    <p  class="ps-2"><strong><a href="https://docs.woocommerce.com/document/woocommerce-rest-api/"><?= __("Press here", 'app-expert')?></a> <?= __("to find the steps to get the consumer and secret keys.", 'app-expert')?></strong></p>
</div>