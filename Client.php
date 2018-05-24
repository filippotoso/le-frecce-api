<?php

namespace FilippoToso\LeFrecceAPI;

use GuzzleHttp\Client as HTTPClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\BadResponseException;
use Carbon\Carbon;

class Client
{

    /**
     * Where to save the cookies
     * @var object
     */
    protected $cookieJar;

    protected function newClient() {
        return  new HTTPClient(['cookies' => true]);
    }

    /**
     * Execute an HTTP GET request to Qwant API
     * @param  String $url The url of the API endpoint
     * @return Array|FALSE  The result of the request
     */
    protected function getJSON($url) {

        $client = $this->newClient();

        try {
            $res = $client->request('GET', $url, [
                'headers' => [
                    'Accept'     => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0',
                ],
                'cookies' => $this->cookieJar,
            ]);
        }
        catch (BadResponseException $e) {
            return FALSE;
        }

        $data = json_decode($res->getBody(), TRUE);

        return $data;

    }

    /**
     * Execute an HTTP GET request to Qwant API
     * @param  String $url The url of the API endpoint
     * @return Array|FALSE  The result of the request
     */
    protected function get($url) {

        $client = $this->newClient();

        try {
            $res = $client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0',
                ],
                'cookies' => $this->cookieJar,
            ]);
        }
        catch (BadResponseException $e) {
            return FALSE;
        }

        return (string) $res->getBody();

    }


    /**
     * Execute an HTTP POST request to Qwant API
     * @param  String $url The url of the API endpoint
     * @param  Array $data The parameters of the request
     * @return Array|FALSE  The result of the request
     */
    protected function postJSON($url, $data = []) {

        $client = $this->newClient();

        try {
            $res = $client->request('POST', $url, [
                'json' => $data,
                'headers' => [
                    'Accept'     => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0',
                ],
                'cookies' => $this->cookieJar,
            ]);
        }
        catch (BadResponseException $e) {
            return FALSE;
        }

        $data = json_decode($res->getBody(), TRUE);

        return $data;

    }

    /**
     * Execute an HTTP POST request to Qwant API
     * @param  String $url The url of the API endpoint
     * @param  Array $data The parameters of the request
     * @return Array|FALSE  The result of the request
     */
    protected function post($url, $data = []) {

        $client = $this->newClient();

        try {
            $res = $client->request('POST', $url, [
                'form_params' => $data,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0',
                ],
                'cookies' => $this->cookieJar,
            ]);
        }
        catch (BadResponseException $e) {
            return FALSE;
        }

        return (string) $res->getBody();

    }

    /**
     * Generate an API url based on the provided path
     * @method getUrl
     * @param  String $path The api call path
     * @return String      The result API url
     */
    protected function getUrl($path, $api = true) {
        $baseUrl = $api ? 'https://www.lefrecce.it/msite/api' : 'https://www.lefrecce.it/msite';
        return sprintf('%s/%s', $baseUrl, $path);
    }

    /**
     * Create a new client class
     * @method __construct
     * @param  CookieJarInterface|null    $cookieJar The cookie jar used to keep track of the session
     */
    public function __construct($cookieJar = null) {
        $this->cookieJar = is_null($cookieJar) ? new CookieJar : $cookieJar;
    }

    /**
     * Autocomplete locations names
     * @method locations
     * @param  string    $name
     * @return array
     */
    public function locations($name) {
        $url = $this->getUrl('geolocations/locations?name=' . urlencode($name));
        return $this->getJSON($url);
    }

    /**
     * Get journey solutions
     * @method solutions
     * @param  [type]    $origin       [description]
     * @param  [type]    $destination  [description]
     * @param  string    $arflag       [description]
     * @param  [type]    $adate        [description]
     * @param  [type]    $atime        [description]
     * @param  integer   $adultno      [description]
     * @param  integer   $childno      [description]
     * @param  string    $direction    [description]
     * @param  boolean   $frecce       [description]
     * @param  boolean   $onlyRegional [description]
     * @param  [type]    $rdate        [description]
     * @param  [type]    $rtime        [description]
     * @param  [type]    $codeList     [description]
     * @return [type]                  [description]
     */
    public function solutions($origin, $destination, $arflag = 'A', $adate = null, $atime = null,
        $adultno = 1, $childno = 0, $direction = 'A', $frecce = true, $onlyRegional = false,
        $rdate = null, $rtime = null, $codeList = null) {

        $params = [
            'origin' => $origin,
            'destination' => $destination,
            'arflag' => in_array($arflag, ['A', 'R']) ? $arflag : 'A',
            'adate' => is_null($adate) ? date('d/m/Y') : $adate,
            'atime' => is_null($atime) ? date('H') : $atime,
            'adultno' => (int) $adultno,
            'childno' => (int) $childno,
            'direction' => in_array($direction, ['A', 'R']) ? $direction : 'A',
            'frecce' => $frecce ? 'true' : 'false',
            'onlyRegional' => $onlyRegional ? 'true' : 'false',
            'rdate' => is_null($rdate) ? date('d/m/Y', time() + 60 * 60 * 24) : $rdate,
            'rtime' => is_null($rtime) ? date('H') : $rtime,
        ];

        if (!is_null($codeList)) {
            $params['codeList'] = $codeList;
        }

        $url = $this->getUrl('solutions?' . http_build_query($params));
        return $this->getJSON($url);

    }

    /**
     * Get solution standard details
     * @method solutionDetails
     * @param  string $solutionId The solution id
     * @return array
     */
    public function solutionDetails($solutionId) {
        $url = $this->getUrl(sprintf('solutions/%s/details', urlencode($solitionId)));
        return $this->getJSON($url);
    }

    /**
     * Get solution standard info
     * @method solutionInfo
     * @param  string $solutionId The solution id
     * @return array
     */
    public function solutionInfo($solutionId) {
        $url = $this->getUrl(sprintf('solutions/%s/info', urlencode($solitionId)));
        return $this->getJSON($url);
    }

    /**
     * Get solution standard offers
     * @method solutionStandardOffers
     * @param  string $solutionId The solution id
     * @return array
     */
    public function solutionStandardOffers($solutionId) {
        $url = $this->getUrl(sprintf('solutions/%s/standardoffers', urlencode($solitionId)));
        return $this->getJSON($url);
    }

    /**
     * Get solution customized offers
     * @method solutionCustomizedOffers
     * @param  string $solutionId The solution id
     * @return array
     */
    public function solutionCustomizedOffers($solutionId) {
        $url = $this->getUrl(sprintf('solutions/%s/customizedoffers', urlencode($solitionId)));
        return $this->getJSON($url);
    }

    /**
     * Logs in
     * @method login
     * @param  string $username The user's username
     * @param  string $password The user's password
     * @param  boolean $forceUpperCase By default the website requires uppercase password
     * @return string
     */
    public function login($username, $password, $forceUpperCase = true) {

        $params = [
            'j_username' => $username,
            'j_password' => $forceUpperCase ? strtoupper($password) : $password,
        ];

        $url = $this->getUrl('users/login');
        return $this->post($url, $params);

    }

    /**
     * Logs out
     * @method logout
     * @return string
     */
    public function logout() {
        $url = $this->getUrl('ibm_security_logout', false);
        return $this->post($url);
    }

    /**
     * Get the user profile (after login)
     * @method userProfile
     * @return array
     */
    public function userProfile() {
        $url = $this->getUrl('users/profile');
        return $this->getJSON($url);
    }

    /**
     * Get the purchases of the current user
     * @method userPurchases
     * @param  [type]        $datefrom          [description]
     * @param  [type]        $dateto            [description]
     * @param  boolean       $searchbydeparture [description]
     * @return array
     */
    public function userPurchases($datefrom, $dateto = null, $searchbydeparture = true) {

        $params = [
            'finalized' => 'true',
            'datefrom' => $datefrom,
            'dateto' => is_null($dateto) ? date('d/m/Y') : $dateto,
            'searchbydeparture' => $searchbydeparture ? 'true' : 'false',
        ];

        $url = $this->getUrl('users/purchases?' . http_build_query($params));

        return $this->getJSON($url);

    }

    /**
     * Get the details of a specific purchase / sale
     * @method saleDetails
     * @param  string      $saleId The sale Id
     * @return array
     */
    public function saleDetails($saleId) {
        $url = $this->getUrl(sprintf('users/sales/%s', urlencode($saleId)));
        return $this->getJSON($url);
    }

    /**
     * Download a ticket associated with a specific purchase / sale
     * @method downloadTicket
     * @param  string      $saleId    The sale Id
     * @param  integer     $tsId      The ticket Id. For single purchases it will be 1 (the default value)
     * @param  string|null $filename  The path where to save the ticket. If null, the function returns the file content.
     * @return string
     */
    public function downloadTicket($saleId, $tsId = 1, $filename = null) {

        $url = $this->getUrl(sprintf('users/sales/%s/travel?lang=it&tsid=%s', urlencode($saleId), urlencode($tsId)));

        $content = $this->get($url);

        if (!is_null($filename)) {
            file_put_contents($filename, $content);
            return true;
        }

        return $content;

    }



}
