<?php

namespace GE;

require __DIR__ . '/constants.php';
require __DIR__ . '/ge_user_class.php';
require __DIR__ . '/appliance.php';

$GE_global_object;

class GE {

    private $username;
    private $password;
    protected $token;
    protected $cookieStorage;
    protected $client;
    protected $date_format;
    protected $userId;

    function __construct(string $username, string $password, array $token = NULL) {
        if (empty($username) || empty($password)) {
            throw new \Exception('You need to specify a SmartHQ username/email and a password');
        }
        $this->username = $username;
        $this->password = $password;
        $this->token = $token;

        $this->cookieStorage = new \GuzzleHttp\Cookie\CookieJar;
        $this->client = new \GuzzleHttp\Client([
            'cookies' => $this->cookieStorage,
            'http_errors' => false
        ]);
    }

    /**
     * Initialize current class using the GE global object
     */
    protected function __init() {
        global $GE_global_object;
        if (empty($GE_global_object)) {
            throw new \Exception('You need to instanciate the Smart Things API first');
        }
        $this->client = $GE_global_object->client;
        $this->token = $GE_global_object->token;
        $this->date_format = $GE_global_object->date_format;
        $this->userId = $GE_global_object->userId;
    }

    /**
     * Returns the bearer token used for authentication
     */
    public function init() {
        if (!$this->is_logged_in()) {
            if (empty($this->token)) {
                $login_token = $this->login();
            } else {
                $login_token = $this->refresh_token($this->token['refresh_token']);
            }            
            if (empty($login_token)) {
                throw new \Exception('Unable to login to the SmartHQ API');
            } else {
                $this->token = $login_token;
            }
        }
        global $GE_global_object;
        $GE_global_object = $this;
        return $this->token;
    }

    /**
     * Check if a Bearer token is valid and not expired
     */
    private function is_logged_in() : bool {
        if (!empty($this->token) && time() < $this->token['expires_in']) {
            return true;
        }
        return false;
    }

    /**
     * Start the SmartHQ login process
     */
    private function login() : array {
        $auth_code = $this->get_authorization_code();
        $bearer = $this->get_bearer($auth_code);
        $bearer['expires_in'] = time() + $bearer['expires_in']; 
        return $bearer;
    }

    /**
     * Acquire authorization code from the SmartHQ endpoint
     */
    private function get_authorization_code() : string {
        # Load SmartHQ login page
        $login_page = $this->client->request('GET', GE_const::$LOGIN_URL . '/oauth2/auth', [
            'query' => [
                'client_id' => GE_const::$CLIENT_ID,
                'response_type' => 'code',
                'access_type' => 'offline',
                'redirect_uri' => GE_const::$REDIRECT_URI
            ]
        ]);
        if ($login_page->getStatusCode() >= 400) {
            throw new \Exception('Unable to get the login page. The server responded with a ' . $login_page->getStatusCode() . ' error code.');
        }

        #Extract the signature and CSRF tokens from the HTML
        $login_page = $login_page->getBody()->getContents();
        $html = (new \simplehtmldom\HtmlDocument())->load($login_page);
        $signature = $html->find('input[name="signature"]')[0]->value;
        $csrf_token = $html->find('input[name="_csrf"]')[0]->value;

        #Request the offline authentication code from the endpoint
        $code_request = $this->client->request('POST', GE_const::$LOGIN_URL . '/oauth2/g_authenticate', [
            'allow_redirects' => false,
            'form_params' => [
                'username' => $this->username,
                'password' => $this->password,
                'signature' => $signature,
                '_csrf' => $csrf_token
            ]
        ]);
        if ($code_request->getStatusCode() >= 400) {
            throw new \Exception('Unable to fulfill the code request. The server responded with a ' . $code_request->getStatusCode() . ' error code.');
        }
        $auth_code = $this->get_string_query($code_request->getHeader('Location')[0], '=', '&');
        if (empty($auth_code)) {
            throw new \Exception('The authorization code is empty or malformed');
        }
        return $auth_code;
    }

