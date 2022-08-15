<?php

// Returns markup for name badge fields defined in bet_option('badge_form_fields')
function build_form_fields()
{

    $list_of_fields = get_option('badge_form_fields');
    $fields_build = '';

    
    
    foreach ($list_of_fields as $index => $field) {

        $label = '';
        $field_mk = '';

        if (in_array($field->type, array('text'))) {
            $parent_label = '<label for="' .  $field->name . '">' . $field->label . '</label>';
            $field_mk = '<input id="bf_' . $index . '_inst-0" class="badge-field-input" type="' . $field->type . '" name="' . $field->name . '" field="' . $field->name . '">';
        
        } else if ($field->type == 'select') {
            $parent_label = '<label for="' .  $field->name . '">' . $field->label . '</label>';
            $field_mk = '<select id="bf_' . $index . '_inst-0" class="badge-field-input" name="' . $field->name . '" field="' . $field->name . '">';
            $field_mk .= '<option value="" disabled selected hidden></option>';
            for ($i = 0; $i < count($field->options); $i++) {
                $field_mk .= '<option value="' . $field->options[$i] . '">' . $field->options[$i] . '</option>';
            }

            $field_mk .= '</select>';

        } else if ($field->type == 'radio') {
            $parent_label = '<label for="' .  $field->name . '">' . $field->label . '</label>';
            $field_mk = '<div class="radio-options">';
            
            for ($i = 0; $i < count($field->options); $i++) {
                $radio_opt = '<input id="bf_' . $index . '_' . $i . '_inst-0" class="badge-field-input" field="' . $field->name . '" name="' . $field->name . '" type="' . $field->type . '" value="' . $field->options[$i] . '">' . $field->options[$i] . '</option>';
                $radio_opt = '<div class="radio-option" field="' . $field->name . '">' . $label . $radio_opt . '</div>';
                $field_mk .= $radio_opt;
                $label = '';
            }

            $field_mk .= '</div>';
        }

        $field_mk_pre = '<div class="badge-form-field-wrap ' . $field->type . '" >';
        $field_mk_pre .= $parent_label . $label . $field_mk;
        $field_mk_pre .= '</div>';

        $fields_build .= $field_mk_pre;
    }

    $fields_mk = '<form id="badge_form-0" class="badge-form"><div class="badge-form-inner" >';
    $fields_mk .= $fields_build;
    $fields_mk .= '<div class="remove-form-button-wrap"><button type="button" style="visibility: hidden" class="remove-form-button" id="remove_form_button-0" name="remove-form-button">X</button></div>';
    $fields_mk .= '</div><form>';

    return $fields_mk;
}

// Wrapper func for form field markup
function build_form()
{
    $cart_url_raw = wc_get_cart_url();

    $content = '';
    $content .= '<div class="badge-form-container">';
    $content .= '<div class="badge-form-wrap" name="badge_form_field">';
    $content .= build_form_fields();
    $content .= '</div>';
    $content .= '<button class="badge-form-button button" type="button" onclick="newField()">Add More</button>';
    
    $content .= '<button class="badge-form-button button" type="button" rel="nofollow" onclick="addToCart(' . get_option('badge_form_field_product_id') . ', \'' . $cart_url_raw . '\')" class="add_to_cart_button ajax_add_to_cart">Add to cart</button>';
    $content .= '</div>';
    return $content;
}

function badge_form_func()
{
    wp_enqueue_script('badge_form_script');
    wp_enqueue_style('badge_form_style');

    if (get_option('badge_form_field_product_id') != '') {
        return build_form();
    } else {
        return '';
    }
}
