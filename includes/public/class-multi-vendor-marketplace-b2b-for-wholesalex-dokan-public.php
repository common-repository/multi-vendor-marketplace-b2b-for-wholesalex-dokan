<?php

/**
 * The public functionality of the plugin.
 *
 * @link       https://www.wpxpo.com/
 * @since      1.0.0
 *
 * @package    Wholesalex_Dokan
 */

 use WHOLESALEX\WHOLESALEX_Dynamic_Rules;
 use WHOLESALEX\WHOLESALEX_Product;
 use WHOLESALEX_PRO\AccountPage;
 use WHOLESALEX_PRO\Conversation;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * The public functionality of the plugin.
 */
class Wholesalex_Dokan_Public {


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

	}
	

	/**
	 * Check WholesaleX Pro and conversation is activated or not
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_conversation_active() {

		return defined('WHOLESALEX_PRO_VER') && version_compare(WHOLESALEX_PRO_VER,'1.2.3','>=') && function_exists('wholesalex_pro') && 'yes' === wholesalex()->get_setting( 'wsx_addon_conversation' );
	}

	/**
	 * Add WholesaleX Dynamic Rules and Conversation Page in Vendor Dashboard
	 *
	 * @param array $urls URLs
	 * @return array
	 * @since 1.0.0
	 */
	public function add_wholesalex_menu_pages_dokan_vendor_dashboard_nav( $urls ) {
		if(!function_exists('wholesalex')) {
			return $urls;
		}
		if ( 'yes' === wholesalex()->get_setting( 'dokan_vendor_dynamic_rule_status', 'yes' ) ) {
			$urls['wholesalex-dynamic-rules'] = array(
				'title' => esc_html__( 'Dynamic Rules', 'multi-vendor-marketplace-b2b-for-wholesalex-dokan' ),
				'icon'  => '<i class="fas fa-solid fa-layer-group"></i>',
				'url'   => dokan_get_navigation_url( 'wholesalex-dynamic-rules' ),
				'pos'   => 55,
			);
		}
		if ( $this->is_conversation_active() && 'yes' === wholesalex()->get_setting( 'dokan_vendor_conversation_status', 'yes' ) ) {
			$urls['wholesalex-conversations'] = array(
				'title' => esc_html__( 'Conversations', 'multi-vendor-marketplace-b2b-for-wholesalex-dokan' ),
				'icon'  => '<i class="fas fa-solid fa-comments"></i>',
				'url'   => dokan_get_navigation_url( 'wholesalex-conversations' ),
				'pos'   => 56,
			);
		}
		 return $urls;
	}

	/**
	 * Add WholesaleX Dynamic Rules and Conversation Page Content in Vendor Dashboard Query
	 *
	 * @param array $query_vars Query Vars
	 * @return void
	 * @since 1.0.0
	 */
	public function add_wholesalex_menu_pages_dokan_dashboard_query( $query_vars ) {
		if(!function_exists('wholesalex')) {
			return $query_vars;
		}
		if ( 'yes' === wholesalex()->get_setting( 'dokan_vendor_dynamic_rule_status', 'yes' ) ) {
			$query_vars['wholesalex-dynamic-rules'] = 'wholesalex-dynamic-rules';
		}
		if ( $this->is_conversation_active() && 'yes' === wholesalex()->get_setting( 'dokan_vendor_conversation_status', 'yes' ) ) {
			$query_vars['wholesalex-conversations'] = 'wholesalex-conversations';
		}
		return $query_vars;
	}

	public function load_menu_pages_template( $query_vars ) {
		if ( isset( $query_vars['wholesalex-dynamic-rules'] ) ) {
			?>
			<div class="dokan-dashboard-wrap multi-vendor-marketplace-b2b-for-wholesalex-dokan-dashboard-wrap">
				<?php do_action( 'dokan_dashboard_content_before' ); ?>

				<div class="dokan-dashboard-content multi-vendor-marketplace-b2b-for-wholesalex-dokan-dashboard-content">
					<?php do_action( 'dokan_help_content_inside_before' ); ?>

					<div class="dashboard-content-area">
						<article class="dashboard-content-area">
							<?php $this->dynamic_rules_content(); ?>
						</article>
					</div>

					<?php do_action( 'dokan_dashboard_content_inside_after' ); ?>
				</div>

				<?php do_action( 'dokan_dashboard_content_after' ); ?>

			</div>
			<?php
		}
		if ($this->is_conversation_active() && isset( $query_vars['wholesalex-conversations'] )  ) {
			?>
			<div class="dokan-dashboard-wrap multi-vendor-marketplace-b2b-for-wholesalex-dokan-dashboard-wrap">
			<?php do_action( 'dokan_dashboard_content_before' ); ?>

				<div class="dokan-dashboard-content multi-vendor-marketplace-b2b-for-wholesalex-dokan-dashboard-content">
				<?php do_action( 'dokan_help_content_inside_before' ); ?>

					<div class="dashboard-content-area">
						<article class="dashboard-content-area">
						<?php
						$this->conversations_content();
						?>
						</article>
					</div>

						<?php do_action( 'dokan_dashboard_content_inside_after' ); ?>
				</div>

					<?php do_action( 'dokan_dashboard_content_after' ); ?>

			</div>
				<?php

		}
	}

	/**
	 * Add Dokan Related Settings on WholesaleX Settings Page
	 *
	 * @param array $fields Settings Fields.
	 * @return array
	 */
	public function dokan_wholesalex_settings_field( $fields ) {
		$settings = array(
			'dokan_wholesalex' => array(
				'label' => __( 'Dokan Integration', 'multi-vendor-marketplace-b2b-for-wholesalex-dokan' ),
				'attr'  => array(
					'dokan_vendor_dynamic_rule_status' => array(
						'type'    => 'switch',
						'label'   => __( 'Dynamic Rules', 'multi-vendor-marketplace-b2b-for-wholesalex-dokan' ),
						'desc'    => __( 'Enable Dynamic Rule feature for vendors.', 'multi-vendor-marketplace-b2b-for-wholesalex-dokan' ),
						'default' => 'yes',
					),
					'dokan_vendor_rolewise_wholesalex_price' => array(
						'type'    => 'switch',
						'label'   => __( 'Role-Based Pricing', 'multi-vendor-marketplace-b2b-for-wholesalex-dokan' ),
						'desc'    => __( 'Let vendors add wholesale prices based on user roles.', 'multi-vendor-marketplace-b2b-for-wholesalex-dokan' ),
						'default' => 'yes',
					),
					'dokan_vendor_product_wholesalex_section_status' => array(
						'type'    => 'switch',
						'label'   => __( 'WholesaleX Options', 'multi-vendor-marketplace-b2b-for-wholesalex-dokan' ),
						'desc'    => __( ' Enable WholesaleX options on product editing page.', 'multi-vendor-marketplace-b2b-for-wholesalex-dokan' ),
						'default' => 'yes',
					),

				),
			),
		);

		$fields = wholesalex()->insert_into_array( $fields, $settings );

		if($this->is_conversation_active()) {
			if(isset($fields['dokan_wholesalex'],$fields['dokan_wholesalex']['attr']) && is_array($fields['dokan_wholesalex']['attr'])) {
				$fields['dokan_wholesalex']['attr']['dokan_vendor_conversation_status'] = array(
					'type'    => 'switch',
					'label'   => __( 'Conversation', 'wholesalex' ),
					'desc'    => __( 'Enable WholesaleX conversation feature for vendors and marketplace admin.', 'wholesalex' ),
					'default' => 'yes',
				);			
			}
		}

		return $fields;
	}



	/**
	 * Check Current User Is Valid Dokan Seller
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_seller() {
		$status = false;
		if ( function_exists( 'dokan_is_user_seller' ) && function_exists( 'dokan_get_current_user_id' ) && function_exists( 'wholesalex' ) ) {
			$status = dokan_is_user_seller( dokan_get_current_user_id() );
		}
		return $status;
	}

	/**
	 * Get Vendor Dynamic Rules
	 *
	 * @param array $rules Dynamic Rules
	 * @since 1.0.0
	 * @return array
	 */
	public function get_dokan_vendors_dynamic_rules( $rules ) {
		// Check is in dokan seller dashboard
		// If current page is dokan seller dashboard, return all dynamic rules which is created by the vendor and its stuff.
		if ( function_exists('dokan_is_seller_dashboard') && dokan_is_seller_dashboard() ) {
			$vendor_rules = array();
			foreach ( $rules as $rule ) {
				if ( isset( $rule['created_from'] ) && 'dokan_vendor_dashboard' === $rule['created_from'] ) {
					$vendor_rules[] = $rule;
				}
			}
			return $vendor_rules;
		} else {
			return $rules;
		}
	}

	/**
	 * Set WholesaleX RestAPI Permission
	 *
	 * By Default Only The user who has manage_users capability, he can get restapi permission.
	 * If current user is dokan seller, then we allow restapi permission for this user.
	 *
	 * @param boolean $status WholesaleX RestAPI Permission
	 * @return boolean
	 * @since 1.0.0
	 */
	public function set_restapi_permission( $status ) {
		return $status || $this->is_seller();
	}


	/**
	 * Add a key to the dynamic rules, which is created by vendors
	 * This key is used to determine which dynamic rules is created by vendor
	 *
	 * @param array   $rule Dynamic Rule.
	 * @param boolean $is_frontend If the request comes from frontend/any vendor dashboard
	 * @return void
	 * @since 1.0.0
	 */
	public function add_meta_on_vendor_created_dynamic_rules( $rule, $is_frontend ) {

		if ( $is_frontend && $this->is_seller() ) {
			$user_id              = dokan_get_current_user_id();
			$rule['created_from'] = dokan_is_user_seller( $user_id ) ? 'dokan_vendor_dashboard' : '';
		}

		return $rule;
	}


	/**
	 * Set Dynamic Rule Types for Dokan Vendors
	 * All Dynamic Rules does not work for vendor, here specifiy which dynamic rules are available for vendors
	 *
	 * @param array $rule_types Rule Types
	 * @return array
	 * @since 1.0.0
	 */
	public function dynamic_rule_types_for_dokan_vendors( $rule_types ) {

		if ( function_exists( 'dokan_is_seller_dashboard' ) && $this->is_seller() && dokan_is_seller_dashboard() ) {
			if ( isset( $rule_types['cart_discount'] ) ) {
				unset( $rule_types['cart_discount'] );
			}
			if ( isset( $rule_types['payment_discount'] ) ) {
				unset( $rule_types['payment_discount'] );
			}
			if ( isset( $rule_types['payment_order_qty'] ) ) {
				unset( $rule_types['payment_order_qty'] );
			}
			if ( isset( $rule_types['extra_charge'] ) ) {
				unset( $rule_types['extra_charge'] );
			}
			if ( isset( $rule_types['pro_extra_charge'] ) ) {
				unset( $rule_types['pro_extra_charge'] );
			}
			if ( isset( $rule_types['pro_restrict_product_visibility'] ) ) {
				unset( $rule_types['pro_restrict_product_visibility'] );
			}
			if ( isset( $rule_types['restrict_product_visibility'] ) ) {
				unset( $rule_types['restrict_product_visibility'] );
			}
		}

		return $rule_types;
	}

	/**
	 * Set Dynamic Rule Filter for Dokan Vendors
	 * All Dynamic Rules Filter does not work for vendor, here specifiy which dynamic rules filter are available for vendors
	 *
	 * @param array $options Dynamic Rules Filter Options
	 * @return array
	 * @since 1.0.0
	 */
	public function dynamic_rules_product_filter_dokan_vendors( $options ) {

		if ( function_exists( 'dokan_is_seller_dashboard' ) && $this->is_seller() && dokan_is_seller_dashboard() ) {
			if ( isset( $options['all_products'] ) ) {
				unset( $options['all_products'] );
			}
			if ( isset( $options['cat_in_list'] ) ) {
				unset( $options['cat_in_list'] );
			}
			if ( isset( $options['cat_not_in_list'] ) ) {
				unset( $options['cat_not_in_list'] );
			}
		}

		return $options;
	}

	/**
	 * Set Dynamic Rule Conditions for Dokan Vendors
	 * All Dynamic Rules Conditions does not work for vendor, here specifiy which dynamic rules Conditions are available for vendors
	 *
	 * @param array $options Dynamic Rules Conditions Options
	 * @return array
	 * @since 1.0.0
	 */
	public function dynamic_rules_conditions_dokan_vendors( $options ) {

		if ( function_exists( 'dokan_is_seller_dashboard' ) && $this->is_seller() && dokan_is_seller_dashboard() ) {
			if ( isset( $options['cart_total_qty'] ) ) {
				unset( $options['cart_total_qty'] );
			}
			if ( isset( $options['cart_total_value'] ) ) {
				unset( $options['cart_total_value'] );
			}
			if ( isset( $options['cart_total_weight'] ) ) {
				unset( $options['cart_total_weight'] );
			}
		}

		return $options;
	}


	/**
	 * Set Dynamic Rules Page Content on Vendor Dashboard
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function dynamic_rules_content() {
		wp_enqueue_script( 'wholesalex_dynamic_rules' );
		$__dynamic_rules = array_values( wholesalex()->get_dynamic_rules_by_user_id() );
		$__dynamic_rules = apply_filters( 'wholesalex_get_all_dynamic_rules', array_values( $__dynamic_rules ) );

		if ( empty( $__dynamic_rules ) ) {
			$__dynamic_rules = array(
				array(
					'id'    => floor( microtime( true ) * 1000 ),
					'label' => __( 'New Rule', 'wholesalex' ),
				),
			);
		}
		wp_localize_script(
            'wholesalex_dynamic_rules',
            'whx_dr',
            apply_filters(
                'wholesalex_dokan_dynamic_rules_localize_data',
                array(
                    'fields' => WHOLESALEX_Dynamic_Rules::get_dynamic_rules_field(),
                    'rule'   => $__dynamic_rules,
                    'nonce'  => wp_create_nonce('whx-export-dynamic-rules'),
                    'i18n'     => array(
                        'dynamic_rules' => __('Dynamic Rules', 'wholesalex'),
                        'please_fill_all_fields' => __('Please Fill All Fields.', 'wholesalex'),
                        'minimum_product_quantity_should_greater_then_free_product_qty' => __('Minimum Product Quantity Should Greater then Free Product Quantity.', 'wholesalex'),
                        'rule_title' => __('Rule Title', 'wholesalex'),
                        'create_dynamic_rule' => __('Create Dynamic Rule', 'wholesalex'),
                        'import' => __('Import', 'wholesalex'),
                        'export' => __('Export', 'wholesalex'),
                        'untitled' => __('Untitled', 'wholesalex'),
                        'duplicate_of' => __('Duplicate of ', 'wholesalex'),
                        'delete_this_rule' => __('Delete this Rule.', 'wholesalex'),
                        'duplicate_this_rule' => __('Duplicate this Rule.', 'wholesalex'),
                        'show_hide_rule_details' => __('Show/Hide Rule Details.', 'wholesalex'),
                        'vendor' => __('Vendor #', 'wholesalex'),
                        'untitled_rule' => __('Untitled Rule', 'wholesalex'),
                        'error_occured' => __('Error Occured!', 'wholesalex'),
                        'map_csv_fields_to_dynamic_rules' => __('Map CSV Fields to Dynamic Rules', 'wholesalex'),
                        'select_field_from_csv_msg' => __('Select fields from your CSV file to map against role fields, or to ignore during import.', 'wholesalex'),
                        'column_name' => __('Column name', 'wholesalex'),
                        'map_to_field' => __('Map to field', 'wholesalex'),
                        'do_not_import' => __('Do not import', 'wholesalex'),
                        'run_the_importer' => __('Run the importer', 'wholesalex'),
                        'importing' => __('Importing', 'wholesalex'),
                        'upload_csv' => __('Upload CSV', 'wholesalex'),
                        'you_can_upload_only_csv_file_format' => __('You can upload only csv file format', 'wholesalex'),
                        'your_dynamic_rules_are_now_being_importing' => __('Your Dynamic Rules are now being imported..', 'wholesalex'),
                        'update_existing_rules' => __('Update Existing Rules', 'wholesalex'),
                        'select_update_exising_rule_msg' => __('Selecting "Update Existing Rules" will only update existing rules. No new rules will be added.', 'wholesalex'),
                        'continue' => __('Continue', 'wholesalex'),
                        'dynamic_rule_imported' => __(' Dynamic Rules Imported.', 'wholesalex'),
                        'dynamic_rule_updated' => __(' Dynamic Rules Updated.', 'wholesalex'),
                        'dynamic_rule_skipped' => __(' Dynamic Rules Skipped.', 'wholesalex'),
                        'dynamic_rule_failed' => __(' Dynamic Rules Failed.', 'wholesalex'),
                        'view_error_logs' => __('View Error Logs', 'wholesalex'),
                        'dynamic_rule' => __('Dynamic Rule', 'wholesalex'),
                        'reason_for_failure' => __('Reason for failure', 'wholesalex'),
                        'import_dynamic_rules' => __('Import Dynamic Rules', 'wholesalex'),
                )
                )
            )
        );
		?>
		<div id="_wholesalex_dynamic_rules_frontend"></div>
		<?php
	}


	/**
	 * Add WholesaleX Div on Each Variations Pricing Section
	 * Will Inject JS to this div
	 *
	 * @param int     $loop Loop
	 * @param array   $variation_data Variation Data
	 * @param WP_Post $variation Variation
	 * @return void
	 */
	public function product_variations_wholesale_pricing( $loop, $variation_data, $variation ) {
		?>
				<div class="_wholesalex_single_product_settings options-group show_if_variable"></div>
		<?php
	}


	/**
	 * Add Wholesale Pricing Section
	 *
	 * @param WP_Post $post Post
	 * @param string  $post_id Product ID
	 * @return void
	 */
	public function add_wholesalex_pricing( $post, $post_id ) {
		if ( ! $this->is_seller() ) {
			return;
		}
		wp_enqueue_script( 'wholesalex_product' );

		$discounts = array();
		if ( $post_id ) {
			$product = wc_get_product( $post_id );
			if ( $product ) {
				$is_variable = 'variable' === $product->get_type();
				if ( $is_variable ) {
					if ( $product->has_child() ) {
						$childrens = $product->get_children();
						foreach ( $childrens as $key => $child_id ) {
							$discounts[ $child_id ] = wholesalex()->get_single_product_discount( $child_id );
						}
					}
				} else {
					$discounts[ $post_id ] = wholesalex()->get_single_product_discount( $post_id );
				}
			}
		}

		wp_localize_script(
			'wholesalex_components',
			'wholesalex_single_product',
			array(
				'fields'             => WHOLESALEX_Product::get_product_fields(),
				'discounts'          => $discounts,
				'is_dokan_dashboard' => true,
			),
		);
		?>
		<div class="_wholesalex_single_product_settings options-group hide_if_external hide_if_variable"></div>
		<?php
	}

	/**
	 * Save Variation Product Wholesale Price
	 *
	 * @param string $vairation_id Variation ID
	 * @return void
	 * @since 1.0.0
	 */
	public function save_variation_product_wholesalex_price( $vairation_id ) {
		if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_key( $_POST['security'] ), 'save-variations' ) ) {
			return;
		}

		if ( isset( $_POST[ 'wholesalex_single_product_tiers_' . $vairation_id ] ) ) {
			$product_discounts = $this->sanitize( json_decode( wp_unslash( $_POST[ 'wholesalex_single_product_tiers_' . $vairation_id ] ), true ) ); //phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wholesalex()->save_single_product_discount( $vairation_id, $product_discounts );
		}
	}

	/**
	 * WholesaleX Section on Dokan Edit Product Page
	 * This section contain selection of tier layout and product visibility control
	 *
	 * @since 1.0.0
	 */
	public function wholesalex_section() {
		global $post;

		// Enqueue wholesalex_product Script, which already registered in wholesalex free version
		wp_enqueue_script( 'wholesalex_product' );

		// Get product wholesalex settings
		$settings = wholesalex()->get_single_product_setting();

		// Localize WholesaleX Fields for this section and settings.
		wp_localize_script(
			'wholesalex_components',
			'wholesalex_product_tab',
			array(
				'fields'   => WHOLESALEX_Product::get_product_settings(),
				'settings' => isset( $settings[ $post->ID ] ) ? $settings[ $post->ID ] : array(),
			),
		);
		$wholesalex_section_heading     = apply_filters( 'wholesalex_dokan_single_product_section_heading', __( 'WholesaleX', 'wholesalex' ) );
		$wholesalex_section_sub_heading = apply_filters( 'wholesalex_dokan_single_product_section_subheading', __( 'Set advanced Wholesale options', 'wholesalex' ) );
		?>
			<div class="dokan_wholesalex dokan-edit-row dokan-clearfix"> 
				<div class="dokan-section-heading" data-togglehandler="dokan_wholesalex">
					<h2><i class="fas fa-cog" aria-hidden="true"></i> <?php echo esc_html( $wholesalex_section_heading ); ?></h2>
					<p><?php echo esc_html( $wholesalex_section_sub_heading ); ?></p>
					<a href="#" class="dokan-section-toggle">
						<i class="fas fa-sort-down fa-flip-vertical" aria-hidden="true"></i>
					</a>
					<div class="dokan-clearfix"></div>
				</div>
				<div class="dokan-section-content" style="display: none;">
					<div class="panel woocommerce_options_panel" id="wholesalex_tab_data"></div>
				</div>
			</div>
		<?php
	}

	/**
	 * Save New Product Rolewise Wholesale and Tier Pricing
	 *
	 * @param int   $product_id Product ID.
	 * @param array $postdata Product Data.
	 * @return void
	 */
	public function save_single_product_prices( $product_id, $postdata ) {
		if(!$this->is_seller()) {
			return;
		}
		if ( isset( $postdata[ 'wholesalex_single_product_tiers_' . $product_id ] ) ) {
			$product_discounts = $this->sanitize( json_decode( wp_unslash( $postdata[ 'wholesalex_single_product_tiers_' . $product_id ] ), true ) ); //phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Missing
			wholesalex()->save_single_product_discount( $product_id, $product_discounts );
		}
	}




	// Conversations Releated Functions

	/**
	 * Modify Get Conversations Query on WholesaleX Conversatiosn Page to hide dokan vendor messages
	 *
	 * @param array  $args WP_Query Args
	 * @param boolen $is_frontend  check is the request comes from frontend/vendor dashboard
	 * @return array
	 */
	public function modify_conversations_args( $args, $is_frontend ) {
		if ( $is_frontend && function_exists('dokan_get_current_user_id') ) {
			$q = array(
				'key'     => 'wholesalex_conversation_dokan_vid',
				'value'   => dokan_get_current_user_id(),
				'compare' => '=',
			);
			if ( ! isset( $args['meta_query'] ) && is_array( $args['meta_query'] ) ) {
				$args['meta_query'] = array();
			}
			$args['meta_query'][] = $q;
		} else {
			$q = array(
				'key'     => 'wholesalex_conversation_dokan_vid',
				'compare' => 'NOT EXISTS',
			);
			if ( ! isset( $args['meta_query'] ) && is_array( $args['meta_query'] ) ) {
				$args['meta_query'] = array();
			}
			$args['meta_query'][] = $q;
		}

		return $args;
	}

	/**
	 * Conversations Page Content
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function conversation_page_content() {
		wp_enqueue_script( 'whx_conversation' );
		wp_enqueue_script( 'wholesalex_node_vendors' );
		wp_enqueue_script( 'wholesalex_components' );

		$heading_data = array();

		// Prepare as heading data
		foreach ( Conversation::get_wholesalex_conversation_columns() as $key => $value ) {
			$data               = array();
			$data['all_select'] = '';
			$data['name']       = $key;
			$data['title']      = $value;
			if ( 'action' == $key ) {
				$data['type'] = '3dot';
			} else {
				$data['type'] = 'text';
			}

			$heading_data[ $key ] = $data;
		}

		$heading_data['title']['status']  = 'yes';
		$heading_data['user']['status']   = 'yes';
		$heading_data['status']['status'] = 'yes';
		$heading_data['type']['status']   = 'yes';
		$heading_data['email']['status']  = 'yes';
		$heading_data['action']['status'] = 'yes';

		wp_localize_script(
            'whx_conversation',
            'whx_conversation',
            apply_filters(
                'wholesalex_dokan_conversation_localize_data',
                array(
                    'heading'              => $heading_data,
                    'bulk_actions'         => wholesalex()->insert_into_array(Conversation::get_conversation_bulk_action(), array('' => __('Bulk Actions', 'wholesalex')), 0),
                    'statuses'             => wholesalex()->insert_into_array(
                        Conversation::get_conversation_status(),
                        array('' => __('Select Status', 'wholesalex')),
                        0
                    ),
                'types'                => wholesalex()->insert_into_array(
                    Conversation::get_conversation_types(),
                    array('' => __('Select Type', 'wholesalex')),
                    0
                ),
                'new_conversation_url' => admin_url('post-new.php?post_type=wsx_conversation'),
                'post_statuses'        => Conversation::get_post_statuses(),
                'frontend_url'         => dokan_get_navigation_url('wholesalex-conversations'),
                )
            )
        );

		?>
			<div id="wholesalex_conversation_root_frontend"> </div>
		<?php
	}

	/**
	 * Conversation Lists and View Content
	 */
	public function conversations_content() {
		$action = 'listing';
		if ( isset( $_GET['conv'] ) && ! empty( $_GET['conv'] ) ) {
			$action = 'view';
		}
		switch ( $action ) {
			case 'listing':
				$this->conversation_page_content();
				break;
			case 'view':
				wp_enqueue_style( 'dashicons' );
				wp_enqueue_script( 'wholesalex-pro-public' );
				wp_localize_script(
					'wholesalex-pro-public',
					'wholesalex_conversation',
					array(
						'create_nonce'       => wp_create_nonce( 'wholesalex-new-conversation' ),
						'recaptcha_site_key' => wholesalex()->get_setting( '_settings_google_recaptcha_v3_site_key' ),
						'recaptcha_status'   => wholesalex()->get_setting( 'wsx_addon_recaptcha' ),
					)
				);

				do_action( 'wholesalex_conversation_metabox_content_account_page' );

				AccountPage::view_conversation( sanitize_key( $_GET['conv'] ), dokan_get_navigation_url( 'wholesalex-conversations' ) );
				break;

			default:
				// code...
				break;
		}
	}


	/**
	 * Add Vendors Fields in New Conversations
	 *
	 * @return void
	 */
	public function add_vendor_fields_in_conversation() {

		// if conversation enabled for vendor then
		$all_vendors = get_users(
			array(
				'meta_key' => 'dokan_store_name',
				'fields'   => 'ids',
			)
		);

		?>
		<div class="wsx-conversation-element wsx-conversation-form-vendor-selection">
				<label for="text"><?php echo esc_html( wholesalex()->get_language_n_text( '_language_conversations_vendor', __( 'Vendor', 'wholesalex' ) ) ); ?></label>
				<select name="conversation_vendor" id="conversation_vendor">
				<option value=''>Select Vendor </option>
				<?php
				foreach ( $all_vendors as $vid ) {
					?>
						<option value="<?php echo esc_attr( $vid ); ?>"><?php echo esc_html( get_user_meta( $vid, 'dokan_store_name', true ) ); ?></option>
					<?php
				}
				?>
				</select>
			</div>

		<?php
	}


	/**
	 * Add Vendor as Valid Recipient
	 *
	 * @param int|string $conv_id Conversation ID
	 * @return void
	 */
	public function add_vendor_as_recipient( $conv_id ) {
		if ( isset( $_POST['wpnonce'] ) && wp_verify_nonce( sanitize_key( $_POST['wpnonce'] ), 'wholesalex-new-conversation' ) ) {
			if ( isset( $_POST['conversation_vendor'] ) && ! empty( $_POST['conversation_vendor'] ) ) {
				$vendor_id = sanitize_text_field( $_POST['conversation_vendor'] );
				update_post_meta( $conv_id, 'wholesalex_conversation_dokan_vid', $vendor_id );
			}
		 }
	}

	/**
	 * Add Vendor Columns on My Account Conversation Area
	 *
	 * @param array $columns Conversation Columns
	 * @return array
	 * @since 1.0.0
	 */
	public function add_vendor_columns( $columns ) {
		$columns = wholesalex()->insert_into_array( $columns, array( 'dokan_vendor' => wholesalex()->get_language_n_text( '_language_conversations_vendor', __( 'Vendor', 'wholesalex' ) ) ), 1 );
		return $columns;
	}

	/**
	 * Populate Vendor Column Data on Conversation Page (My account)
	 *
	 * @param string     $column_id Column Key.
	 * @param int|string $conv_id Conversation ID.
	 * @return void
	 */
	public function populate_vendor_column_data( $column_id, $conv_id ) {
		if ( 'dokan_vendor' == $column_id ) {
			$vendor_id  = get_post_meta( $conv_id, 'wholesalex_conversation_dokan_vid', true );
			$store_name = get_user_meta( $vendor_id, 'dokan_store_name', true );

			?>
				<td class="wsx-conversation-list-item">
					<?php echo esc_html( $store_name ); ?>
				</td>
			<?php

		}
	}

	/**
	 * Allow Vendors to View Conversation
	 *
	 * @param string     $status Conversation Status.
	 * @param string|int $author_id Conversation Author ID.
	 * @param int|string $conv_id Conversation ID
	 * @return boolean
	 */
	public function allow_vendor_to_view_conversation( $status, $author_id, $conv_id ) {
		$recipient_vid = get_post_meta( $conv_id, 'wholesalex_conversation_dokan_vid', true );
		if ( function_exists('dokan_get_current_user_id') && dokan_get_current_user_id() == $recipient_vid ) {
			$status = true;
		}

		return $status;

	}

	/**
	 * Add Vendor ID as Allowed Author
	 *
	 * @param array $allowed_authors Authors.
	 * @return array
	 */
	public function add_vendor_id_as_valid_post_author( $allowed_authors ) {
		if(function_exists('dokan_get_current_user_id')) {
			$allowed_authors[] = dokan_get_current_user_id();
		}

		return $allowed_authors;
	}


	/**
	 * Add Conversation Vendor Reply class
	 *
	 * @param string $class Class Name.
	 * @param string $author Author ID.
	 * @param string $conv_id Conversation ID
	 * @return void
	 */
	public function add_conversation_vendor_reply_class( $class, $author, $conv_id ) {
		if ( function_exists('dokan_get_current_user_id') && dokan_get_current_user_id() != $author ) {
			$class = 'wsx-reply-left';
		}

		return $class;
	}


	/**
	 * WholesaleX Dokan Sanitizer
	 *
	 * @param array $data Data.
	 * @since 1.0.0
	 * @return array $data Sanitized Array
	 */
	public function sanitize( $data ) {
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$data[ $key ] = $this->sanitize( $value );
			} else {
				$data[ $key ] = sanitize_text_field( $value );
			}
		}
		return $data;
	}


}
