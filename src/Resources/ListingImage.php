<?php

namespace Etsy\Resources;

use Etsy\Resource;

/**
 * Listing Image class.
 *
 * @link https://developers.etsy.com/documentation/reference#tag/ShopListing-Image
 * @author Rhys Hall hello@rhyshall.com
 *
 * @property string $url_fullxfull
 * @property string $listing_image_id
 * @property string $listing_id
 * @property string $shop_id
 */
class ListingImage extends Resource {

  /**
   * Delete the listing image.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/deleteListingImage
   * @return boolean
   */
  public function delete(): bool
  {
    return $this->deleteRequest(
      "/application/shops/{$this->shop_id}/listings/{$this->listing_id}/images/{$this->listing_image_id}"
    );
  }
}
