function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}


    jQuery(document).ready(function(){
    jQuery("body").on('click',".table_btn", function() {
    var cat_id = jQuery(this).attr("data-id");
    var totalp = jQuery(this).attr("data-type");
    var catname = jQuery(this).attr("data-name");
    var subcatname = jQuery(this).attr("data-sub");
   // alert(subcatname);
     jQuery.ajax({
      type: 'POST',
      url: etqaan.ajax_url,
      dataType: "json",
      data: {
      action: "Sku_Category_Products",        
      cat_id: cat_id,
      totalp: totalp,
      catname: catname,
      subcatname: subcatname,       
      },     
      beforeSend: function(){
      jQuery('#loading-image').show();
      jQuery('#process').css('display', 'block');
      jQuery('#pleasewait').css("display", "block");  
      //jQuery('#uploadStatus').html('<img src="<?php echo SK_DB_PLUGIN_URL ?>/assets/img/loading.jpg"');
      },
         error:function(){
        //jQuery('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');
      },
      success: function (response) {
      jQuery('#loading-image').css("display", "none"); 
      jQuery('#pleasewait').css("display", "none");    
        if(response == 'ok'){
        jQuery('#uploadForm')[0].reset();
        jQuery('.row.properties_list').empty();
        jQuery('.row.properties_list').text(response.content); 
        jQuery('#loading-image').css("display", "none");  
        jQuery('#pleasewait').css("display", "none");     
        //jQuery('#uploadStatus').html('<p style="color:#28A74B;">File has uploaded successfully!</p>');
        }else if(response == 'err'){
        jQuery('#uploadStatus').html('<p style="color:#EA4335;">Please select a valid file to upload.</p>');
        jQuery('#loading-image').hide();
        }
        //console.log(response);
              
      }
    })   
       // Update price and Points

 jQuery("body").on('click',".setting_table_btns", function() {
    var scat_id = jQuery(this).attr("data-id");
    //alert(scat_id);
    // var totalp = jQuery(this).attr("data-type");
    var scatname = jQuery(this).attr("data-name");
    //alert(catname);
     jQuery.ajax({
      type: 'POST',
      url: etqaan.ajax_url,
      dataType: "json",
      data: {
      action: "Sku_Category_Products_price_update",        
      scat_id: scat_id,
      scatname: scatname,       
      },     
      beforeSend: function(){
      jQuery('#sloading-image').show();
      jQuery('#sprocess').css('display', 'block');
      jQuery('#spleasewait').css("display", "block");  
      //jQuery('#uploadStatus').html('<img src="<?php echo SK_DB_PLUGIN_URL ?>/assets/img/loading.jpg"');
      },
         error:function(){
        //jQuery('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');
      },
      success: function (response) {
      jQuery('#sloading-image').css("display", "none"); 
      jQuery('#spleasewait').css("display", "none");    
        if(response == 'ok'){
        jQuery('#suploadForm')[0].reset();
        jQuery('.row.properties_list').empty();
        jQuery('.row.properties_list').text(response.content); 
        jQuery('#sloading-image').css("display", "none");  
        jQuery('#spleasewait').css("display", "none");     
        //jQuery('#uploadStatus').html('<p style="color:#28A74B;">File has uploaded successfully!</p>');
        }else if(response == 'err'){
        jQuery('#suploadStatus').html('<p style="color:#EA4335;">Please select a valid file to upload.</p>');
        jQuery('#sloading-image').hide();
        }
        //console.log(response);
              
      }
    })   
    });
    });

    //pagination 
    // Get Query String
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

function advance_filter(paged){  
      jQuery.ajax({
      type: 'POST',
      url: etqaan.ajax_url,
      dataType: "json",
      data: {
        action: "demo_pagination_posts",       
        paged : paged      
      },
      success: function (response) {
      //alert(response);        
        console.log(response);
        jQuery('#pagination_table').empty();
        jQuery('#pagination_table').html(response.content);
        jQuery('#pr_cateory nav.pagination').remove();
        jQuery(response.pagination).insertAfter( ".pagination_table" );    
        //jQuery('.result-count').html(response.count)     
        jQuery('#pr_cateory, nav.ajax-pagination').show();
        
      },
      error: function( MLHttpRequest, textStatus, errorThrown ) {
                console.log( MLHttpRequest );
                console.log( textStatus );
                console.log( errorThrown );
                jQuery('#pagination_table').html( 'No posts found' );
                jQuery('#pagination_table').fadeIn();
            }
    })
}
      

jQuery(document).ready(function(){  
// Ajax Pagination
  jQuery('body').on('click', 'nav.ajax-pagination a', function(e) {
    //alert(advance_filter(getParameterByName('pagenum', jQuery(this).attr('href'))));
     e.preventDefault();     
      advance_filter(getParameterByName('pagenum', jQuery(this).attr('href')));
  });
  // getsubcategory
  jQuery('select[name="sku_dbcategory"]').on('change', function() {
    //alert(this.value);
      jQuery.ajax({
      type: 'POST',
      url: etqaan.ajax_url,
      dataType: "json",
      data: {
        action: "skudb_subcategory_dropdown",
        category_id: this.value,
      },
      success: function (response) {
        //console.log(response);
        jQuery('select[name="skudb_subcategory"]').html(response.content);
      }
    })
  });

  });
// category filter 

  jQuery(document).ready(function(){ 
   jQuery('#skusubcat-filter').submit(function (ef) {       
    ef.preventDefault();    
    var cat_id = jQuery('select[name="sku_dbcategory"]').val(); 
    var category_id = jQuery('select[name="skudb_subcategory"]').val();     
    jQuery.ajax({
      type: 'POST',
      url: etqaan.ajax_url,
      dataType: "json",
      data: {
        action: "Sku_fillter_categorires_data",        
        cat_id: cat_id,
        category_id: category_id,
      },
       beforeSend: function(){     
      jQuery('#loading-image').show();
      jQuery('#process').css('display', 'block');
      jQuery('#pleasewait').css("display", "block");  
      //jQuery('#uploadStatus').html('<img src="<?php echo SK_DB_PLUGIN_URL ?>/assets/img/loading.jpg"');
      },
      success: function (response) {
        //alert(response);
      jQuery('#pagination_table').empty();
      jQuery('#pagination_table').html(response.content); 
      jQuery('#pr_cateory nav.pagination').remove();  
      jQuery('#loading-image').hide();
      jQuery('#process').css('display', 'none');
      jQuery('#pleasewait').css("display", "none");   
      }
    })
   }); 
  });  

  function progress_bar_process(percentage, timer)
  {
   jQuery('.progress-bar').css('width', percentage + '%');
   if(percentage > 100)
   {
    clearInterval(timer);
    //jQuery('.table_btn')[0].reset();
    jQuery('#process').css('display', 'none');
    jQuery('.progress-bar').css('width', '0%');
    jQuery('.table_btn').attr('disabled', false);
    jQuery('#success_message').html("<div class='alert alert-success'>Data Saved</div>");
    setTimeout(function(){
     jQuery('#success_message').html('');
    }, 5000);
   }
  }    

});
       // Update price and Points
 jQuery(document).ready(function(){
 jQuery("body").on('click',".setting_table_btns", function() {
    var scat_id = jQuery(this).attr("data-id");   
    var pfiled  =  jQuery('input[name="field-1'+scat_id+'"]').val(); 
    var prfiled  = jQuery('input[name="field-2'+scat_id+'"]').val(); 
    //alert(prfiled);
    //alert(catname);
     jQuery.ajax({
      type: 'POST',
      url: etqaan.ajax_url,
      //dataType: "json",
      data: {
      action: "Sku_Category_Products_price_update",        
      scat_id: scat_id,       
      pfiled :pfiled,
      prfiled: prfiled      
      },     
      beforeSend: function(){
      jQuery('#sloading-image').show();     
      jQuery('#spleasewait').css("display", "block");  
      //jQuery('#uploadStatus').html('<img src="<?php echo SK_DB_PLUGIN_URL ?>/assets/img/loading.jpg"');
      },
         error:function(){

        //jQuery('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');
      },
      success: function (response) {
      alert(response);
      jQuery('#sloading-image').css("display", "none"); 
      jQuery('#spleasewait').css("display", "none");    
      if(response == 'err'){
        jQuery('#suploadStatus').html('<p style="color:#EA4335;">Please select a valid file to upload.</p>');
        jQuery('#sloading-image').hide();
        }
        //console.log(response);              
      }
    })   
    });
    });