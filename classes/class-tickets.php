<?php

namespace AA_ConfPayments;

class Tickets {

    /**
     * Hold Cart Class
     * @var \AA_ConfPayments\Cart
     */
    var $cart;

    /**
     *  Load CMB2 meta boxes
     */
    public function cmb2_sample_metaboxes(){
        // Start with an underscore to hide fields from custom fields list
        $prefix = '_cpaa_';

        /**
         * Initiate the metabox
         */
        $cmb = new_cmb2_box( array(
            'id'            => 'cp_ticket_metabox',
            'title'         => __( 'Ticket Options', 'cmb2' ),
            'object_types'  => array( 'conference_ticket', ), // Post type
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true, // Show field names on the left
            // 'cmb_styles' => false, // false to disable the CMB stylesheet
            // 'closed'     => true, // Keep the metabox closed by default
        ) );



        $cmb->add_field( array(
            'name' => 'On Sale',
            'desc' => 'Check this box to display ticket on site frontend',
            'id'   => $prefix. 'cp_ticket_onsale',
            'type' => 'checkbox',
        ) );

        $cmb->add_field( array(
            'name' => 'Price ',
            'desc' => 'Price excluding VAT',
            'id' => $prefix. 'ticket_price',
            'type' => 'text_money',
            'before_field' => '£', // Replaces default '$'
        ) );

        $cmb->add_field( array(
            'name' => 'Early Bird Price ',
            'desc' => 'Price excluding VAT',
            'id' => $prefix. 'earlybird_price',
            'type' => 'text_money',
            'before_field' => '£', // Replaces default '$'
        ) );

        // Regular text field
        $cmb->add_field( array(
            'name' => 'Early Bird end date',
            'id'   => $prefix. 'earlybird_end_date',
            'type' => 'text_date',
        ) );

        $cmb->add_field( array(
            'name' => 'Product SKU ',
            'desc' => 'Unique product identifer',
            'id' => $prefix. 'product_sku',
            'type' => 'text'
        ) );

        // Add other metaboxes as needed
    }

    /**
     * Edit columns displayed in WP Admin for Conference Ticket
     * @param $columns
     * @return array
     */
    public function edit_conference_ticket_columns($columns){
        $columns = array();
        unset($columns['cb']);
        unset($columns['title']);
        unset($columns['cb']);
        $columns['cb'] = '<input type="checkbox" />';
        $columns['title'] = 'Name';
        $columns['status'] = 'On Sale';
        $columns['price'] = 'Price';
        $columns['earlybird'] = 'Early Bird Price';
        $columns['ebend'] = 'Early Bird end Date';
        $columns['date'] = 'Date';

        return $columns;
    }

    /**
     * Display custom values for custom columns
     * @param $columns
     */
    public function conference_ticket_columns_content($columns){
        $post_id = get_the_ID();

        switch ($columns){
            case 'status':
                echo (get_post_meta($post_id, CPAAPREFIX.'cp_ticket_onsale', true)) ? 'On Sale' : 'Off Sale';
                break;

            case 'price':
                if(empty(get_post_meta($post_id,  CPAAPREFIX.'ticket_price', true))) echo __('Unknown');
                else
                    echo get_post_meta($post_id,  CPAAPREFIX.'ticket_price', true);
                break;
            case 'earlybird':
                echo get_post_meta($post_id,  CPAAPREFIX.'earlybird_price', true);
                break;
            case 'ebend':
                echo get_post_meta($post_id,  CPAAPREFIX.'earlybird_end_date', true);

                break;
            default:
                break;
        }
    }
}