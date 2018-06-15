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

    /**
     * Change the urls if the user is logged in
     * @var boolean
     */
    public $loggedIn = false;

    /**
     * Define the language of the interface
     * @var string
     */
    protected $lang = 'en-US';

    protected function newClient() {
        return new HTTPClient([
            'cookies' => true,
            'headers' => [
                'Accept-Language' => $this->lang,
            ],
        ]);
    }

    /**
     * Execute an HTTP GET request
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
            dd( $e);
            return FALSE;
        }

        $data = json_decode($res->getBody(), TRUE);

        return $data;

    }

    /**
     * Execute an HTTP GET request
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
     * Execute an HTTP POST request
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
     * Execute an HTTP POST request
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
     * Execute an HTTP request
     * @param  String $url The url of the API endpoint
     * @param  Array $data The parameters of the request
     * @return Array|FALSE  The result of the request
     */
    protected function json($url, $data = [], $method = 'POST') {

        $client = $this->newClient();

        try {
            $res = $client->request($method, $url, [
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
     * Generate an API url based on the provided path
     * @method getUrl
     * @param  String $path The api call path
     * @return String      The result API url
     */
    protected function getUrl($path, $api = true) {
        $baseUrl = $api ? 'https://www.lefrecce.it/msite/api/users' : 'https://www.lefrecce.it/msite';
        return sprintf('%s/%s', $baseUrl, $path);
    }

    /**
     * Create a new client class
     * @method __construct
     * @param  CookieJarInterface|null    $cookieJar The cookie jar used to keep track of the session
     */
    public function __construct($language = 'en-US', $cookieJar = null) {
        $this->language = $language;
        $this->cookieJar = is_null($cookieJar) ? new CookieJar : $cookieJar;
    }

    /**
     * Autocomplete locations names
     * @method locations
     * @param  String    $name
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
     * @param  String    $arflag       [description]
     * @param  [type]    $adate        [description]
     * @param  [type]    $atime        [description]
     * @param  integer   $adultno      [description]
     * @param  integer   $childno      [description]
     * @param  String    $direction    [description]
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
        ];

        if (!is_null($codeList)) {
            $params['codeList'] = $codeList;
        }

        if (!is_null($rdate)) {
            $params['rdate'] = $rdate;
        }

        if (!is_null($rtime)) {
            $params['rtime'] = $rtime;
        }

        $url = $this->getUrl('solutions?' . http_build_query($params));

        return $this->getJSON($url);

    }

    /**
     * Get solution standard details
     * @method solutionDetails
     * @param  String $solutionId The solution id
     * @return array
     */
    public function solutionDetails($solutionId) {
        $url = $this->getUrl(sprintf('solutions/%s/details', urlencode($solutionId)));
        return $this->getJSON($url);
    }

    /**
     * Get solution standard info
     * @method solutionInfo
     * @param  String $solutionId The solution id
     * @return array
     */
    public function solutionInfo($solutionId) {
        $url = $this->getUrl(sprintf('solutions/%s/info', urlencode($solutionId)));
        return $this->getJSON($url);
    }

    /**
     * Get solution standard offers
     * @method solutionStandardOffers
     * @param  String $solutionId The solution id
     * @return array
     */
    public function solutionStandardOffers($solutionId) {
        $url = $this->getUrl(sprintf('solutions/%s/standardoffers', urlencode($solutionId)));
        return $this->getJSON($url);
    }

    /**
     * Get solution customized offers
     * @method solutionCustomizedOffers
     * @param  String $solutionId The solution id
     * @return array
     */
    public function solutionCustomizedOffers($solutionId) {
        $url = $this->getUrl(sprintf('solutions/%s/customizedoffers', urlencode($solutionId)));
        return $this->getJSON($url);
    }

    /**
     * Logs in
     * @method login
     * @param  String $username The user's username
     * @param  String $password The user's password
     * @param  boolean $forceUpperCase By default the website requires uppercase password
     * @return string
     */
    public function login($username, $password, $forceUpperCase = true) {

        $params = [
            'j_username' => $username,
            'j_password' => $forceUpperCase ? strtoupper($password) : $password,
        ];

        $url = $this->getUrl('login');

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
        $url = $this->getUrl('profile');
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

        $url = $this->getUrl('purchases?' . http_build_query($params));

        return $this->getJSON($url);

    }

    /**
     * Get the details of a specific purchase / sale
     * @method saleDetails
     * @param  String      $saleId The sale Id
     * @return array
     */
    public function saleDetails($saleId) {
        $url = $this->getUrl(sprintf('sales/%s', urlencode($saleId)));
        return $this->getJSON($url);
    }

    /**
     * Download a ticket associated with a specific purchase / sale
     * @method downloadTicket
     * @param  String      $saleId    The sale Id
     * @param  integer     $tsId      The ticket Id. For single purchases it will be 1 (the default value)
     * @param  String|null $filename  The path where to save the ticket. If null, the function returns the file content.
     * @return string
     */
    public function downloadTicket($saleId, $tsId = 1, $filename = null) {

        $url = $this->getUrl(sprintf('sales/%s/travel?lang=it&tsid=%s', urlencode($saleId), urlencode($tsId)));

        $content = $this->get($url);

        if (!is_null($filename)) {
            file_put_contents($filename, $content);
            return true;
        }

        return $content;

    }

    /**
     * Get / Set the current language
     * @method language
     * @param  null|string   $language If null, returns the current language, otherwise set the language
     * @return string
     */
    public function language($lang = null) {
        if (!is_null($lang)) {
            $this->lang = $lang;
        }
        return $this->lang;
    }

    /**
     * [travels description]
     * @method travels
     * @param  String  $solutionId The current solution Id
     * @param  Array  $selections An assoc array with one or more xmlid and travelertype
     * @return Array
     */
    public function travels($solutionId, $selections) {

        $url = $this->getUrl('travels');
        $params = [
            'idsolution' => $solutionId,
            'selections' => $selections,
            'revalidate' => true,
        ];

        return $this->postJSON($url, $params);

    }

    /**
     * [sals description]
     * @method sales
     * @param  Array $travelIds The current travel Ids
     * @return Array
     */

    public function sales($travelIds) {

        $url = $this->getUrl('sales');

        $params = [];
        foreach ($travelIds as $travelId) {
            $params[] = ['idtravel' => $travelId];
        }

        return $this->postJSON($url, $params);

    }

    /**
     * [salesTravelers description]
     * @method salesTravelers
     * @param  String         $travelId The current travel Id
     * @param  String         $offerId  The current offer Id
     * @return Array
     */
    public function salesTravelers($travelId, $offerId) {
        $url = $this->getUrl(sprintf('sales/%s/travellers/details?offeredservicelist=%s', urlencode($travelId), urlencode($offerId)));
        return $this->getJSON($url);
    }

    /**
     * [salesPassengers description]
     * @method salesPassengers
     * @param  String          $travelId  [description]
     * @param  String          $arflag    [description]
     * @param  Array          $travelers [description]
     * @return Array
     */
    public function salesPassengers($travelId, $arflag, $travelers) {

        $url = $this->getUrl(sprintf('sales/%s/passengers', urlencode($travelId)));

        $params = [
            'arflag' => $arflag,
            'validate' => true,
        ];

        $defaults = [
            'Tipo Viaggiatore' => 'ADULTO',
            'Nome' => '',
            'Cognome' => '',
            'Loyalty Code' => '',
            'Data di nascita' => '',
            'Tipo documento' => '',
            'Numero documento' => '',
            'DATA_DOCUMENTO' => '',
            'Nazione' => '',
            'Provincia di emissione' => '',
            'Comune di emissione' => '',
            'NAZIONE_DI_NASCITA' => '',
            'PROVINCIA_DI_NASCITA' => '',
            'COMUNE_DI_NASCITA' => '',
        ];

        foreach ($travelers as $id => $travelerParams) {

            $traveler = [
                'id' => $id,
            ];

            $travelerParams = array_merge($defaults, $travelerParams);

            foreach ($travelerParams as $key => $value) {
                $traveler['travellerParameters'][] = [
                    'name' => $key,
                    'value' => $value,
                ];
            }

            $params['travelers'][] = $traveler;
        }

        return $this->json($url, $params, 'PUT');

    }

    /**
     * [salesPaymentModes description]
     * @method salesPaymentModes
     * @return [type]            [description]
     */
    public function salesPaymentModes($travelId) {

        $url = $this->getUrl(sprintf('sales/%s/paymentmodes?isPostoClick=false&isInvoice=undefined', urlencode($travelId)));
        return $this->getJSON($url);

    }

    public function salesOrder($travelId, $paymentId, $amount, $invoice = false) {

        $url = $this->getUrl(sprintf('sales/%s/order', urlencode($travelId)));

        $params = [
            'invoice' => $invoice,
            'orderParameterList' => null,
            'pin' => '',
            'payments' => [
                [
                    'paymentid' => $paymentId,
                    'amount' => number_format($amount, 2),
                ]
            ],
        ];

        return $this->postJSON($url, $params);

    }

}
