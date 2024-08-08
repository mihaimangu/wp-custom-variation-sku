<?php
/**
 * Plugin Name: Custom Variation SKU
 * Description: Adds an external SKU field to WooCommerce product variations.
 * Version: 1.0
 * Author: Mihai Mangu
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Add custom field to variations
add_action( 'woocommerce_product_after_variable_attributes', 'cvs_add_custom_field_to_variations', 10, 3 );
function cvs_add_custom_field_to_variations( $loop, $variation_data, $variation ) {
    woocommerce_wp_text_input( array(
        'id' => 'external_sku[' . $variation->ID . ']',
        'label' => __('External SKU', 'woocommerce'),
        'desc_tip' => true,
        'description' => __('Enter the external SKU for this variation.', 'woocommerce'),
        'value' => get_post_meta( $variation->ID, '_external_sku', true )
    ) );
}

// Save the custom field value
add_action( 'woocommerce_save_product_variation', 'cvs_save_custom_field_variations', 10, 2 );
function cvs_save_custom_field_variations( $variation_id, $i ) {
    $external_sku = isset($_POST['external_sku'][$variation_id]) ? $_POST['external_sku'][$variation_id] : '';
    if ( ! empty( $external_sku ) ) {
        update_post_meta( $variation_id, '_external_sku', sanitize_text_field( $external_sku ) );
    } else {
        delete_post_meta( $variation_id, '_external_sku' );
    }
}

// Display the custom field value on the variation product page (optional)
add_filter( 'woocommerce_available_variation', 'cvs_load_custom_field_to_variation_frontend' );
function cvs_load_custom_field_to_variation_frontend( $variation ) {
    $variation['_external_sku'] = get_post_meta( $variation['variation_id'], '_external_sku', true );
    return $variation;
}
