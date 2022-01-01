<?php

require __DIR__ . '/../vendor/autoload.php';

$auth = parse_ini_file(__DIR__ . '/auth.ini');

session_start();

# Get the stored GE Bearer token from $_SESSION or from wherever you like
$ge_token = $_SESSION['GE_token'] ?? NULL;

# Setup the GE class
$ge = new \GE\GE($auth['username'], $auth['password'], $ge_token);
# Initialize the GE class and get a bearer token in return
$return_token =  $ge->init();

# Store the returned token to a $_SESSION or setup your own storage object
$_SESSION['GE_token'] = $return_token;

# Initialize the GE\User object
$ge_user = new \GE\User();

# Get user appliances
$appl = $ge_user->get_appliances()[0];

# Run commands on the appliance
// $appl->power_on();
// $appl->set_temperature(25);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GE API</title>
</head>
<body>
    <h3>Basic Info</h3>
    <pre><code>
        <?=print_r($appl->info())?>
    </code></pre>
    <h3>Full Info</h3>
    <pre><code>
        <?=print_r($appl->status())?>
    </code></pre>
</body>
</html>

