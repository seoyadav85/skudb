<?php 
// upload product using category id 

function Sku_Category_Products(){
$client_id = get_option("client_id");
$client_secret = get_option("client_secret");
$response_status = get_option("response_status");
$post_type = 'product';
if ($client_id && $client_secret) {
$catid = $_POST['cat_id'];
// for productdata
$productdata = wp_remote_post('http://3.137.120.6/skudb/api/auth/getClientsProducts', array(
'method' => 'POST',
'timeout'     => 45,
'headers' => array(
//'Content-Type' => 'multipart/form-data',
'ACCESS-KEY' =>$client_id,
'SECRET-KEY' =>$client_secret
), 
'body' => array(
'page' => 1,
'category_id' => 15, 
)  
));
$subcat_code = wp_remote_retrieve_response_code($productdata);
if ( is_wp_error( $productdata )) {
$error_message = $productdata->get_error_message();
echo "Something went wrong: $error_message";
return false;
} else {   
 $responceDatas = json_decode(wp_remote_retrieve_body( $productdata ) );

 // echo '<pre>';
 // print_r($responceDatas->data->product_images);
 // echo '</pre>';
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['cat_id'] )) {
foreach($responceDatas->data->data as $results){
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
    $sub_category_extra_images= $results->sub_category_extra_images;  // array
    $get_product_attributes   = $results->get_product_attributes;  // array
    //the array of arguements to be inserted with wp_insert_post   
    $front_post = array(
    'post_title'    => $title,
    'post_status'   => 'publish', 
    'post_content'  => $descprtion,  
    'post_excerpt'  => $props,     
    'post_type'     => $post_type, 
    );
    //insert the the post into database by passing $new_post to wp_insert_post   
    $product_id = wp_insert_post($front_post);
    $url = $main_image;
    $product_image = Product_image_upload_create($url,$product_id ); 
    wp_set_object_terms($product_id, $product_tags, 'product_tag', true );
    $datacat = wp_set_object_terms( $product_id, $category_name , 'product_cat', true ); 

    //$child= wp_insert_term($subcategory_name, 'product_cat' ,array( 'slug'=>'sanajykumar789', 'parent' =>$datacat[0]));    
    update_post_meta($product_id, "_sku", $sku_code);
    update_post_meta($product_id, "_price", (float)$cost_price, true);
    update_post_meta($product_id, "_regular_price", (float)$discount_price, true);
    update_post_meta($product_id, "_sale_price", (float)$cost_price, true); 
    update_post_meta( $product_id, 'points', $points, true );
    $items = array();
    foreach($product_images as $results){    
    $galimg = $results->image;
    $dataimages = Product_Gallary_image_upload_create($galimg ,$product_id);
    $media = get_attached_media($dataimages, $product_id);
    $items[] = $dataimages;
    }
    $attach_ids=implode(',',$items);
    $item_price = array();
    foreach($get_product_attributes as $results){
     $attr = $results->get_product_attribute_values;  
     $item_price[] = $results->price;   
     $itemvalue = array();
     foreach($attr  as $datas){
      $attrname = $datas->get_attr_value_lang;
      $dt = $attrname->get_attr_value;
      $gt = $dt->get_attr_lang;
      $td = $gt->get_attr;
      $itemvalue[] = $td->attributes_name;

     
      }
    }
    foreach($product_attributes as $results){
    $data = $results->attributeValues;
    $sdata = $results->attribute_name;

    foreach($data as $keyvalue){
    $varvalue = $keyvalue->attribute_value_name;
    $variation_data =  array(
    'attributes' => array(
     $sdata  => $varvalue,        
    ),
    'sku'           => '',
    'regular_price' => '22.00',
    'sale_price'    => '',
    'stock_qty'     => 100,
  );
 create_product_variation($product_id, $variation_data );

  

    }
   
    }



// The function to be run


    //create_product_variation($product_id, $product_attributes)
    update_post_meta($product_id, '_product_image_gallery', $attach_ids);    
    update_post_meta( $product_id, 'qr_code', $qr_code, true);
    update_post_meta( $product_id, 'video', $video, true);
    // update_post_meta( $product_id, '_store_product_uri', $ProductURL );
    // update_post_meta( $product_id, '_visibility', 'visible' );
    // update_post_meta( $product_id, '_stock_status', 'instock' );

    //product extra option

    // update_post_meta( $product_id, 'restaurant_id', $restaurant_id, true );

    // update_post_meta( $product_id, 'live_product_id', $live_product_id, true );

    // update_post_meta( $product_id, 'image_type', $image_type, true );

    // update_post_meta( $product_id, 'warehouse_id', $warehouse_id, true );

    // update_post_meta( $product_id, 'size_guide_image', $size_guide_image, true );

    // update_post_meta( $product_id, 'exp_month', $exp_month, true );

    // update_post_meta( $product_id, 'exp_days', $exp_days, true );

    // update_post_meta( $product_id, 'is_digital_item', $is_digital_item, true );

    // update_post_meta( $product_id, 'products_type', $products_type, true );

    // update_post_meta( $product_id, 'gender', $gender, true );

    // update_post_meta( $product_id, 'recipe_description', $recipe_description, true );

    // update_post_meta( $product_id, 'sku_code', $sku_code, true );

    // update_post_meta( $product_id, 'video', $video, true );

    // update_post_meta( $product_id, 'serve', $serve, true );

    // update_post_meta( $product_id, 'points', $points, true );

    // update_post_meta( $product_id, 'points_percent', $points_percent, true );

    // update_post_meta( $product_id, 'extra_kilopoints', $extra_kilopoints, true );

    // update_post_meta( $product_id, 'extra_kilopoints_percent', $extra_kilopoints_percent, true );

    // update_post_meta( $product_id, 'prepration_time', $prepration_time, true );

    // update_post_meta( $product_id, 'product_for', $product_for, true );

    // update_post_meta( $product_id, 'shop_type', $shop_type, true );

    // update_post_meta( $product_id, 'delivery_hours', $delivery_hours, true );

    // update_post_meta( $product_id, 'product_url', $product_url, true );

    // update_post_meta( $product_id, 'is_active', $is_active, true );

    // update_post_meta( $product_id, 'is_deleted', $is_deleted, true );

    // update_post_meta( $product_id, 'is_show', $is_show, true );

    // update_post_meta( $product_id, 'buy_one_get_one', $buy_one_get_one, true );

    // update_post_meta( $product_id, 'props', $props, true );

    // update_post_meta( $product_id, 'celebrity_id', $celebrity_id, true );


    // update_post_meta( $product_id, 'customization', $customization, true );

    // update_post_meta( $product_id, 'customize_option', $customize_option, true );

    // update_post_meta( $product_id, 'is_trending', $is_trending, true );

    // update_post_meta( $product_id, 'is_ready', $is_ready, true );

    // update_post_meta( $product_id, 'is_gift_sync', $is_gift_sync, true );

    // update_post_meta( $product_id, 'is_favorite', $is_favorite, true );

    // update_post_meta( $product_id, 'is_topping', $is_topping, true );

    // update_post_meta( $product_id, 'avg_rating', $avg_rating, true );

    // update_post_meta( $product_id, 'can_rate', $can_rate, true );

    // update_post_meta( $product_id, 'total_rate', $total_rate, true );

    // update_post_meta( $product_id, 'is_show', $is_show, true );

    // update_post_meta( $product_id, 'product_points', $product_points, true );

    // update_post_meta( $product_id, 'qr_code', $qr_code, true );

 }
   }
 }
}
  }



