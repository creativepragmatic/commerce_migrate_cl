#Overview

This module is derived from a custom Ubercart 6 to Commerce 8 migration module I am currently working on.  For the most part, billing profiles, orders, and line items should work for most scenarios.  The product catalog of the use case for the migration I am working on is atypical so I made the product migration here more generic.  It should work with the default product types and product variation types.  It's meant to be more of a starting point or example than a one size fits all migration.  You will probably need to customize the product and variation migrations.

The idea is to share my approach and experiences.  Hopefully, others will build on it.

##Getting Started

1. Install commerce_migrate and dependencies.

2. Running the following command sets the source database for the core migrations.  The 'd6_user' migration is the only Commerce Migrate dependency.

        drush migrate-upgrade --legacy-db-url=mysql://username:password@127.0.0.1/d6database --legacy-root=http://127.0.0.1/d6site --configure-only

3. Setting the database target in the settings.php file is necessary for the Commerce migrations.  I wasn't able to get the migration to work with any other key then 'migrate'.

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

7. If a migration hangs and you need to roll it back or reset it, you can use a command similar to the following:

        DROP TABLE `migrate_map_d6_ubercart_product`, `migrate_message_d6_ubercart_product`;
        DELETE FROM `config` WHERE `config`.`collection` = '' AND `config`.`name` = 'migrate.migration.d6_ubercart_product';
        DELETE FROM `key_value` WHERE `key_value`.`collection` = 'migrate:high_water' AND `key_value`.`name` = 'd6_ubercart_product';
        DELETE FROM `key_value` WHERE `key_value`.`collection` = 'migrate_last_imported' AND `key_value`.`name` = 'd6_ubercart_product';
        DELETE FROM `key_value` WHERE `key_value`.`collection` = 'migrate_status' AND `key_value`.`name` = 'd6_ubercart_product';

##Issues and Notes

1. Most of the issues I encountered are commented in the source files but when migrating products, a number of nonsensical products will appear equal to the number of products imported with file names similar to the following:

        bigustebrecluspocrobrocledusecrustudanovewerouajaswamapredaspobehestocrachuwibredapawupobri

2. I know this is caused by the stubs used in the products and product variations migrations but I'm not sure if they're being created by an issue in the migrate module or because I don't fully understand it. I remove them with the following SQL commands:

        DELETE FROM `commerce_product_variation_field_data` 
          WHERE uid = 0;

        DELETE FROM `commerce_product__variations` 
          WHERE `variations_target_id` NOT IN (
            SELECT `variation_id` 
            FROM `commerce_product_variation_field_data`
          );

        DELETE FROM `commerce_product_variation` 
          WHERE `variation_id` NOT IN (
            SELECT `variation_id` 
            FROM `commerce_product_variation_field_data`
          );

        DELETE FROM `commerce_product_field_data` WHERE `uid` = 0;

        DELETE FROM `commerce_product` 
          WHERE `product_id` NOT IN (
            SELECT `product_id` 
            FROM `commerce_product_field_data`
          );

3. When I ran Migrate Drupal, files with nonsensical names like this also appeared.  I removed those with the following commands:

    DELETE FROM `file_managed` WHERE `uid` != 7;
    DELETE FROM `file_usage` WHERE `type` = 'user';
    find . -size 0c -delete

4. For product variations, the title field does not get migrated in. I'm not sure why.

5. Order totals get zero'd out during migration because of the order total recalculation code at the end of the preSave method in Order.php. I opened an issue at: https://www.drupal.org/node/2686029

6. Orders are currently being migrated before line items but I'm not sure this is the best approach.  I don't remember the 'commerce_order__line_items' table being there when I started but it populating it may require another template.
