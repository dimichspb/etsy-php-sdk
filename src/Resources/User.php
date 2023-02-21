<?php

namespace Etsy\Resources;

use Etsy\Collection;
use Etsy\Resource;

/**
 * User resource class. Represents an Etsy User.
 *
 * @link https://developers.etsy.com/documentation/reference/#tag/User
 * @author Rhys Hall hello@rhyshall.com
 *
 * @property float $id
 * @property string $login_name
 * @property string $email
 */
class User extends Resource {

    /**
     * Get all addresses for this user.
     *
     * @param array $params
     * @return Collection|UserAddress[]
     */
    public function getAddresses(array $params = []): Collection
    {
        return $this->request(
            "GET",
            "/application/user/addresses",
            "UserAddress",
            $params
        );
    }

    /**
     * Gets a single address for this user.
     *
     * @NOTE this endpoint is not yet active.
     *
     * @param int $address_id
     * @return UserAddress
     */
    public function getAddress(int $address_id): UserAddress
    {
        return $this->request(
            "GET",
            "/application/user/addresses/{$address_id}",
            "UserAddress"
        );
    }

    /**
     * @param string $name
     * @return Shop|null
     */
    public function getShop(string $name): ?Shop
    {
        $shop = $this->getShops();

        return $shop->shop_name === $name? $shop: null;
    }

    /**
     * Gets the user's Etsy shops.
     *
     * @param array $params
     * @return Shop
     */
    public function getShops(array $params = []): Shop
    {
        return $this->request(
            "GET",
            "/application/users/{$this->user_id}/shops",
            "Shop",
            $params
        );
    }
}
