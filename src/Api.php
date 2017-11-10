<?php

namespace Zurmo;

/*
 * Zurmo API Functions
*/

class Api
{

    protected $url;
    protected $username;
    protected $password;

    public function __construct($url, $username, $password)
    {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
    }

    /*
     * Zurmo API Authentication
    */
    private function auth()
    {
        $headers = array(
            'Accept: application/json',
            'ZURMO-AUTH-USERNAME: ' . $this->username,
            'ZURMO-AUTH-PASSWORD: ' . $this->password,
            'ZURMO-API-REQUEST-TYPE: REST',
        );
        // auth
        try {
            $response = json_decode($this->call($this->url.'/app/index.php/zurmo/api/auth', 'POST', $headers), true);
            if (isset($response['status']) && $response['status'] == 'SUCCESS' && isset($response['sessionId'])) {
                return $response['data'];
            } else {
                throw new Exception($response['errors']);
            }
        } catch(Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    /*
     * Zurmo API Query Builder
    */
    protected function query($endpoint, $type, $data)
    {
        // auth
        $authenticationData = $this->auth();
        // prepare headers
        $headers = array(
            'Accept: application/json',
            'ZURMO-SESSION-ID: ' . $authenticationData['sessionId'],
            'ZURMO-TOKEN: ' . $authenticationData['token'],
            'ZURMO-API-REQUEST-TYPE: REST',
        );
        // make the call
        try {
            $response = json_decode($this->call($this->url.$endpoint, $type, $headers, array('data' => $data)), true);
            if (isset($response['status']) && $response['status'] == 'SUCCESS') {
                return $response['data'];
            } else {
                throw new Exception($response['errors']);
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
     }

    /*
     * Zurmo API Caller
    */
    private function call($url, $method, $headers, $data = array())
    {
        if ($method == 'PUT') {
            $headers[] = 'X-HTTP-Method-Override: PUT';
        }

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

        switch($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($handle, CURLOPT_POST, true);
                curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'PUT':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'DELETE':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        $response = curl_exec($handle);
        if(curl_errno($handle)) {
            return 'error:' . curl_error($handle);
        }
        return $response;
    }
}
