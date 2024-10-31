<?php

/**
 * The file that defines the core plugin class
 *
 *
 * @since      1.0.0
 *
 * @package    Wholesalex_Dokan
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, load dependencies
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wholesalex_Dokan
 */
class Wholesalex_Dokan {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wholesalex_Dokan_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * Dependency Plugins
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $plugin    All Dependency Plugins
	 */
	public $plugins;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public area.
	 *
	 * @since    1.0.0
	 */

	public function __construct($plugins=array()) {
		if ( defined( 'WHOLESALEX_DOKAN_VERSION' ) ) {
			$this->version = WHOLESALEX_DOKAN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'multi-vendor-marketplace-b2b-for-wholesalex-dokan';
		$this->plugins = $plugins;

		$this->load_dependencies();

		$this->set_locale();

		$dependency_statuses = $this->check_required_plugins_status();

		if($this->is_dependency_pass($dependency_statuses)) {
			$this->define_public_hooks();
		} else {
			// dependency check failed. show notice

			$this->define_notices($dependency_statuses);
			add_action( 'wp_ajax_install_wholesalex', array( $this, 'wholesalex_installation_callback' ) );
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wholesalex_Dokan_Loader. Orchestrates the hooks of the plugin.
	 * - Wholesalex_Dokan_i18n. Defines internationalization functionality.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-multi-vendor-marketplace-b2b-for-wholesalex-dokan-loader.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-multi-vendor-marketplace-b2b-for-wholesalex-dokan-i18n.php';
		/**
		 * The class responsible for public functionality of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/public/class-multi-vendor-marketplace-b2b-for-wholesalex-dokan-public.php';

		$this->loader = new Wholesalex_Dokan_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wholesalex_Dokan_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wholesalex_Dokan_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
     * Register all of the hooks related to the public/frontend functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
	private function define_public_hooks() {
		
		$plugin_public = new Wholesalex_Dokan_Public();

		$this->loader->add_filter('wholesalex_setting_fields',$plugin_public,'dokan_wholesalex_settings_field',99);

		$this->loader->add_filter('dokan_get_dashboard_nav',$plugin_public,'add_wholesalex_menu_pages_dokan_vendor_dashboard_nav');

		$this->loader->add_filter('dokan_query_var_filter',$plugin_public,'add_wholesalex_menu_pages_dokan_dashboard_query');

		$this->loader->add_filter('dokan_load_custom_template',$plugin_public,'load_menu_pages_template');

		$wholesalex_settings = get_option( 'wholesalex_settings',array() );
		$dynamic_rule_status = isset($wholesalex_settings['dokan_vendor_dynamic_rule_status']) ? $wholesalex_settings['dokan_vendor_dynamic_rule_status']: 'yes';
		$rolewise_pricing = isset($wholesalex_settings['dokan_vendor_rolewise_wholesalex_price']) ? $wholesalex_settings['dokan_vendor_rolewise_wholesalex_price']: 'yes';
		$wholesalex_section_status = isset($wholesalex_settings['dokan_vendor_product_wholesalex_section_status']) ? $wholesalex_settings['dokan_vendor_product_wholesalex_section_status']: 'yes';
		$conversation_status = isset($wholesalex_settings['dokan_vendor_conversation_status']) ? $wholesalex_settings['dokan_vendor_conversation_status']: 'yes';

		// Dynamic Rules.
		if ( 'yes' === $dynamic_rule_status ) {

			$this->loader->add_filter('dynamic_rules_restapi_permission_callback',$plugin_public,'set_restapi_permission');

			$this->loader->add_filter('wholesalex_save_dynamic_rules',$plugin_public,'add_meta_on_vendor_created_dynamic_rules',10,2);

			$this->loader->add_filter('wholesalex_dynamic_rules_rule_type_options',$plugin_public,'dynamic_rule_types_for_dokan_vendors');

			$this->loader->add_filter('wholesalex_dynamic_rules_product_filter_options',$plugin_public,'dynamic_rules_product_filter_dokan_vendors');

			$this->loader->add_filter('wholesalex_dynamic_rules_condition_options',$plugin_public,'dynamic_rules_conditions_dokan_vendors');

			$this->loader->add_filter('wholesalex_get_all_dynamic_rules',$plugin_public,'get_dokan_vendors_dynamic_rules');

		}


		// Rolewise Wholesale Price.
		if ( 'yes' === $rolewise_pricing ) {

			$this->loader->add_action('dokan_product_edit_after_pricing',$plugin_public,'add_wholesalex_pricing',10,2);

			$this->loader->add_action('dokan_new_product_added',$plugin_public,'save_single_product_prices',10,2);

			$this->loader->add_action('dokan_product_updated',$plugin_public,'save_single_product_prices',10,2);

			$this->loader->add_action('dokan_variation_options_pricing',$plugin_public,'product_variations_wholesale_pricing',10,3);
		}

		/**
		 * WholesaleX Section Status.
		 */
		if ( 'yes' === $wholesalex_section_status ) {
			$this->loader->add_action('dokan_product_edit_after_main',$plugin_public,'wholesalex_section',10,2);
		}

		// Conversations
		if ( 'yes' === $conversation_status) {

			$this->loader->add_action('wholesalex_new_conversation_form_before_type',$plugin_public,'add_vendor_fields_in_conversation');

			$this->loader->add_action('wholesalex_conversation_created',$plugin_public,'add_vendor_as_recipient');

			$this->loader->add_filter('wholesalex_conversation_my_account_columns',$plugin_public,'add_vendor_columns');

			$this->loader->add_action('wholesalex_conversation_my_account_default_column_values',$plugin_public,'populate_vendor_column_data', 10,2);

			$this->loader->add_filter('wholesalex_get_conversations_args',$plugin_public,'modify_conversations_args', 10,2);

			$this->loader->add_filter('wholesalex_conversation_restapi_permission_callback',$plugin_public,'set_restapi_permission');

			$this->loader->add_filter('wholesalex_addon_conversation_has_eligibility_to_view_conversation',$plugin_public,'allow_vendor_to_view_conversation',10,3);

			$this->loader->add_filter('wholesalex_addon_conversation_view_author_ids',$plugin_public,'add_vendor_id_as_valid_post_author');

			$this->loader->add_filter('wholesalex_addon_conversation_reply_class',$plugin_public,'add_conversation_vendor_reply_class',10,3);

		}




	}

	/**
     * Register all of the hooks related to the public/frontend functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
	private function define_notices($plugin_statuses) {
		if(is_admin() && current_user_can( 'activate_plugins' )) {
			foreach ($plugin_statuses as $key => $value) {
				switch ($key) {
					case 'WholesaleX':
						add_action( 'admin_notices', array( $this, 'wholesalex_intro_notice' ) );
						break;
					
					default:
						# code...
						break;
				}
			}
		}
	}


	public function wholesalex_intro_notice() {
		// check wholesalex is installed or not.
		$wholesalex_installed = file_exists( WP_PLUGIN_DIR . '/wholesalex/wholesalex.php' );

		$regular_text = $wholesalex_installed?esc_html__('Activate','multi-vendor-marketplace-b2b-for-wholesalex-dokan'):esc_html__('Install','multi-vendor-marketplace-b2b-for-wholesalex-dokan');
		$processing_text = $wholesalex_installed?esc_html__('Activating..','multi-vendor-marketplace-b2b-for-wholesalex-dokan'):esc_html__('Installing..','multi-vendor-marketplace-b2b-for-wholesalex-dokan');
		$processed_text = $wholesalex_installed?esc_html__('Activated','multi-vendor-marketplace-b2b-for-wholesalex-dokan'):esc_html__('Installed','multi-vendor-marketplace-b2b-for-wholesalex-dokan');


		if(defined('WHOLESALEX_VER') && WHOLESALEX_VER) {
			return;
		}
		
	
			?>
				<style>
					/*----- WholesaleX Into Notice ------*/
					.notice.notice-success.multi-vendor-marketplace-b2b-for-wholesalex-dokan-wholesalex-notice {
						border-left-color: #4D4DFF;
						padding: 0;
					}

					.multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-container {
						display: flex;
					}

					.multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-container a{
						text-decoration: none;
					}

					.multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-container a:visited{
						color: white;
					}

					.multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-image {
						padding-top: 15px;
						padding-left: 12px;
						padding-right: 12px;
						background-color: #f4f4ff;
						max-width: 40px;
					}
					.multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-image img{
						max-width: 100%;
					}

					.multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-content {
						width: 100%;
						padding: 16px;
						display: flex;
						flex-direction: column;
						gap: 8px;
					}

					.multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-wholesalex-button {
						max-width: fit-content;
						padding: 8px 15px;
						font-size: 16px;
						color: white;
						background-color: #4D4DFF;
						border: none;
						border-radius: 2px;
						cursor: pointer;
						margin-top: 6px;
						text-decoration: none;
					}

					.multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-heading {
						font-size: 18px;
						font-weight: 500;
						color: #1b2023;
					}

					.multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-content-header {
						display: flex;
						justify-content: space-between;
						align-items: center;
					}

					.multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-close .dashicons-no-alt {
						font-size: 25px;
						height: 26px;
						width: 25px;
						cursor: pointer;
						color: #585858;
					}

					.multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-close .dashicons-no-alt:hover {
						color: red;
					}

					.multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-content-body {
						font-size: 14px;
						color: #343b40;
					}

					.multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-wholesalex-button:hover {
						background-color: #6C6CFF;
						color: white;
					}

					span.multi-vendor-marketplace-b2b-for-wholesalex-dokan-bold {
						font-weight: bold;
					}
					a.multi-vendor-marketplace-b2b-for-wholesalex-dokan-wholesalex-pro-dismiss:focus {
						outline: none;
						box-shadow: unset;
					}
					.loading {
						width: 16px;
						height: 16px;
						border: 3px solid #FFF;
						border-bottom-color: transparent;
						border-radius: 50%;
						display: inline-block;
						box-sizing: border-box;
						animation: rotation 1s linear infinite;
						margin-left: 10px;
					}

					@keyframes rotation {
						0% {
							transform: rotate(0deg);
						}

						100% {
							transform: rotate(360deg);
						}
					}
					/*----- End WholesaleX Into Notice ------*/

				</style>
				<div class="notice notice-success multi-vendor-marketplace-b2b-for-wholesalex-dokan-wholesalex-notice">
					<div class="multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-container">
						<div class="multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-image"><img src="<?php echo esc_url( WHOLESALEX_DOKAN_URL ) . 'assets/img/wholesalex-icon.svg'; ?>"/></div>
						<div class="multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-content">
							<div class="multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-content-header">
								<div class="multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-heading">
									<?php echo esc_html__( 'WholesaleX for Dokan needs the “WholesaleX” plugin to run.', 'multi-vendor-marketplace-b2b-for-wholesalex-dokan' ); //phpcs:ignore WordPress.Security.EscapeOutput ?>
								</div>
							</div>
							<?php if(current_user_can( 'install_plugins' )) {
								?>
								<a id="multi-vendor-marketplace-b2b-for-wholesalex-dokan_install_wholesalex" class="multi-vendor-marketplace-b2b-for-wholesalex-dokan-notice-wholesalex-button " ><?php echo esc_html($regular_text); ?></a>
								<?php
							} ?>
						</div>
					</div>
				</div>

				<script>
					const installWholesaleX = (element)=>{
						element.innerHTML = "<?php echo esc_html($processing_text); ?> <span class='loading'></span>";
						const wholesalex_dokan_ajax = "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>";
						const formData = new FormData();
						formData.append('action','install_wholesalex');
						formData.append('wpnonce',"<?php echo esc_attr( wp_create_nonce( 'install_wholesalex' ) ); ?>");
						fetch(wholesalex_dokan_ajax, {
							method: 'POST',
							body: formData,
						})
						.then(res => res.json())
						.then(res => {
							if(res) {
								if (res.success ) {
									element.innerHTML = "<?php echo esc_html($processed_text); ?>";
								} else {
									console.log("installation failed..");
								}
							}
							location.reload();
						})
					}
					const wholesalex_dokan_element = document.getElementById('multi-vendor-marketplace-b2b-for-wholesalex-dokan_install_wholesalex');
					wholesalex_dokan_element.addEventListener('click',(e)=>{
						e.preventDefault();
						installWholesaleX(wholesalex_dokan_element);
					})
				</script>
			<?php
		
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
	 * @return    Wholesalex_Dokan_Loader    Orchestrates the hooks of the plugin.
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

	/**
	 * Check Dependency Plugin Installed or not
	 *
	 * @return boolean
	 */
	public function check_required_plugins_status() {
		$active_plugins = get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
		}

		$dependency_statuses = array();

		foreach ($this->plugins as $key=> $plugin) {
			$is_exist = file_exists( WP_PLUGIN_DIR . '/'.$plugin['path'] );
			$is_active = $is_exist &&  in_array( $plugin['path'], $active_plugins, true );
			$dependency_statuses[$key] = array(
				'path' => $plugin['path'],
				 'is_exist' => $is_exist,
				 'is_active' => $is_active
			);
				
		}

		return $dependency_statuses;
	}


	public function is_dependency_pass($dependency_statuses) {
		
		foreach ($dependency_statuses as $key=> $plugin) {
			if(!$plugin['is_active']) {
				return false;
			}
		}
		return true && defined('WHOLESALEX_VER') && version_compare(WHOLESALEX_VER,'1.2.4','>=');
	}


	/**
	 * WholesaleX Installation Callback From Banner.
	 *
	 * @return void
	 */
	public function wholesalex_installation_callback() {
		if ( ! isset( $_POST['wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['wpnonce'] ) ), 'install_wholesalex' ) ) {
			wp_send_json_error( 'Nonce Verification Failed' );
			die();
		}

		$wholesalex_installed = file_exists( WP_PLUGIN_DIR . '/wholesalex/wholesalex.php' );

		if ( ! $wholesalex_installed ) {
			$status = $this->plugin_install( 'wholesalex' );
			if ( $status && ! is_wp_error( $status ) ) {
				$activate_status = activate_plugin( 'wholesalex/wholesalex.php', '', false, true );
				if ( is_wp_error( $activate_status ) ) {
					wp_send_json_error( array( 'message' => __( 'WholesaleX Activation Failed!', 'multi-vendor-marketplace-b2b-for-wholesalex-dokan' ) ) );
				}
			} else {
				wp_send_json_error( array( 'message' => __( 'WholesaleX Installation Failed!', 'multi-vendor-marketplace-b2b-for-wholesalex-dokan' ) ) );
			}
		} else {
			$is_wc_active = is_plugin_active( 'wholesalex/wholesalex.php' );
			if ( ! $is_wc_active ) {
				$activate_status = activate_plugin( 'wholesalex/wholesalex.php', '', false, true );
				if ( is_wp_error( $activate_status ) ) {
					wp_send_json_error( array( 'message' => __( 'WholesaleX Activation Failed!', 'multi-vendor-marketplace-b2b-for-wholesalex-dokan' ) ) );
				}
			}
		}

		wp_send_json_success( __( 'Successfully Installed and Activated', 'multi-vendor-marketplace-b2b-for-wholesalex-dokan' ) );

	}

	/**
	 * Plugin Install
	 *
	 * @param string $plugin Plugin Slug.
	 * @return boolean
	 * @since 2.6.1
	 */
	public function plugin_install( $plugin ) {

		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $plugin,
				'fields' => array(
					'sections' => false,
				),
			)
		);

		if ( is_wp_error( $api ) ) {
			return $api->get_error_message();
		}

		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$result   = $upgrader->install( $api->download_link );

		return $result;
	}

	

}
