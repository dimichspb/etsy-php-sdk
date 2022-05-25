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
   * @param integer/string $address_id
   * @return UserAddress
   */
  public function getAddress($address_id): UserAddress
  {
    return $this->request(
      "GET",
      "/application/user/addresses/{$address_id}",
      "UserAddress"
    );
  }

  /**
   * Gets the user's Etsy shop.
   *
   * @return Shop
   */
  public function getShop(): Shop
  {
    return $this->request(
      "GET",
      "/application/users/{$this->user_id}/shops",
      "Shop"
    );
  }

}
