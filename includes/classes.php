<?php

// Badge form field
class Field {
    public $id;
    public $type;
    public $label;
    public $name;
    public $placeholder_text;
    public $description;
    public $options;
    public $options_value;

    public function __construct($id, $type, $label, $name, $placeholder_text, $description, $options = null){
        $this->id = $id;
        $this->type = $type;
        $this->label = $label;
        $this->name = $name;
        $this->placeholder_text = $placeholder_text;
        $this->description = $description;

        if ($type == 'select' || $type == 'radio')
        {
            if (gettype($options) == 'array')
            {
                $this->options = $options;

                $options_value = array();
                foreach ($options as $opt)
                {
                    $opt_val = str_replace(" ", "_", $opt);
                    $opt_val = strtolower($opt_val);
                    array_push($options_value, $opt_val);
                }
                $this->options_value = $options_value;
            }
        }
    }
}

// Check if WP table is available
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

// List of enabled fields
class table extends WP_List_table{
    
    public $fields;

    function get_columns()
    {
        $columns = array(
            'label' => 'Label',
            'type' => 'Field Type',
            'description' => 'Description',
            'options' => 'Options'
        );
        return $columns;
    }

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $this->fields;
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) { 
            case 'label':
                return $item[ $column_name ] ;
            case 'type':
                return $item[ $column_name ] ;
            case 'description':
                return $item[ $column_name ] ;
            case 'options':
                return implode(';', $item[ $column_name ]);
            default:
                return $item[ $column_name ] ; //Show the whole array for troubleshooting purposes
        }
    }

    function column_options($item)
    {
        global $admin_edit_form;
        global $admin_edit_form_id;
        global $admin_edit_form_field_name;


        if ($admin_edit_form && $admin_edit_form_field_name == 'options' && $item['id'] == $admin_edit_form_id )
        {   
            $badge_fields_objs = get_option('badge_form_fields', 'none');
            foreach($badge_fields_objs as $badge_obj)
            {
                if($item['id'] == $badge_obj->id)
                {
                    $pre_fill = implode(';', $badge_obj->options);
                    
                }
            }
            
            return sprintf( '<form action="'.esc_url( admin_url() ).'admin.php?page=ozwear-namebadge-admin-menu" method="POST"><input type="text" name="content" value="'.$pre_fill.'"><input type="hidden" name="id" value="'.$item['id'].'"><input type="hidden" name="field" value="options"><input type="hidden" name="page" value="ozwear-namebadge-admin-menu"><input type="hidden" name="action" value="update"><button type="submit">Save</button></form>' );
        }
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&id=%s&field=%s">Edit Options</a>',$_REQUEST['page'],'edit',$item['id'], 'options'),
        );
        if ($item['type'] != 'text')
        {
            return sprintf('%1$s %2$s', implode(';', $item['options']), $this->row_actions($actions) );
        } else {
            return sprintf(' ');
        }
    }

    function column_description($item)
    {
        global $admin_edit_form;
        global $admin_edit_form_id;
        global $admin_edit_form_field_name;

        if ($admin_edit_form && $admin_edit_form_field_name == 'description' && $item['id'] == $admin_edit_form_id )
        {   
            $badge_fields_objs = get_option('badge_form_fields', 'none');
            foreach($badge_fields_objs as $badge_obj)
            {
                if($item['id'] == $badge_obj->id)
                {
                    $pre_fill = $badge_obj->description;
                    
                }
            }
            
            return sprintf( '<form action="'.esc_url( admin_url() ).'admin.php?page=ozwear-namebadge-admin-menu" method="POST"><input type="text" name="content" value="'.$pre_fill.'"><input type="hidden" name="id" value="'.$item['id'].'"><input type="hidden" name="field" value="description"><input type="hidden" name="page" value="ozwear-namebadge-admin-menu"><input type="hidden" name="action" value="update"><button type="submit">Save</button></form>' );
        }
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&id=%s&field=%s">Edit Description</a>',$_REQUEST['page'],'edit',$item['id'], 'description'),
        );

        return sprintf('%1$s %2$s', $item['description'], $this->row_actions($actions) );
    }

    function column_label($item)
    {
        global $admin_edit_form;
        global $admin_edit_form_id;
        global $admin_edit_form_field_name;

        if ($admin_edit_form && $admin_edit_form_field_name == 'label' && $item['id'] == $admin_edit_form_id )
        {   
            $badge_fields_objs = get_option('badge_form_fields', 'none');
            foreach($badge_fields_objs as $badge_obj)
            {
                if($item['id'] == $badge_obj->id)
                {
                    $pre_fill = $badge_obj->label;
                    
                }
            }
            
            return sprintf( '<form action="'.esc_url( admin_url() ).'admin.php?page=ozwear-namebadge-admin-menu" method="POST"><input type="text" name="content" value="'.$pre_fill.'"><input type="hidden" name="id" value="'.$item['id'].'"><input type="hidden" name="field" value="label"><input type="hidden" name="page" value="ozwear-namebadge-admin-menu"><input type="hidden" name="action" value="update"><button type="submit">Save</button></form>' );
        }
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&id=%s&field=%s">Edit Label</a>',$_REQUEST['page'],'edit',$item['id'], 'label'),
            'delete' => sprintf('<a href="?page=%s&action=%s&id=%s">Delete Field</a>',$_REQUEST['page'],'delete',$item['id'],),
        );

        return sprintf('%1$s %2$s', $item['label'], $this->row_actions($actions) );
    }

    function column_type($item)
    {
        global $admin_edit_form;
        global $admin_edit_form_id;
        global $admin_edit_form_field_name;

        if ($admin_edit_form && $admin_edit_form_field_name == 'type' && $item['id'] == $admin_edit_form_id )
        {   
            $badge_fields_objs = get_option('badge_form_fields', 'none');
            foreach($badge_fields_objs as $badge_obj)
            {
                if($item['id'] == $badge_obj->id)
                {
                    $pre_fill = $badge_obj->type;
                    
                }
            }
            
            return sprintf( '<form action="'.esc_url( admin_url() ).'admin.php?page=ozwear-namebadge-admin-menu" method="POST" id="edit-field-type"><select name="content" "><option value="text">Text</option><option value="radio">Radio</option><option value="select">Select</option><input type="hidden" name="id" value="'.$item['id'].'"><input type="hidden" name="field" value="type"><input type="hidden" name="page" value="ozwear-namebadge-admin-menu"><input type="hidden" name="action" value="update"><button type="submit">Save</button></form>' );
        }
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&id=%s&field=%s">Edit Type</a>',$_REQUEST['page'],'edit',$item['id'], 'type'),
        );

        return sprintf('%1$s %2$s', $item['type'], $this->row_actions($actions) );
    }

    function display_tablenav($which)
    {
        if($which == 'none')
        {
            ?><div></div <?php
        }
    }
}

