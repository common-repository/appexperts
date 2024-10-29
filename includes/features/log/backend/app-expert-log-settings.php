<?php
class App_ExpertLog_Settings{

    private $_current_feature;
    public function __construct(App_Expert_Feature $_current_feature)
    {
        $this->_current_feature = $_current_feature;
        add_filter('ae_settings_tabs',array($this, 'add_notification_tab'), 10, 1);
    }

    public function add_notification_tab ($tabs)
    {
        $tabs['logs'] = [
            "tab_name"=>  __( 'Logs', 'app-expert' ),
            "tab_view"=>  $this->_current_feature->get_current_path() . "templates/settings-tabs/logs.php"
        ];
        return $tabs;
    }

}
