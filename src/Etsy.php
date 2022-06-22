<?php

namespace Etsy;

use Etsy\OAuth\Client;
use Etsy\Exception\ApiException;
use Etsy\Resources\Listing;
use Etsy\Resources\ShippingCarrier;
use Etsy\Resources\ShippingProfile;
use Etsy\Resources\Shop;
use Etsy\Resources\Taxonomy;
use Etsy\Resources\User;
use Etsy\Utils\Request;
use stdClass;

class Etsy {

  /**
   * @var string
   */
  protected $api_key;

  /**
   * @var string
   */
  protected $client_id;

  /**
   * @var Client
   */
  public static $client;

  /**
   * @var integer|string
   */
  protected $user;

  public function __construct(
    string $client_id,
    string $api_key,
    array $config = []
  ) {
    $this->client_id = $client_id;
    $this->api_key = $api_key;
    static::$client = new Client($client_id);
    static::$client->setApiKey($api_key);
    static::$client->setConfig($config);
  }

  /**
   * Returns a resource object from the request result.
   *
   * @param object $response
   * @param string $resource
   * @return mixed
   */
  public static function getResource(
    $response,
    string $resource
  ) {
    if(!$response || ($response->error ?? false)) {
      return null;
    }
    if(isset($response->results)) {
      return static::createCollection($response, $resource);
    }
    return static::createResource($response, $resource);
  }

  /**
   *
   */
  public static function createCollection(
    $response,
    string $resource
  ): Collection
  {
    $collection = new Collection($resource, $response->uri);
    if(!count($response->results) || !isset($response->results)) {
      return $collection;
    }
    $collection->data = static::createCollectionResources(
      $response->results,
      $resource
    );
    return $collection;
  }

  /**
   * Creates an array of a single Etsy resource.
   *
   * @param array $records
   * @param string $resource
   * @return array
   */
  public static function createCollectionResources(array $records, string $resource): array
  {
    $resource = __NAMESPACE__ . "\\Resources\\{$resource}";
    return array_map(function($record) use($resource) {
      return new $resource($record);
    }, $records);
  }

    /**
     * Creates a new Etsy resource.
     *
     * @param stdClass $record
     * @param string $resource
     * @return Resource
     */
  public static function createResource(
      stdClass $record,
      string $resource
  ): Resource
  {
    $resource = __NAMESPACE__ . "\\Resources\\{$resource}";
    return new $resource($record);
  }

  /**
   * Check to confirm connectivity to the Etsy API with an application
   *
   * @link https://developers.etsy.com/documentation/reference#operation/ping
   * @return integer|false
   */
  public function ping()
  {
    $response = static::$client->get("/application/openapi-ping");
    return $response->application_id ?? false;
  }

  /**
   * Only supports getting the user for who the current API KEY is associated with.
   *
   * @return User
   */
  public function getUser(): User
  {
    $user_id = explode(".", $this->api_key)[0];
    $response = static::$client->get("/application/users/{$user_id}");
    return static::getResource($response, "User");
  }

    /**
     * Gets an Etsy shop. If no shop_id is specified the current user will be queried for an associated shop.
     *
     * @param int|null $shop_id
     * @return Shop
     */
  public function getShop(
    int $shop_id = null
  ): Shop
  {
    if (!$shop_id) {
      return $this->getUser()->getShop();
    }
    $response = static::$client->get("/application/shops/{$shop_id}");
    return static::getResource($response, "Shop");
  }

    /**
     * Search for shops using a keyword.
     *
     * @param string $keyword
     * @param array $params
     * @return Collection|Shop[]
     * @throws ApiException
     */
  public function getShops(string $keyword, array $params = []) : Collection
  {
    if(!strlen(trim($keyword))) {
      throw new ApiException("You must specify a keyword when searching for Etsy shops.");
    }
    $params['shop_name'] = $keyword;
    $response = static::$client->get(
      "/application/shops",
      $params
    );

    return static::getResource($response, "Shop");
  }

  /**
   * Retrieves the full hierarchy tree of seller taxonomy nodes.
   *
   * @return Collection|Taxonomy[]
   */
  public function getSellerTaxonomy(): Collection
  {
    $response = static::$client->get(
      "/application/seller-taxonomy/nodes"
    );
    return static::getResource($response, "Taxonomy");
  }

    /**
     * Retrieves the properties of seller taxonomy node.
     *
     * @param int $taxonomy_id
     * @return Collection
     */
  public function getSellerTaxonomyProperties(int $taxonomy_id): Collection
  {
      $response = static::$client->get(
          "/application/seller-taxonomy/nodes/{$taxonomy_id}/properties"
      );
      return static::getResource($response, "TaxonomyProperty");
  }

    /**
     * Retrieves a list of available shipping profiles
     *
     * @param int $shop_id
     * @return Collection|ShippingProfile[]
     */
    public function getShippingProfiles(int $shop_id): Collection
    {
        $response = static::$client->get(
            "/application/shops/{$shop_id}/shipping-profiles"
        );
        return static::getResource($response, "ShippingProfile");
    }

  /**
   * Retrieves a list of available shipping carriers and the mail classes associated with them for a given country
   *
   * @param string $iso_code
   * @return Collection|ShippingCarrier[]
   */
  public function getShippingCarriers(string $iso_code)
  {
    $response = static::$client->get(
      "/application/shipping-carriers",
      [
        "origin_country_iso" => $iso_code
      ]
    );
    return static::getResource($response, "ShippingCarrier");
  }

  /**
   * Gets an individual listing on Etsy.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getListing
   * @param float $listing_id
   * @param array $includes
   * @return Listing
   */
  public function getListing(
    float $listing_id,
    array $includes = []
  ): Listing
  {
    $response = static::$client->get(
      "/application/listings/{$listing_id}",
      [
        'includes' => $includes
      ]
    );
    return static::getResource($response, "Listing");
  }

  /**
   * Gets all public listings on Etsy. Filter with keyword param.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/findAllListingsActive
   * @param array $params
   * @return Collection|Listing[]
   */
  public function getPublicListings(array $params = []): Collection
  {
    $response = static::$client->get(
      "/application/listings/active",
      $params
    );
    return static::getResource($response, "Listing");
  }

    /**
     * Get the specified Etsy listings. Supports a maximum of 100 listing IDs.
     *
     * @link https://developers.etsy.com/documentation/reference#operation/getListingsByListingIds
     * @param array $listing_ids
     * @param array $includes
     * @return Collection|Listing[]
     * @throws ApiException
     */
  public function getListings(
    array $listing_ids,
    array $includes = []
  ): Collection
  {
    if(!count($listing_ids)
      || count($listing_ids) > 100) {
      throw new ApiException("Query requires at least one listing ID and cannot exceed a maximum of 100 listing IDs.");
    }
    $response = static::$client->get(
      "/application/listings/batch",
      [
        "listing_ids" => $listing_ids,
        "includes" => $includes
      ]
    );
    return static::getResource($response, "Listing");
  }

}
