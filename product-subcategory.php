<?php 
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
   'timeout'     => 3600,
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