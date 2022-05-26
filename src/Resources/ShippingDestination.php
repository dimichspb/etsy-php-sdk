<?php

namespace Etsy\Resources;

use Etsy\Resource;
use stdClass;

/**
 * Shipping Destination class.
 *
 * @link https://developers.etsy.com/documentation/reference#operation/createShopShippingProfileDestination
 * @author Rhys Hall hello@rhyshall.com
 *
 * @property float $shipping_profile_destination_id
 * @property float $shipping_profile_id
 * @property float $shop_id
 * @property stdClass $primary_cost
 * @property stdClass $secondary_cost
 * @property string $destination_country_iso
 * @property int $min_delivery_days
 * @property int $max_delivery_days
 * @property float $shipping_carrier_id
 * @property string $mail_class
 */
class ShippingDestination extends Resource {

  /**
   * Updates the shipping profile.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/updateShopShippingProfileDestination
   * @param array $data
   * @return ShippingDestination
   */
  public function update(array $data): ShippingDestination
  {
    return $this->updateRequest(
      "/application/shops/{$this->shop_id}/shipping-profiles/{$this->shipping_profile_id}/destinations/{$this->shipping_profile_destination_id}",
      $data
    );
  }

  /**
   * Delete the shipping profile.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/deleteShopShippingProfileDestination
   * @return boolean
   */
  public function delete(): bool
  {
    return $this->deleteRequest(
      "/application/shops/{$this->shop_id}/shipping-profiles/{$this->shipping_profile_id}/destinations/{$this->shipping_profile_destination_id}"
    );
  }
}
