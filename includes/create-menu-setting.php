<?php 
// menu page and setting page
 
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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<div class="tab">
  <button class="tablinks" onclick="openCity(event, 'api_key')">Api Details</button>
  <button class="tablinks" onclick="openCity(event, 'pr_cateory')">Product Category</button>
  <button class="tablinks" onclick="openCity(event, 'pay_setting')">Payment Seetings</button>
</div>

<div id="api_key" class="tabcontent">
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
<div id="pr_cateory" class="tabcontent">
 <div style="overflow-x:auto;">
  <span id="success_message"></span>
<?php
 global $wpdb;
 $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
 $limit   = 10;
 $offset  = ( $pagenum - 1 ) * $limit; 
 $catresults = $wpdb->get_results("SELECT r.id, r.subcat_id, r.subcat_name, r.image_url, r.total_product, c.name,c.cat_id FROM wp_skudb_subcategory AS r INNER JOIN wp_skudb_table AS c ON c.cat_id = r.category_id LIMIT $offset, $limit"); 
 $categories = $wpdb->get_results("SELECT `name`,`cat_id` FROM wp_skudb_table");  
 $subcategories = $wpdb->get_results("SELECT `subcat_name`,`category_id`, `subcat_id` FROM wp_skudb_subcategory"); 
 $rowcount = $wpdb->get_var("SELECT count(*)  FROM wp_skudb_subcategory");
//print_r($catresults);
if($catresults){
?>
<form method="post" action="" id="skusubcat-filter">
  <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" name="sku_dbcategory">
  <option selected>Select Category</option>
  <?php 
  foreach($categories as  $data){?>
 <option value="<?php echo $data->cat_id;?>"><?php echo $data->name;?></option>
 <?php } ?>
    
</select>

<select class="form-select form-select-sm" aria-label=".form-select-sm example" name="skudb_subcategory">
  <option value="">
    <?php echo __( 'First select the categories', 'heaven11' ); ?>
  </option>
</select>
<input type="submit" class="btn btn-primary" value="Filter"/>
  </form>
  <table class="product_cat pagination_table" id="pagination_table">
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
    <?php  $count++;  } }  ?>    
  </table>
  <?php 
//     $total        = $rowcount;
//     $num_of_pages = ceil( $total / $limit );
//     $page_links   = paginate_links( array(
//     'base'      => add_query_arg( 'pagenum', '%#%' ),
//     'format'    => '',
//     'prev_text' => __( '&laquo;', 'aag' ),
//     'next_text' => __( '&raquo;', 'aag' ),
//     'total'     => $num_of_pages,
//     'current'   => $pagenum
// ) );
 
// if ( $page_links ) {
//     echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
// }

//custom pagination 

$big = 999999999; // need an unlikely integer
                 $totalpages = ceil( $rowcount / $limit );
                 $current = $pagenum;
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
                 $pagination = paginate_links($paginate_args);
                 echo "<nav class='pagination ajax-pagination'>";
                 echo $pagination;
                 echo "</nav>";
  } ?>
  <img  style="display: none; text-align: center;" id="loading-image" src="<?php echo SK_DB_PLUGIN_URL ?>/assets/img/LoaderIcon.gif"/>
  <div id="pleasewait" style="display:none;">Please wait...</div>
  <div id="uploadStatus"></div>
  <!-- Progress bar -->
 <div class="form-group" id="process" style="display:none;">
<!--         <div class="progress">
       <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="">
       </div>
      </div> -->
       </div>
</div>

</div>

<div id="pay_setting" class="tabcontent">
  <table class="product_cat" id="settingstable">
    <tr>
      <th>Id</th>
      <th>Category Name</th>
      <th>Update Category Product Price %</th>
      <th>Update Category Product Points %</th> 
      <th>Action</th>     
    </tr>

    <?php 

  $terms  = get_terms( ['taxonomy' => 'product_cat','parent' =>0, 'hide_empty' => false] );
    if($terms ){
    $rcount =1;
    foreach($terms  as $tname){
   $child_terms  = get_terms( ['taxonomy' => 'product_cat','parent' =>$tname->term_id, 'hide_empty' => false] ); 
    foreach($child_terms as $childterm){
   // $totalp = totalpages($catresult->total_product); ?>    
    <form class="price_update_form" id="price_update_form_<?php echo $childterm->term_id;?>">
    <tr>
    <th><?php echo $rcount;?></th>    
    <th><?php echo $childterm->name;?></th>    
    <th>    
    <div class="form-group">
    <div class="field">
    <input type="number"  id="price_update" name="field-1<?php echo $childterm->term_id;?>" placeholder="Product Price %">
    </div>
    </div>
   </th>
    <th>
    <div class="form-group">
    <div class="field">
    <input type="number"  id="points_update" name="field-2<?php echo $childterm->term_id;?>" placeholder="Point %">
    </div>
    </div>
   </th> 
    <th>
     <button type="button" class="setting_table_btns" data-id="<?php echo $childterm->term_id;?>" data-name="<?php echo $childterm->slug;?>">Update</button>
    </th>      
    </tr>
    </form>
    <?php  $rcount++; }}} ?>  
  </table>
<img  style="display: none; text-align: center;" id="sloading-image" src="<?php echo SK_DB_PLUGIN_URL ?>/assets/img/LoaderIcon.gif"/>
  <div id="spleasewait" style="display:none;">Please wait...</div>
</div>

<?php

}

function totalpages($tproduct){
$toproduct = $tproduct/10;
if($toproduct <= 1){
$toproduct = 1;
}elseif(is_float($toproduct)) {
 $toproduct = intval($toproduct +1);
}else{
 $toproduct = intval($tproduct/10);
}
return $toproduct;
}
