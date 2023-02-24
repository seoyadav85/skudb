<?php
/**
 * Plugin Name: Skudb 
 * Version: 1.3.8
 * Plugin URI: http://3.137.120.6/skudb
 * Description: Payment gateway plugin by Cashfree Payments for Woocommerce sites.
 * Author: devcashfree
 * Author URI: https://www.inventcolabssoftware.com/
 * Developer: Inventcolabs Dev  
 * Domain Path: /languages
 * Requires at least: 4.4
 * Tested up to: 6.0
 * WC requires at least: 3.0
 * WC tested up to: 6.3.1
 *
 *
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) || exit;

define( 'SK_DB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'SK_DB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'SK_DB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SK_DB_PLUGIN_FILENAME', __FILE__ );


// add_action( 'init', function(){
//     $assets_url = plugin_dir_url( __FILE__ );
//     //Setup menu
//     if( is_admin() ){
//         new Apex_Menu( $assets_url );
//     }
//     //Setup REST API
// });


function Sk_db_activation() {
 global $wpdb , $table_prefix;

 Skudb_create_plugin_database_table();
//
}

register_activation_hook( __FILE__ , 'Sk_db_activation');

register_deactivation_hook( __FILE__ , 'Sk_db_activation');

/**
     * Show action links on the plugin screen.
     *
     * @param mixed $links Plugin Action links.
     *
     * @return array
     */
    function Skudb_Product_plugin_links( $links ) {

    $plugin_links = array(
        '<a href="' . admin_url( 'admin.php?page=sku-db' ) . '">' . __( 'Setting', 'sku-product' ) . '</a>'
    );
    
    return array_merge( $plugin_links, $links );
}

function Skudb_Plugin_Scripts() {
 $plugin_url = plugin_dir_url( __FILE__ );
 wp_enqueue_style( 'style',  $plugin_url . "assets/css/sk-style.css");
 wp_enqueue_script( 'skujquery',  $plugin_url . "assets/js/sku-custom.js");
 //wp_enqueue_script('custom_js', 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js');

wp_localize_script( 'skujquery', 'etqaan', array(
        'ajax_url' => admin_url( 'admin-ajax.php' )
    ) );

 }

add_action( 'admin_enqueue_scripts', 'Skudb_Plugin_Scripts' );




add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'Skudb_Product_plugin_links' );

add_action('admin_menu', 'Skudb_plugin_setup_menu');

 
function Skudb_plugin_setup_menu(){
    add_menu_page(
    'tools.php', 
    'Skudb',     
    'manage_options',
    'sku-db',
    'Skudb_Menu_init_Callback' 
 );
}

function Skudb_Menu_init_Callback(){ ?>
<div class="tab">
  <button class="tablinks" onclick="openCity(event, 'London')">Api Details</button>
  <button class="tablinks" onclick="openCity(event, 'Paris')">Product Category</button>
  <button class="tablinks" onclick="openCity(event, 'Tokyo')">Others</button>
</div>

<div id="London" class="tabcontent">
  <form method="post" action="<?php echo admin_url( 'admin-post.php'); ?>">

   <input type="hidden" name="action" value="oauth_submit" />

   <h3>Skudb  Api Details</h3>

   <p>
      <label><?php _e("ACCESS-KEY:", "skudb-api"); ?></label>
      <input class="" type="text" name="client_id" value="<?php echo get_option('client_id')?>" />
   </p>
   <p>
      <label><?php _e("SECRET-KEY:", "skudb-api"); ?></label>
      <input class="" type="password" name="client_secret" value="<?php echo get_option('client_secret')?>" />
   </p>

   <input class="button button-primary" type="submit" value="<?php _e("Authorize", "skudb-api"); ?>" />

</form>
</div>
<div id="Paris" class="tabcontent">
 <div style="overflow-x:auto;">
<?php global $wpdb;    
$catresults = $wpdb->get_results( "SELECT * FROM wp_skudb_table");?>
  <table class="product_cat">
    <tr>
      <th>Id</th>
      <th>Category Name</th>
      <th>Total Product</th>
      <th>Action</th>      
    </tr>
    <?php if($catresults){
    $count =1;
    foreach($catresults as $catresult){?>
    <tr>
    <th><?php echo $count;?></th>
    <th><?php echo $catresult->name;?></th>
    <th><?php echo $catresult->total_outlet;?></th>
    <th class="table_btn" data-id="<?php echo $catresult->cat_id;?>"><a href="javascript:void(0)">Import</a></th>      
    </tr>
    <?php  $count++; } } ?>
    
  </table>
</div>

</div>

<div id="Tokyo" class="tabcontent">
  <h3>Tokyo</h3>
  <p>Tokyo is the capital of Japan.</p>
</div>

<?php

}

