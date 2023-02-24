<?php 
// sku api call 

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
   'timeout'     => 300,
   'headers' => array(
   'Content-Type' => 'multipart/form-data',
   'ACCESS-KEY' =>$client_id,
   'SECRET-KEY' =>$client_secret
), 
));

//$databrand = Skudb_insert_Product_Current_Page(1);
$response_code = wp_remote_retrieve_response_code($response);
if ( is_wp_error( $response ) || $response_code != 200 ) {
//$error_message = $response_code->get_error_message();
//print_r($error_message);
echo '<script type="text/javascript">alert("Please enter Valid keys");history.back();</script>';
die();
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
 //$dataid = Skudb_insert_Product_Brands(); 
 foreach ($responceData->data as $results) { 
  $cat_id =  $results->id;
  $name   = $results->name;
  $type =   $results->image_type;   
  $file_path   = $results->image; 
  $total_outlet =  $results->total_client_product;
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

}// subcategroy data import

  }  
}

  }
header("Location: ./?page=sku-db");
}

//subcategory
function Skudb_insert_Subcategory($catid) {
  global $wpdb;
  $cattable = $wpdb->prefix.'skudb_subcategory';
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
  //return $responceDatas;
foreach($responceDatas->data  as $results){  
$subcat_id =  $results->id;
$main_category_id   = $results->main_category_id;
$category_id =   $results->category_id;   
$parent_id   = $results->parent_id; 
$subcat_name =  $results->name;
$total_product =  $results->total_client_product;
$image_url   = $results->image;
if($total_product > 0){
$datum = $wpdb->get_results("SELECT * FROM $cattable WHERE subcat_id= '".$subcat_id."'");
   if($wpdb->num_rows <= 0) {  
  $datas = $wpdb->insert( $cattable, 
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