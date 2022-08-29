<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://dranjo.com
 * @since      1.0.0
 *
 * @package    build_app_online
 * @subpackage build_app_online/includes
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
 * @package    build_app_online
 * @subpackage build_app_online/includes
 * @author     Dranjo <support@dranjo.com>
 */
class build_app_online {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      build_app_online_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'build_app_online_VERSION' ) ) {
			$this->version = build_app_online_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'dranjo-app-connect';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_payments_hooks();

		//Booking Hooks
		$this->define_booking_hooks();

		//Booking Hooks
		$this->define_blog_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - build_app_online_Loader. Orchestrates the hooks of the plugin.
	 * - build_app_online_i18n. Defines internationalization functionality.
	 * - build_app_online_Admin. Defines all hooks for the admin area.
	 * - build_app_online_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-build-app-online-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-build-app-online-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-dranjo-app-connect-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-build-app-online-public.php';

		/**
		 * The class responsible for defining all actions that occur in the multivendor of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-build-app-online-multivendor.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-build-app-online-payments.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-build-app-online-booking.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-build-app-online-blog-public.php';

		$this->loader = new build_app_online_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the build_app_online_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new build_app_online_i18n();

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

		$plugin_admin = new build_app_online_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'add_vendor_type_fields' );
		$this->loader->add_action( 'edit_user_profile_update', $plugin_admin, 'save_vendor_type_fields' );

		$this->loader->add_action( 'init', $plugin_admin, 'handle_orgin' );

		//$this->loader->add_action( 'wpcf7_mail_sent', $plugin_admin, 'wpcf7_mail_sent', 10, 1 );

		//$this->loader->add_action( 'wcfm_after_enquiry_submit', $plugin_admin, 'wcfm_after_enquiry_submit', 10, 6 );

        $this->loader->add_action( 'woocommerce_new_order', $plugin_admin, 'neworder',  10, 1  );

        $this->loader->add_filter( 'woocommerce_thankyou_order_received_text', $plugin_admin, 'send_admin_and_vendor_push_notification', 199, 2 );

        $this->loader->add_action( 'save_post_product', $plugin_admin, 'save_new_post', 10, 3  );
        $this->loader->add_filter('wcml_load_multi_currency_in_ajax', $plugin_admin, 'loadCurrency', 20, 1);

        $this->loader->add_action('woocommerce_order_status_changed', $plugin_admin, 'order_status_changed', 10, 1  );

        $this->loader->add_filter('woocommerce_rest_product_object_query', $plugin_admin, 'mstoreapp_prepare_product_query', 10, 2);

        $this->loader->add_filter('woocommerce_rest_product_cat_query', $plugin_admin, 'remove_uncategorized_category', 10, 1);

        /* For All Multi Vendor */
        $this->loader->add_action('wp_ajax_dranjo-app-connect-upload_image', $plugin_admin, 'uploadimage');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-upload_image', $plugin_admin, 'uploadimage');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-upload_images', $plugin_admin, 'uploadimages');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-upload_images', $plugin_admin, 'uploadimages');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-new_chat_message', $plugin_admin, 'flutter_new_chat_message');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-new_chat_message', $plugin_admin, 'flutter_new_chat_message');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-test', $plugin_admin, 'test');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-test', $plugin_admin, 'test');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-site_details', $plugin_admin, 'site_details');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-site_details', $plugin_admin, 'site_details');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-product_addons', $plugin_admin, 'get_product_addons');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-product_addons', $plugin_admin, 'get_product_addons');

        $this->loader->add_action('wp_ajax_app_save_options', $plugin_admin, 'app_save_options');
        $this->loader->add_action('wp_ajax_nopriv_app_save_options', $plugin_admin, 'app_save_options');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-bao_options', $plugin_admin, 'app_get_options');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-bao_options', $plugin_admin, 'app_get_options');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-save_options', $plugin_admin, 'app_save_options');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-save_options', $plugin_admin, 'app_save_options');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-jwt_token', $plugin_admin, 'firebase_jwt_token');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-jwt_token', $plugin_admin, 'firebase_jwt_token');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-update_user_metavalue', $plugin_admin, 'update_user_meta_value');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-update_user_metavalue', $plugin_admin, 'update_user_meta_value');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-order_statuses', $plugin_admin, 'get_order_statuses');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-order_statuses', $plugin_admin, 'get_order_statuses');

        /* This is for WC Marketplace only */
        $this->loader->add_filter('wcmp_rest_prepare_dc_vendor_object', $plugin_admin, 'mstoreapp_prepare_vendors_query', 10, 3);

        /* WC Marketplace and WCFM Same Function, Dokan Different Function. */
        $this->loader->add_filter('woocommerce_rest_shop_order_object_query', $plugin_admin, 'mstoreapp_prepare_order_query', 10, 2);

        /* For Dokan and WCFM Only */
        $this->loader->add_action('wp_ajax_dranjo-app-connect-update-vendor-product', $plugin_admin, 'update_vendor_product');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-update-vendor-product', $plugin_admin, 'update_vendor_product');

        /* For Dokan Only */
        $this->loader->add_filter('woocommerce_rest_prepare_product_object', $plugin_admin, 'mstoreapp_prepare_product', 10, 3);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new build_app_online_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_filter( 'query_vars', $plugin_public, 'add_query_vars' );

		//$this->loader->add_filter( 'woocommerce_product_query_tax_query', $plugin_public, 'update_product_query_tax_query', 10, 2 );

		$this->loader->add_action('wp_ajax_dranjo-app-connect-add_all_products_cart', $plugin_public, 'add_all_products_cart');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-add_all_products_cart', $plugin_public, 'add_all_products_cart');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-set_user_cart', $plugin_public, 'set_user_cart');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-set_user_cart', $plugin_public, 'set_user_cart');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-dotapp', $plugin_public, 'dotapp');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-dotapp', $plugin_public, 'dotapp');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-keys', $plugin_public, 'keys');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-keys', $plugin_public, 'keys');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-login', $plugin_public, 'login');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-login', $plugin_public, 'login');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-cart', $plugin_public, 'cart');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-cart', $plugin_public, 'cart');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-apply_coupon', $plugin_public, 'apply_coupon');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-apply_coupon', $plugin_public, 'apply_coupon');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-test', $plugin_public, 'test');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-test', $plugin_public, 'test');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-remove_coupon', $plugin_public, 'remove_coupon');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-remove_coupon', $plugin_public, 'remove_coupon');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-update_shipping_method', $plugin_public, 'update_shipping_method');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-update_shipping_method', $plugin_public, 'update_shipping_method');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-remove_cart_item', $plugin_public, 'remove_cart_item');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-remove_cart_item', $plugin_public, 'remove_cart_item');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-get_checkout_form', $plugin_public, 'get_checkout_form');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-get_checkout_form', $plugin_public, 'get_checkout_form');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-update_order_review', $plugin_public, 'update_order_review');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-update_order_review', $plugin_public, 'update_order_review');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-add_to_cart', $plugin_public, 'add_to_cart');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-add_to_cart', $plugin_public, 'add_to_cart');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-add_product_to_cart', $plugin_public, 'add_product_to_cart');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-add_product_to_cart', $plugin_public, 'add_product_to_cart');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-payment', $plugin_public, 'payment');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-payment', $plugin_public, 'payment');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-userdata', $plugin_public, 'userdata');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-userdata', $plugin_public, 'userdata');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-json_search_products', $plugin_public, 'json_search_products');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-json_search_products', $plugin_public, 'json_search_products');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-nonce', $plugin_public, 'nonce');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-nonce', $plugin_public, 'nonce');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-passwordreset', $plugin_public, 'passwordreset');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-passwordreset', $plugin_public, 'passwordreset');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-get_country', $plugin_public, 'get_country');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-get_country', $plugin_public, 'get_country');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-get_wishlist', $plugin_public, 'get_wishlist');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-get_wishlist', $plugin_public, 'get_wishlist');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-wishlistids', $plugin_public, 'get_wishlistids');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-wishlistids', $plugin_public, 'get_wishlistids');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-update_wishlist', $plugin_public, 'update_wishlist');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-update_wishlist', $plugin_public, 'update_wishlist');

        //Delete
        $this->loader->add_action('wp_ajax_dotapp_get_wishlist', $plugin_public, 'fetch_wishlist');
        $this->loader->add_action('wp_ajax_nopriv_dotapp_get_wishlist', $plugin_public, 'fetch_wishlist');

        //Delete
        $this->loader->add_action('wp_ajax_dranjo-app-connect-remove_wishlist', $plugin_public, 'remove_wishlist');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-remove_wishlist', $plugin_public, 'remove_wishlist');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-page_content', $plugin_public, 'pagecontent');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-page_content', $plugin_public, 'pagecontent');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-set_fulfill_status', $plugin_public, 'set_fulfill_status');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-set_fulfill_status', $plugin_public, 'set_fulfill_status');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-facebook_login', $plugin_public, 'facebook_login');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-facebook_login', $plugin_public, 'facebook_login');

	    $this->loader->add_action('wp_ajax_dranjo-app-connect-google_login', $plugin_public, 'google_login');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-google_login', $plugin_public, 'google_login');

	    $this->loader->add_action('wp_ajax_dranjo-app-connect-apple_login', $plugin_public, 'apple_login');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-apple_login', $plugin_public, 'apple_login');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-otp_verification', $plugin_public, 'otp_verification');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-otp_verification', $plugin_public, 'otp_verification');

	    $this->loader->add_action('wp_ajax_dranjo-app-connect-logout', $plugin_public, 'logout');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-logout', $plugin_public, 'logout');

	    $this->loader->add_action('wp_ajax_dranjo-app-connect-emptyCart', $plugin_public, 'emptyCart');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-emptyCart', $plugin_public, 'emptyCart');

	    $this->loader->add_action('wp_ajax_dranjo-app-connect-update_user_notification', $plugin_public, 'update_user_notification');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-update_user_notification', $plugin_public, 'update_user_notification');

	    $this->loader->add_action('wp_ajax_dranjo-app-connect-email-otp', $plugin_public, 'email_otp');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-email-otp', $plugin_public, 'email_otp');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-reset-user-password', $plugin_public, 'reset_user_password');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-reset-user-password', $plugin_public, 'reset_user_password');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-create-user', $plugin_public, 'create_user');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-create-user', $plugin_public, 'create_user');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-update-address', $plugin_public, 'update_address');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-update-address', $plugin_public, 'update_address');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-get-states', $plugin_public, 'get_states');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-get-states', $plugin_public, 'get_states');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-product-attributes', $plugin_public, 'product_attributes');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-product-attributes', $plugin_public, 'product_attributes');

        //$this->loader->add_action('wp_ajax_dranjo-app-connect-locations', $plugin_public, 'locations');
        //$this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-locations', $plugin_public, 'locations');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-wallet', $plugin_public, 'get_wallet');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-wallet', $plugin_public, 'get_wallet');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-woo_refund_key', $plugin_public, 'woo_refund_key');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-woo_refund_key', $plugin_public, 'woo_refund_key');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-categories', $plugin_public, 'get_categories');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-categories', $plugin_public, 'get_categories');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-products', $plugin_public, 'getProducts');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-products', $plugin_public, 'getProducts');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-product', $plugin_public, 'getProduct');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-product', $plugin_public, 'getProduct');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-orders', $plugin_public, 'getOrders');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-orders', $plugin_public, 'getOrders');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-order', $plugin_public, 'getOrder');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-order', $plugin_public, 'getOrder');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-customer', $plugin_public, 'getCustomerDetail');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-customer', $plugin_public, 'getCustomerDetail');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-product_details', $plugin_public, 'getProductDetail');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-product_details', $plugin_public, 'getProductDetail');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-product_reviews', $plugin_public, 'getProductReviews');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-product_reviews', $plugin_public, 'getProductReviews');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-cancel_order', $plugin_public, 'cancel_order');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-cancel_order', $plugin_public, 'cancel_order');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-update-cart-item-qty', $plugin_public, 'updateCartQty');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-update-cart-item-qty', $plugin_public, 'updateCartQty');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-apply-vendor', $plugin_public, 'dokan_apply_for_vendor');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-apply-vendor', $plugin_public, 'dokan_apply_for_vendor');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-checkout_form', $plugin_public, 'checkout_form');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-checkout_form', $plugin_public, 'checkout_form');

        $this->loader->add_filter( 'woocommerce_product_data_store_cpt_get_products_query', $plugin_public, 'handling_custom_meta_query_keys', 10, 3 );

        $this->loader->add_action('wp_ajax_dranjo-app-connect-downloads', $plugin_public, 'get_downloads');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-downloads', $plugin_public, 'get_downloads');

        $this->loader->add_action('wp_ajax_fcm_details', $plugin_public, 'fcm_details');
        $this->loader->add_action('wp_ajax_nopriv_fcm_details', $plugin_public, 'fcm_details');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-store_details', $plugin_public, 'store_details');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-store_details', $plugin_public, 'store_details');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-design_app_details', $plugin_public, 'design_app_details');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-design_app_details', $plugin_public, 'design_app_details');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-taxonomy', $plugin_public, 'blocks_taxonomy');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-taxonomy', $plugin_public, 'blocks_taxonomy');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-myblocks', $plugin_public, 'bao_my_blocks');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-myblocks', $plugin_public, 'bao_my_blocks');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-block', $plugin_public, 'get_blocks');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-block', $plugin_public, 'get_blocks');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-delete_my_account', $plugin_public, 'delete_my_account');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-delete_my_account', $plugin_public, 'delete_my_account');

        //$this->loader->add_filter('woocommerce_login_redirect', $plugin_public, 'wc_custom_user_redirect', 101, 3);

        //---REWARD POINTS--------/
        $this->loader->add_action('wp_ajax_dranjo-app-connect-ajax_maybe_apply_discount', $plugin_public, 'ajax_maybe_apply_discount');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-ajax_maybe_apply_discount', $plugin_public, 'ajax_maybe_apply_discount');
		
		$this->loader->add_action('wp_ajax_dranjo-app-connect-getPointsHistory', $plugin_public, 'getPointsHistory');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-getPointsHistory', $plugin_public, 'getPointsHistory');

        $plugin_multivendor = new build_app_online_Multivendor( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_filter( 'posts_clauses', $plugin_multivendor, 'mstoreapp_location_filter', 99, 2);

        //For WC Marketplace geo user query
        $this->loader->add_action( 'pre_user_query', $plugin_multivendor, 'geo_location_user_query', 99, 1  );
        
        $this->loader->add_action( 'pre_get_users', $plugin_multivendor, 'pre_get_users', 99, 1  );

        $this->loader->add_action('wp_ajax_dranjo-app-connect-vendor_reviews', $plugin_multivendor, 'get_vendor_reviews');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-vendor_reviews', $plugin_multivendor, 'get_vendor_reviews');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-vendor_details', $plugin_multivendor, 'get_vendor_details');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-vendor_details', $plugin_multivendor, 'get_vendor_details');

	    $this->loader->add_action('wp_ajax_dranjo-app-connect-vendors', $plugin_multivendor, 'get_vendors');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-vendors', $plugin_multivendor, 'get_vendors');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-contact_vendor', $plugin_multivendor, 'contact_vendor');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-contact_vendor', $plugin_multivendor, 'contact_vendor');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-add_vendor_review', $plugin_multivendor, 'add_vendor_review');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-add_vendor_review', $plugin_multivendor, 'add_vendor_review');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-vendor_categories', $plugin_multivendor, 'getVendorCategories');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-vendor_categories', $plugin_multivendor, 'getVendorCategories');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-update_user_meta', $plugin_public, 'update_user_meta');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-update_user_meta', $plugin_public, 'update_user_meta');

	}

	private function define_payments_hooks() {

		$plugin_payments = new build_app_online_Payments( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action('wp_ajax_dotapp_verify_payment', $plugin_payments, 'verify_payment');
        $this->loader->add_action('wp_ajax_nopriv_dotapp_verify_payment', $plugin_payments, 'verify_payment');

        $this->loader->add_action('wp_ajax_dranjo-app-connect_razorpay_order_id', $plugin_payments, 'get_razorpay_order_id');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect_razorpay_order_id', $plugin_payments, 'get_razorpay_order_id');

	}

	private function define_booking_hooks() {

		$plugin_booking = new build_app_online_Booking( $this->get_plugin_name(), $this->get_version() );

        //UnComment Only for Booking
        $this->loader->add_filter('woocommerce_rest_prepare_shop_order_object', $plugin_booking, 'mstoreapp_prepare_order', 10, 3);

	    $this->loader->add_action('wp_ajax_dranjo-app-connect-get_booking', $plugin_booking, 'get_booking');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-get_booking', $plugin_booking, 'get_booking');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-create_booking', $plugin_booking, 'create_booking');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-create_booking', $plugin_booking, 'create_booking');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-user_booking', $plugin_booking, 'get_user_booking');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-user_booking', $plugin_booking, 'get_user_booking');

	}

	private function define_blog_hooks() {

		$plugin_blog = new build_app_online_Blog_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action('wp_ajax_dranjo-app-connect-blog-posts', $plugin_blog, 'get_posts');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-blog-posts', $plugin_blog, 'get_posts');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-blog-design_app_details', $plugin_blog, 'design_app_details');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-blog-design_app_details', $plugin_blog, 'design_app_details');

		$this->loader->add_action('wp_ajax_dranjo-app-connect-blog-details', $plugin_blog, 'app_details');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-blog-details', $plugin_blog, 'app_details');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-blog-login', $plugin_blog, 'login');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-blog-login', $plugin_blog, 'login');

        $this->loader->add_action( 'save_post', $plugin_blog, 'save_new_post', 10, 3  );

        $this->loader->add_action('wp_ajax_dranjo-app-connect-blog-site_details', $plugin_blog, 'site_details');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-blog-site_details', $plugin_blog, 'site_details');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-blog-save_options', $plugin_blog, 'app_save_options');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-blog-save_options', $plugin_blog, 'app_save_options');

		$this->loader->add_action('wp_ajax_dranjo-app-connect-blog-fcm_details', $plugin_blog, 'fcm_details');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-blog-fcm_details', $plugin_blog, 'fcm_details');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-blog-get_bookmark', $plugin_blog, 'get_bookmark');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-blog-get_bookmark', $plugin_blog, 'get_bookmark');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-blog-bookmarkids', $plugin_blog, 'get_bookmarkids');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-blog-bookmarkids', $plugin_blog, 'get_bookmarkids');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-blog-update_bookmark', $plugin_blog, 'update_bookmark');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-blog-update_bookmark', $plugin_blog, 'update_bookmark');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-blog-myblocks', $plugin_blog, 'bao_my_blocks');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-blog-myblocks', $plugin_blog, 'bao_my_blocks');

        $this->loader->add_action('wp_ajax_dranjo-app-connect-blog-block', $plugin_blog, 'get_blocks');
        $this->loader->add_action('wp_ajax_nopriv_dranjo-app-connect-blog-block', $plugin_blog, 'get_blocks');

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
	 * @return    build_app_online_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
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

}
