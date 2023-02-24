<?php 

function get_total_product_number(){
$client_id = get_option("client_id");
$client_secret = get_option("client_secret");
if($client_id && $client_secret) {
$productdata = wp_remote_post('http://3.137.120.6/skudb/api/auth/getClientsProducts', array(
'method' => 'POST',
'timeout'     => 45,
'headers' => array(
'ACCESS-KEY' =>$client_id,
'SECRET-KEY' =>$client_secret
), 
'body' => array(
'page' => 1, 
)  
));
$subcat_code = wp_remote_retrieve_response_code($productdata);
//die();
if ( is_wp_error( $productdata )) {
$error_message = $productdata->get_error_message();
echo "Something went wrong: $error_message";
return false;
} else {   
 $responceDatas = json_decode(wp_remote_retrieve_body( $productdata ) );
 return $responceDatas->data->last_page;
}
 }
}

function Sku_All_Products_Import(){
global $wpdb;
$client_id = get_option("client_id");
$client_secret = get_option("client_secret");
//$response_status = get_option("response_status");
if ($client_id && $client_secret) {
//$catid = $_POST['cat_id'];
$totalp = get_total_product_number();
if($totalp){
// for productdata
for($i=1; $i <= $totalp; $i++) { 
$productdata = wp_remote_post('http://3.137.120.6/skudb/api/auth/getClientsProducts', array(
'method' => 'POST',
'timeout'     => 45,
'headers' => array(
'ACCESS-KEY' =>$client_id,
'SECRET-KEY' =>$client_secret
), 
'body' => array(
'page' => $i, 
)  
));
$subcat_code = wp_remote_retrieve_response_code($productdata);
//die();
if ( is_wp_error( $productdata )) {
$error_message = $productdata->get_error_message();
echo "Something went wrong: $error_message";
return false;
} else { 
$product_json = $wpdb->prefix . 'product_json';
$datum = $wpdb->get_results("SELECT * FROM $product_json WHERE page_id= '".$i."'");    
 $responceDatas = json_decode(wp_remote_retrieve_body( $productdata ) ); 
 $prresponce = json_encode(wp_remote_retrieve_body( $productdata ) );
 //print_r($responceDatas);
 if($wpdb->num_rows <= 0) {  
  $datas = $wpdb->insert( $product_json, array('page_id' => $i, 'cat_id' => $i,'jason_data' => $prresponce, 
  array('%d','%d','%s')));  
   if($datas){
   echo 'Record insert Sucessfuly.';
    }else{
        echo 'not inserted';
        echo $wpdb->last_error;
         echo "Something wrong..!";
       $wpdb->show_errors();

    }
} 
}
 }
 }
 //die('error');
}

  }    


