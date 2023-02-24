<?php 
// product variation

 /**
 * Save a new product attribute from his name (slug).
 *
 * @since 3.0.0
 * @param string $name  | The product attribute name (slug).
 * @param string $label | The product attribute label (name).
 */
function save_product_attribute_from_name( $name, $label='', $set=true ){
    if( ! function_exists ('get_attribute_id_from_name') ) return;

    global $wpdb;

    $label = $label == '' ? ucfirst($name) : $label;
    $attribute_id = get_attribute_id_from_name( $name );

    if( empty($attribute_id) ){
        $attribute_id = NULL;
    } else {
        $set = false;
    }
    $args = array(
        'attribute_id'      => $attribute_id,
        'attribute_name'    => $name,
        'attribute_label'   => $label,
        'attribute_type'    => 'select',
        'attribute_orderby' => 'menu_order',
        'attribute_public'  => 0,
    );


    if( empty($attribute_id) ) {
        $wpdb->insert(  "{$wpdb->prefix}woocommerce_attribute_taxonomies", $args );
        set_transient( 'wc_attribute_taxonomies', false );
    }

    if( $set ){
        $attributes = wc_get_attribute_taxonomies();
        $args['attribute_id'] = get_attribute_id_from_name( $name );
        $attributes[] = (object) $args;
        //print_r($attributes);
        set_transient( 'wc_attribute_taxonomies', $attributes );
    } else {
        return;
    }
}

/**
 * Get the product attribute ID from the name.
 *
 * @since 3.0.0
 * @param string $name | The name (slug).
 */
function get_attribute_id_from_name( $name ){
    global $wpdb;
    $attribute_id = $wpdb->get_col("SELECT attribute_id
    FROM {$wpdb->prefix}woocommerce_attribute_taxonomies
    WHERE attribute_name LIKE '$name'");
    return reset($attribute_id);
}

/**
 * Create a new variable product (with new attributes if they are).
 * (Needed functions:
 *
 * @since 3.0.0
 * @param array $data | The data to insert in the product.
 */

function create_product_variation( $data ){
    if( ! function_exists ('save_product_attribute_from_name') ) return;

    $postname = sanitize_title( $data['title'] );
    $author = empty( $data['author'] ) ? '1' : $data['author'];

    $post_data = array(
        'post_author'   => $author,
        'post_name'     => $postname,
        'post_title'    => $data['title'],
        'post_content'  => $data['content'],
        'post_excerpt'  => $data['excerpt'],
        'post_status'   => 'publish',
        'ping_status'   => 'closed',
        'post_type'     => 'product',
        'guid'          => home_url( '/product/'.$postname.'/' ),
    );

    // Creating the product (post data)
    $product_id = wp_insert_post( $post_data );

    // Get an instance of the WC_Product_Variable object and save it
    $product = new WC_Product_Variable( $product_id );
    $product->save();

    ## ---------------------- Other optional data  ---------------------- ##
    ##     (see WC_Product and WC_Product_Variable setters methods)

    // THE PRICES (No prices yet as we need to create product variations)

    // IMAGES GALLERY
    if( ! empty( $data['gallery_ids'] ) && count( $data['gallery_ids'] ) > 0 )
        $product->set_gallery_image_ids( $data['gallery_ids'] );

    // SKU
    if( ! empty( $data['sku'] ) )
        $product->set_sku( $data['sku'] );

    // STOCK (stock will be managed in variations)
    $product->set_stock_quantity( $data['stock'] ); // Set a minimal stock quantity
    $product->set_manage_stock(true);
    $product->set_stock_status('');

    // Tax class
    if( empty( $data['tax_class'] ) )
        $product->set_tax_class( $data['tax_class'] );

    // WEIGHT
    if( ! empty($data['weight']) )
        $product->set_weight(''); // weight (reseting)
    else
        $product->set_weight($data['weight']);

    $product->validate_props(); // Check validation

    ## ---------------------- VARIATION ATTRIBUTES ---------------------- ##

    $product_attributes = array();

    foreach( $data['attributes'] as $key => $terms ){
        $taxonomy = wc_attribute_taxonomy_name($key); // The taxonomy slug
        $attr_label = ucfirst($key); // attribute label name
        $attr_name = ( wc_sanitize_taxonomy_name($key)); // attribute slug

        // NEW Attributes: Register and save them
        if( ! taxonomy_exists( $taxonomy ) )
            save_product_attribute_from_name( $attr_name, $attr_label );

        $product_attributes[$taxonomy] = array (
            'name'         => $taxonomy,
            'value'        => '',
            'position'     => '',
            'is_visible'   => 0,
            'is_variation' => 1,
            'is_taxonomy'  => 1
        );

        foreach( $terms as $value ){
            $term_name = ucfirst($value);
            $term_slug = sanitize_title($value);

            // Check if the Term name exist and if not we create it.
            if( ! term_exists( $value, $taxonomy ) )
                wp_insert_term( $term_name, $taxonomy, array('slug' => $term_slug ) ); // Create the term

            // Set attribute values
            wp_set_post_terms( $product_id, $term_name, $taxonomy, true );
        }
    }
    update_post_meta( $product_id, '_product_attributes', $product_attributes );
    $product->save(); // Save the data
}

create_product_variation( array(
    'author'        => '', // optional
    'title'         => 'Woo special one',
    'content'       => '<p>This is the product content <br>A very nice product, soft and clear…<p>',
    'excerpt'       => 'The product short description…',
    'regular_price' => '16', // product regular price
    'sale_price'    => '', // product sale price (optional)
    'stock'         => '10', // Set a minimal stock quantity
    'image_id'      => '', // optional
    'gallery_ids'   => array(), // optional
    'sku'           => '', // optional
    'tax_class'     => '', // optional
    'weight'        => '', // optional
    // For NEW attributes/values use NAMES (not slugs)
    'attributes'    => array(
        'Attribute 1'   =>  array( 'Value 1', 'Value 2' ),
        'Attribute 2'   =>  array( 'Value 1', 'Value 2', 'Value 3' ),
    ),
) );