<?php

namespace GE;

class User extends GE {

    function __construct($date_format = 0) {
        parent::__init();

        global $GE_global_object;
        if (is_int($date_format)) {
            switch ($date_format) {
                case 1:
                    $GE_global_object->date_format = 'd/m/Y H:i:s';
                    break;
                case 2:
                    $GE_global_object->date_format = 'Y/m/d H:i:s';
                    break;
                default:
                    $GE_global_object->date_format = 'm/d/Y H:i:s';
                    break;
            }
        } else {
            $GE_global_object->date_format = $date_format;
        }
        if (empty($this->userId)) {
            $this->userId = $this->info()['userId'];
            $GE_global_object->userId = $this->userId;
        }
    }

    /**
     * Get basic user information
     */
    public function info() : array {
        $user_request = parent::call('GET', '/v1/user');
        if ($user_request['code'] >= 400) {
            throw new \Exception('Unable to list appliances. The server responded with a ' . $user_request['code'] . ' error code.');
        }
        return $user_request['response'];
    }

    /**
     * Get all user appliances
     */
    function get_appliances() : array {
        $all_appliances = $this->list_appliances();
        $appliances_objects = [];
        foreach ($all_appliances as $appliance) {
            switch ($appliance['type']) {
                case 'Split Air Conditioner':
                    $appliances_objects[] = new AC($appliance);
                    break;
                default:
                    $appliances_objects[] = new Generic($appliance);
                    break;
            }
        }
        return $appliances_objects;
    }

    /**
     * Request all user appliances from the GE Appliances API
     */
    private function list_appliances() : array {
        $appliances_request = parent::call('GET', '/v1/appliance');
        if ($appliances_request['code'] >= 400) {
            throw new \Exception('Unable to list appliances. The server responded with a ' . $appliances_request['code'] . ' error code.');
        }
        return $appliances_request['response']['items'];
    }

}

?>