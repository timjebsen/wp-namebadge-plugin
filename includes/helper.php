<?php

// Return nested kv array of each product (badge) and it's meta data
// ex: [ ['Field_1' => 'data_1', 'Field_2' => 'data_2'], [...] ]
function get_list_of_field_data($order, $product_id)
{
    $field_data = array();

    if (gettype($order) == 'string') {
        $order = wc_get_order($order);
    } 

    foreach ($order->get_items() as $item_key => $item_values) {

        // Get only product_id and it's metadata ('custom_data')
        if ($item_values->get_product_id() == $product_id) {
      
            $item_data = $item_values->get_data()['meta_data'];

            $product_metadata_list = array();
            foreach ($item_data as $data_point) {

                $product_metadata_list[$data_point->get_data()['key']] = $data_point->get_data()['value'];

            }
            array_push($field_data, $product_metadata_list);
        }
    }

    return $field_data;
}


function build_csv($filename, $items_in_order)
{

    $has_header = false;
    $csv_dir = plugin_dir_path(__FILE__) . '../csv/';
    $file_path = $csv_dir . $filename;

    if (!file_exists($file_path)) {
        // loop over each badge
        foreach ($items_in_order as $order_item) {
            $fp = fopen($file_path, 'a');
            var_dump(array_keys($order_item));
            
            if (!$has_header) {
                fputcsv($fp, array_keys($order_item));
                $has_header = true;
            }
            fputcsv($fp, $order_item);
            fclose($fp);
        }
    }
}

function build_csv_from_order($order)
{

    $items_in_order = get_list_of_field_data($order, get_option('badge_form_field_product_id'));

    if (! empty($items_in_order))
    {
        $filename = 'orderid_' . $order->get_id() . '.csv';
        build_csv($filename, $items_in_order);
        return $filename;

    } else
    {
        return false;
    }
    
}


function get_csv_table_items()
{
    $table_items = array();

    // Get all files in the csv folder
    $files = scandir(plugin_dir_path(__FILE__).'../csv/');

    if($files)
    {
        foreach($files as $file)
        {
            if(substr($file, -3)=='csv')
            {
                try{
                    if (preg_match('/.*_([0-9]*)/', $file, $order_id))
                    {
                        // Read csv data
                        $csv_data = array_map('str_getcsv', file(plugin_dir_path(__FILE__).'../csv/'.$file));
                        
                        $header = array_shift($csv_data);
                        $badge_data = array();
                        foreach($csv_data as $row) {
                            $badge_data[] = array_combine($header, $row);
                        }

                        $row_item = array('orderid' => $order_id[1], 'badges_data' => $badge_data, 'download' => $file);
                        array_push($table_items, $row_item);
                    }

                } catch (Exception $e) {

                }
                
                
            }
        }
    }
    return $table_items;
}

function bd_action_delete_field($get_data)
{
    $fields = get_option('badge_form_fields', 'none');

    foreach ($fields as $field_key => $field) {
        if ($field->id == $get_data['id']) {
            unset($fields[$field_key]);
            update_option('badge_form_fields', $fields);
        }
    }
}

function bd_action_update_field($post_data)
{
    $fields = get_option('badge_form_fields', 'none');

    foreach ($fields as $field) {
        if ($field->id == $post_data['id']) {
            if ($post_data['field'] == 'options') {
                $new_opts = explode(';', $post_data['content']);
                $field->options = $new_opts;
                update_option('badge_form_fields', $fields);
            } else if ($post_data['field'] == 'description') {
                $field->description = $post_data['content'];
                update_option('badge_form_fields', $fields);
            } else if ($post_data['field'] == 'label') {
                $field->label = $post_data['content'];
                update_option('badge_form_fields', $fields);
            } else if ($post_data['field'] == 'type') {
                $field->type = $post_data['content'];
                update_option('badge_form_fields', $fields);
            } else {
                continue;
            }
        }
    }
}

function bd_add_new_field($post_data)
{
    $label = $post_data['label'];
    $type = $post_data['type'];
    $description = $post_data['description'];
    $options = explode(';', $post_data['options']);
    $id = 0;

    // Get new id
    $fields = get_option('badge_form_fields');
    if (!$fields) {
        $fields = array();
    }
    foreach ($fields as $field) {
        if ($field->id >= $id) {
            $id = $field->id + 1;
        }
    }

    // format name
    $name = str_replace(' ', '-', strtolower($label));

    $new_field = new Field($id, $type, $label, $name, $label, $description);
    if ($type == 'select' || $type == 'radio') {
        $new_field->options = $options;
    }

    // array_push( $fields, $new_field);
    $fields[] = $new_field;
    check_reserved_terms();
    update_option('badge_form_fields', $fields);
}

// Check for conflicting terms
function check_reserved_terms()
{

    $terms = file_get_contents(plugin_dir_path(__FILE__) . 'resources/reserved_terms.txt');
    $terms = explode("\n", $terms);

    $badge_fields_objs = get_option('badge_form_fields', 'none');
    foreach ($badge_fields_objs as $badge_obj) {

        // Append randon int
        if (in_array($badge_obj->name, $terms)) {
            $badge_obj->name = $badge_obj->name . '-' . rand();
        }
    }
    update_option('badge_form_fields', $badge_fields_objs);
}

function badge_form_remove_product_summary() {
    global $product;
    $id = $product->get_id();

    if($id == get_option('badge_form_field_product_id')) {
        the_title( '<h1 class="product_title entry-title">', '</h1>' );
        ?><p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>"><?php echo $product->get_price_html(); ?> per badge</p><?php
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
        remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
        remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
        echo badge_form_func();

    }
}