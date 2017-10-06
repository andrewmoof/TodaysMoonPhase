<?php

/**
 * 
 * moon phase calculation class
 * based on http://astrogalaxy.ru/275.html formula
 * 
 * Live URL: https://twitter.com/TodaysMoonPhase
 * Description: This script calculate moon phase
 * Version: 0.5
 * Author: andrewmoof
 * Author URI: http://moofmedia.com/
 * 
 */

class MoonPhase {

    private $moon_phase = NULL;
    private $moon_age = NULL;
    private const FIRST_MOON_YEAR = 2014;

    public function __construct() {
        $curr_time = time();

        $moon_year = $this->calc_moon_year($curr_time);

        $this->moon_age = $this->calc_moon_age($curr_time, $moon_year);
        $this->moon_phase = $this->calc_moon_phase($this->moon_age);
    }
    
    public function get_moon_phase() {
        return $this->moon_phase;
    }
    
    public function get_moon_age() {
        return $this->moon_age;
    }
    
    public function get_moon_state() {
        return ($this->moon_phase + array('age'=>$this->moon_age));
    }

    private function calc_moon_year($time) {

        $earth_year = date('Y', $time);
        $moon_year = 1;
        $i = self::FIRST_MOON_YEAR;

        while ($i != $earth_year) {
            $i++;
            $moon_year++;

            if ($moon_year > 19) $moon_year = 0;
        }

        return $moon_year;
    }

    private function calc_moon_age($time, $moon_year) {

        $moon_age = ($moon_year * 11) - 14 + (int) date('j', $time) + (int) date('n', $time);

        while ($moon_age > 29) {
            $moon_age -= 30;
        }

        return $moon_age;
    }

    private function calc_moon_phase($moon_age) {

        $moon_phase = array();

        if ($moon_age == 0) {
            $moon_phase['phase'] = 0;
            $moon_phase['desc'] = 'New Moon';
            $moon_phase['icon'] = html_entity_decode('&#127761;', 0, 'UTF-8');
        } elseif ($moon_age == 15) {
            $moon_phase['phase'] = 2;
            $moon_phase['desc'] = 'Full Moon';
            $moon_phase['icon'] = html_entity_decode('&#127765;', 0, 'UTF-8');
        } elseif ($moon_age < 15) {
            $moon_phase['phase'] = 1;
            $moon_phase['desc'] = 'First Quarter';

            if ($moon_age < 5) {
                $moon_phase['icon'] = html_entity_decode('&#127762;', 0, 'UTF-8');
            } elseif ($moon_age > 10) {
                $moon_phase['icon'] = html_entity_decode('&#127764;', 0, 'UTF-8');
            } else {
                $moon_phase['icon'] = html_entity_decode('&#127763;', 0, 'UTF-8');
            }
        } elseif ($moon_age > 15) {
            $moon_phase['phase'] = 3;
            $moon_phase['desc'] = 'Third Quarter';

            if ($moon_age > 25) {
                $moon_phase['icon'] = html_entity_decode('&#127768;', 0, 'UTF-8');
            } elseif ($moon_age < 20) {
                $moon_phase['icon'] = html_entity_decode('&#127766;', 0, 'UTF-8');
            } else {
                $moon_phase['icon'] = html_entity_decode('&#127767;', 0, 'UTF-8');
            }
        }
        return $moon_phase;
    }
}

?>