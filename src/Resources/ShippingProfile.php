<?php

namespace Etsy\Resources;

use Etsy\Resource;

/**
 * Shipping Profile resource class. Represents a Shop's shipping profile in Etsy.
 *
 * @link https://developers.etsy.com/documentation/reference/#tag/Shop-ShippingProfile
 * @author Rhys Hall hello@rhyshall.com
 *
 * @property string $title
 * @property string $origin_country_iso
 * @property string $origin_postal_code
 * @property float $primary_cost
 * @property float $secondary_cost
 * @property int $min_processing_time
 * @property int $max_processing_time
 * @property string $processing_time_unit
 * @property string $destination_region
 * @property float $shipping_profile_id
 * @property float $shop_id
);
 */
class ShippingProfile extends Resource {

  /**
   * @var array
   */
  protected $_associations = [
    'shipping_profile_destinations' => 'ShippingDestination',
    'shipping_profile_upgrades' => "ShippingUpgrade"
  ];

  /**
   * Updates the shipping profile.
   *
   * @link https://developers.etsy.com/documentation/reference/#operation/updateShopShippingProfile
   * @param array $data
   * @return ShippingProfile
   */
  public function update(array $data): ShippingProfile
  {
    return $this->updateRequest(
      "/application/shops/{$this->shop_id}/shipping-profiles/{$this->shipping_profile_id}",
      $data
    );
  }

  /**
   * Delete the shipping profile.
   *
   * @link https://developers.etsy.com/documentation/reference/#operation/deleteShopShippingProfile
   * @return boolean
   */
  public function delete(): bool
  {
    return $this->deleteRequest(
      "/application/shops/{$this->shop_id}/shipping-profiles/{$this->shipping_profile_id}"
    );
  }

  /**
   * Creates a new shipping destination.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/createShopShippingProfileDestination
   * @param array $data
   * @return ShippingDestination
   */
  public function createShippingDestination(array $data): ShippingDestination
  {
    $destination =  $this->request(
      "POST",
      "/application/shops/{$this->shop_id}/shipping-profiles/{$this->shipping_profile_id}/destinations",
      "ShippingDestination",
      $data
    );
    // Add the shop ID property to the destination.
    $destination->shop_id = $this->shop_id;
    // Add the new shipping destination to the associated property.
    $this->_properties->shipping_profile_destinations[] = $destination;
    return $destination;
  }

  /**
   * Creates a new shipping upgrade.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/createShopShippingProfileUpgrade
   * @param array $data
   * @return ShippingUpgrade
   */
  public function createShippingUpgrade(array $data): ShippingUpgrade
  {
    $upgrade =  $this->request(
      "POST",
      "/application/shops/{$this->shop_id}/shipping-profiles/{$this->shipping_profile_id}/upgrades",
      "ShippingUpgrade",
      $data
    );
    // Add the shop ID property to the destination.
    $upgrade->shop_id = $this->shop_id;
    // Add the new shipping upgrade to the associated property.
    $this->_properties->shipping_profile_upgrades[] = $upgrade;
    return $upgrade;
  }

}
