<?php

namespace GE;

require __DIR__ . '/appliances/common_methods.php';
require __DIR__ . '/appliances/generic.php';
require __DIR__ . '/appliances/ac.php';

interface Appliance {

    public function info() : object;
    
}

?>