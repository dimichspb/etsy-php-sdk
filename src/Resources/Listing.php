<?php

namespace Etsy\Resources;

use Etsy\Collection;
use Etsy\Resource;
use Etsy\Exception\ApiException;

/**
 * Listing resource class. Represents an Etsy listing.
 *
 * @link https://developers.etsy.com/documentation/reference#tag/ShopListing
 * @author Rhys Hall hello@rhyshall.com
 *
 * @property string $shop_id
 * @property string $listing_id
 * @property string $title
 * @property string $description
 * @property int $quantity
 * @property string $who_made
 * @property string $when_made
 * @property float $taxonomy_id
 * @property array $materials
 * @property bool $is_supply
 * @property float $shipping_profile_id
 * @property \stdClass $price
 * @property string $state
 *
 */
class Listing extends Resource {

  /**
   * @var array
   */
  protected $_associations = [
    "Shop" => "Shop",
    "User" => "User",
    "Images" => "Image"
  ];

  public function create(array $data): Listing
  {
      return $this->createRequest(
          "/application/shops/{$this->shop_id}/listings",
          $data
      );
  }

  /**
   * Update the Etsy listing.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/updateListing
   * @param array $data
   * @return Listing
   */
  public function update(array $data): Listing
  {
    return $this->updateRequest(
      "/application/shops/{$this->shop_id}/listings/{$this->listing_id}",
      $data
    );
  }

  /**
   * Delete the listing.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/deleteListing
   * @return boolean
   */
  public function delete(): bool
  {
    return $this->deleteRequest(
      "/application/shops/{$this->shop_id}/listings/{$this->listing_id}"
    );
  }

