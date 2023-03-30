<?php
/**
 * Plugin Name: Skudb 
 * Version: 1.0.0
 * Plugin URI: http://3.137.120.6/skudb
 * Description: Skudb product import plugin.
 * Author: Inventcolabs Software
 * Author URI: https://www.inventcolabssoftware.com/
 * Developer: Inventcolabs Dev  
 * Domain Path: /languages
 * Requires at least:5.2
 * Requires PHP:7.2 
 * License:GPL v2 or later
 * License URI:https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:skudb-plugin
 */

if(Skudb_payment_is_woocommerce_active()){
defined( 'ABSPATH' ) || exit;
define( 'SK_DB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'SK_DB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'SK_DB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SK_DB_PLUGIN_FILENAME', __FILE__ );

function Sk_db_activation() {
 global $wpdb , $table_prefix;

 Skudb_create_plugin_database_table(); 
//
}

function delete_Sk_db_activation() {
    global $wpdb;
    $table_name = $wpdb->prefix.'skudb_brands';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
    delete_option("devnote_plugin_db_version");
}

register_activation_hook( __FILE__ , 'Sk_db_activation');

register_deactivation_hook( __FILE__ , 'delete_Sk_db_activation');

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
// all action 
add_action( 'admin_enqueue_scripts', 'Skudb_Plugin_Scripts' );
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'Skudb_Product_plugin_links' );
add_action('admin_menu', 'Skudb_plugin_setup_menu');
add_action( "admin_post_oauth_submit", "Skudb_handle_oauth" );
//import subcategory product
add_action( 'wp_ajax_nopriv_Sku_Category_Products', 'Sku_Category_Products');
add_action( 'wp_ajax_Sku_Category_Products', 'Sku_Category_Products' );
//price and points
add_action( 'wp_ajax_nopriv_Sku_Category_Products_price_update', 'Sku_Category_Products_price_update');
add_action( 'wp_ajax_Sku_Category_Products_price_update', 'Sku_Category_Products_price_update' );
// pagination
add_action( 'wp_ajax_demo_pagination_posts', 'demo_pagination_posts' );
add_action( 'wp_ajax_nopriv_demo_pagination_posts', 'demo_pagination_posts' );
//subcategory
add_action( 'wp_ajax_nopriv_skudb_subcategory_dropdown', 'skudb_subcategory_dropdown' );
add_action( 'wp_ajax_skudb_subcategory_dropdown', 'skudb_subcategory_dropdown' ); 
// category subcategory filter 
add_action( 'wp_ajax_nopriv_Sku_fillter_categorires_data', 'Sku_fillter_categorires_data' );
add_action( 'wp_ajax_Sku_fillter_categorires_data', 'Sku_fillter_categorires_data' );

// Product Refersh 
add_action( 'wp_ajax_nopriv_sku_my_Referesh_Product_Insert', 'sku_my_Referesh_Product_Insert' );
add_action( 'wp_ajax_sku_my_Referesh_Product_Insert', 'sku_my_Referesh_Product_Insert' );
// Product complete update Point

add_action( 'woocommerce_thankyou', 'Point_Update_on_order_status_completed', 20, 2 );

add_action( 'init', 'Brand_taxonomies_custom_taxonomies', 11 );

include( plugin_dir_path( __FILE__ ) . 'includes/create-menu-setting.php');
include( plugin_dir_path( __FILE__ ) . 'includes/product-category.php');
//include( plugin_dir_path( __FILE__ ) . 'includes/product-subcategory.php');
include( plugin_dir_path( __FILE__ ) . 'includes/product-brand.php');
include( plugin_dir_path( __FILE__ ) . 'includes/product-data-import.php');
include( plugin_dir_path( __FILE__ ) . 'includes/sku-create-table.php');
include( plugin_dir_path( __FILE__ ) . 'includes/img-upload.php');
include( plugin_dir_path( __FILE__ ) . 'includes/gallary-image.php');
include( plugin_dir_path( __FILE__ ) . 'includes/brandtaxnomy.php');
//include( plugin_dir_path( __FILE__ ) . 'includes/allproduct.php');
include( plugin_dir_path( __FILE__ ) . 'includes/skudb-packet-product.php');
add_action( 'woocommerce_after_single_product_summary', 'Custom_Qr_code_extra_field_show', 1 );
add_filter('upload_mimes', 'Sku_additional_mime_types', 1, 1);
}else{

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );   
deactivate_plugins( '/Skudb/skudb.php', true );
add_action( 'admin_notices', 'Woocommerece_Mandatory_notice' );

}
/**
 * @return bool
 */
function Skudb_payment_is_woocommerce_active()
{
    $active_plugins = (array) get_option('active_plugins', array());

    if (is_multisite()) {
        $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
    }

    return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
}


function Woocommerece_Mandatory_notice() {     ?>
<div class="notice notice-warning is-dismissible">
    <p>Please First Active Woocommerece Plugin ... !</p>
</div>
<?php 
}

