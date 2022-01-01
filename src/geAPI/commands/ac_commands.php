<?php

namespace GE;

trait CMD_AC {

    public function is_on() : bool {
        $power_status = $this->get_value_of('0x7A0F');
        if ($power_status == '01') {
            return true;
        }
        return false;
    }

    public function ambient_temperature() : string {
        $ambient_temp = $this->get_value_of('0x7A02');
        $ambient_temp = DECODER_AC::temperature($ambient_temp, $this->temp_unit);
        return $ambient_temp;
    }

    public function target_temperature() : string {
        $target_temp = $this->get_value_of('0x7003');
        $target_temp = DECODER_AC::temperature($target_temp, $this->temp_unit);
        return $target_temp;
    }

    public function fan_speed() : string {
        $fan_speed = $this->get_value_of('0x7A00');
        $fan_speed = DECODER_AC::fan_setting($fan_speed);
        return $fan_speed;
    }

    public function operation_mode() : string {
        $op_mode = $this->get_value_of('0x7A01');
        $op_mode = DECODER_AC::operation_mode($op_mode);
        return $op_mode;
    }

    public function auto_swing() : bool {
        $auto_swing = $this->get_value_of('0x7B07');
        if ($auto_swing == '01') {
            return true;
        }
        return false;
    }

    private function get_value_of(string $erd) : string {
        $val = parent::call('GET', '/v1/appliance/' . $this->id . '/erd');
        if ($val['code'] == 200) {
            foreach ($val['response']['items'] as $item) {
                if ($item['erd'] == $erd) {
                    return $item['value'];
                }
            }
        }
        return '';
    }

    public function power_on(bool $async = false) : bool {
        return parent::send_command($this->id, '0x7A0F', '01', $async);
    }

    public function power_off(bool $async = false) : bool {
        return parent::send_command($this->id, '0x7A0F', '00', $async);
    }

    public function set_temperature(int $temp, bool $async = false) : bool {
        if (!empty($temp) && is_int($temp) && $temp > 0) {
            $temp_hex = ENCODER_AC::temperature($temp, $this->temp_unit);
            return parent::send_command($this->id, '0x7003', $temp_hex, $async);
        }
        return false;
    }

    public function set_fan_speed(int $speed, bool $async = false) : bool {
        if ($speed >= 0 && $speed <= 9) {
            $speed_hex = ENCODER_AC::fan_speed($speed);
            return parent::send_command($this->id, '0x7A00', $speed_hex, $async);
        }
        return false;
    }

    public function set_operation_mode(int $mode, bool $async = false) : bool {
        if ($mode >= 0 && $mode <= 9) {
            $mode_hex = ENCODER_AC::operation_mode($mode);
            return parent::send_command($this->id, '0x7A01', $mode_hex, $async);
        }
        return false;
    }

    public function sleep_mode_on(bool $async = false) : bool {
        return parent::send_command($this->id, '0x7B05', '01', $async);
    }

    public function sleep_mode_off(bool $async = false) : bool {
        return parent::send_command($this->id, '0x7B05', '00', $async);
    }

    public function auto_swing_on(bool $async = false) : bool {
        return parent::send_command($this->id, '0x7B07', '01', $async);
    }

    public function auto_swing_off(bool $async = false) : bool {
        return parent::send_command($this->id, '0x7B07', '00', $async);
    }
}

?>