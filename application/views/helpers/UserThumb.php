<?php

class View_Helper_UserThumb extends Mg_View_Helper_AbstractHelper
{
    public function userThumb(App_Model_User $user)
    {
        $url = false;

        switch ($user->service_type) {
            // Facebook oauth logic
            case App_Model_User::SERVICE_FB:
                $url = "https://graph.facebook.com/{$user->service_id}/picture";
            break;

            // Google oauth logic
            case App_Model_User::SERVICE_GOOGLE:
            break;

            // Twitter oauth logic
            case App_Model_User::SERVICE_TWITTER:
            break;
        }

        // return placeholder when URL isn't specified
        if (!$url) {
            return '<span class="user-thumb"></span>';
        }

        return <<<HTML
        <img class="user-thumb" src="{$url}" />
HTML;
    }
}