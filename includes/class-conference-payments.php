<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://addaeabeng.co.uk
 * @since      1.0.0
 *
 * @package    Conference_Payments
 * @subpackage Conference_Payments/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Conference_Payments
 * @subpackage Conference_Payments/includes
 * @author     Addae <Abeng>
 */
namespace AA_ConfPayments;

class Conference_Payments {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Conference_Payments_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

    /**
     * @var Tickets
     */
    public $tickets;

    /**
     * @var Cart
     */
    public $cart;

    /**
     * @var Transactions
     */
    public $transactions;

    /**
     * @var Resources
     */
    public $resources;

    /**
     * @var Tickets
     */
    public $order;

    /**
     * URLs and Paths used by the plugin
     *
     * @var array
     */
	public $locations = array();

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        $locate = $this->locate_plugin();

		if ( defined( 'CONFERENCE_PAYMENTS_VERSION' ) ) {
			$this->version = CONFERENCE_PAYMENTS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'conference-payments';

        spl_autoload_register( array( $this, 'autoload' ) );

        $this->locations = array(
            'plugin'    => $locate['plugin_basename'],
            'dir'       => $locate['dir_path'],
            'url'       => $locate['dir_url'],
            'inc_dir'   => $locate['dir_path'] . 'includes/',
            'class_dir' => $locate['dir_path'] . 'classes/',
        );
     //  $this->start_session();
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function start_session(){
        if(!session_id()) {
            session_start();
        }
    }

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Conference_Payments_Loader. Orchestrates the hooks of the plugin.
	 * - Conference_Payments_i18n. Defines internationalization functionality.
	 * - Conference_Payments_Admin. Defines all hooks for the admin area.
	 * - Conference_Payments_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'cmb2/init.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-conference-payments-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-conference-payments-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-conference-payments-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-conference-payments-public.php';

        require_once $this->locations['inc_dir'] . 'functions.php';

        $this->cart = new Cart();
        $this->tickets = new Tickets();
        $this->resources = new Resources();
        $this->order = new Orders($this);
        $this->transactions = new Transactions();
		$this->loader = new Conference_Payments_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Conference_Payments_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Conference_Payments_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Conference_Payments_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'cmb2_admin_init',$this->tickets, 'cmb2_sample_metaboxes' );
        $this->loader->add_filter( 'cmb2_meta_box_url',$plugin_admin, 'update_cmb2_meta_box_url');
        $this->loader->add_filter('manage_edit-conference_ticket_columns', $this->tickets , 'edit_conference_ticket_columns');
        $this->loader->add_action('manage_conference_ticket_posts_custom_column', $this->tickets , 'conference_ticket_columns_content', 10,2);
        $this->loader->add_filter('manage_edit-conf_ticket_order_columns', $this->order , 'edit_conf_ticket_order_columns');
        $this->loader->add_action('manage_conf_ticket_order_posts_custom_column', $this->order , 'conf_ticket_order_columns_content', 10,2);
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Conference_Payments_Public( $this->get_plugin_name(), $this->get_version(), $this );
        $this->loader->add_action('init', $plugin_public, 'register_types');
        $this->loader->add_action('init', $plugin_public, 'add_tickets');
        $this->loader->add_action('init', $plugin_public, 'empty_cart');
        $this->loader->add_action('init', $plugin_public, 'save_user_details');
        $this->loader->add_action('init', $plugin_public, 'cancel_order');
        $this->loader->add_action('init', $plugin_public, 'new_ticket_transaction');
        $this->loader->add_action('dpc_order_deleted', $this->cart, 'empty_cart');
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Conference_Payments_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}


    /**
     * Autoloader for classes
     *
     * @param string $class
     */
    public function autoload( $class ) {
        // Check if class has a namespace
        if ( ! preg_match( '/^(?P<namespace>.+)\\\\(?P<autoload>[^\\\\]+)$/', $class, $matches ) ) {
            return;
        }

        static $reflection;

        if ( empty( $reflection ) ) {
            $reflection = new \ReflectionObject( $this );
        }

        // Check class is part of plugin namespace
        if ( $reflection->getNamespaceName() !== $matches['namespace'] ) {
            return;
        }

        $autoload_name = $matches['autoload'];
        $autoload_dir  = \trailingslashit( $this->locations['class_dir'] );
        $autoload_path = sprintf( '%sclass-%s.php', $autoload_dir, strtolower( str_replace( '_', '-', $autoload_name ) ) );

        if ( is_readable( $autoload_path ) ) {
            require_once $autoload_path;
        }
    }


    /**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

    /**
     * Version of plugin_dir_url() which works for plugins installed in the plugins directory,
     * and for plugins bundled with themes.
     *
     * @throws \Exception
     *
     * @return array
     */
    private function locate_plugin() {
        $dir_url         = trailingslashit( plugins_url( '', dirname( __FILE__ ) ) );
        $dir_path        = plugin_dir_path( dirname( __FILE__ ) );
        $dir_basename    = basename( $dir_path );
        $plugin_basename = trailingslashit( $dir_basename ) . $dir_basename . '.php';

        return compact( 'dir_url', 'dir_path', 'dir_basename', 'plugin_basename' );
    }

}
