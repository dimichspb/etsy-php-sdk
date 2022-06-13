<?php

namespace Etsy\Resources;

use Etsy\Collection;
use Etsy\Resource;

/**
 * Receipt resource class.
 *
 * @link https://developers.etsy.com/documentation/reference#tag/Shop-Receipt
 * @author Rhys Hall hello@rhyshall.com
 *
 * @property int $receipt_id
 * @property string $name
 * @property string $first_line
 * @property string $second_line
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $country_iso
 * @property bool $is_shipped
 */
class Receipt extends Resource {

  /**
   * @var array
   */
  protected $_associations = [
    'shipments' => 'Shipment'
  ];

  /**
   * Creates a new Shipment against the receipt.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/createReceiptShipment
   * @param array $data
   * @return Receipt
   */
  public function createShipment(array $data): Receipt
  {
    $receipt = $this->request(
      "POST",
      "/application/shops/{$this->shop_id}/receipts/{$this->receipt_id}/tracking",
      "Receipt",
      $data
    );
    return $this;
  }

  /**
   * Gets all transactions for the receipt.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getShopReceiptTransactionsByReceipt
   * @return Collection|Transaction[]
   */
  public function getTransactions(): Collection {
    return $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/receipts/{$this->receipt_id}/transactions",
      "Transaction"
    );
  }

  /**
   * Gets all payments for the receipt.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getShopPaymentByReceiptId
   * @return Collection|Payment[]
   */
  public function getPayments(): Collection {
    return $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/receipts/{$this->receipt_id}/payments",
      "Payment"
    );
  }

  /**
   * Get all listings associated with the receipt.
   *
   * @link https://developers.etsy.com/documentation/reference#operation/getListingsByShopReceipt
   * @param array $params
   * @return Collection|Listing[]
   */
  public function getListings(array $params = []): Collection {
    return $this->request(
      "GET",
      "/application/shops/{$this->shop_id}/receipts/{$this->receipt_id}/listings",
      "Listing",
      $params
    );
  }

}
