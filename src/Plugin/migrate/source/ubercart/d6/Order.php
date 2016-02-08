<?php

/**
 * @file
 * Contains \Drupal\commerce_migrate\Plugin\migrate\source\ubercart\d6\Order.
 */

namespace Drupal\commerce_migrate\Plugin\migrate\source\ubercart\d6;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\Core\Database\Database;

/**
 * @MigrateSource(
 *   id = "d6_ubercart_order"
 * )
 */
class Order extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('uc_orders', 'uo')
      ->fields('uo', ['order_id', 'uid', 'order_status', 'order_total', 
        'primary_email', 'host', 'data', 'created', 'modified', 'currency']);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'order_id' => $this->t('Order ID'),
      'uid' => $this->t('User ID of order'),
      'order_status' => $this->t('Order status'),
      'order_total' => $this->t('Grand total of order'),
      'primary_email' => $this->t('Email associated with order'),
      'host' => $this->t('IP address of customer'),
      'data' => $this->t('Order attributes'),
      'created' => $this->t('Date/time of order creation'),
      'modified' => $this->t('Date/time of last order modification'),
      'currency' => $this->t('Curency used'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    # Sets billing address to address on most recent order. This may not be
    # appropriate for most use cases since it would require to maintain an
    # old Ubercart version of the site to lookup addresses for old orders.
    $default = Database::getConnection('default');
    $profile = $default->select('profile', 'p')
      ->fields('p', ['profile_id'])
      ->condition('uid', $row->getSourceProperty('uid'))
      ->execute()
      ->fetchCol();
    $row->setSourceProperty('billing_id', $profile[0]);

    //drush_print_r(unserialize($row->getSourceProperty('data')));

    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'order_id' => [
        'type' => 'integer',
        'alias' => 'uo',
      ],
    ];
  }
}
