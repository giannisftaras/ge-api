<?php

namespace GE;

class DECODER_common {

    public static function measurement_units(int $val) : string {
        switch ($val) {
            case 0:
                return 'Farenheit';
            case 1:
                return 'Celcius';
            default:
                return 'Unkown';
        }
    }

    public static function appliance_type(string $val) : string {
        switch ($val) {
            case '00':
                return 'Water Heater';
            case '01':
                return 'Dryer';
            case '02':
                return 'Washer';
            case '03':
                return 'Fridge';
            case '04':
                return 'Microwave';
            case '05':
                return 'Advantium';
            case '06':
                return 'Dish Washer';
            case '07':
                return 'Oven';
            case '08':
                return 'Electric Range';
            case '09':
                return 'Gas Range';
            case '0A':
                return 'Air Conditioner';
            case '0B':
                return 'Electric Cooktop';
            case '11':
                return 'Cooktop';
            case '0C':
                return 'Pizza Oven';
            case '0D':
                return 'Gas Cooktop';
            case '0E':
                return 'Split Air Conditioner';
            case '0F':
                return 'Hood';
            case '10':
                return 'POE Water Filter';
            case '15':
                return 'Water Softener';
            case '16':
                return 'Portable Air Conditioner';
            case '17':
                return 'Washer & Dryer Combination';
            case '14':
                return 'Zone Line';
            case '12':
                return 'Delivery Box';
            case '1A':
                return 'Coffee Maker';
            case '1B':
                return 'Opal Ice Maker';
            case '1D':
                return 'Dehumidifier';
            default:
                return 'Unkown';
        }
    }

    /**
     * Get the software version from a 4byte HEX code
     */
    public static function software_version(string $val) : string {
        $v_spl = str_split($val, 2);
        $v_spl_dec = array_map('hexdec', $v_spl);
        return implode('.', $v_spl_dec);
    }

    public static function hex_to_bool(string $hex, bool $as_string = false) {
        if (hexdec($hex)) {
            return ($as_string) ? 'true' : true;
        }
        return ($as_string) ? 'false' : false;
    }

    public static function hex_to_int(string $hex) : int {
        return hexdec($hex);
    }

    public static function power(string $hex) : string {
        switch ($hex) {
            case '00':
                return 'Off';
            case '01':
                return 'On';
            case 'FF':
                return 'Not Available';
            default:
                return 'Unknown';
        }
    }
    
}

?>