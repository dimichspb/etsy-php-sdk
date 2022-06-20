<?php

namespace Etsy\Utils;

use Etsy\OAuth\Client;

/**
 *  HTTP request utilities.
 *
 * @author Rhys Hall hello@rhyshall.com
 */
class Request {

    /**
     * @var Client
     */
    protected static $client;

    public static function setClient(Client $client)
    {
        self::$client = $client;
    }
  /**
   * Prepares the request query parameters.
   *
   * @param array $params
   * @return string
   */
  public static function prepareParameters(array $params) {
    $query = http_build_query($params);
    return $query;
  }

  /**
   * Prepares any files in the POST data. Expects a path for files.
   *
   * @param array $params
   * @return array
   */
  public static function prepareFile(array $params) {
    if(!isset($params['image']) && !isset($params['file'])) {
      return false;
    }
    $type = isset($params['image']) ? 'image' : 'file';
    return [
      [
        'name' => $type,
        //'contents' => fopen($params[$type], 'r')
        'contents' => self::$client->get($params[$type]),
      ]
    ];
  }

  /**
   * Returns a query string as an array.
   *
   * @param string $query
   * @return array
   */
  public static function getParamaters($query) {
    parse_str($query, $params);
    return $params;
  }

}
