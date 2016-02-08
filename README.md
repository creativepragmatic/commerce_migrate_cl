#Overview

This module is derived from an Ubercart 6 to Commerce 8 migration module I am currently working on.  For the most part, billing profiles, orders and line items should work for most scenarios.  The product catalog of the use case for the migration I am working on is atypical so I made the product migration here more generic.  It should work with the default product types and product variation types.  It's meant to be more of a starting point or example than a one size fits all migration.  You will probably need to customize the product and variation migrations.

The idea is to share my approach and experiences.  Hopefully, others will build on it.

##Getting Started

1. Install commerce_migrate and dependencies.

2. Running the following command sets the source database for the core migrations.  The 'd6_user' migration is the only Commerce Migrate dependency.

drush migrate-upgrade --legacy-db-url=mysql://username:password@127.0.0.1/d6database --legacy-root=http://127.0.0.1/d6site --configure-only

3. Setting the database target in the settings.php file is necessary for the Commerce migrations.  I wasn't able to get the migration to work with any other key than 'migrate'.

$databases = array (
  'default' => array (
    'default' => array (
       'database' => 'd8target',
       'username' => '',
       'password' => '',
       'prefix' => '',
       'host' => 'localhost',
       'port' => '3306',
       'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
       'driver' => 'mysql',
    ),
  ),
  'migrate' => array (
    'default' => array (
      'database' => 'd6ubercartsource',
      'username' => '',
      'password' => '',
      'prefix' => '',
      'host' => 'localhost',
      'port' => '3306',
      'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
      'driver' => 'mysql',
    ),
  ),
);

4. Import a currency and create a store. Orders won't appear unless an associated store exists.

5. Enable Migrate Drupal, then migrate 'd6_user' and its dependencies.

6. Run the Ubercart migrations.

7. If a migration hangs and you need to roll it back or resett it, you can use a command similar to the following:

DROP TABLE `migrate_map_d6_ubercart_product`, `migrate_message_d6_ubercart_product`;
DELETE FROM `config` WHERE `config`.`collection` = '' AND `config`.`name` = 'migrate.migration.d6_ubercart_product';
DELETE FROM `key_value` WHERE `key_value`.`collection` = 'migrate:high_water' AND `key_value`.`name` = 'd6_ubercart_product';
DELETE FROM `key_value` WHERE `key_value`.`collection` = 'migrate_last_imported' AND `key_value`.`name` = 'd6_ubercart_product';
DELETE FROM `key_value` WHERE `key_value`.`collection` = 'migrate_status' AND `key_value`.`name` = 'd6_ubercart_product';

##Issues

Most of the issues I encountered are commented in the source files but when migrating products, a number of nonsensical products will appear equal to the number of products imported with file names similar to the following:

bigustebrecluspocrobrocledusecrustudanovewerouajaswamapredaspobehestocrachuwibredapawupobri

I know this is caused by the stubs used in the products and product variations migrations but I'm not sure if they're being created by an issue in the migrate module or because I don't fully understand it.  When I ran Migrate Drupal, files with nonsensical names like this also appeared.
