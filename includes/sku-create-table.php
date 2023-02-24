<?php 
// create table when plugin activate

function Skudb_create_plugin_database_table()
{
//require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
      global $wpdb;
        $table = $wpdb->prefix . 'skudb_table';
        $subcattable = $wpdb->prefix . 'skudb_subcategory';
        $subbrand = $wpdb->prefix . 'skudb_brands';
        $product_json = $wpdb->prefix . 'product_json';
        $charset = $wpdb->get_charset_collate();
        $charset_collate = $wpdb->get_charset_collate();       
        $tab1 = "CREATE TABLE IF NOT EXISTS $table (
        id int(11) NOT NULL auto_increment,
        cat_id int(11) NOT NULL,
        name varchar(70) NOT NULL,       
        type varchar(100) NOT NULL,
        file_path varchar(100) NOT NULL,
        total_outlet varchar(100) NOT NULL,
        outlets varchar(100) NOT NULL,        
        PRIMARY KEY  (id)
        ) $charset_collate;";

        $tab2 = "CREATE TABLE IF NOT EXISTS $subcattable (
        id int(11) NOT NULL auto_increment,
        subcat_id int(11) NOT NULL,
        main_category_id int(11) NOT NULL,
        category_id int(11) NOT NULL,
        parent_id int(11) NOT NULL,
        subcat_name varchar(50) NOT NULL,        
        image_url varchar(100) NOT NULL,
        total_product varchar(100) NOT NULL,            
        PRIMARY KEY  (id)
        ) $charset_collate;";

        $tab3 = "CREATE TABLE IF NOT EXISTS $subbrand (
        id int(11) NOT NULL auto_increment,
        brand_id int(11) NOT NULL,
        name varchar(30) NOT NULL,
        brand_type int(50) NOT NULL,
        type varchar(50) NOT NULL,
        file_path varchar(50) NOT NULL,        
        total_outlet varchar(100) NOT NULL,
        outlets varchar(100) NOT NULL,            
        PRIMARY KEY  (id)
        ) $charset_collate;";
         $tab4 = "CREATE TABLE IF NOT EXISTS $product_json (
        id int(11) NOT NULL auto_increment,
        cat_id int(11) NOT NULL,
        page_id int(11) NOT NULL,
        jason_data longtext COLLATE utf8_unicode_ci,                    
        PRIMARY KEY  (id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $tab1 );
        dbDelta( $tab2 ); 
        dbDelta( $tab3);
        dbDelta( $tab4);
        }

