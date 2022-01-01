<?php

namespace GE;

require __DIR__ . '/../erds/ac_erds.php';
require __DIR__ . '/../commands/ac_commands.php';

class AC extends GE implements Appliance {

    use METHODS_common;
    use ERDS_common;
    use CMD_common;
    use CMD_AC;
    use ERDS_AC;

    private $temp_unit;

    function __construct(array $appliance_info) {
        parent::__init();

        $this->info = $appliance_info;
        $this->id = $appliance_info['applianceId'];
        $this->type = $appliance_info['type'];
        $this->brand = $appliance_info['brand'];
        $this->nickname = $appliance_info['nickname'];

        $this->get_all_appliance_info();
        $this->temp_unit = $this->get_temperature_unit();
    }

    /**
     * Get the appliance temperature unit (Farenheit or Celcius)
     */
    private function get_temperature_unit() : int {
        foreach ($this->erd_list as $erd) {
            if ($erd['erd'] == '0x0007') {
                return hexdec($erd['value']);
            }
        }
        return 0;
    }

    /**
     * Map ERD codes and values to actual - readable data
     */
    private function erds() : array {
        $erds = array_unique(array_merge($this->common_erds, $this->erd_codes));
        foreach ($this->erd_list as $index => $item) {
            $appliance_erds[$index]['erd'] = $erds[$item['erd']];
            $appliance_erds[$index]['value'] = $this->erd_mapping($item['erd'], $item['value']);
            $appliance_erds[$index]['time'] = gmdate($this->date_format, strtotime($item['time']));
        }
        return $appliance_erds;
    }

    private function erd_mapping(string $erd_code, string $val) {
        switch ($erd_code) {
            case '0x0007':
                return DECODER_common::measurement_units($val);
            case '0x0008':
                return DECODER_common::appliance_type($val);
            case '0x0100':
                    return DECODER_common::software_version($val);
            case '0x0101':
                return DECODER_common::software_version($val);
            case '0x0102':
                return DECODER_common::hex_to_bool($val, true);
            case '0x7003':
                return DECODER_AC::temperature($val, $this->temp_unit);
            case '0x7A00':
                return DECODER_AC::fan_setting($val);
            case '0x7A01':
                return DECODER_AC::operation_mode($val);
            case '0x7A02':
                return DECODER_AC::temperature($val, $this->temp_unit);
            case '0x7A0F':
                return DECODER_common::power($val);
            case '0x7B00':
                return DECODER_AC::available_modes($val);
            case '0x7B05':
                return DECODER_common::power($val);
            case '0x7B06':
                return DECODER_AC::temperature_range($val, $this->temp_unit);
            case '0x7B07':
                return DECODER_common::power($val);
            default:
                return $val;
        }
    }

}

?>