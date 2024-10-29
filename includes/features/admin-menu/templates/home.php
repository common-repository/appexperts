<div class="wrap app-exp-container-wrapper">
    <div class="pt-1 text-center">
        <img src="<?= $this->_current_feature->get_current_url()."assets/images/logo.png"?>" alt="Logo">
    </div>
    <div class="m-4 row">
        <div class="col-md-6">
            <?php include_once $this->_current_feature->get_current_path()."templates/home-sections/steps-wizard.php";?>
        </div>
        <div class="col-md-6">
            <?php include_once $this->_current_feature->get_current_path()."templates/home-sections/how-it-works.php";?>
        </div>
    </div>
</div>