  /**
   * Get the listing properties associated with the listing.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getListingProperties
   * @return Collection[Etsy\Resources\ListingProperty]
   */
  public function getListingProperties(): Collection
  {
    return $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/listings/{$this->listing_id}/properties",
      "ListingProperty"
    )
      ->append([
        'shop_id' => $this->shop_id,
        'listing_id' => $this->listing_id
      ]);
  }

  /**
   * Get a specific listing property.
   *
   * @NOTE This method is not ready for use and will return a 501 repsonse.
   * @link https://developers.etsy.com/documentation/reference#operation/getListingProperty
   * @param integer|string $property_id
   * @return ListingProperty
   */
  public function getListingProperty($property_id): ListingProperty
  {
    $listing_property = $this->request(
      "GET",
      "/application/listings/{$this->listing_id}/properties/{$property_id}",
      "ListingProperty"
    );
    if($listing_property)
  {
      $listing_property->shop_id = $this->shop_id;
      $listing_property->listing_id = $this->listing_id;
    }
    return $listing_property;
  }

  /**
   * Get the listing files associated with the listing.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getAllListingFiles
   * @return Collection[Etsy\Resources\ListingFile]
   */
  public function getFiles(): Collection
  {
    return $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/listings/{$this->listing_id}/files",
      "ListingFile"
    )
      ->append(["shop_id" => $this->shop_id]);
  }

  /**
   * Get a specific listing file.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getListingFile
   * @param integer|string $listing_file_id
   * @return ListingFile
   */
  public function getFile($listing_file_id): ListingFile
  {
    $listing_file = $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/listings/{$this->listing_id}/files/{$listing_file_id}",
      "ListingFile"
    );
    if($listing_file)
  {
      $listing_file->shop_id = $this->shop_id;
    }
    return $listing_file;
  }

  /**
   * Uploads a listing file.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/uploadListingFile
   * @param array $data
   * @return Collection
   */
  public function uploadFile(array $data): Collection
  {
    if(!isset($data['image']) && !isset($data['listing_image_id']))
  {
      throw new ApiException("Request requires either 'listing_file_id' or 'file' paramater.");
    }
    $listing_file = $this->request(
      "POST",
      "/application/shops/{$this->shop_id}/listings/{$this->listing_id}/files",
      "ListingFile",
      $data
    );
    if($listing_file)
  {
      $listing_file->shop_id = $this->shop_id;
    }
    return $listing_file;
  }

  /**
   * Get the Listing Images for the listing.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getListingImages
   * @return Collection[Etsy\Resources\ListingImage]
   */
  public function getImages(): Collection
  {
    return $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/listings/{$this->listing_id}/images",
      "ListingImage"
    )
      ->append(["shop_id" => $this->shop_id]);
  }

  /**
   * Get a specific listing image.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getListingImage
   * @param integer|string $listing_image_id
   * @return ListingImage
   */
  public function getImage($listing_image_id): ListingImage
  {
    $listing_image = $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/listings/{$this->listing_id}/images/{$listing_image_id}",
      "ListingImage"
    );
    if($listing_image)
  {
      $listing_image->shop_id = $this->shop_id;
    }
    return $listing_image;
  }

  /**
  * Upload a listing image.
  *
  * @link https://developers.etsy.com/documentation/reference#operation/uploadListingImage
  * @param array $data
  * @return Collection
  * @throws ApiException
  */
  public function uploadImage(array $data): Collection
  {
    if(!isset($data['image']) && !isset($data['listing_image_id'])) {
      throw new ApiException("Request requires either 'listing_image_id' or 'image' paramater.");
    }
    $listing_image = $this->request(
      "POST",
      "/application/shops/{$this->shop_id}/listings/{$this->listing_id}/images",
      "ListingImage",
      $data
    );
    if($listing_image) {
      $listing_image->shop_id = $this->shop_id;
    }
    return $listing_image;
  }

  /**
   * Get the inventory for the listing.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getListingInventory
   * @return ListingInventory
   */
  public function getInventory(): ListingInventory
  {
    $inventory = $this->request(
      "GET",
      "/application/listings/{$this->listing_id}/inventory",
      "ListingInventory"
    );
    // Assign the listing ID to associated inventory products.
    array_map(
      (function($product){
        $product->listing_id = $this->listing_id;
      }),
      ($inventory->products ?? [])
    );
    return $inventory;
  }

  /**
   * Update the inventory for the listing.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/updateListingInventory
   * @param array $data
   * @return ListingInventory
   */
  public function updateInventory(array $data): ListingInventory
  {
    $inventory = $this->request(
      "PUT",
      "/application/listings/{$this->listing_id}/inventory",
      "ListingInventory",
      $data
    );
    // Assign the listing ID to associated inventory products.
    array_map(
      (function($product){
        $product->listing_id = $this->listing_id;
      }),
      ($inventory->products ?? [])
    );
    return $inventory;
  }

  /**
   * Get a specific product for a listing. Use this method to bypass going through the ListingInventory resource.
   *
   * @link https://developers.etsy.com/documentation/reference#tag/ShopListing-Product
   * @param integer|string $product_id
   * @return ListingProduct
   */
  public function getProduct($product_id): ListingProduct
  {
    $product = $this->request(
      "GET",
      "/application/listings/{$this->listing_id}/inventory/products/{$product_id}",
      "ListingProduct"
    );
    if($product)
  {
      $product->listing_id = $this->listing_id;
    }
    return $product;
  }

  /**
   * Get a translation for the listing in a specific language.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getListingTranslation
   * @param string $language
   * @return ListingTranslation
   */
  public function getTranslation(
    string $language
  ): ListingTranslation
  {
    $translation = $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/listings/{$this->listing_id}/translations/{$language}",
      "ListingTranslation"
    );
    if($translation)
  {
      $translation->shop_id = $this->shop_id;
    }
    return $translation;
  }

  /**
   * Creates a listing translation.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/createListingTranslation
   * @param string $language
   * @param array $data
   * @return Collection
   */
  public function createTranslation(
    string $language,
    array $data
  ): Collection
  {
    $translation = $this->request(
      "POST",
      "/application/shops/{$this->shop_id}/listings/{$this->listing_id}/translations/{$language}",
      "ListingTranslation",
      $data
    );
    if($translation)
  {
      $translation->shop_id = $this->shop_id;
    }
    return $translation;
  }

  /**
   * Gets variation images for the listing.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getListingVariationImages
   * @return Collection[Etsy\Resources\ListingVariationImage]
   */
  public function getVariationImages(): Collection
  {
    return $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/listings/{$this->listing_id}/variation-images",
      "ListingVariationImage"
    );
  }

  /**
   * Updates variation images for the listing. You MUST pass data for ALL variation images, including the ones you are not updating, as this method will override all existing variation images.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/updateVariationImages
   * @param array $data
   * @return Collection[Etsy\Resources\ListingVariationImage]
   */
  public function updateVariationImages(array $data): Collection
  {
    return $this->request(
      "POST",
      "/application/shops/{$this->shop_id}/listings/{$this->listing_id}/variation-images",
      "ListingVariationImage",
      $data
    );
  }

}
