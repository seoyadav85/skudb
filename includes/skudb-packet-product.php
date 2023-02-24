<?php 
// add another interval
// function Sku_cron_add_minute( $schedules ) {
//     // Adds once every minute to the existing schedules.
//     $schedules['everyminute'] = array(
//         'interval' => 60,
//         'display' => __( 'Once Every Minute' )
//     );
//     return $schedules;
// }
// add_filter( 'cron_schedules', 'Sku_cron_add_minute' );

// // hook that function onto our scheduled event:
// add_action ('skumycronjob', 'sku_my_repeat_function'); 

// // create a scheduled event (if it does not exist already)
// function sku_cronstarter_activation() {
//     if( !wp_next_scheduled( 'skumycronjob' ) ) {  
//        wp_schedule_event( time(), 'everyminute', 'skumycronjob' );  
//     }
// }
// // and make sure it's called whenever WordPress loads
// add_action('wp', 'sku_cronstarter_activation');

// function sku_my_repeat_function(){
// global $wpdb;
// $post_type = 'product';
// $product_json = $wpdb->prefix . 'product_json';
// $allproduct = $wpdb->get_results( "SELECT  `jason_data` FROM $product_json"); 
// if(!empty($allproduct)){ 
// foreach($allproduct as $resultdata){
// $responceDatas = json_decode($resultdata->jason_data);
// $i =1;
// foreach($responceDatas->data->data as $results){
//  //$percent = intval($i/$total * 100)."%";  
//     $title                    = $results->name;
//     $descprtion               = $results->long_description;  
//     $restaurant_id            = $results->restaurant_id; 
//     $live_product_id          = $results->live_product_id;
//     $image_type               = $results->image_type;
//     $warehouse_id             = $results->warehouse_id;
//     $brand_id                 = $results->brand_id;  
//     $main_category_id         = $results->main_category_id;
//     $category_id              = array($results->category_id);  
//     $sub_category_id          = $results->sub_category_id; 
//     $main_image               = $results->main_image;
//     $size_guide_image         = $results->size_guide_image; 
//     $exp_month                = $results->exp_month; 
//     $exp_days                 = $results->exp_days;
//     $products_type            = $results->products_type;  
//     $is_digital_item          = $results->is_digital_item; 
//     $gender                   = $results->gender; 
//     $recipe_description       = $results->recipe_description; 
//     $sku_code                 = $results->sku_code;
//     $chef_amount              = $results->chef_amount;
//     $celebrity_amount         = $results->celebrity_amount;
//     $cost_price               = $results->cost_price;  
//     $discount_price           = $results->discount_price; 
//     $admin_amount             = $results->admin_amount; 
//     $total_amount             = $results->total_amount;
//     $price                    = $results->price;  
//     $video                    = $results->video;
//     $out_of_stock             = $results->out_of_stock;  
//     $serve                    = $results->serve; 
//     $points                   = $results->points;
//     $points_percent           = $results->points_percent; 
//     $extra_kilopoints         = $results->extra_kilopoints;
//     $extra_kilopoints_percent = $results->extra_kilopoints_percent;  
//     $prepration_time          = $results->prepration_time; 
//     $product_for              = $results->product_for;
//     $shop_type                = $results->shop_type;  
//     $delivery_time            = $results->delivery_time;
//     $delivery_hours           = $results->delivery_hours;  
//     $weight                   = $results->weight; 
//     $qty                      = $results->qty;
//     $product_url              = $results->product_url; 
//     $created_at               = $results->created_at;  
//     $is_active                = $results->is_active;
//     $is_deleted               = $results->is_deleted;  
//     $is_show                  = $results->is_show; 
//     $buy_one_get_one          = $results->buy_one_get_one;
//     $props                    = wp_kses_post($results->props); 
//     $celebrity_id             = $results->celebrity_id;
//     $customization            = $results->customization;  
//     $customize_option         = $results->customize_option; 
//     $is_trending              = $results->is_trending;
//     $is_ready                 = $results->is_ready;  
//     $is_gift_sync             = $results->is_gift_sync;
//     $category_name            = $results->category_name;  
//     $subcategory_name         = $results->subcategory_name; 
//     $main_category_name       = $results->main_category_name;
//     $brand_name               = $results->brand_name; 
//     $currency                 = $results->currency;  
//     $is_favorite              = $results->is_favorite;
//     $is_topping               = $results->is_topping;  
//     $attributes               = $results->attributes;  // arrsay 
//     $product_attributes       = $results->product_attributes; // array   
//     $add_on                   = $results->add_on; // array 
//     $avg_rating               = $results->avg_rating;
//     $sub_category_desc        = $results->sub_category_desc;  
//     $can_rate                 = $results->can_rate; 
//     $total_rate               = $results->total_rate;
//     $product_attr             = $results->product_attr;  
//     $product_tags             = array($results->product_tags);
//     $product_points           = $results->product_points;  
//     $product_images           = $results->product_images; // array -- gallary image 
//     $qr_code                  = $results->qr_code;
//     $sku_product_id           =  $results->id;    
//     $sub_category_extra_images= $results->sub_category_extra_images;  // array
//     $get_product_attributes   = $results->get_product_attributes;  // array
//     //the array of arguements to be inserted with wp_insert_post   
//     global $wpdb;
//     $check = $wpdb->query($wpdb->prepare("SELECT * FROM `wp_postmeta` WHERE `meta_key` = 'sku_product_id' AND `meta_value` = $sku_product_id"));
//     if($check <= 0){
//     $front_post = array(
//     'post_title'    => $title,
//     'post_status'   => 'publish', 
//     'post_content'  => $descprtion,  
//     'post_excerpt'  => $props,     
//     'post_type'     => $post_type, 
//     );
//     //insert the the post into database by passing $new_post to wp_insert_post   
//     $product_id = wp_insert_post($front_post);
//     $url = $main_image;
//     $product_image = Product_image_upload_create($url,$product_id ); 
//     wp_set_object_terms($product_id, $product_tags, 'product_tag', true );   
//     wp_set_object_terms($product_id, $brand_name, 'brands_data', true ); 
//     $datacat = wp_set_object_terms( $product_id, $category_name , 'product_cat', true ); 
//     wp_set_object_terms( $product_id, $subcategory_name , 'product_cat', true );
//     //$child= wp_insert_term($subcategory_name, 'product_cat' ,array( 'slug'=>'sanajykumar789', 'parent' =>$datacat[0]));    
//     update_post_meta($product_id, "_sku", $sku_code);
//     //update_post_meta($product_id, "_price", (float)$cost_price, true);
//     update_post_meta($product_id, "_regular_price", (float)$total_amount, true);
//     if(!empty($total_amount > $discount_price )):
//     update_post_meta($product_id, "_sale_price", (float)$discount_price, true); 
//     endif;
//     update_post_meta( $product_id, 'points', $points, true );
//     update_post_meta( $product_id,  'sku_product_id' , $sku_product_id, true);
//     update_post_meta( $product_id, '_stock_status', 'instock',true);
//     $items = array();
//     foreach($product_images as $results){    
//     $galimg = $results->image;
//     $dataimages = Product_Gallary_image_upload_create($galimg ,$product_id);
//     $media = get_attached_media($dataimages, $product_id);
//     $items[] = $dataimages;
//     }
//     $attach_ids=implode(',',$items);
//     if($product_attributes){
//     wp_set_object_terms($product_id, 'variable', 'product_type');   
//     foreach($product_attributes as $results){
//     $data = $results->attributeValues;
//     $sdata = $results->attribute_name;
//     //print_r($data);
//     foreach($data as $keyvalue){
//     $varvalue = $keyvalue->attribute_value_name; 
//     $price = $keyvalue->price;   
//     $discount_prices = $keyvalue->discount_price;
//      // varianiton product add //
//     //wp_set_object_terms( $post_id, 'variable', 'product_type' );
//     $attr_label = $sdata;
//     $attr_slug = sanitize_title($attr_label);
//     // $data = save_product_attribute_from_name($attr_label);
//     $attributes_array[$attr_slug] = array(
//     'name' => $attr_label,
//     'value' => $varvalue,
//     'is_visible' => '1',     
//     'is_variation' => '1',
//     'is_taxonomy' => '0' // for some reason, this is really important       
// );
// update_post_meta( $product_id, '_product_attributes', $attributes_array );

