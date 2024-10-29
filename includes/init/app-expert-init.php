<?php

class App_Expert_Init {

    public function __construct() {
        $this->includes();
    }

    public function includes() {
        $include_arr = array_merge(
                [
                    //load composer packages
                    APP_EXPERT_PATH . 'vendor/autoload.php'
                ],
                //load abstracts
                glob(APP_EXPERT_PATH . "includes/init/abstracts/*.php"),
                //
                glob(APP_EXPERT_PATH . "includes/init/common/*.php"),
                //load integrations
                glob(APP_EXPERT_PATH . "includes/integrations/*/*.php"),
                //load features
                glob(APP_EXPERT_PATH . "includes/features/*/*.php")
        );
        foreach ($include_arr as $file) {
            require_once($file);
        }
    }

}

new App_Expert_Init();

