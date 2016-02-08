<?php

/**
 * @file
 * Contains \Drupal\commerce_migrate\Plugin\migrate\source\ubercart\d6\Product.
 */

namespace Drupal\commerce_migrate\Plugin\migrate\source\ubercart\d6;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * @MigrateSource(
 *   id = "d6_ubercart_product"
 * )
 */
class Product extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {

    // You will need probably need to add a 'left join' for each additional 
    // field.
    $query = $this->select('node', 'n')
      ->fields('n', array('nid', 'vid', 'type', 'title', 'uid', 'created', 
        'changed', 'status'));
    $query->innerJoin('uc_products', 'ucp', 'n.nid = ucp.nid AND n.vid = ucp.vid');
    $query->leftJoin('node_revisions', 'nr', 'n.nid = nr.nid AND n.vid = nr.vid');
    $query->leftJoin('filter_formats', 'ff', 'nr.format = ff.format');
    $query->fields('ucp', array('model', 'sell_price'));
    $query->fields('nr', array('body', 'teaser'));
    $query->fields('ff', array('name'));
    $query->condition('type', 'product', '=');
    $query->distinct();

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {

    $fields = [
      'nid' => $this->t('Node ID'),
      'uid' => $this->t('User ID of person who added product'),
      'title' => $this->t('Product name'),
      'body' => $this->t('Product description'),
      'status' => $this->t('Published status'),
      'created' => $this->t('Date product created'),
      'changed' => $this->t('Last time product changed'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    // This is just an example of how to import images into a multiple image
    // field.
    //$image_ids = $this->select('content_field_image_cache', 'cfic')
    //  ->fields('cfic', ['field_image_cache_fid'])
    //  ->condition('nid', $row->getSourceProperty('nid'))
    //  ->condition('vid', $row->getSourceProperty('vid'))
    //  ->execute()
    //  ->fetchCol();
    //$row->setSourceProperty('images', $image_ids);

    $row->setSourceProperty('name', str_replace(' ', '_', strtolower($row->getSourceProperty('name'))));


    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'nid' => [
        'type' => 'integer',
        'alias' => 'n',
      ],
    ];
  }
}