function create_product_variation( $product_id, $variation_data ){
    // Get the Variable product object (parent)
    $product = wc_get_product($product_id);

    $variation_post = array(
        'post_title'  => $product->get_name(),
        'post_name'   => 'product-'.$product_id.'-variation',
        'post_status' => 'publish',
        'post_parent' => $product_id,
        'post_type'   => 'product_variation',
        'guid'        => $product->get_permalink()
    );

    // Creating the product variation
    $variation_id = wp_insert_post( $variation_post );

    // Get an instance of the WC_Product_Variation object
    $variation = new WC_Product_Variation( $variation_id );

    // Iterating through the variations attributes
    foreach ($variation_data['attributes'] as $attribute => $term_name )
    {
        $taxonomy = 'pa_'.$attribute; // The attribute taxonomy

        // If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
        if( ! taxonomy_exists( $taxonomy ) ){
            register_taxonomy(
                $taxonomy,
               'product_variation',
                array(
                    'hierarchical' => false,
                    'label' => ucfirst( $attribute ),
                    'query_var' => true,
                    'rewrite' => array( 'slug' => sanitize_title($attribute) ), // The base slug
                ),
            );
        }

        // Check if the Term name exist and if not we create it.
        if( ! term_exists( $term_name, $taxonomy ) )
            wp_insert_term( $term_name, $taxonomy ); // Create the term

        $term_slug = get_term_by('name', $term_name, $taxonomy )->slug; // Get the term slug

        // Get the post Terms names from the parent variable product.
        $post_term_names =  wp_get_post_terms( $product_id, $taxonomy, array('fields' => 'names') );

        // Check if the post term exist and if not we set it in the parent variable product.
        if( ! in_array( $term_name, $post_term_names ) )
            wp_set_post_terms( $product_id, $term_name, $taxonomy, true );

        // Set/save the attribute data in the product variation
        update_post_meta( $variation_id, 'attribute_'.$taxonomy, $term_slug );
    }

    ## Set/save all other data

    // SKU
    if( ! empty( $variation_data['sku'] ) )
        $variation->set_sku( $variation_data['sku'] );

    // Prices
    if( empty( $variation_data['sale_price'] ) ){
        $variation->set_price( $variation_data['regular_price'] );
    } else {
        $variation->set_price( $variation_data['sale_price'] );
        $variation->set_sale_price( $variation_data['sale_price'] );
    }
    $variation->set_regular_price( $variation_data['regular_price'] );

    // Stock
    if( ! empty($variation_data['stock_qty']) ){
        $variation->set_stock_quantity( $variation_data['stock_qty'] );
        $variation->set_manage_stock(true);
        $variation->set_stock_status('');
    } else {
        $variation->set_manage_stock(false);
    }
    
    $variation->set_weight(''); // weight (reseting)

    $variation->save(); // Save the data
}

