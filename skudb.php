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