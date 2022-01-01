<?php

namespace GE;

class DECODER_AC {

    public static function temperature(string $hex, int $temp_unit = 0) : int {
        $temp = hexdec($hex);
        if ($temp_unit) {
            $temp = round(($temp - 32) / 1.8);
        }
        return $temp;
    }

    public static function fan_setting(string $hex) : string {
        switch (hexdec($hex)) {
            case 0:
                return 'Default';
            case 1:
                return 'Auto';
            case 2:
                return 'Low';
            case 3:
                return 'Low Auto';
            case 4:
                return 'Medium';
            case 5:
                return 'Medium Auto';
            case 8:
                return 'High';
            case 9:
                return 'High Auto';
            default:
                return 'Unknown';
        }
    }

    public static function operation_mode(string $hex) : string {
        switch (hexdec($hex)) {
            case 0:
                return 'Cool';
            case 1:
                return 'Fan Only';
            case 2:
                return 'Energy Saver';
            case 3:
                return 'Heat';
            case 4:
                return 'Dry';
            case 5:
                return 'Auto';
            case 9:
                return 'Default';
            default:
                return 'Unknown';
        }
    }

    public static function available_modes(string $hex) : array {
        $modes = hexdec($hex);
        return self::avm_dec($modes & 1 == 1, $modes & 2 == 2, $modes & 4 == 4);
    }

    /**
     * Decode availabe modes into a mutlidimensional array
     */
    private static function avm_dec(int $has_heat, int $has_dry, int $has_eco) : array {
        $available_modes = [];
        if ($has_heat) {
            $available_modes['Heat'] = 'Yes';
        }
        if ($has_dry) {
            $available_modes['Dry'] = 'Yes';
        }
        if ($has_eco) {
            $available_modes['Eco'] = 'Yes';
        }
        return $available_modes;
    }

    public static function temperature_range(string $hex, int $temp_unit = 0) : array {
        $min_max = str_split($hex, 2);
        if (hexdec($min_max[0]) == 255 || hexdec($min_max[1]) == 255) {
            $min = self::temperature(dechex(60), $temp_unit);
            $max = self::temperature(dechex(86), $temp_unit);
        } else {
            $min = self::temperature($min_max[0], $temp_unit);
            $max = self::temperature($min_max[1], $temp_unit);
        }
        return ['Min' => $min, 'Max' => $max];
    }

}

?>