<?php 
// get Product Brand name 

//Brands Tag 
function Skudb_insert_Product_Current_Page() {
global $wpdb;
$skudb_brands = $wpdb->prefix.'skudb_brands';
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
// echo '<pre>';
// print_r($brandsdata->data->last_page);
// echo '</pre>';

//foreach($brandsdata as $key){
$lastpage = intval($brandsdata->data->last_page);
if ($client_id && $client_secret) {
// for Brands  
//$j =1;
 for($i=1;$i<=$lastpage; $i++){
  $allbrands = wp_remote_post('http://3.137.120.6/skudb/api/auth/getClientsBrands', array(
   'method' => 'POST',
   'timeout'     => 45,
   'headers' => array(
   //'Content-Type' => 'multipart/form-data',
   'ACCESS-KEY' =>$client_id,
   'SECRET-KEY' =>$client_secret
), 
 'body' => array(
 'page' => $i, 
)  
));

$brands_codes = wp_remote_retrieve_response_code($allbrands);
if (is_wp_error( $allbrands )) {
$error_message = $brands->get_error_message();
echo "Something went wrong: $error_message";
return false;
} else {   
$brandsdatas = json_decode(wp_remote_retrieve_body( $allbrands ) );
//   echo '<pre>';
// print_r($brandsdatas);
// echo'</pre>';
foreach($brandsdatas->data->data as $bresults){
$brand_id   = $bresults->id;
$name =   $bresults->name;   
$brand_type   = $bresults->brand_type; 
$type =  $bresults->type;
$file_path =  $bresults->file_path;
$total_outlet =  $bresults->total_outlet;
$outlets   = $bresults->outlets;

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
// if($datum){
//   echo "test done";
// }else{
//  echo  $wpdb->last_error;
// }
}
}
//$i++; 
}
}
//
//}
 //return $lastpage;


}
  
}
}



  //Brands Tag 
// function Skudb_insert_Product_Brands() {
// $totalpage = Skudb_insert_Product_Current_Page();
// global $wpdb;
// $skudb_brands = $wpdb->prefix.'skudb_brands';
// // Get the saved application info
// $client_id = get_option("client_id");
// $client_secret = get_option("client_secret");   
// if ($client_id && $client_secret) {
// // for Brands  
// $j =1;
//  for($i=1;$i<=$totalpage; $i++){
//   $brands = wp_remote_post('http://3.137.120.6/skudb/api/auth/getClientsBrands', array(
//    'method' => 'POST',
//    'timeout'     => 45,
//    'headers' => array(
//    //'Content-Type' => 'multipart/form-data',
//    'ACCESS-KEY' =>$client_id,
//    'SECRET-KEY' =>$client_secret
// ), 
//  'body' => array(
//  'page' => $j, 
// )  
// ));
// $brands_code = wp_remote_retrieve_response_code($brands);
// if (is_wp_error( $brands )) {
// $error_message = $brands->get_error_message();
// echo "Something went wrong: $error_message";
// return false;
// } else {   
// $brandsdata = json_decode(wp_remote_retrieve_body( $brands ) );
// foreach($brandsdata->data->data as $results){
// $brand_id   = $results->id;
// $name =   $results->name;   
// $brand_type   = $results->brand_type; 
// $type =  $results->type;
// $file_path =  $results->file_path;
// $total_outlet =  $results->total_outlet;
// $outlets   = $results->outlets;

// $datum = $wpdb->get_results("SELECT * FROM $skudb_brands WHERE brand_id = '".$brand_id."'");
//    if($wpdb->num_rows <= 0) {  
//    $data = $wpdb->insert( $skudb_brands, 
//     array(
//     'brand_id' => $brand_id, 
//     'name' => $name,    
//     'brand_type' => $brand_type,
//     'type' => $type, 
//     'file_path' => $file_path, 
//     'total_outlet' => $total_outlet,
//     'outlets' => $outlets ),
//     array( '%d', '%s', '%s', '%s', '%s', '%s', '%s') 
//     ); 
//   }  
// }
// }
// $j++; 
// }
// }
//   }

