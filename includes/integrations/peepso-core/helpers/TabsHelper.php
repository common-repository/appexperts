<?php

namespace helpers;
use PeepSoFriendsRequests;
use PeepSoGroupUser;
use PeepSoGroupUsers;

/**
 * ==================================
 * Author : marina
 * Date :6/24/21.
 * ==================================
 **/
class TabsHelper
{
    public static function getTabSubItems($tabname, $obj, $profile_url = "")
    {
        $subItems = [];
        switch ($tabname) {
            case 'stream':
            case 'about':
            case 'media':
            case 'audio & video':
            case 'groups':
            case 'files':
            case 'settings':
                break;
            case 'friends':
                if (!class_exists('PeepSoFriendsRequests')) {
                    break;
                }
                $friends_requests = PeepSoFriendsRequests::get_instance();
                $count = count($friends_requests->get_received_requests());
                $subItems = [
                    [
                        'id' => $tabname . "_friends",
                        'label' => __('Friends', 'friendso'),
                        'url' => $profile_url . 'friends',
                    ]
                ];
                if ($obj->get_id() == get_current_user_id()) {
                    $subItems[] = [
                        'id' => $tabname . "_requests",
                        'label' => __('Friend requests', 'friendso'),
                        'count' => (int)$count,
                        'url' => $profile_url . 'requests',
                    ];
                }
                break;
            case 'photos':
                if (!class_exists('PeepSoSharePhotos')) {
                    break;
                }
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
                if (!class_exists('PeepSoGroupUsers')) {
                    break;
                }
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