add_action( "admin_post_oauth_submit", "Skudb_handle_oauth" );

function Skudb_handle_oauth() {
   // If the form was just submitted, save the values
   // (Step 1 above)
   if ( isset($_POST["client_id"]) && isset($_POST["client_secret"]) ) {
   update_option( "client_id", $_POST["client_id"], TRUE );
   update_option("client_secret", $_POST["client_secret"], TRUE);
   }

   // Get the saved application info
   $client_id = get_option("client_id");
   $client_secret = get_option("client_secret");
   $response_status = get_option("response_status");
   if ($client_id && $client_secret) {
   $response = wp_remote_post('http://3.137.120.6/skudb/api/auth/getClientsCategory', array(
   'method' => 'POST',
   'timeout'     => 45,
   'headers' => array(
   'Content-Type' => 'multipart/form-data',
   'ACCESS-KEY' =>$client_id,
   'SECRET-KEY' =>$client_secret
), 
));

//$databrand = Skudb_insert_Product_Current_Page(1);
$response_code = wp_remote_retrieve_response_code($response);
if ( is_wp_error( $response ) || $response_code != 200 ) {
$error_message = $response->get_error_message();
echo "Something went wrong: $error_message";
update_option("response_status", 0, TRUE);
} else {   
 $responceData = json_decode(wp_remote_retrieve_body( $response ) ); 
 // echo '<pre>';
 // print_r($brandsvalue);
 // echo '</pre>';
  global $wpdb;
 $tablename = $wpdb->prefix.'skudb_table';
 $cattable = $wpdb->prefix.'skudb_subcategory';
  Skudb_insert_Product_Current_Page();
  $dataid = Skudb_insert_Product_Brands(); 
 foreach ($responceData->data as $results) {  
  $cat_id =  $results->id;
  $name   = $results->name;
  $type =   $results->image_type;   
  $file_path   = $results->image; 
  $total_outlet =  $results->total_product;
  $outlets   = $results->total_dish;
  $datresponse = Skudb_insert_Subcategory($cat_id);  
  $datum = $wpdb->get_results("SELECT * FROM $tablename WHERE cat_id= '".$cat_id."'");
   if($wpdb->num_rows <= 0) {  
   $data = $wpdb->insert( $tablename, 
    array(
    'cat_id' => $cat_id, 
    'name' => $name,    
    'type' => $type,
    'file_path' => $file_path, 
    'total_outlet' => $total_outlet, 
    'outlets' => $outlets ),
    array( '%d', '%s', '%s', '%s', '%s', '%s' ) 
     );  
   // if($data){
   // echo 'Record insert Sucessfuly.';
   //  }else{
   //      echo 'not inserted';
   //      echo $wpdb->last_error;
   //       echo "Something wrong..!";
   //     $wpdb->show_errors();

   //  }
}// subcategroy data import
foreach($datresponse->data  as $results){
$subcat_id =  $results->id;
$main_category_id   = $results->main_category_id;
$category_id =   $results->category_id;   
$parent_id   = $results->parent_id; 
$subcat_name =  $results->name;
$total_product =  $results->total_product;
$image_url   = $results->image;
  $datum = $wpdb->get_results("SELECT * FROM $cattable WHERE subcat_id= '".$subcat_id."'");
   if($wpdb->num_rows <= 0) {  
   $data = $wpdb->insert( $cattable, 
    array(
    'subcat_id' => $subcat_id, 
    'main_category_id' => $main_category_id,    
    'category_id' => $category_id,
    'parent_id' => $parent_id, 
    'subcat_name' => $subcat_name, 
    'total_product' => $total_product,
    'image_url' => $image_url ),
    array( '%d', '%d', '%d', '%d', '%s', '%s', '%s' ) 
     );  
   // if($data){
   // echo 'Record insert Sucessfuly.';
   //  }else{
   //      echo 'not inserted';
   //      echo $wpdb->last_error;
   //       echo "Something wrong..!";
   //     $wpdb->show_errors();

   //  }
}    

}



   }  
}

  }

}
//subcategory
function Skudb_insert_Subcategory($catid) {
   // Get the saved application info
   $client_id = get_option("client_id");
   $client_secret = get_option("client_secret");
   $response_status = get_option("response_status");
   if ($client_id && $client_secret) {
// for subcategory
  $subcategory = wp_remote_post('http://3.137.120.6/skudb/api/auth/getClientsSubCategory', array(
   'method' => 'POST',
   'timeout'     => 45,
   'headers' => array(
   //'Content-Type' => 'multipart/form-data',
   'ACCESS-KEY' =>$client_id,
   'SECRET-KEY' =>$client_secret
), 
 'body' => array(
 'category_id' => $catid, 
)  
));
$subcat_code = wp_remote_retrieve_response_code($subcategory);

if ( is_wp_error( $subcategory )) {
$error_message = $subcategory->get_error_message();
echo "Something went wrong: $error_message";
return false;
} else {   
 $responceDatas = json_decode(wp_remote_retrieve_body( $subcategory ) );
 return $responceDatas;
  
}

  }

}

