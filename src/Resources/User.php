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
        /** @var Collection $shop */
        $shops = $this->getShops();
        foreach ($shops as $shop) {
            if (!$shop instanceof \stdClass) {
                continue;
            }
            if (!property_exists($shop, 'shop_name')) {
                continue;
            }
            if ($shop->shop_name === $name) {
                return new Shop($this->etsy, $shop);
            }
        }

        return null;
    }

    /**
     * Gets the user's Etsy shops.
     *
     * @param array $params
     * @return Collection|Shop
     */
    public function getShops(array $params = [])
    {
        return $this->request(
            "GET",
            "/application/users/{$this->user_id}/shops",
            "Shop",
            $params
        );
    }
}