    /**
     * Get string between two specified characters
     */
    private function get_string_query(string $string, string $start, string $end) : string {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * Get a Bearer Token for the GE Appliances API using an authorization code
     */
    private function get_bearer(string $auth_code) : array {
        $bearer_request = $this->client->request('POST', GE_const::$LOGIN_URL . '/oauth2/token', [
            'allow_redirects' => false,
            'form_params' => [
                'code' => $auth_code,
                'client_id' => GE_const::$CLIENT_ID,
                'client_secret' => GE_const::$CLIENT_SECRET,
                'redirect_uri' => GE_const::$REDIRECT_URI,
                'grant_type' => 'authorization_code'
            ],
            'auth' => [
                GE_const::$CLIENT_ID, 
                GE_const::$CLIENT_SECRET
            ]
        ]);
        if ($bearer_request->getStatusCode() >= 400) {
            throw new \Exception('Unable to fulfill the Bearer request. The server responded with a ' . $bearer_request->getStatusCode() . ' error code.');
        }
        $bearer = json_decode($bearer_request->getBody()->getContents(), true);
        if (empty($bearer)) {
            throw new \Exception('The Bearer token is empty or malformed');
        }
        return $bearer;
    }

    /**
     * Refresh an expired access token using the refresh token
     */
    private function refresh_token(string $refresh_token) : array {
        $bearer_request = $this->client->request('POST', GE_const::$LOGIN_URL . '/oauth2/token', [
            'allow_redirects' => false,
            'form_params' => [
                'client_id' => GE_const::$CLIENT_ID,
                'client_secret' => GE_const::$CLIENT_SECRET,
                'redirect_uri' => GE_const::$REDIRECT_URI,
                'grant_type' => 'refresh_token',
                'refresh_token' => $refresh_token
            ],
            'auth' => [
                GE_const::$CLIENT_ID, 
                GE_const::$CLIENT_SECRET
            ]
        ]);
        if ($bearer_request->getStatusCode() >= 400) {
            throw new \Exception('Unable to fulfill the Refresh request. The server responded with a ' . $bearer_request->getStatusCode() . ' error code.');
        }
        $bearer = json_decode($bearer_request->getBody()->getContents(), true);
        if (empty($bearer)) {
            throw new \Exception('The Refresh Bearer token is empty or malformed');
        }
        return $bearer;
    }

    /**
     * Make an API request
     */
    protected function call(string $request_type = 'GET', string $url = '', array $body = NULL) {
        $request_body['headers'] = [
            'Authorization' => 'Bearer ' . $this->token['access_token']
        ];
        if (!empty($body)) {
            foreach ($body as $key => $request) {
                $request_body[$key] = $request;
            }
        }
        if ($request_type === 'POST') {
            $call = $this->client->request('POST', GE_const::$API_URL . $url, $request_body);
        } else {
            $call = $this->client->request('GET', GE_const::$API_URL . $url, $request_body);
        }
        $code = $call->getStatusCode();      
        $response = $call->getBody()->getContents();
        return ['code' => $code, 'response' => json_decode($response, true)];
    }

    /** 
     * Send a command for the appliance to execute
     */
    protected function send_command(string $applianceId, string $command_erd, string $erd_val, bool $async = false) : bool {
        $form_data = [
            'kind' => 'appliance#erdListEntry',
            'userId' => $this->userId,
            'applianceId' => $applianceId,
            'erd' => $command_erd,
            'value' => $erd_val,
            'ackTimeout' => 10,
            'delay' => 0
        ];
        if ($async) {
            $appliance_request = $this->client->requestAsync('POST', GE_const::$API_URL . '/v1/appliance/' . $applianceId . '/erd/' . $command_erd, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token['access_token']
                ],
                \GuzzleHttp\RequestOptions::JSON => $form_data
            ]);
            return true;
        } else {
            $appliance_request = $this->client->request('POST', GE_const::$API_URL . '/v1/appliance/' . $applianceId . '/erd/' . $command_erd, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token['access_token']
                ],
                \GuzzleHttp\RequestOptions::JSON => $form_data
            ]);
            $appliance_response = json_decode($appliance_request->getBody()->getContents(), true);
            if ($appliance_request->getStatusCode() == 200 && $appliance_response['status'] == 'success') {
                return true;
            }
            return false;
        }
    }

}

?>