<?php

namespace GE;

class ENCODER_AC {

    public static function temperature(int $temp, int $temp_unit = 0) : string {
        if ($temp_unit) {
            $temp = round(($temp * 1.8) + 32);
        }
        return dechex($temp);
    }

    public static function fan_speed(int $speed) : string {
        switch ($speed) {
            case 1:
                return '01';
            case 2:
                return '02';
            case 3:
                return '03';
            case 4:
                return '04';
            case 5:
                return '05';
            case 8:
                return '08';
            case 9:
                return '09';
            default:
                return '00';
        }
    }

    public static function operation_mode(int $mode) : string {
        switch ($mode) {
            case 1:
                return '01';
            case 2:
                return '02';
            case 3:
                return '03';
            case 4:
                return '04';
            case 5:
                return '05';
            case 9:
                return '09';
            default:
                return '00';
        }
    }

}

?>