# PHP - General Electric / SmartHQ API
This library is a simple PHP wrapper for the General Electric / SmartHQ API. At the moment the library utilizes only a single endpoint from the API since I don't have all the devices listed to test it with. If you want support for more devices you can implement your own calls & functions and create a pull request to merge it to the main branch.

Additional required libraries (included in the main package):

 -  [GuzzleHTTP](https://github.com/guzzle/guzzle): for the API calls
 - [SimpleHTMLDOM](https://github.com/voku/simple_html_dom):  for the login process

#### Supported devices:
 - Split Air Conditioner

#### Installation:
You can install the library using `composer` or by simply downloading this repository and including it in your project.
Installation using `composer`:

    composer require giannisftaras/ge-api

#### Usage:

Make sure you implement a storage object in order to store the Bearer / Access token generated during the login flow which will help in loading times and when quering the API in order to avoid logging in all the time.

```php
<?php
	require  __DIR__  .  '/../vendor/autoload.php';
	$auth = parse_ini_file(__DIR__  .  '/auth.ini');

	session_start();
	# Get the stored GE Bearer token from $_SESSION or from wherever you like
	$ge_token = $_SESSION['GE_token'] ?? NULL;
		 
	# Setup the GE class
	$ge = new \GE\GE($auth['username'], $auth['password'], $ge_token);

	# Initialize the GE class and get a bearer token in return
	$return_token = $ge->init();

	# Store the returned token to a $_SESSION or setup your own storage object
	$_SESSION['GE_token'] = $return_token;
	
	# Initialize the GE\User object
	$ge_user = new \GE\User();

	# Get user appliances
	$appl = $ge_user->get_appliances()[0];

	# Run commands on the appliance
	$appl->power_on();
	$appl->set_temperature(25);
?>
```
You can view the AC class in `/src/geAPI/commands/ac_commands.php` for all available functions and commands.
