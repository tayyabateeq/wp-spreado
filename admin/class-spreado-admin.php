<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://synavos.com
 * @since      1.0.0
 *
 * @package    Spreado
 * @subpackage Spreado/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Spreado
 * @subpackage Spreado/admin
 * @author     Synavos <ask@synavos.com>
 */
class Spreado_Admin {

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
		// Hook to add admin menu
		add_action('admin_menu', array($this, 'spreado_add_admin_menu'));

		// Hook to register settings
		add_action('admin_init', array($this,'spreado_register_settings'));

		// add_action('show_user_profile', array($this, 'csf_show_api_id_field'));
		// add_action('edit_user_profile', array($this, 'csf_show_api_id_field'));

		// add_action('personal_options_update', array($this,'csf_save_api_id_field'));
		// add_action('edit_user_profile_update', array($this,'csf_save_api_id_field'));

		

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
		 * defined in Spreado_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Spreado_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/spreado-admin.css', array(), $this->version, 'all' );
		// Enqueue Bootstrap CSS
		wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
		
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
		 * defined in Spreado_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Spreado_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/spreado-admin.js', array( 'jquery' ), $this->version, false );
		// Enqueue Bootstrap JS
		wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), null, true);
	}

	function spreado_add_admin_menu() {
		// Define your custom icon URL
		$icon_url = plugin_dir_url( __FILE__ ) . 'favicon.png';
		add_menu_page(
			'Spreado',   			 	 	   				   // Page title
			'Spreado',     		  				  		  	  // Menu title
			'manage_options',          		 				 // Capability
			'spreado',            	   		 				 // Menu slug
			array($this, 'spread_register_form'),    		 // Callback function
			$icon_url, 											  	  // Icon URL
			20                      	   				   // Position
		);
		// Add submenu page for API URLs
		add_submenu_page(
			'spreado',                   // Parent slug
			'API URLs',                  // Page title
			'API URLs',                  // Menu title
			'manage_options',            // Capability
			'spreado_api_urls',          // Menu slug
			array($this, 'spread_api_urls_form') // Callback function
		);
	}

	function spreado_register_settings() {
		register_setting('spreado_options_group', 'spreado_public_key');
		register_setting('spreado_options_group', 'spreado_private_key');
		// New settings for API URLs
		register_setting('spreado_api_urls_group', 'spreado_register_api_url');
		register_setting('spreado_api_urls_group', 'spreado_reward_api_url');
	}

	function spread_register_form() {
		$logo_url = plugins_url('spreado-logo.svg', __FILE__);
		$public_key = get_option('spreado_public_key');
		$private_key = get_option('spreado_private_key');
		$is_readonly = !empty($public_key) && !empty($private_key);
	
		?>
		<div class="wrap">
			<div class="wp-heading-inline">
				<img src="<?php echo esc_url($logo_url); ?>" alt="Spreado Logo" style="vertical-align: middle; margin-right: 10px; height: 32px;" />
			</div>
			<form method="post" action="options.php" class="form" id="spreado-form">
				<div class="registration-header d-flex justify-content-between">
					<h1>Media Company Registration</h1>
					<?php if ($is_readonly): ?>
						<a href="https://qa.pact.synavos.net/login" class="go-to-dashboard">Go to Dashboard</a>
					<?php endif; ?>
				</div>
				<?php
				settings_fields('spreado_options_group');
				do_settings_sections('spreado');
				?>
				<div class="registration-form d-flex">
					<div class="form-group">
						<label for="spreado_public_key">Public Key</label>
						<input type="text" id="spreado_public_key" name="spreado_public_key" value="<?php echo esc_attr($public_key); ?>" class="form-control" <?php echo $is_readonly ? 'readonly' : ''; ?>/>
					</div>
					<div class="form-group">
						<label for="spreado_private_key">Private Key</label>
						<div class="input-group">
							<input type="password" id="spreado_private_key" name="spreado_private_key" value="<?php echo esc_attr($private_key); ?>" class="form-control" <?php echo $is_readonly ? 'readonly' : ''; ?> />
							<div class="input-group-append">
								<span class="input-group-text" id="spreado-toggle-private-key">
									<span class="dashicons dashicons-visibility" id="private-key-eye-icon"></span>
								</span>
							</div>
						</div>					
					</div>
				</div>
				<?php if (!$is_readonly) submit_button('Save'); ?>
				<?php if ($is_readonly): ?>
					<div class="d-flex flex-column align-items-center justify-content-center">
						<button type="button" id="change-keys" class="button button-secondary">Change Private and Public Key</button>
						<?php submit_button('Save', 'primary', 'submit', false); ?>
					</div>
				<?php endif; ?>
			</form>
		</div>
		<?php
	}

	function spread_api_urls_form() {
		$register_api_url = get_option('spreado_register_api_url');
		$reward_api_url = get_option('spreado_reward_api_url');
		$logo_url = plugins_url('spreado-logo.svg', __FILE__);
		?>
		<div class="wrap">
			<div class="wp-heading-inline">
				<img src="<?php echo esc_url($logo_url); ?>" alt="Spreado Logo" style="vertical-align: middle; margin-right: 10px; height: 32px;" />
			</div>
			<form method="post" action="options.php">
				<div class="registration-header">
					<h1>API URLs Settings</h1>
				</div>
				<?php
				settings_fields('spreado_api_urls_group');
				do_settings_sections('spreado_api_urls');
				?>
				<div class="registration-form d-flex">
					<div class="form-group">
						<label for="spreado_register_api_url">Register API URL</label>
						<input type="url" name="spreado_register_api_url" value="<?php echo esc_attr($register_api_url); ?>" class="form-control"/>
					</div>
					<div class="form-group">
						<label for="spreado_reward_api_url">Reward API URL</label>
						<input type="url" name="spreado_reward_api_url" value="<?php echo esc_attr($reward_api_url); ?>" class="form-control" />
					</div>
				</div>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
	// Add a custom user meta field to the user profile edit screen
	function csf_show_api_id_field($user) {
		?>
		<h3><?php _e("Additional Information", "blank"); ?></h3>

		<table class="form-table">
			<tr>
				<th><label for="api_id"><?php _e("API ID"); ?></label></th>
				<td>
					<input type="text" name="api_id" id="api_id" value="<?php echo esc_attr(get_user_meta($user->ID, 'api_id', true)); ?>" class="regular-text" /><br />
					<span class="description"><?php _e("This is the API ID associated with this user."); ?></span>
				</td>
			</tr>
		</table>
		<?php
	}

	// Save custom user meta field when user profile is updated
function csf_save_api_id_field($user_id) {
    if (current_user_can('edit_user', $user_id)) {
        update_user_meta($user_id, 'api_id', sanitize_text_field($_POST['api_id']));
    }
}

}