// List of enabled fields
class csv_table extends WP_List_table{
    
    public $data;

    function get_columns()
    {
        $columns = array(
            'orderid' => 'Order Num.',
            'badges_data' => 'Badges Data',
            'download' => 'Download CSV',
        );
        return $columns;
    }

    function display_tablenav($which)
    {
        if($which == 'none')
        {
            ?><div></div><?php
        }
    }

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $this->data;
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) { 
            case 'orderid':
                return $item[ $column_name ] ;
            case 'badges_data':
                return $item[ $column_name ] ;
            case 'download':
                return $item[ $column_name ] ;
            // default:
            //     return $item[ $column_name ] ; //Show the whole array for troubleshooting purposes
        }
    }

    function column_download($badges_data)
    {  

        $button = '';
        $file_dir = plugin_dir_path( __FILE__ ).'../csv/'.$badges_data['download'];
        $button .= '<a class="button" href="'.plugin_dir_url(__FILE__).'../includes/download.php?path='.$file_dir.'&fname='.$badges_data['download'].'" download rel="noopener noreferrer" target="_blank">Download csv</a>';
        
        return $button;
    }

    function column_badges_data($badges_data)
    {
        $rows = '<ul>';

        foreach($badges_data['badges_data'] as $badge_data)
        {
            $rows .= '<li>';

            $rows .= implode(', ', array_map(
                function ($v, $k) {
                    if(is_array($v)){
                        return $k.'[]: '.implode('&'.$k.'[]: ', $v);
                    }else{
                        return $k.': '.$v;
                    }
                }, 
                $badge_data, 
                array_keys($badge_data)
            ));
            
            $rows .= '</li>';
        }

        $rows .= '</ul>';

        // $rows = $badges_data['badges_data'][0];
        
        return $rows;
    }
}

?>