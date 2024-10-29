<?php
/**
 * ==================================
 * Author : marina
 * Date :7/27/21.
 * ==================================
 **/
class App_Expert_Peepso_Groups_Activity_Helper
{

    public static function getCurrentReaction($act_id)
    {
        global $wpdb;
        $sql = "SELECT reaction_type "
            . " FROM `{$wpdb->prefix}" . PeepSoReactionsModel::TABLE . "`"
            . " WHERE `reaction_act_id`=%d "
            . " AND `reaction_user_id`=%d ";

        $sql = $wpdb->prepare($sql, $act_id, get_current_user_id());

        return $wpdb->get_var($sql);
    }

    public static function getCommentCount($post_id)
    {
        global $wpdb;
        $sql = "SELECT count(*)"
            . " FROM `{$wpdb->prefix}" . PeepSoActivity::TABLE_NAME . "`"
            . " WHERE `act_comment_object_id` =  %d ";

        $sql = $wpdb->prepare($sql, $post_id);

        return $wpdb->get_var($sql);
    }

    public static function getReactionCount($act_id)
    {
        $reactionsModel = new PeepSoReactionsModel();
        $reactionsModel->init($act_id);
        $total_reactions = 0;
        $reactions = [];
        foreach ($reactionsModel->reactions as $react_id => $reaction) {

            $count = $reactionsModel->get_reactions_count($react_id);

            if ($count > 0) {
                $reactions[] = ["id" => $react_id, "count" => $count];
                $total_reactions += $count;
            }
        }

        return [
            "data" => $reactions,
            'total_count' => $total_reactions,
            'current_user_reaction' => self::getCurrentReaction($act_id)
        ];

    }
}