// $parent_id = $product_id;
// $variation = array(
//     'post_title'   => get_the_title() . ' (variation)',
//     'post_content' => '',
//     'post_status'  => 'publish',
//     'post_parent'  => $parent_id,
//     'post_type'    => 'product_variation'
// );
// $variation_id = wp_insert_post( $variation );
// update_post_meta( $variation_id, '_manage_stock', "yes" ,true);
// update_post_meta( $variation_id, '_regular_price', $price ,true );
// if(!empty($price > $discount_prices )):
// update_post_meta( $variation_id, '_sale_price', $discount_price  ,true);
// endif;
// update_post_meta( $variation_id, '_stock', 100,true );
// update_post_meta( $variation_id, '_stock_status', 'instock',true);
// update_post_meta( $variation_id, 'attribute_' . $attr_slug, $varvalue ,true);
// WC_Product_Variable::sync( $parent_id );
// }
// }
// }
// // The function to be run
// //create_product_variation($product_id, $product_attributes)
// update_post_meta($product_id, '_product_image_gallery', $attach_ids ,true);    
// update_post_meta($product_id, 'qr_code', $qr_code, true);
// update_post_meta( $product_id, 'video', $video, true);    
// $parent_id = $product_id; // Or get the variable product id dynamically


//  }
//  $i++;
//    }
//   }