// function gdgf()
// {
 
//   $df = Skudb_insert_Product_Brands(1);
//     $df->last_page
//     for($i=1;$i<=$df->last_page; $i++)
//     {

//            $df = Skudb_insert_Product_Brands($i+1);
//     }
// }

//Brands Tag 
function Skudb_insert_Product_Current_Page() {
   // Get the saved application info
   $client_id = get_option("client_id");
   $client_secret = get_option("client_secret");    
   if ($client_id && $client_secret) {
// for Brands  
  $brands = wp_remote_post('http://3.137.120.6/skudb/api/auth/getClientsBrands', array(
   'method' => 'POST',
   'timeout'     => 45,
   'headers' => array(
   //'Content-Type' => 'multipart/form-data',
   'ACCESS-KEY' =>$client_id,
   'SECRET-KEY' =>$client_secret
), 
 'body' => array(
 'page' => 1, 
)  
));
$brands_code = wp_remote_retrieve_response_code($brands);
if (is_wp_error( $brands )) {
$error_message = $brands->get_error_message();
echo "Something went wrong: $error_message";
return false;
} else {   
$brandsdata = json_decode(wp_remote_retrieve_body( $brands ) );
foreach($brandsdata as $key){
$lastpage = intval($key->last_page);
}
 return $lastpage;
}
  
}
}



  //Brands Tag 
function Skudb_insert_Product_Brands() {
$totalpage = Skudb_insert_Product_Current_Page();
global $wpdb;
$skudb_brands = $wpdb->prefix.'skudb_brands';
// Get the saved application info
$client_id = get_option("client_id");
$client_secret = get_option("client_secret");   
if ($client_id && $client_secret) {
// for Brands  
$j =1;
 for($i=1;$i<=$totalpage; $i++){
  $brands = wp_remote_post('http://3.137.120.6/skudb/api/auth/getClientsBrands', array(
   'method' => 'POST',
   'timeout'     => 45,
   'headers' => array(
   //'Content-Type' => 'multipart/form-data',
   'ACCESS-KEY' =>$client_id,
   'SECRET-KEY' =>$client_secret
), 
 'body' => array(
 'page' => $j, 
)  
));
$brands_code = wp_remote_retrieve_response_code($brands);
if (is_wp_error( $brands )) {
$error_message = $brands->get_error_message();
echo "Something went wrong: $error_message";
return false;
} else {   
$brandsdata = json_decode(wp_remote_retrieve_body( $brands ) );
foreach($brandsdata->data->data as $results){
$brand_id   = $results->id;
$name =   $results->name;   
$brand_type   = $results->brand_type; 
$type =  $results->type;
$file_path =  $results->file_path;
$total_outlet =  $results->total_outlet;
$outlets   = $results->outlets;

$datum = $wpdb->get_results("SELECT * FROM $skudb_brands WHERE brand_id = '".$brand_id."'");
   if($wpdb->num_rows <= 0) {  
   $data = $wpdb->insert( $skudb_brands, 
    array(
    'brand_id' => $brand_id, 
    'name' => $name,    
    'brand_type' => $brand_type,
    'type' => $type, 
    'file_path' => $file_path, 
    'total_outlet' => $total_outlet,
    'outlets' => $outlets ),
    array( '%d', '%s', '%s', '%s', '%s', '%s', '%s') 
    ); 
  }  
}
}
$j++; 
}
}
  }

  // Product categories wise import 
