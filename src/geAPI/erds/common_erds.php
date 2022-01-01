<?php

namespace GE;

trait ERDS_common {

    private $common_erds = [
        '0x0001' => 'Model number', // MODEL_NUMBER
        '0x0002' => 'Serial number', // SERIAL_NUMBER
        '0x0007' => 'Temperature unit', // TEMPERATURE_UNIT
        '0x0008' => 'Appliance type', // APPLIANCE_TYPE
        '0x0100' => 'Wifi Module Software version', // WIFI_MODULE_SW_VERSION
        '0x0101' => 'Wifi Module Software new version available', // WIFI_MODULE_SW_VERSION_AVAILABLE
        '0x0102' => 'Module is updating', // ACM_UPDATING
    ];

}

?>