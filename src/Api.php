<?php

namespace Zurmo;

/*
 * Zurmo API Functions
*/

class Api
{

    public $url;
    public $username;
    public $password;

    public function __construct($url, $username, $password)
    {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
    }

    /*
     * Login Function
     * Authenticates a user for the API
    */
    public function login()
    {
        $headers = array(
            'Accept: application/json',
            'ZURMO-AUTH-USERNAME: ' . $this->username,
            'ZURMO-AUTH-PASSWORD: ' . $this->password,
            'ZURMO-API-REQUEST-TYPE: REST',
        );

        $response = ApiRestHelper::createApiCall($this->url.'/app/index.php/zurmo/api/login', 'POST', $headers);
        $response = json_decode($response, true);
        if ($response['status'] == 'SUCCESS') {
            return $response['data'];
        } else {
            return $response;
        }
    }

    /*
     * Query API
     * Queries the API
    */
    public function query($endpoint, $type, $data)
    {
        # Login to API
        $authenticationData = $this->login();

        # Add code to check if user is logged successfully
        if(!array_key_exists('sessionId', $authenticationData)) {
            return var_dump($authenticationData);
        }

        # Set headers
        $headers = array(
            'Accept: application/json',
            'ZURMO-SESSION-ID: ' . $authenticationData['sessionId'],
            'ZURMO-TOKEN: ' . $authenticationData['token'],
            'ZURMO-API-REQUEST-TYPE: REST',
        );

        # Make API call
        $response = ApiRestHelper::createApiCall($this->url.$endpoint, $type, $headers, array('data' => $data));
        $response = json_decode($response, true);

        # Handle Response
        if ($response['status'] == 'SUCCESS') {
            $contact = $response['data'];
            return $contact;
            //Do something with contact data
        } else {
            // Error
            $errors = $response['errors'];
            return $errors;
            // Do something with errors, show them to user
        }
     }


 /*
  * Non-REST functions
  * All the "standard" REST functions can be accessed using the ZurmoREST class.
  * These are the oddballs who need special treatment
 */

    /*
     * Contact Attributes
     * List all the attributes for a particular contact
    */
    public function contactAttributes($id)
    {
        $authenticationData = $this->login();
        //Add code to check if user is logged successfully

        $headers = array(
            'Accept: application/json',
            'ZURMO-SESSION-ID: ' . $authenticationData['sessionId'],
            'ZURMO-TOKEN: ' . $authenticationData['token'],
            'ZURMO-API-REQUEST-TYPE: REST',
        );
        $response = ApiRestHelper::createApiCall($this->url.'/app/index.php/contacts/contact/api/read/'.$id, 'GET', $headers);
        // Decode json data
        #return var_dump($response);
        $response = json_decode($response, true);
        if ($response['status'] == 'SUCCESS') {
            $contactAttributes = $response['data'];
            return $contactAttributes;
            //Do something with contact attributes
        } else {
            // Error
            $errors = $response['errors'];
            return $errors;
            // Do something with errors
        }
    }

    /*
     * Contact States
     * List all the available "states" (i.e. status types) for a contact
    */
    public function contactStates()
    {
        $authenticationData = $this->login();
        //Add code to check if user is logged successfully

        $headers = array(
            'Accept: application/json',
            'ZURMO-SESSION-ID: ' . $authenticationData['sessionId'],
            'ZURMO-TOKEN: ' . $authenticationData['token'],
            'ZURMO-API-REQUEST-TYPE: REST',
        );
        $response = ApiRestHelper::createApiCall($this->url.'/app/index.php/contacts/contactState/api/list/', 'GET', $headers);
        // Decode json data
        #return var_dump($response);
        $response = json_decode($response, true);

        if ($response['status'] == 'SUCCESS') {
            $contactAttributes = $response['data'];
            return $contactAttributes;
            //Do something with contact attributes
        } else {
            // Error
            $errors = $response['errors'];
            return $errors;
            // Do something with errors
        }
    }
}