add_action( 'wp_ajax_nopriv_Sku_Category_Products', 'Sku_Category_Products');
add_action( 'wp_ajax_Sku_Category_Products', 'Sku_Category_Products' );

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
    wp_set_object_terms( $product_id, $category_name , 'product_cat', true );    
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
    update_post_meta($product_id, '_product_image_gallery', $attach_ids);    
    // update_post_meta( $product_id, '_price', $productPrice_converted );
    // update_post_meta( $product_id, '_weight', $ProductWeight );
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



// create table when plugin activate

function Skudb_create_plugin_database_table()
{
//require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
      global $wpdb;
        $table = $wpdb->prefix . 'skudb_table';
        $subcattable = $wpdb->prefix . 'skudb_subcategory';
        $subbrand = $wpdb->prefix . 'skudb_brands';
        $charset = $wpdb->get_charset_collate();
        $charset_collate = $wpdb->get_charset_collate();       
        $sql = "CREATE TABLE IF NOT EXISTS $table (
        id int(11) NOT NULL auto_increment,
        cat_id int(11) NOT NULL,
        name varchar(15) NOT NULL,       
        type varchar(100) NOT NULL,
        file_path varchar(100) NOT NULL,
        total_outlet varchar(100) NOT NULL,
        outlets varchar(100) NOT NULL,        
        PRIMARY KEY  (id)
        ) $charset_collate;

        CREATE TABLE IF NOT EXISTS $subcattable (
        id int(11) NOT NULL auto_increment,
        subcat_id int(11) NOT NULL,
        main_category_id int(11) NOT NULL,
        category_id int(11) NOT NULL,
        parent_id int(11) NOT NULL,
        subcat_name varchar(50) NOT NULL,        
        image_url varchar(100) NOT NULL,
        total_product varchar(100) NOT NULL,            
        PRIMARY KEY  (id)
        ) $charset_collate;

        CREATE TABLE IF NOT EXISTS $subbrand (
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
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );    
    
}

function Product_image_upload_create($imgurl, $post_id){
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
// Add Featured Image to Post
$image_url        = $imgurl; // Define the image URL here
$image_name       = basename($image_url);
$upload_dir       = wp_upload_dir(); // Set upload folder
$image_data       = file_get_contents($image_url); // Get image data
$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
$filename         = basename( $unique_file_name ); // Create image file name
// Check folder permission and define file location
if( wp_mkdir_p( $upload_dir['path'] ) ) {
  $file = $upload_dir['path'] . '/' . $filename;
} else {
  $file = $upload_dir['basedir'] . '/' . $filename;
}
// Create the image  file on the server
file_put_contents( $file, $image_data );
//file_put_contents($file, fopen($image_url, 'r'));
// Check image file type
$wp_filetype = wp_check_filetype( $filename, null );

// Set attachment data
$attachment = array(
    'post_mime_type' => $wp_filetype['type'],
    'post_title'     => sanitize_file_name( $filename ),
    'post_content'   => '',
    'post_status'    => 'inherit'
);
// Create the attachment
$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
// Define attachment metadata
$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
// Assign metadata to attachment
wp_update_attachment_metadata( $attach_id, $attach_data );
// And finally assign featured image to post
set_post_thumbnail( $post_id, $attach_id );

}




function Product_Gallary_image_upload_create($pimgurl, $pid){
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
// Add Featured Image to Post
$image_url        = $pimgurl; // Define the image URL here
$image_name       = basename($image_url);
$upload_dir       = wp_upload_dir(); // Set upload folder
$image_data       = file_get_contents($image_url); // Get image data
$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
$filename         = basename( $unique_file_name ); // Create image file name
// Check folder permission and define file location
if( wp_mkdir_p( $upload_dir['path'] ) ) {
  $file = $upload_dir['path'] . '/' . $filename;
} else {
  $file = $upload_dir['basedir'] . '/' . $filename;
}
// Create the image  file on the server
file_put_contents( $file, $image_data );
$wp_filetype = wp_check_filetype( $filename, null );
// Set attachment data
$attachment = array(
    'post_mime_type' => $wp_filetype['type'],
    'post_title'     => sanitize_file_name( $filename ),
    'post_content'   => '',
    'post_status'    => 'inherit'
);
// Create the attachment
$attach_id = wp_insert_attachment( $attachment, $file, $pid );
// Define attachment metadata
$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
// Assign metadata to attachment
wp_update_attachment_metadata( $attach_id, $attach_data );
//update_post_meta($pid,  '_product_image_gallery', $attach_id, true);
return $attach_id;

}

//