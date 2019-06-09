<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://addaeabeng.co.uk
 * @since      1.0.0
 *
 * @package    Conference_Payments
 * @subpackage Conference_Payments/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Conference_Payments
 * @subpackage Conference_Payments/admin
 * @author     Addae <Abeng>
 */
namespace AA_ConfPayments;

class Conference_Payments_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/conference-payments-admin.css', array(), $this->version, 'all' );

	}




	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/conference-payments-admin.js', array( 'jquery' ), $this->version, false );
	}


    function update_cmb2_meta_box_url( $url ) {
        /*
         * If you use a symlink, the css/js urls may have an odd path stuck in the middle, like:
         * http://SITEURL/wp-content/plugins/Users/jt/Sites/CMB2/cmb2/js/cmb2.js?ver=X.X.X
         * Or something like that.
         *
         * INSTEAD of completely replacing the URL,
         * It is best to do a str_replace. This ensures you only change the url if it's
         * pointing to the broken resource. This ensures that if another version of CMB2
         * is loaded (i.e. in a 3rd part plugin), that their correct URL will load,
         * rather than forcing yours.
         */

        return '/wp-content/plugins/conference-payments/cmb2';
    }





}
