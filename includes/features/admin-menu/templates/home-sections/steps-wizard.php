<h4 class="app-exp-section-title">
    <?php _e('APP Experts Step wizard', 'app-expert'); ?>
</h4>
<?php $tabs=[
        "1"=>[
                "class"         =>"active",
                "description"   => __("Creating a new application", 'app-expert'),
                "template-name" => "create_new_application.php",
                "tab-class"     => "active"
        ],
        "2"=>[
                "class"         => "",
                "description"   => __("Configure your application", 'app-expert'),
                "template-name" => "configure_application.php",
                "tab-class"     => ""
        ],
        "3"=>[
                "class"         => "",
                "description"   => __("Connecting your website", 'app-expert'),
                "template-name" => "connect_website.php",
                "tab-class"     => ""
        ],
        "4"=>[
                "class"         =>"",
                "description"   => __("Application design", 'app-expert'),
                "template-name" => "application_design.php",
                "tab-class"     => ""
        ],
        "5"=>[
                "class"         =>"",
                "description"   => __("Export your application", 'app-expert'),
                "template-name" => "export_application.php",
                "tab-class"     => ""
        ],
];?>

<div class="mt-5 shadow-lg mb-5 bg-body rounded">
    <h6 class="p-2 text-center app-exp-section-hint rounded"> <?php _e('A guide to create your application with App Experts', 'app-expert'); ?></h6>
    <section class="signup-step-container">
            <div class="row d-flex justify-content-center">
                <div class="col-md-12">
                    <div class="wizard">
                        <div class="wizard-inner">
                            <div class="connecting-line"></div>
                            <ul class="nav nav-tabs" role="tablist">
                                <?php foreach($tabs as $i=>$tab){?>
                                    <li role="presentation" class="wizard-step <?= $tab['class']; ?>">
                                        <a href="#step<?= $i; ?>" data-toggle="tab" aria-controls="step<?= $i; ?>"
                                           role="tab" aria-expanded="true">
                                            <span class="round-tab"><?= $i; ?></span>
                                        </a>
                                        <p class=""><?= $tab['description']; ?></p>
                                    </li>
                                <?php }?>
                            </ul>
                        </div>
                        <div class="p-3 tab-content" id="main_form">
                            <?php foreach ($tabs as $i=>$tab){?>
                                <div class="tab-pane <?= $tab['tab-class']; ?>" role="tabpanel" id="step<?= $i; ?>">
                                    <?php include_once $this->_current_feature->get_current_path()."templates/home-sections/steps/".$tab['template-name']; ?>
                                </div>
                            <?php }?>
                                <div class="clearfix"></div>
                            </div>
                    </div>
                </div>
            </div>
    </section>
</div>