<?php

// Set custom data as custom cart data in the cart item
add_filter('woocommerce_add_cart_item_data', 'save_custom_data_in_cart_object', 30, 3);
function save_custom_data_in_cart_object($cart_item_data, $product_id, $variation_id)
{
    global $custom_data_key;

    if ($product_id == get_option('badge_form_field_product_id')) {
        foreach ($_GET as $key => $value) {
            if ($key != 'add-to-cart') {
                $cart_item_data[$custom_data_key][$key] = esc_attr($value);
            }
        }
    }

    return $cart_item_data;
}

// Display Custom data in cart and checkout pages
add_filter('woocommerce_get_item_data', 'custom_data_on_cart_and_checkout', 99, 2);
function custom_data_on_cart_and_checkout($cart_data, $cart_item = null)
{
    global $custom_data_key;


    if (isset($cart_item[$custom_data_key])) {
        $badge_fields_objs = get_option('badge_form_fields');
        $name = 'Field Value';

        foreach ($cart_item[$custom_data_key] as $key => $value) {
            foreach ($badge_fields_objs as $obj) {
                if ($key == $obj->name) {
                    $name = $obj->label;
                }
            }

            $cart_data[] = array(
                'name' => $name,
                'value' => $value
            );
        }
    }

    return $cart_data;
}

add_action('woocommerce_checkout_create_order_line_item', 'add_custom_note_order_item_meta', 20, 4);
function add_custom_note_order_item_meta($item, $cart_item_key, $values, $order)
{
    global $custom_data_key;

    if (isset($values[$custom_data_key])) {
        $badge_fields_objs = get_option('badge_form_fields', 'none');

        foreach ($values[$custom_data_key] as $key => $value) {
            $name = 'Field Name';

            foreach ($badge_fields_objs as $obj) {
                if ($key == $obj->name) {
                    $name = $obj->label;
                }
            }
            $item->update_meta_data($name,  $value);
        }
    }
}

// Build and attach CSV to woocommerce email order
add_filter('woocommerce_email_attachments', 'attach_order_notice', 10, 3);
function attach_order_notice($attachments, $email_id, $order)
{

    // Only for "New Order" email notification (for admin)
    if ($email_id == 'new_order') {

        try {
            // Build CSV and get filename
            // Retunrns false if no badge product available
            $is_filename = build_csv_from_order($order);
            if ($is_filename != false) {
                // Attach to email
                $attachments[] = plugin_dir_path(__FILE__) . '../csv/' . $is_filename;
            }
        } catch (Exception $e) {
        }
    }
    return $attachments;
}

?>