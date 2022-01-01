<?php

namespace GE;

trait ERDS_AC {

    private $erd_codes = [
        '0x7003' => 'Target temperature', // AC_TARGET_TEMPERATURE
        '0x7A00' => 'Fan setting', // AC_FAN_SETTING
        '0x7A01' => 'Operation mode', // AC_OPERATION_MODE
        '0x7A02' => 'Ambient temperature', // AC_AMBIENT_TEMPERATURE
        '0x7A0F' => 'Power status', // AC_POWER_STATUS
        '0x7B00' => 'Available modes', // SAC_AVAILABLE_MODES
        '0x7B05' => 'Sleep mode', // SAC_SLEEP_MODE
        '0x7B06' => 'Target temperature range', // SAC_TARGET_TEMPERATURE_RANGE
        '0x7B07' => 'Auto swing mode' // SAC_AUTO_SWING_MODE
    ];

}

?>