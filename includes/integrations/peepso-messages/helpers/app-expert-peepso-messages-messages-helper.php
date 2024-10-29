<?php

class App_Expert_Peepso_Messages_Messages_Helper{

	const RECIPIENTS_TABLE = 'peepso_message_recipients';
	const PARTICIPANTS_TABLE	='peepso_message_participants';

	public static function get_message_read_status($message_id,$conv_id)
	{
        global $wpdb;
        $recipients_table = $wpdb->prefix . self::RECIPIENTS_TABLE;
		$participants_table = $wpdb->prefix . self::PARTICIPANTS_TABLE;

        $recipients_sql = 'SELECT COUNT(*) FROM `' . $recipients_table . '`
            WHERE `mrec_msg_id` = %d AND `mrec_viewed` = 0';
		
        $recipients_count = (int) $wpdb->get_var($wpdb->prepare($recipients_sql, $message_id));

		$participants_sql = 'SELECT COUNT(*) FROM `' . $participants_table . '` 
		WHERE `mpart_msg_id`= %d and mpart_read_notif = 0';

        $participants_count = (int) $wpdb->get_var($wpdb->prepare($participants_sql, $conv_id));

        if($recipients_count > 0 || $participants_count > 0)
        {
            return false;
        }
        return true;
	}

    public static function get_last_message_in_conversation($msg_id)
	{
        global $wpdb;
        $sql = 'SELECT `posts`.*, `mrec`.*
				FROM `' . $wpdb->posts . '` `posts`
				LEFT JOIN `'. $wpdb->prefix . PeepSoMessageRecipients::TABLE . '` `mrec`
					ON `mrec`.`mrec_msg_id` = `posts`.`ID` AND `mrec`.`mrec_user_id` = %d
				LEFT JOIN `' . $wpdb->prefix . PeepSoMessageParticipants::TABLE .'` `mpart`
					ON `mrec`.`mrec_parent_id` = `mpart`.`mpart_msg_id` AND `mpart`.`mpart_user_id` = `mrec`.`mrec_user_id`
				WHERE
					`mpart`.`mpart_msg_id` IS NOT NULL';

		$sql .= ' AND `mrec`.`mrec_parent_id` = %d';

		$sql .= ' GROUP BY `posts`.`ID` ORDER BY `posts`.`post_date` DESC ';
        
		$msgs = $wpdb->get_results($wpdb->prepare($sql,get_current_user_id() ,$msg_id));
		return $msgs[0];
	}
}