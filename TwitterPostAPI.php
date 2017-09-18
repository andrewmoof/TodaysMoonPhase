<?php

/*
 * SmallSimple twitter post lib. Tweet - without 3rd party libraries.
 * 
 * Live URL: https://twitter.com/TodaysMoonPhase
 * Description: This script calculate moon phase
 * Version: 0.2
 * Author: andrewmoof
 * Author URI: http://moofmedia.com/
 * 
 */

class TwitterPostAPI {

    const POST_URL = 'https://api.twitter.com/1.1/statuses/update.json';

    private $twitter_setting = array();
    private $ctime = 0;

    public function __construct($init_setting = array()) {

        $this->twitter_setting = $init_setting;
        $this->ctime = time();
    }

    private function post_prepare($message) {
        return rawurlencode(substr($message, 0, 138));
    }

    private function construct_url($url, $param) {
        return ($url . '?' . $param);
    }

    private function oauth_signature($param) {

        $data = $this->get_data($param);
        $key = $this->get_key();

        return rawurlencode(base64_encode(hash_hmac('sha1', $data, $key, true)));
    }

    private function get_data($param) {
        return 'POST&' . rawurlencode(self::POST_URL) . '&' . rawurlencode($this->get_oauth_hash($param));
    }

    private function get_key() {
        return rawurlencode($this->twitter_setting['consumer_api_secret']) . '&' . rawurlencode($this->twitter_setting['oauth_access_token_secret']);
    }

    private function get_oauth_hash($param) {

        $oauth_hash = '';
        $oauth_hash .= 'oauth_consumer_key=' . $this->twitter_setting['consumer_api_key'];
        $oauth_hash .= '&oauth_nonce=' . $this->ctime;
        $oauth_hash .= '&oauth_signature_method=HMAC-SHA1';
        $oauth_hash .= '&oauth_timestamp=' . $this->ctime;
        $oauth_hash .= '&oauth_token=' . $this->twitter_setting['oauth_access_token'];
        $oauth_hash .= '&oauth_version=1.0';
        $oauth_hash .= '&' . $param;

        return $oauth_hash;
    }

    private function get_oauth_header($oauth_signature) {

        $oauth_header = '';
        $oauth_header .= 'oauth_consumer_key="' . $this->twitter_setting['consumer_api_key'] . '", ';
        $oauth_header .= 'oauth_nonce="' . $this->ctime . '", ';
        $oauth_header .= 'oauth_signature="' . $oauth_signature . '", ';
        $oauth_header .= 'oauth_signature_method="HMAC-SHA1", ';
        $oauth_header .= 'oauth_timestamp="' . $this->ctime . '", ';
        $oauth_header .= 'oauth_token="' . $this->twitter_setting['oauth_access_token'] . '", ';
        $oauth_header .= 'oauth_version="1.0"';

        return array("Authorization: OAuth {$oauth_header}", 'Expect:');
    }

    function post_tweet($message = '') {

        $message = $this->post_prepare($message);
        $curl_url = $this->construct_url(self::POST_URL, 'status=' . $message);
        $oauth_signature = $this->oauth_signature('status=' . $message);
        $oauth_header = $this->get_oauth_header($oauth_signature);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $oauth_header);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $curl_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $tweet_res = curl_exec($ch);
        curl_close($ch);

        return $tweet_res;
    }

}

?>