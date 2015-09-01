<?php

/**
 * Connection manager for the Mash API
 * 
 * @author Daniel da Silva (daniel.silva@flipside.org)
 * @copyright Flipside 2014
 * @package MASH Rest API
 * @version 1.0
 * @access public
 */
class MashApiConnection {
  
  /**
   * Default MASH url.
   */
  const DEFAULT_URL = 'http://192.168.0.102:3000/api';
  
  /**
   * Url of MASH server.
   * @var String
   */
  private $URL = NULL;
  
  /**
   * User bearer token. Provided by MASH.
   * @var String
   */
  private $bearer_token = NULL;
  
  /**
   * Headers for the request.
   * @var Array
   */
  private $headers = array('Accept: application/json');
  
  /**
   * Code from last response.
   * @var int
   */
  private $request_http_code = NULL;
  
  /**
   * Info from last response.
   * @var Array
   */
  private $request_response_info = NULL;
  
  /**
   * Error details from last response.
   * @var Array
   */
  private $request_error = array(
    'error' => NULL,
    'no' => NULL,
  );
  
  /**
   * Build function. Creates a new instance and sets the bearer token.
   * 
   * @param $bearer_token
   * @param $url
   *   The mash server url.
   * 
   * @return MashApiConnection 
   */
  static function build($bearer_token, $url = null) {
    $api = new MashApiConnection();
    $api->setBearer($bearer_token);
    $api->setMashUrl($url);

    return $api;
  }
  
  /**
   * Sets the access token.
   * @param $bearer_token
   * 
   * @return self
   */
  public function setBearer($bearer_token) {
    $this->bearer_token = $bearer_token;
    $this->headers[] = 'Authorization: Bearer ' . $this->bearer_token;

    return $this;
  }
  
  /**
   * Sets the mash url.
   * @param $bearer_token
   *   If null, reverts to default.
   * 
   * @return self
   */
  public function setMashUrl($url) {
    if (!$url) {
      $url = MashApiConnection::DEFAULT_URL;
    }
    $this->URL = $url;
    return $this;
  }
  
  /**
   * Get the http code of the last request.
   * 
   * @return int 
   */
  public function getCode() {
    return $this->request_http_code;
  }

  /**
   * Get the error details of the last request.
   * 
   * @return array 
   */
  public function getError() {
    return $this->request_error;
  }

  /**
   * Get the info of the last request.
   * 
   * @return array 
   */
  public function getInfo() {
    return $this->request_response_info;
  }

  /**
   * Resets the data relative to the last request.
   * 
   * @return self 
   */
  public function reset() {
    $this->request_http_code = NULL;
    $this->request_error = array(
      'error' => NULL,
      'no' => NULL,
    );
    $this->request_response_info = NULL;
    return $this;
  }
  
  /**
   * Performs a request to url using the given methods and payload.
   * 
   * @param String $url
   *   Url to request stating with slash.
   * 
   * @param String $method
   *   Method to use, GET, POST, PUT, DELETE.
   * 
   * @param array $payload
   *   Request parameters.
   */
  public function doRequest($url, $method = 'GET', $payload = array()) {
    $url = $this->URL . $url;    
    $this->reset();

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);

    switch ($method) {
      case 'POST' :
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payload));
        break;

      case 'PUT' :
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payload));
        break;

      case 'DELETE' :
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payload));
        break;

      default :
        $url .= '?' . http_build_query($payload);
        break;
    }
    
    curl_setopt($curl, CURLOPT_URL, $url);

    $api_response = curl_exec($curl);

    $this->request_error = array(
      'error' => curl_error($curl),
      'no' => curl_errno($curl)
    );
    $this->request_response_info = curl_getinfo($curl);
    curl_close($curl);

    if ($api_response !== FALSE) {
      $this->request_http_code = $this->request_response_info['http_code'];
      return json_decode($api_response);
    }
    else {
      return FALSE;
    }
  }
}
