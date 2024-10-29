<?php
Class App_Expert_Log_Init extends App_Expert_Feature{
    function get_dir()
    {
        return plugin_dir_path(__FILE__);
    }
    public function _include_hooks(){
        parent::_include_hooks();
        register_shutdown_function( array( $this, 'log_errors' ) );
        add_action('upgrader_process_complete', array( $this, 'log_upgrade' ) , 10, 2);
        add_action( 'activated_plugin'  , array( $this, 'log_activated_plugin' ) );
        add_action( 'deactivated_plugin', array( $this, 'log_deactivated_plugin' ) );

        $this->_inst_classes();
    }
    public function _inst_classes(){
        new App_Expert_Log_CronJob($this);
        new App_ExpertLog_Settings($this);
    }

    public function get_current_url(){
        return  APP_EXPERT_URL."includes/features/log/";

    }
    public function get_current_path(){
        return APP_EXPERT_PATH."includes/features/log/";
    }
    public function log_errors(){
        $error = error_get_last();
        if( is_array( $error ) && !empty($error) ){
            $msg = sprintf( __( '%1$s in %2$s on line %3$s', 'app-expert' ), $error['message'], $error['file'], $error['line'] );
            App_Expert_Logger::fatal($msg,$error);
        }
    }
    public function log_upgrade( $upgrader, $extra ) {
        App_Expert_Logger::info("An Upgrade Happened :",["upgrader"=>$upgrader, "extra"=>$extra]);
    }
    public function log_activated_plugin( $plugin_name ) {
        App_Expert_Logger::info("An Activation  Happened for Plugin:".$plugin_name);
    }
    public function log_deactivated_plugin( $plugin_name ) {
        App_Expert_Logger::info("A Deactivation  Happened for Plugin:".$plugin_name);
    }
}
//todo:find a better way to create new object
new App_Expert_Log_Init();