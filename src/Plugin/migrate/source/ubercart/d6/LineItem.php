<?php

/**
 * @file
 * Contains \Drupal\commerce_migrate\Plugin\migrate\source\ubercart\d6\LineItem.
 */

namespace Drupal\commerce_migrate\Plugin\migrate\source\ubercart\d6;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\Core\Database\Database;

/**
 * @MigrateSource(
 *   id = "d6_ubercart_line_item"
 * )
 */
class LineItem extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('uc_order_products', 'uop')
      ->fields('uop', ['order_product_id', 'order_id', 'nid', 'title', 'qty', 'price', 'data']);
    $query->innerJoin('uc_orders', 'uo', 'uop.order_id = uo.order_id');
    $query->fields('uo', ['created', 'modified', 'currency']);

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'order_product_id' => $this->t('Line item ID'),
      'order_id' => $this->t('Order ID'),
      'nid' => $this->t('Product ID'),
      'title' => $this->t('Product name'),
      'qty' => $this->t('Quantity sold'),
      'price' => $this->t('Price of product sold'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'order_product_id' => [
        'type' => 'integer',
        'alias' => 'uop',
      ],
    ];
  }
}
