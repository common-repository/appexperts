<?php
class App_Expert_Notification_Database  {

    public  $AE_Notification_Table_name,$AE_User_Notification_Table_name;
    private static $_obj;
    private function __construct(){
        global $wpdb;
        $this->AE_wpdb  = $wpdb;
        $this->AE_Notification_Table_name       = $wpdb->prefix . NOTIFICATION_TABLE;
        $this->AE_User_Notification_Table_name  = $wpdb->prefix . USER_NOTIFICATION_TABLE;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    }
    public function create_notification_table(){
        $db_table_name =$this->AE_Notification_Table_name ;
        $charset_collate = $this->AE_wpdb->get_charset_collate();


        //Check to see if the table exists already, if not, then create it
        if($this->AE_wpdb->get_var( "show tables like '$db_table_name'" ) == $db_table_name ) return;
        $sql = "CREATE TABLE $db_table_name (
                        id int(11) NOT NULL auto_increment,
                        title longtext NOT NULL,
                        content longtext NOT NULL,
                        target longtext,
                        segment longtext NOT NULL,
                        attachment_id BIGINT(20) UNSIGNED,
                        type varchar(255),
                        object_id BIGINT(20) UNSIGNED,
                        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        UNIQUE KEY id (id)
                ) $charset_collate;";
        dbDelta( $sql );

    }
    public function create_user_notification_table(){

        $db_table_name =$this->AE_User_Notification_Table_name ;
        $charset_collate = $this->AE_wpdb->get_charset_collate();

        //Check to see if the table exists already, if not, then create it
        if($this->AE_wpdb->get_var( "show tables like '$db_table_name'" ) == $db_table_name ) return;

        $sql = "CREATE TABLE $db_table_name (
                        id int(11) NOT NULL auto_increment,
                        notification_id BIGINT(20) UNSIGNED NOT NULL,
                        user_id BIGINT(20) UNSIGNED NOT NULL,
                        is_read int(1) UNSIGNED NOT NULL DEFAULT '0',
                        UNIQUE KEY id (id)
                ) $charset_collate;";

        dbDelta( $sql );
    }

    public function run(){
        $this->create_notification_table();
        $this->create_user_notification_table();
    }

    public static function get_instance(){
        if(!self::$_obj) self::$_obj=new self();
        return self::$_obj;
    }
}
$_ins=App_Expert_Notification_Database::get_instance();
$_ins->run();
