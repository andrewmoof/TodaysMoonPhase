<?php

/*
 * Small Simple twitter post lib. Tweet - without 3rd party libraries via API.
 *
 * Live URL: https://twitter.com/TodaysMoonPhase
 * Description: This script calculate moon phase
 * Version: 0.4
 * Author: andrewmoof
 * Author URI: http://moofmedia.com/
 *
 */

class TwitterPostAPI {
    private

    const POST_URL = 'https://api.twitter.com/1.1/statuses/update.json';

    private $twitter_setting = array();
    private $ctime = 0;

    public function __construct($init_setting = array()) {

        $this->twitter_setting = $init_setting;
        $this->ctime = time();
    }

    public function post_tweet($message = '') {

        $message = $this->post_prepare($message);
        $curl_url = $this->construct_url(self::POST_URL, 'status=' . $message);
        $oauth_header = $this->get_oauth_header('status=' . $message);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $oauth_header);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $curl_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $tweet_res = curl_exec($ch);

        if (curl_error($ch))
            $tweet_res = json_encode(array('errors' => curl_error($ch)));

        curl_close($ch);

        return $tweet_res;
    }

    private function post_prepare($message) {
        return rawurlencode(substr($message, 0, 138));
    }

    private function construct_url($url, $param) {
        return ($url . '?' . $param);
    }

    private function get_oauth_header($status) {

        $oauth_signature = $this->get_oauth_signature($status);
        $oauth_header = $this->get_oauth_string(',', 'oauth_signature=' . $oauth_signature);

        return array("Authorization: OAuth {$oauth_header}", 'Expect:');
    }

    private function get_oauth_signature($param) {

        $data = $this->prepare_post_data($param);
        $key = $this->encode_key();

        return rawurlencode(base64_encode(hash_hmac('sha1', $data, $key, true)));
    }

    private function prepare_post_data($param) {
        return 'POST&' . rawurlencode(self::POST_URL) . '&' . rawurlencode($this->get_oauth_hash($param));
    }

    private function encode_key() {
        return rawurlencode($this->twitter_setting['consumer_api_secret']) . '&' . rawurlencode($this->twitter_setting['oauth_access_token_secret']);
    }

    private function get_oauth_hash($param) {

        $oauth_hash = $this->get_oauth_string('&', $param);
        return $oauth_hash;
    }

    private function get_oauth_string($separator = '&', $extra = null) {

        $oauth_string = '';
        $oauth_string .= 'oauth_consumer_key=' . $this->twitter_setting['consumer_api_key'] . $separator;
        $oauth_string .= 'oauth_nonce=' . $this->ctime . $separator;
        $oauth_string .= 'oauth_signature_method=HMAC-SHA1' . $separator;
        $oauth_string .= 'oauth_timestamp=' . $this->ctime . $separator;
        $oauth_string .= 'oauth_token=' . $this->twitter_setting['oauth_access_token'] . $separator;
        $oauth_string .= 'oauth_version=1.0';

        if (isset($extra))
            $oauth_string .= $separator . $extra;

        return $oauth_string;
    }

}

?>