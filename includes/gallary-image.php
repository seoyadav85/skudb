<?php 
// create Product Gallary image from external url 
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