//  }


// }
// new code 
function Sku_cron_add_minute( $schedules ) {
    // Adds once every minute to the existing schedules.
    $schedules['everyminute'] = array(
        'interval' => 60,
        'display' => __( 'Once Every Minute' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'Sku_cron_add_minute' );

// hook that function onto our scheduled event:
add_action ('skumycronjob', 'sku_my_repeat_function'); 

// create a scheduled event (if it does not exist already)
function sku_cronstarter_activation() {
    if( !wp_next_scheduled( 'skumycronjob' ) ) {  
       wp_schedule_event( time(), 'everyminute', 'skumycronjob' );  
    }
}
// and make sure it's called whenever WordPress loads
add_action('wp', 'sku_cronstarter_activation');

function sku_my_repeat_function(){
global $wpdb;
$post_type = 'product';
$product_json = $wpdb->prefix . 'product_json';
$allproduct = $wpdb->get_results( "SELECT  `jason_data` FROM $product_json"); 
if(!empty($allproduct)){ 
foreach($allproduct as $resultdata){
$responceDatas = json_decode($resultdata->jason_data);
$i =1;
foreach($responceDatas->data->data as $results){
 //$percent = intval($i/$total * 100)."%";  
    $title                    = $results->name;
    $descprtion               = $results->long_description;  
    $restaurant_id            = $results->restaurant_id; 
    $live_product_id          = $results->live_product_id;
    $image_type               = $results->image_type;
    $warehouse_id             = $results->warehouse_id;
    $brand_id                 = $results->brand_id;  
    $main_category_id         = $results->main_category_id;
    $category_id              = array($results->category_id);  
    $sub_category_id          = $results->sub_category_id; 
    $main_image               = $results->main_image;
    $size_guide_image         = $results->size_guide_image; 
    $exp_month                = $results->exp_month; 
    $exp_days                 = $results->exp_days;
    $products_type            = $results->products_type;  
    $is_digital_item          = $results->is_digital_item; 
    $gender                   = $results->gender; 
    $recipe_description       = $results->recipe_description; 
    $sku_code                 = $results->sku_code;
    $chef_amount              = $results->chef_amount;
    $celebrity_amount         = $results->celebrity_amount;
    $cost_price               = $results->cost_price;  
    $discount_price           = $results->discount_price; 
    $admin_amount             = $results->admin_amount; 
    $total_amount             = $results->total_amount;
    $price                    = $results->price;  
    $video                    = $results->video;
    $out_of_stock             = $results->out_of_stock;  
    $serve                    = $results->serve; 
    $points                   = $results->points;
    $points_percent           = $results->points_percent; 
    $extra_kilopoints         = $results->extra_kilopoints;
    $extra_kilopoints_percent = $results->extra_kilopoints_percent;  
    $prepration_time          = $results->prepration_time; 
    $product_for              = $results->product_for;
    $shop_type                = $results->shop_type;  
    $delivery_time            = $results->delivery_time;
    $delivery_hours           = $results->delivery_hours;  
    $weight                   = $results->weight; 
    $qty                      = $results->qty;
    $product_url              = $results->product_url; 
    $created_at               = $results->created_at;  
    $is_active                = $results->is_active;
    $is_deleted               = $results->is_deleted;  
    $is_show                  = $results->is_show; 
    $buy_one_get_one          = $results->buy_one_get_one;
    $props                    = wp_kses_post($results->props); 
    $celebrity_id             = $results->celebrity_id;
    $customization            = $results->customization;  
    $customize_option         = $results->customize_option; 
    $is_trending              = $results->is_trending;
    $is_ready                 = $results->is_ready;  
    $is_gift_sync             = $results->is_gift_sync;
    $category_name            = $results->category_name;  
    $subcategory_name         = $results->subcategory_name; 
    $main_category_name       = $results->main_category_name;
    $brand_name               = $results->brand_name; 
    $currency                 = $results->currency;  
    $is_favorite              = $results->is_favorite;
    $is_topping               = $results->is_topping;  
    $attributes               = $results->attributes;  // arrsay 
    $product_attributes       = $results->product_attributes; // array   
    $add_on                   = $results->add_on; // array 
    $avg_rating               = $results->avg_rating;
    $sub_category_desc        = $results->sub_category_desc;  
    $can_rate                 = $results->can_rate; 
    $total_rate               = $results->total_rate;
    $product_attr             = $results->product_attr;  
    $product_tags             = array($results->product_tags);
    $product_points           = $results->product_points;  
    $product_images           = $results->product_images; // array -- gallary image 
    $qr_code                  = $results->qr_code;
    $sku_product_id           =  $results->id;    
    $sub_category_extra_images= $results->sub_category_extra_images;  // array
    $get_product_attributes   = $results->get_product_attributes;  // array
    $postname = sanitize_title($title );
    //the array of arguements to be inserted with wp_insert_post   
    global $wpdb;
    $check = $wpdb->query($wpdb->prepare("SELECT * FROM `wp_postmeta` WHERE `meta_key` = 'sku_product_id' AND `meta_value` = $sku_product_id"));
    if($check <= 0){
    $front_post = array(
    'post_title'    => $title,
    'post_status'   => 'publish', 
    'post_content'  => $descprtion,  
    'post_excerpt'  => $props,         
    'post_type'     => $post_type, 
    'guid'          => home_url( '/product/'.$postname.'/' )
    );
    //insert the the post into database by passing $new_post to wp_insert_post   
    $product_id = wp_insert_post($front_post);
    $url = $main_image;
    $product_image = Product_image_upload_create($url,$product_id ); 
    wp_set_object_terms($product_id, $product_tags, 'product_tag', true );   
    wp_set_object_terms($product_id, $brand_name, 'brands_data', true ); 
    wp_set_object_terms( $product_id, $category_name , 'product_cat', true ); 
    //$child= wp_insert_term($subcategory_name, 'product_cat' ,array( 'slug'=>'sanajykumar789', 'parent' =>$datacat[0]));    
    update_post_meta($product_id, "_sku", $sku_code);
    //update_post_meta($product_id, "_price", (float)$cost_price, true);
    update_post_meta($product_id, "_regular_price", (float)$total_amount, true);
    update_post_meta($product_id, "_price", (float)$total_amount, true);
    if(!empty($total_amount > $discount_price )):
    update_post_meta($product_id, "_sale_price", (float)$discount_price, true); 
    update_post_meta($product_id, "_price", (float)$discount_price, true);
    endif;
    update_post_meta( $product_id, 'points', $points, true );
    update_post_meta( $product_id,  'sku_product_id' , $sku_product_id, true);
    update_post_meta( $product_id, '_stock_status', 'instock',true);
    $items = array();
    foreach($product_images as $results){    
    $galimg = $results->image;
    $dataimages = Product_Gallary_image_upload_create($galimg ,$product_id);
    $media = get_attached_media($dataimages, $product_id);
    $items[] = $dataimages;
    }
    $attach_ids=implode(',',$items);
    if($product_attributes){
    wp_set_object_terms($product_id, 'variable', 'product_type');   
    foreach($product_attributes as $results){
    $data = $results->attributeValues;
    $sdata = $results->attribute_name;
    //print_r($data);
    foreach($data as $keyvalue){
    $varvalue = $keyvalue->attribute_value_name; 
    $price = $keyvalue->price;   
    $discount_prices = $keyvalue->discount_price;
     // varianiton product add //
    //wp_set_object_terms( $post_id, 'variable', 'product_type' );
    $attr_label = $sdata;
    $attr_slug = sanitize_title($attr_label);
     $data =  array(        
        'attributes'    => array(
            $attr_label   => array($varvalue),
            
        ),
    ) ;    
      ## ---------------------- VARIATION ATTRIBUTES ---------------------- ##
    $product_attributes = array();

    foreach( $data['attributes'] as $key => $terms ){
        $attr_name = ucfirst($key);
        $taxonomy = sanitize_title($key);
        //$taxonomy = wc_attribute_taxonomy_name(wp_unslash($key));
        // NEW Attributes: Register and save them
        
        if (taxonomy_exists($taxonomy))
        {
            $attribute_id = wc_attribute_taxonomy_id_by_name($attr_slug);   
        }else{
            $attribute_id = add_custom_attribute($attr_name);
        }
        
        $product_attributes[$taxonomy] = array (
            'name'         => $taxonomy,
            'value'        => $varvalue,            
            'is_visible'   => 1,
            'is_variation' => 1,
            'is_taxonomy'  => 0
        );
update_post_meta( $product_id, '_product_attributes', $product_attributes );
        if($attribute_id){
            // Iterating through the variations attributes
            foreach ($terms as $term_name )
            {
                $taxonomy = 'pa_'.$attr_slug; // The attribute taxonomy
        
                // If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
                if( ! taxonomy_exists( $taxonomy ) ){
                    register_taxonomy(
                        $taxonomy,
                    'product_variation',
                        array(
                            'hierarchical' => false,
                            'label' => $attr_name,
                            'query_var' => true,
                            'rewrite' => array( 'slug' => $attr_slug), // The base slug
                        ),
                    );
                }
        
                // Check if the Term name exist and if not we create it.
                if( ! term_exists( $term_name, $taxonomy ) ){
                    wp_insert_term( $term_name, $taxonomy ); // Create the term
                }
                //$term_slug = get_term_by('name', $term_name, $taxonomy )->slug; // Get the term slug

                // Get the post Terms names from the parent variable product.
                $post_term_names =  wp_get_post_terms( $product_id, $taxonomy, array('fields' => 'names') );

                // Check if the post term exist and if not we set it in the parent variable product.
                if( ! in_array( $term_name, $post_term_names ) )                   
                $parent_id = $product_id;
                $variation = array(
                'post_title'   => get_the_title() . ' (variation)',
                'post_content' => '',
                'post_status'  => 'publish',
                'post_parent'  => $parent_id,
                'post_type'    => 'product_variation'
                );
                $variation_id = wp_insert_post( $variation );
                wp_set_post_terms($variation_id, $term_name, $taxonomy, true );
                update_post_meta( $variation_id, '_manage_stock', "yes" ,true);
                update_post_meta( $variation_id, '_regular_price', $price);
                if(!empty($price > $discount_prices )):
                update_post_meta( $variation_id, '_sale_price', $discount_price);
                endif;
                update_post_meta( $variation_id, '_stock', 100,true );
                update_post_meta( $variation_id, '_stock_status', 'instock',true);
                update_post_meta( $variation_id, 'attribute_' . $taxonomy, $varvalue);
                WC_Product_Variable::sync( $parent_id );

                // Set the attribute data in the product variation
                //update_post_meta($variation_id, 'attribute_'.$taxonomy, $term_slug );
            }
        }
    }
//     $attributes_array[$attr_slug] = array(
//     'name' => 'test',
//     'value' => $varvalue,
//     'is_visible' => '1',     
//     'is_variation' => '1',
//     'is_taxonomy' => '0' // for some reason, this is really important       
// );



}
}
}
// The function to be run
//create_product_variation($product_id, $product_attributes)
update_post_meta($product_id, '_product_image_gallery', $attach_ids ,true);    
update_post_meta($product_id, 'qr_code', $qr_code, true);
update_post_meta( $product_id, 'video', $video, true);    
$parent_id = $product_id; // Or get the variable product id dynamically


 }
 $i++;
   }
  }

 }


}

function add_custom_attribute($nam){

    $attrs = array();      
    $attributes = wc_get_attribute_taxonomies(); 

    $slug = sanitize_title($nam);

    foreach ($attributes as $key => $value) {
        array_push($attrs,$attributes[$key]->attribute_name);                    
    } 

    if (!in_array( $nam, $attrs ) ) {          
        $args = array(
            'slug'    => $slug,
            'name'   => __( $nam, 'woocommerce' ),
            'type'    => 'select',
            'orderby' => 'menu_order',
            'has_archives'  => false,
        );                    
        return wc_create_attribute($args);
    }               
}
