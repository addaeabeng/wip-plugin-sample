<?php

namespace AA_ConfPayments;

class Shortcodes {

    /**
     * Hold Plugin class
     * @var \AA_ConfPayments\Conference_Payments
     */
    public $plugin;

    /**
     * Hold Cart Class
     * @var \AA_ConfPayments\Cart
     */
    public $cart;


    /**
     * Shortcodes constructor.
     * @param $plugin object
     */
    public function __construct( $plugin ){
        $this->plugin = $plugin;
        $this->cart = $plugin->cart;
        $this->order = $plugin->order;
    }

    /**
     * Loads list of tickets templaye
     * @return string
     */
    public function display_ticket_listing(){
        ob_start();
        require $this->plugin->locations['dir']."/public/partials/ticket-list.php";
        $output = ob_get_contents(); // end output buffering
        ob_end_clean();
        return $output;
    }

    /**
     * Loads template to display cart
     * @return string
     */
    public function display_cart(){
        ob_start();
        require $this->plugin->locations['dir']."/public/partials/cart.php";
        $output = ob_get_contents(); // end output buffering
        ob_end_clean();
        return $output;
    }

    /**
     * Load template to display form for user to enter detaols
     * @return string
     */
    public function display_ticket_details(){
        ob_start();
        require $this->plugin->locations['dir']."/public/partials/ticket-details.php";
        $output = ob_get_contents(); // end output buffering
        ob_end_clean();
        return $output;
    }

    /**
     * Display order confirmation page before transfer to external payment service
     * @return string
     */
    public function display_confirm_order(){
        ob_start();
        require $this->plugin->locations['dir']."/public/partials/confirm-order.php";
        $output = ob_get_contents(); // end output buffering
        ob_end_clean();
        return $output;
    }

    /**
     * Display order completion template
     * @return string
     */
    public function display_order_complete(){
        ob_start();
        require $this->plugin->locations['dir']."/public/partials/order-complete.php";
        $output = ob_get_contents(); // end output buffering
        ob_end_clean();
        return $output;
    }
}