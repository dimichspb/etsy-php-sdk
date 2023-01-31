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
    public $client;

    /**
     * @var integer|string
     */
    protected $user;

    public function __construct(
        string $client_id,
        string $api_key,
        Client $client,
        array $config = []
    ) {
        $this->client_id = $client_id;
        $this->api_key = $api_key;
        $this->client = $client;
        $this->client->setApiKey($api_key);
        $this->client->setConfig($config);
    }

    /**
     * Returns a resource object from the request result.
     *
     * @param object $response
     * @param string $resource
     * @return mixed
     */
    public function getResource(
        $response,
        string $resource
    ) {
        if(!$response || ($response->error ?? false)) {
            return null;
        }
        if(isset($response->results)) {
            return $this->createCollection($response, $resource);
        }
        return $this->createResource($response, $resource);
    }

    /**
     *
     */
    public function createCollection(
        $response,
        string $resource
    ): Collection
    {
        $collection = new Collection($this, $resource, $response->uri);
        if(!count($response->results) || !isset($response->results)) {
            return $collection;
        }
        $collection->data = $this->createCollectionResources(
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
    public function createCollectionResources(array $records, string $resource): array
    {
        $resource = __NAMESPACE__ . "\\Resources\\{$resource}";
        return array_map(function($record) use($resource) {
            return new $resource($this, $record);
        }, $records);
    }

    /**
     * Creates a new Etsy resource.
     *
     * @param stdClass $record
     * @param string $resource
     * @return Resource
     */
    public function createResource(
        stdClass $record,
        string $resource
    ): Resource
    {
        $resource = __NAMESPACE__ . "\\Resources\\{$resource}";
        return new $resource($this, $record);
    }

    /**
     * Check to confirm connectivity to the Etsy API with an application
     *
     * @link https://developers.etsy.com/documentation/reference#operation/ping
     * @return integer|false
     */
    public function ping()
    {
        $response = $this->client()->get("/application/openapi-ping");
        return $response->application_id ?? false;
    }

    /**
     * Only supports getting the user for whom the current API KEY is associated with.
     *
     * @return User
     */
    public function getUser(): User
    {
        $user_id = explode(".", $this->api_key)[0];
        $response = $this->client()->get("/application/users/{$user_id}");
        return $this->getResource($response, "User");
    }

    /**
     * Retrieves the full hierarchy tree of seller taxonomy nodes.
     *
     * @return Collection|Taxonomy[]
     */
    public function getSellerTaxonomy(): Collection
    {
        $response = $this->client()->get(
            "/application/seller-taxonomy/nodes"
        );
        return $this->getResource($response, "Taxonomy");
    }

    /**
     * Retrieves the properties of seller taxonomy node.
     *
     * @param int $taxonomy_id
     * @return Collection
     */
    public function getSellerTaxonomyProperties(int $taxonomy_id): Collection
    {
        $response = $this->client()->get(
            "/application/seller-taxonomy/nodes/{$taxonomy_id}/properties"
        );
        return $this->getResource($response, "TaxonomyProperty");
    }

    /**
     * Retrieves a list of available shipping carriers and the mail classes associated with them for a given country
     *
     * @param string $iso_code
     * @return Collection|ShippingCarrier[]
     */
    public function getShippingCarriers(string $iso_code)
    {
        $response = $this->client()->get(
            "/application/shipping-carriers",
            [
                "origin_country_iso" => $iso_code
            ]
        );
        return $this->getResource($response, "ShippingCarrier");
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
    ): ?Listing
    {
        $response = $this->client()->get(
            "/application/listings/{$listing_id}",
            [
                'includes' => $includes
            ]
        );
        return $this->getResource($response, "Listing");
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
        $response = $this->client()->get(
            "/application/listings/active",
            $params
        );
        return $this->getResource($response, "Listing");
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
        $response = $this->client()->get(
            "/application/listings/batch",
            [
                "listing_ids" => $listing_ids,
                "includes" => $includes
            ]
        );
        return $this->getResource($response, "Listing");
    }

    public function client(): Client
    {
        return $this->client;
    }
}
