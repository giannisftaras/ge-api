<?php

namespace GE;

require __DIR__ . '/../erds/common_erds.php';
require __DIR__ . '/../commands/common_commands.php';

$GE_decoders = glob(__DIR__ . '/../converters/decoders/*.php');
foreach ($GE_decoders as $decoder) {
    require($decoder);   
}

$GE_encoders = glob(__DIR__ . '/../converters/encoders/*.php');
foreach ($GE_encoders as $encoder) {
    require($encoder);   
}

trait METHODS_common {

    private $erd_list;

    public $info;
    public $id;
    public $type;
    public $brand;
    public $model;
    public $serial;
    public $nickname;
    public $capabilities;

    public function info() : object {
        return (object) [
            'id' => $this->id,
            'type' => $this->type,
            'brand' => $this->brand,
            'nickname' => $this->nickname,
            'model' => $this->model,
            'serial' => $this->serial,
            'capabilities' => $this->capabilities
        ];
    }

    public function status() : object {
        return (object) $this->erds();
    }

    /**
     * Request all appliance information from the API
     */
    private function get_all_appliance_info() {
        $info_request = parent::call('GET', '/v1/appliance/' . $this->id);
        if ($info_request['code'] == 200) {
            $this->model = $info_request['response']['model'];
            $this->serial = $info_request['response']['serial'];
            $this->capabilities = $info_request['response']['capabilities'];
        }
        $this->erd_list = $this->get_erds();
    }

    /**
     * Get ERD list for a specific appliance
     */
    private function get_erds() : array {
        $erd_request = parent::call('GET', '/v1/appliance/' . $this->id . '/erd');
        if ($erd_request['code'] >= 400) {
            throw new \Exception('Unable to get appliance ERD commands. The server responded with a ' . $erd_request['code'] . ' error code.');
        }
        return $erd_request['response']['items'];
    }

}

?>