function Custom_Qr_code_extra_field_show(){?> 
<img src="<?php echo get_post_meta(get_the_ID(), 'qr_code',  true);?>">
<div class="point">
 <p>Reward Points:- <?php echo get_post_meta(get_the_ID(), 'points', true );?></p>   
</div>
<?php 
}

function Sku_additional_mime_types($mime_types) {
    $mime_types['svg'] = 'image/svg'; //Adding svg extension
    return $mime_types;
}




function Sku_Category_Products_price_update(){
global $wpdb;
global $post;
$scat_id = intval( $_REQUEST[ 'scat_id' ] );
$pfiled = intval( $_REQUEST[ 'pfiled' ] );
$prfiled = intval( $_REQUEST[ 'prfiled' ] );
$args =  array(
    'post_type' => 'product',
    'numberposts' => -1,
    'post_status' => 'publish',
    'tax_query' => array(
        array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $scat_id, /*category name*/
            'operator' => 'IN',
            )
        ),
    );
$query = new WP_Query( $args );
if($query -> have_posts()):
while($query -> have_posts()):
$query -> the_post();
$post_id = get_the_id();
$product = wc_get_product( $post_id );
$pr_type = $product->get_type();
$rpoints = get_post_meta($post_id, 'points', true);
$totalpoints = intval($rpoints*$prfiled)/100;
$tpall =  $totalpoints +$rpoints;
if(!empty($prfiled) && ($prfiled >0 )):
 update_post_meta($post_id, 'points',  $tpall);
  endif;
  if($pr_type =="simple"){
 $rprice = $product->get_regular_price();
// echo $product->get_sale_price();
 $price = $product->get_price();
 $pricedata =  ($price*$pfiled)/100; 
 $trprice = $price+ $pricedata; 
 $trp =  ($rprice*$pfiled)/100; 
 $trptotal = $rprice + $trp;
  // Updating active price and regular price
   if(!empty($pfiled) && ($pfiled >0 )):
    update_post_meta($post_id, '_regular_price', $trptotal);
    update_post_meta($post_id, '_price', $trprice);
  endif;
}
  // Only for variable products
if($pr_type =="variable"){
$variations = $product->get_available_variations();
foreach($variations as $variation_values ){
   $variation_id = $variation_values['variation_id']; // variation id      
   $price = get_post_meta($variation_id, '_price', true);
   if(empty($price)){
    $price= 0;
   } 
   $rprice = get_post_meta($variation_id, '_regular_price', true); 
   $pricedata =  ($price*$pfiled)/100;
   $trprice = $price+ $pricedata; 
   $trp =  ($rprice*$pfiled)/100; 
   $trptotal = $rprice + $trp; 
   // Updating active price and regular price
   if(!empty($pfiled) && ($pfiled > 0 )):
    //echo "hello";
    update_post_meta( $variation_id, '_regular_price', $trptotal);
    update_post_meta( $variation_id, '_price', $trptotal);
   endif;  
    wc_delete_product_transients( $variation_id ); // Clear/refresh the variation cache
    }
    // Clear/refresh the variable product cache
    wc_delete_product_transients( $post_id );
} 
endwhile;
endif;
echo"done";
return false;
die();
}

function demo_pagination_posts() {
  global $wpdb;
 $pagenum = intval( $_REQUEST[ 'paged' ] );
 $limit   = 10;
 $offset  = ( $pagenum - 1 ) * $limit;
    $catresults = $wpdb->get_results("SELECT r.id, r.subcat_id, r.subcat_name, r.image_url, r.total_product, c.name FROM wp_skudb_subcategory AS r INNER JOIN wp_skudb_table AS c ON c.cat_id = r.category_id LIMIT $offset, $limit");
 $rowcount = $wpdb->get_var("SELECT count(*)  FROM wp_skudb_subcategory");
$content = '';
ob_start();
 ?>
  <tr>
      <th>Id</th>
      <th>Category Name</th>
      <th>Sub Category Name</th>
      <th>Total Product</th>
      <th>Action</th>      
    </tr>
    <?php if($catresults){
    $count =$offset+1;
    foreach($catresults as $catresult){ 
    $totalp = totalpages($catresult->total_product);  
    ?>
    <tr>
    <th><?php echo $count;?></th>
    <th><?php echo $catresult->name;?></th>
     <th><?php echo $catresult->subcat_name;?></th>
    <th><?php echo $catresult->total_product;?></th>
    <th class="table_btn" data-sub="<?php echo $catresult->subcat_name;?>" data-name="<?php echo $catresult->name;?>" data-type="<?php echo $totalp;?>" data-id="<?php echo $catresult->subcat_id;?>"><a href="javascript:void(0)">Import</a></th>      
    </tr>
 <?php  $count++;  } } 
 $content .= ob_get_contents();
 ob_end_clean(); 
  $big = 999999999; // need an unlikely integer
    $totalpages = $totalpages = ceil( $rowcount / $limit );
    $current = max( 1, $paged );
    $paginate_args = array(
        'base'   => add_query_arg( 'pagenum', '%#%' ),
         'format' => '',
         'current' => $current,
         'total' => $totalpages,
         'show_all' => False,
         'end_size' => 1,
         'mid_size' => 3,
         'prev_next' => True,
         'prev_text' => __( '<< Previous', 'heaven11' ),
         'next_text' => __( 'Next >>', 'heaven11' ),
         'type' => 'plain',
         'add_args' => False,
         'add_fragment' => '',
         'before_page_number' => '',
         'after_page_number' => ''                 
    );
    $pagination = '';
    if ( $totalpages > 1 ):
        $pagination .= "<nav class='pagination ajax-pagination'>";
        $pagination .= paginate_links( $paginate_args );
        $pagination .= "</nav>";
    endif;
  $return = array(
        'status' => true,
        'data' => $_POST,
        'content' => $content,
        'pagination' => $pagination        
    );
    wp_send_json( $return );
    wp_die();  
}

