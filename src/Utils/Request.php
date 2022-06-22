<?php

namespace Etsy\Utils;

/**
 *  HTTP request utilities.
 *
 * @author Rhys Hall hello@rhyshall.com
 */
class Request {

    /**
    * Prepares the request query parameters.
    *
    * @param array $params
    * @return string
    */
    public static function prepareParameters(array $params): string
    {
        return http_build_query($params);
    }

    /**
    * Returns a query string as an array.
    *
    * @param string $query
    * @return array
    */
    public static function getParameters(string $query): array
    {
        parse_str($query, $params);
        return $params;
    }
}
