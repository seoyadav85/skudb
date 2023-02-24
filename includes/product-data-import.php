<?php 
// upload product using category id 

function Sku_Category_Products(){
global $wpdb;  
$client_id = get_option("client_id");
$client_secret = get_option("client_secret");
$response_status = get_option("response_status");
$post_type = 'product';
if ($client_id && $client_secret) {
$catid = $_POST['cat_id'];
$totalp = $_POST['totalp'];
$catname = $_POST['catname'];
$subcatname = $_POST['subcatname'];
//echo $createSlug = createSlug($catname);
// for productdata
for($i=1; $i <= $totalp; $i++) { 
$productdata = wp_remote_post('http://3.137.120.6/skudb/api/auth/getClientsProducts', array(
'method' => 'POST',
'timeout'     => 300,
'headers' => array(
//'Content-Type' => 'multipart/form-data',
'ACCESS-KEY' =>$client_id,
'SECRET-KEY' =>$client_secret
), 
'body' => array(
'page' => $i,
'sub_category_id' => $catid, 
)  
));
$subcat_code = wp_remote_retrieve_response_code($productdata);
if ( is_wp_error( $productdata )) {
$error_message = $productdata->get_error_message();
echo "Something went wrong: $error_message";
return false;
} else { 
$product_json = $wpdb->prefix . 'product_json';
$subid = $wpdb->prefix . 'skudb_subcategory';
$wpdb->get_results("SELECT * FROM $product_json WHERE page_id = $i AND cat_id = $catid");
$responceDatas = wp_remote_retrieve_body( $productdata );  
 //print_r($responceDatas);
 if($wpdb->num_rows <= 0) {  
  $datas = $wpdb->insert( $product_json, 
  array('page_id' => $i, 'cat_id' => $catid,'jason_data' => $responceDatas,));
  $createSlug = createSlug($catname);
  if (!term_exists($catname, 'product_cat')) { 
 $pcat = wp_insert_term($catname, 'product_cat', array(
'slug' => $createSlug, 'parent' => 0, // must be the ID, not name
));
 $createSlugs = createSlug($subcatname);
 if(!empty($pcat['term_id']) && !empty($createSlugs)):
  if (!term_exists($subcatname, 'product_cat')) { 
 wp_insert_term($subcatname, 'product_cat', array(
'slug' => $createSlugs, 'parent' => $pcat['term_id'] // must be the ID, not name
));
}
endif;
 //$suball = $wpdb->get_results("SELECT * FROM $subid WHERE `category_id` = $catid");
// foreach($suball as $subcat){
//  $createSlugs = createSlug($subcatname);
//   if (!term_exists($subcat->subcat_name, 'product_cat')) { 
//  wp_insert_term($subcat->subcat_name, 'product_cat', array(
// 'slug' => $createSlugs, 'parent' => $pcat['term_id'] // must be the ID, not name
// ));
// }
// }
 }
  
   //if($datas){
 //$percentage = floor(round( (($i / $totalp) * 100), 1 ));//
  //<script>
  //var percentage = <?php echo $percentage;;
  //var timer = setInterval(function(){
  //percentage = percentage + <?php echo $percentage;;
  //progress_bar_process(percentage, timer);
  //}, 1000);
  //</script>
   // <?php }else{    
    //echo $wpdb->last_error;
        

   // }
} 
}
 }
 //die('error');
}

  }    


function createSlug($str) {
 $url = strtolower($str);
 $replacements = ['@'=> "at", '#' => "hash", '$' => "dollar", '%' => "percentage", '&' => "and", '.' => "dot", 
            '+' => "plus", '-' => "minus", '*' => "multiply", '/' => "devide", '=' => "equal to",
            '<' => "less than", '<=' => "less than or equal to", '>' => "greater than", '<=' => "greater than or equal to",];
 $title = strtr($url, $replacements);
 return $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $title);
}