function skudb_subcategory_dropdown() {
    global $wpdb; 
    $subid = $_REQUEST['category_id'];
    $output = '';
    $subcategories = $wpdb->get_results("SELECT `subcat_name`, `subcat_id` FROM wp_skudb_subcategory WHERE `category_id` = $subid"); 
     if ( !empty( $subcategories ) && $_REQUEST['category_id'] ){
        $output .= '<option value="">' . __( 'Select Subcategory', 'heaven11' ) . '</option>';
        foreach ( $subcategories as $category ) {
            $output .= '<option value="' . esc_html( $category->subcat_id ) . '">' . esc_html( $category->subcat_name ) . '</option>';
        }
    } else {
        $output .= '<option value="">' . __( 'Subcategory not available.', 'heaven11' ) . '</option>';
    }
    $return = array(
        'status' => true,
        'data' => $_POST,
        'content' => $output,
    );
    wp_send_json( $return );
    wp_die();
}

// skufillterdata 

function Sku_fillter_categorires_data() {
  global $wpdb;
    $catid = $_REQUEST['cat_id'];
    $subid = $_REQUEST['category_id'];
if ( !empty( $_REQUEST[ 'cat_id' ] ) && !empty( $_REQUEST[ 'category_id' ] ) ):
   $catresults = $wpdb->get_results("SELECT *  FROM wp_skudb_subcategory WHERE category_id = $catid AND subcat_id = $subid");
   $catresults = $wpdb->get_results("SELECT r.id, r.subcat_id, r.subcat_name, r.image_url, r.total_product, c.name FROM wp_skudb_subcategory AS r INNER JOIN wp_skudb_table AS c ON c.cat_id = r.category_id WHERE category_id = $catid AND subcat_id = $subid"); 
    endif; 
if ( !empty( $_REQUEST[ 'cat_id' ] ) && empty( $_REQUEST[ 'category_id' ] ) ):
   $catresults = $wpdb->get_results("SELECT r.id, r.subcat_id, r.subcat_name, r.image_url, r.total_product, c.name FROM wp_skudb_subcategory AS r INNER JOIN wp_skudb_table AS c ON c.cat_id = r.category_id WHERE category_id = $catid"); 
    endif; 

$content = '';
ob_start();
 ?>
  <tr>
      <th>Id</th>
      <th>Category Name</th>
      <th>Sub Category Name</th>
      <th>Total Product</th>
      <th>Action</th>      
    </tr>
    <?php if($catresults){
    $count = 1;
    foreach($catresults as $catresult){ 
    $totalp = totalpages($catresult->total_product);?>
    <tr>
    <th><?php echo $count;?></th>
    <th><?php echo $catresult->name;?></th>
    <th><?php echo $catresult->subcat_name;?></th>
    <th><?php echo $catresult->total_product;?></th>
    <th class="table_btn" data-sub="<?php echo $catresult->subcat_name;?>" data-name="<?php echo $catresult->name;?>" data-type="<?php echo $totalp;?>" data-id="<?php echo $catresult->subcat_id;?>"><a href="javascript:void(0)">Import</a></th>      
    </tr>
 <?php  $count++;  } } 
 $content .= ob_get_contents();
 ob_end_clean(); 
  $return = array(
        'status' => true,
        'data' => $_POST,
        'content' => $content            
    );
    wp_send_json( $return );
    wp_die();  
}

// referesh product insert

function sku_my_Referesh_Product_Insert(){
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

function Point_Update_on_order_status_completed($order_id){
$order = wc_get_order( $order_id );
$user_id = $order->get_user_id();
$userPoint = get_user_meta($user_id, 'latest_Reward_Point', true);
$total = $order->get_total();

$percentage = 10;
$totalamount = ($percentage / 100) * $total;

$totalPoints = ($userPoint+ $totalamount) ? :$totalamount;

update_user_meta($user_id, 'latest_Reward_Point', intval($totalPoints) );


//$items = $order->get_items();
// foreach ( $items as $item ) {
//     $product_name = $item->get_name();
//     $product_id = $item->get_product_id();
//     $product_variation_id = $item->get_variation_id();
// }

}