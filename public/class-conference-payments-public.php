<?php
namespace AA_ConfPayments;
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://addaeabeng.co.uk
 * @since      1.0.0
 *
 * @package    Conference_Payments
 * @subpackage Conference_Payments/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Conference_Payments
 * @subpackage Conference_Payments/public
 * @namespace AA_ConfPayments
 * @author     Addae <Abeng>
 */


class Conference_Payments_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
    * Hold Plugin class
    * @var Conference_Payments
    */
    public $plugin;

    /**
     * Hold Cart class
     * @var \AA_ConfPayments\Cart
     */
    private $cart;

    /**
     * Hold Order class
     * @var \AA_ConfPayments\Orders
     */
    private $order;

    /**
     * Hold Transactions class
     * @var \AA_ConfPayments\Transactions
     */
    private $transaction;

    /**
     * Hold Shortcodes class
     * @var \AA_ConfPayments\Shortcodes
     */
    public $shortcodes;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
     * @param      Conference_Payments    $plugin The main plugin class
	 */
	public function __construct( $plugin_name, $version, $plugin) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->plugin = $plugin;
        $this->cart = $plugin->cart;
        $this->order = $plugin->order;
        $this->transaction = $plugin->transactions;
        $this->shortcodes = new Shortcodes($this->plugin);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Conference_Payments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Conference_Payments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/conference-payments-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Conference_Payments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Conference_Payments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/conference-payments-public.js', array( 'jquery' ), $this->version, false );

	}

    public function register_shortcodes(){

        /**
         * Add main display shortcode
         * @return    void
         */

        add_shortcode( 'list-tickets', array( $this->shortcodes, 'display_ticket_listing' ) );
        add_shortcode( 'display-cart', array( $this->shortcodes, 'display_cart' ) );
        add_shortcode( 'display-details', array( $this->shortcodes, 'display_ticket_details' ) );
        add_shortcode( 'confirm-ticket-order', array( $this->shortcodes, 'display_confirm_order' ) );
        add_shortcode( 'ticket-order-complete', array( $this->shortcodes, 'display_order_complete' ) );
    }

    public function add_tickets(){
        /**
         * Add selected tickets to cart
         */
        if(wp_verify_nonce($_POST['_wpnonce'], 'aact_select_ticket')){

            foreach($_POST['qty'] as $key => $value){
                if($value['qty'] > 0){
                    $ticket = get_post($value['id']);
                    $meta = aact_post_meta_object($value['id']);
                    $price = $meta->{CPAAPREFIX.'ticket_price'};
                    $item = array(
                        'ticket_id' => $value['id'],
                        'name' => $ticket->post_title,
                        'sku' => $meta->_cpaa_product_sku,
                        'qty' => $value['qty'],
                        'price' => $price
                    );
                    $this->cart->add($item);
                }
            }
            wp_redirect(get_permalink(get_page_by_path('ticket-test-details')));
            exit();
        }
    }

    public function empty_cart(){
        /**
         * Empty the cart
         */
        if(wp_verify_nonce($_POST['_wpnonce'], 'aact_clear_cart')){
            $this->cart->empty_cart();
            wp_redirect(get_permalink(get_page_by_path('ticket-test')));
            exit;
        }
    }

    public function new_ticket_transaction(){
        /**
         * Create new transcation to be sent to external API
         */
        if(wp_verify_nonce($_POST['_wpnonce'], 'aact_continue_to_payment')){
            $api_key = DPAPIKEY;
            $orderid = $this->order->get_order_id();
            $order = $this->order->get_order($orderid);
            $_SESSION['cancelkey'] = wp_generate_uuid4();
            $order['complete_url'] = get_permalink(get_page_by_path('ticket-order-complete'));
            $order['cancel_url'] = get_permalink(get_page_by_path('ticket-test'));
            $order['return_url'] = get_permalink(get_page_by_path('confirm-ticket-order'));
            $order['cancel_key'] = $_SESSION['cancelkey'];
            $transaction = $this->transaction->send_transcation_info($order, $api_key);


            if(!is_wp_error($transaction)){
                $url = add_query_arg( 'ticket_token', $transaction->ticket_token, $transaction->redirect_url);
                wp_redirect( $url );
                exit;
            }

            $_SESSION['alert'] = $transaction->get_error_message().' Order Ref: '.$order['details']->orderID;
            $this->transaction->register_transaction();
        }
    }

    public function cancel_order(){
        /**
         * Destroy all order data if user cancels transaction
         */
        if(wp_verify_nonce($_POST['_wpnonce'],'dpc_cancel_order')){
            $orderId = sanitize_text_field($_POST['orderId']);
            if($this->order->delete_order($orderId)){
                $_SESSION['alert'] = 'Your order has been cancelled!';
                wp_redirect(get_permalink(get_page_by_path('ticket-test')));
                exit;
            };

            $_SESSION['alert'] = 'There was a problem deleting this order!';
            wp_redirect(add_query_arg('deleted', 'error',get_permalink(get_page_by_path('confirm-ticket-order'))));
            exit;

        }
    }

    public function save_user_details(){
        /**
         * Save user details
         */
        if(wp_verify_nonce($_POST['_wpnonce'], 'aact_add_order_details')){
            $ticketdetails = array();
            foreach($_POST['ticketdetails'] as $ticket){
                $cleanticket = array_map( function( $a ){ return sanitize_text_field($a); }, $ticket );
                array_push($ticketdetails, $cleanticket);
            }

            $cleanbilling = array_map( function( $a ){ return sanitize_text_field($a); }, $_POST['billing'][0] );

            if(!$this->order->get_order_id()){
                $neworder = $this->order->new_order(
                    array(
                        'ticketdetails' => $ticketdetails,
                        'cart' => $this->cart->cart_items(),
                        'billingdetails' => $cleanbilling,
                        'total' => $this->cart->getTotal()
                    )
                );
            } else {
                $orderid = $this->order->get_order_id();
                $postid = $this->order->get_order($orderid)['ID'];
                $neworder = $this->order->update_order(
                    array(
                        'ID' => $postid,
                        'ticketdetails' => $ticketdetails,
                        'cart' => $this->cart->cart_items(),
                        'billingdetails' => $cleanbilling,
                        'total' => $this->cart->getTotal()
                    )
                );
            }

            if(!is_wp_error($neworder)){
                wp_redirect(get_permalink(get_page_by_path('confirm-ticket-order')));
                exit;
            };

            foreach($neworder->get_error_messages as $msg){
                $_SESSION['alert'][] = $msg;
            }
        }
    }



    public function register_types(){
        /**
         * Register custom post types for plugin
         */

        register_post_type('conference_ticket',array(
            'label'                 => __( 'Conference Ticket', 'conference-ticket' ),
            'description'           => __( 'Conference Ticket', 'conference-ticket' ),
            'supports'              => array( 'title', 'page-attributes', 'content'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => false,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        ));

        register_post_type('conf_ticket_order',array(
            'label'                 => __( 'Conference  Orders', 'conference-ticket' ),
            'description'           => __( 'Conference Orders', 'conference-ticket' ),
            'supports'              => array( 'title', 'page-attributes' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => false,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        ));


        register_post_type('conf_attendee',array(
            'label'                 => __( 'Conference Attendee', 'conference-ticket' ),
            'description'           => __( 'Conference Attendee', 'conference-ticket' ),
            'supports'              => array( 'title', 'page-attributes' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => false,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        ));


        register_post_type('conf_transaction',array(
            'label'                 => __( 'Conference Transactions', 'conference-ticket' ),
            'description'           => __( 'Conference Transactions', 'conference-ticket' ),
            'supports'              => array( 'title', 'page-attributes' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => false,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        ));

        register_post_type('conf_promocode',array(
            'label'                 => __( 'Conference Promocode', 'conference-ticket' ),
            'description'           => __( 'Conference Promocode', 'conference-ticket' ),
            'supports'              => array( 'title', 'page-attributes' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => false,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        ));
    }

}
