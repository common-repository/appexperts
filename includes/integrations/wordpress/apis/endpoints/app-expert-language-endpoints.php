<?php

class App_Expert_Language_Endpoint {

    public function get_active_languages($request) {
        $langs = App_Expert_Language::get_active_languages();
        return App_Expert_Response::success('app_expert_active_languages', __('Active Languages were returned successfully', 'app-expert'),array('languages' => $langs));
    }
}
