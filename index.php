<?php

/*
 * Live URL: https://twitter.com/TodaysMoonPhase
 * Description: This script calculate moon phase and tweet it
 * Version: 0.2
 * Author: andrewmoof
 * Author URI: http://moofmedia.com/
 * 
 */

$twitter_settings = array(
    'consumer_api_key' => 'XXXXXXXXXXXXXXXXXXXXXXXXXX',
    'consumer_api_secret' => 'XXXXXXXXXXXXXXXXXXXXXXXXXX',
    'oauth_access_token' => 'XXXXXXXXXXXXXXXXXXXXXXXXXX',
    'oauth_access_token_secret' => 'XXXXXXXXXXXXXXXXXXXXXXXXXX'
);

require_once './MoonPhase.php';
require_once './TwitterPostAPI.php';

$TodayMoonPhase = new MoonPhase();

$moon_age = $TodayMoonPhase->moon_age;
$moon_phase = $TodayMoonPhase->moon_phase['desc'];
$moon_icon = $TodayMoonPhase->moon_phase['icon'];

$message = 'Moon\'s Age: ' . $moon_age . ' (' . $moon_phase . '). ' . $moon_icon . "\n" . 'Today: ' . date('d F Y, l. ') . "\n#moon #moonage";

$twitter = new TwitterPostAPI($twitter_settings);
$tweet = json_decode($twitter->post_tweet($message));

if (isset($tweet->{'errors'})) {
    var_dump($tweet);
}

?>
