<?php

namespace Etsy\Resources;

use Etsy\Collection;
use Etsy\Resource;
use Etsy\Exception\ApiException;

/**
 * Shop resource class. Represents a Etsy user's shop.
 *
 * @link https://developers.etsy.com/documentation/reference/#tag/Shop
 * @author Rhys Hall hello@rhyshall.com
 *
 * @property float $shop_id
 * @property string $shop_name
 * @property array $languages
 */
class Shop extends Resource {

  /**
   * Update the shop.
   *
   * @param array $data
   * @return Shop
   */
  public function update(array $data): Shop
  {
    return $this->updateRequest(
      "/application/shops/{$this->shop_id}",
      $data
    );
  }

  /**
   * Get all sections for the shop.
   *
   * @param array $params
   * @return Collection
   */
  public function getSections(array $params = []): Collection
  {
    return $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/sections",
      "ShopSection",
      $params
    )
      ->append(['shop_id' => $this->shop_id]);
  }

  /**
   * Get a specific shop section.
   *
   * @param integer|string $section_id
   * @return ShopSection
   */
  public function getSection($section_id): ShopSection
  {
    $section = $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/sections/{$section_id}",
      "ShopSection"
    );
    if($section) {
      $section->shop_id = $this->shop_id;
    }
    return $section;
  }

    /**
     * Creates a new shop section.
     *
     * @param string $title
     * @return Collection
     * @throws ApiException
     */
  public function createSection(string $title): Collection
  {
    if(!strlen(trim($title))) {
      throw new ApiException("Section title cannot be blank.");
    }
    return $this->request(
      "POST",
      "/application/shops/{$this->shop_id}/sections",
      "ShopSection",
      ["title" => $title]
    );
  }

  /**
   * Get all reviews for the shop.
   *
   * @param array $params
   * @return Collection|Review[]
   */
  public function getReviews(array $params = []): Collection
  {
    return $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/reviews",
      "Review",
      $params
    );
  }

  /**
   * Get all shipping profiles for the shop.
   *
   * @return Collection|ShippingProfile[]
   */
  public function getShippingProfiles(): Collection
  {
    $profiles = $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/shipping-profiles",
      "ShippingProfile"
    )
      ->append(['shop_id' => $this->shop_id]);
    // Assign the shop ID to associated resources.
    array_map(
      (function($profile){
        $this->assignShopIdToProfile($profile);
      }),
      $profiles->data
    );
    return $profiles;
  }

  /**
   * Gets a single shipping profile for the shop.
   *
   * @param float $shipping_profile_id
   * @return ShippingProfile
   */
  public function getShippingProfile(float $shipping_profile_id): ShippingProfile
  {
    $profile = $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/shipping-profiles/{$shipping_profile_id}",
      "ShippingProfile"
    );
    // Assign the shop id to the profile and associated resources.
    $this->assignShopIdToProfile($profile);
    return $profile;
  }

  /**
   * Creates a new shipping profile for the shop.
   *
   * @link https://developers.etsy.com/documentation/reference/#operation/createShopShippingProfile
   * @param array $data
   * @return ShippingProfile
   */
  public function createShippingProfile(array $data): ShippingProfile
  {
    $profile = $this->request(
      "POST",
      "/application/shops/{$this->shop_id}/shipping-profiles",
      "ShippingProfile",
      $data
    );
    // Assign the shop id to the profile and associated resources.
    $this->assignShopIdToProfile($profile);
    return $profile;
  }

  /**
   * Assigns the shop ID to a shipping profile.
   *
   * @param ShippingProfile $profile
   * @return void
   */
  private function assignShopIdToProfile(
      ShippingProfile $profile
  ) {
    $profile->shop_id = $this->shop_id;
    array_map(
      (function($destination) {
        $destination->shop_id = $this->shop_id;
      }),
      ($profile->shipping_profile_destinations ?? [])
    );
    array_map(
      (function($upgrade) {
        $upgrade->shop_id = $this->shop_id;
      }),
      ($profile->shipping_profile_upgrades ?? [])
    );
  }

  /**
   * Get all receipts for the shop.
   *
   * @param array $params
   * @return Collection|Receipt[]
   */
  public function getReceipts(array $params = []): Collection
  {
    return $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/receipts",
      "Receipt",
      $params
    )
      ->append(['shop_id' => $this->shop_id]);
  }

  /**
   * Gets a single receipt for the shop.
   *
   * @param float $receipt_id
   * @return Receipt
   */
  public function getReceipt(float $receipt_id): Receipt
  {
    $receipt = $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/receipts/{$receipt_id}",
      "Receipt"
    );
    if($receipt) {
      $receipt->shop_id = $this->shop_id;
    }
    return $receipt;
  }

  /**
   * Get all transactions for the shop.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getShopReceiptTransactionsByShop
   * @param array $params
   * @return Collection|Transaction[]
   */
  public function getTransactions(array $params = []): Collection
  {
    return $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/transactions",
      "Transaction",
      $params
    );
  }

  /**
   * Get a specific transaction for the shop.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getShopReceiptTransaction
   * @param integer|string $transaction_id
   * @return Transaction
   */
  public function getTransaction($transaction_id): Transaction
  {
    return $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/transactions/{$transaction_id}",
      "Transaction"
    );
  }

  /**
   * Get all payment account ledger entries for the shop.
   *
   * @link https://developers.etsy.com/documentation/reference#tag/Ledger-Entry
   * @param array $params
   * @return Collection|LedgerEntry[]
   */
  public function getLedgerEntries(array $params = []): Collection
  {
    return $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/payment-account/ledger-entries",
      "LedgerEntry",
      $params
    )
      ->append(['shop_id' => $this->shop_id]);
  }

  /**
   * Get the specified payments for the shop.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getPayments
   * @param array $payment_ids
   * @return Collection|Payment[]
   */
  public function getPayments(array $payment_ids = []): Collection
  {
    return $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/payments",
      "Payment",
      ["payment_ids" => $payment_ids]
    );
  }

  /**
   * Creates a draft Etsy listing.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/createDraftListing
   * @param array $data
   * @return Listing
   */
  public function createListing(array $data): Listing
  {
      return $this->request(
        "POST",
        "/application/shops/{$this->shop_id}/listings",
        "Listing",
        $data
      );
  }

  /**
   * Get the listings for the shop. This method should be used when querying listings for your own shop.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getListingsByShop
   * @param array $params
   * @return Collection|Listing[]
   */
  public function getListings(array $params = []): Collection
  {
      return $this->request(
        "GET",
        "/application/shops/{$this->shop_id}/listings",
        "Listing",
        $params
      );
  }

  /**
   * Get all active listings for a public shop. Use this method when querying listings for a public shop.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/findAllActiveListingsByShop
   * @param array $params
   * @return Collection|Listing[]
   */
  public function getPublicListings(array $params = []): Collection
  {
      return $this->request(
        "GET",
        "/application/shops/{$this->shop_id}/listings/active",
        "Listing",
        $params
      );
  }

  /**
   * Get the featured listings for the shop.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getFeaturedListingsByShop
   * @param array $params
   * @return Collection|Listing[]
   */
  public function getFeaturedListings(array $params = []): Collection
  {
      return $this->request(
        "GET",
        "/application/shops/{$this->shop_id}/listings/featured",
        "Listing",
        $params
      );
  }

}
