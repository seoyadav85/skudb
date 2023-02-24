<?php 
//hook into the init action and call create_book_taxonomies when it fires
  

  
//create a custom taxonomy name it Brands for your posts
  
function Brand_taxonomies_custom_taxonomies() {
  
// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI
  
  $labels = array(
    'name' => _x( 'Brands', 'taxonomy general name' ),
    'singular_name' => _x( 'Brands', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Brands' ),
    'all_items' => __( 'All Brands' ),
    'parent_item' => __( 'Parent Brands' ),
    'parent_item_colon' => __( 'Parent Brands:' ),
    'edit_item' => __( 'Edit Brands' ), 
    'update_item' => __( 'Update Brands' ),
    'add_new_item' => __( 'Add New Brands' ),
    'new_item_name' => __( 'New Brands Name' ),
    'menu_name' => __( 'Brands' ),
  );    
  
// Now register the taxonomy
  register_taxonomy('brands_data',array('product'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'with_front' => true,
    'show_in_rest' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'brands_data' ),
  ));
  
}

