<?php
/**
 * ==================================
 * Author : marina
 * Date :6/24/21.
 * ==================================
 **/
class App_Expert_Peepso_Groups_Tabs_Helper
{
    public static function getTabSubItems($tabname, $obj, $profile_url = "")
    {
        $subItems = [];
        switch ($tabname) {
            case 'photos':
                $subItems = [
                    [
                        'id' => $tabname . "_latest",
                        'label' => __('Photos', 'friendso'),
                        'url' => $profile_url . 'latest',
                    ],
                    [
                        'id' => $tabname . "_album",
                        'label' => __('Albums', 'friendso'),
                        'url' => $profile_url . 'album',
                    ]
                ];
                break;
            case 'members':
                $PeepSoGroupUsers = new PeepSoGroupUsers($obj->id);
                $PeepSoGroupUser = new PeepSoGroupUser($obj->id, get_current_user_id());
                $PeepSoGroupUsers->update_members_count('banned');
                $PeepSoGroupUsers->update_members_count('pending_user');
                $PeepSoGroupUsers->update_members_count('pending_admin');
                $subItems = [
                    [
                        'id' => $tabname . "_all_members",
                        'label' => __('All Members', 'friendso'),
                        'url' => $profile_url . 'members/',
                    ],
                    [
                        'id' => $tabname . "_management",
                        'label' => __('Management', 'friendso'),
                        'url' => $profile_url . 'members/management',
                    ],
                ];
                if ($PeepSoGroupUser->can('manage_users')) {
                    $subItems[] = [
                        'id' => $tabname . "_invited",
                        'label' => __('Invited', 'friendso'),
                        'count' => (int)$obj->pending_user_members_count,
                        'url' => $profile_url . 'members/invited',
                    ];
                    $subItems[] = [
                        'id' => $tabname . "_pending",
                        'label' => __('Pending', 'friendso'),
                        'count' => (int)$obj->pending_admin_members_count,
                        'url' => $profile_url . 'members/pending',
                    ];
                    $subItems[] = [
                        'id' => $tabname . "_banned",
                        'label' => __('Banned', 'friendso'),
                        'count' => (int)$obj->banned_members_count,
                        'url' => $profile_url . 'members/banned',
                    ];
                }
                break;
        }
        return $subItems;
    }
}