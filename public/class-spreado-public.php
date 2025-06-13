<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://synavos.com
 * @since      1.0.0
 *
 * @package    Spreado
 * @subpackage Spreado/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Spreado
 * @subpackage Spreado/public
 * @author     Synavos <ask@synavos.com>
 */
class Spreado_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Shortcode to display the signup form
		// add_shortcode('csf_signup_form', array($this, 'csf_signup_form'));

		// Handle user registration
		// add_action('admin_post_nopriv_csf_register_user', array($this, 'csf_register_user'));
		// add_action('admin_post_csf_register_user', array($this, 'csf_register_user'));
		// add_action('user_register', array($this, 'csf_register_user'), 10, 1);

		// Hook into the action that triggers when a new user is registered
		add_action('user_register', array($this, 'register_user_to_spreado'));

		// Hook the functions to actions for add api id meta in the user profile
		add_action('show_user_profile', array($this, 'csf_show_api_id_field'));
		add_action('edit_user_profile', array($this,'csf_show_api_id_field'));
		add_action('personal_options_update', array($this,'csf_save_api_id_field'));
		add_action('edit_user_profile_update', array($this,'csf_save_api_id_field'));

		// Hook into WooCommerce order completion event for product purchases
        add_action('woocommerce_order_status_completed', array($this, 'csf_handle_purchase'), 10, 1);
		add_action('woocommerce_order_status_processing', array($this, 'csf_handle_purchase'), 10, 1);

        // Hook into comment creation event for WooCommerce product reviews
        add_action('comment_post', array($this, 'csf_handle_product_review'), 10, 2);
		// Add link to signup page on wp-login.php
		// add_action( 'login_message', array($this, 'add_signup_link_to_login_page' ));

		// Hook into plugin activation to register existing users on Spreado
        add_action('init', array($this, 'register_existing_users_to_spreado'));
		

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
		 * defined in Spreado_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Spreado_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/spreado-public.css', array(), $this->version, 'all' );

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
		 * defined in Spreado_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Spreado_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/spreado-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
     * Function to handle product purchases
     */
    public function csf_handle_purchase($order_id) {
		// Get user ID from order
		$order = wc_get_order($order_id);
		$user_id = $order->get_customer_id();
	
		if ($user_id) { 
			$api_id = get_user_meta($user_id, 'api_id', true);
	
			if ($api_id) {
				$api_url = get_option('spreado_reward_api_url');
				$public_key = get_option('spreado_public_key');
				$private_key = get_option('spreado_private_key');

				$content_data = array();

				// Get content data for purchase
				$items = $order->get_items();
				foreach ($items as $item) {
					$product = $item->get_product();
					$content_data = array(
						'title' => $product->get_name(),
						'image' => wp_get_attachment_url($product->get_image_id()),
						'id' =>  (string) $product->get_id(),
						'info' => $product->get_description()
					);
					break;
				}
				$body = json_encode(array(
					'subscriberId' 	 => $api_id,
					'contentTitle' 	 => $content_data['title'],
					'contentImage' 	 => $content_data['image'],
					'contentId'	   	 => $content_data['id'],
					'rewardType'   	 => 'BUY',
					'rewardCategory' => 'PRODUCT',
					'contentInfo' 	 => $content_data['info']
				));
	
				$response = wp_remote_post($api_url, array(
					'method' => 'POST',
					'headers' => array(
						'Content-Type' => 'application/json',
						'publicKey' => $public_key,
						'privateKey' => $private_key
					),
					'body' => $body
				));
	
				if (is_wp_error($response)) {
					wp_die('Reward API call failed: ' . $response->get_error_message());
				} else {
					$response_data = wp_remote_retrieve_body($response);
				}
			}
		}
	}	
	
	/**
	 * Function to handle product reviews
	 */
	public function csf_handle_product_review($comment_id, $comment_approved) {
		if ($comment_approved == 1) {
			$comment = get_comment($comment_id);
			if ($comment && get_post_type($comment->comment_post_ID) == 'product') {
				$user_id = $comment->user_id;
	
				if (is_user_logged_in() && $user_id) {
					$api_id = get_user_meta($user_id, 'api_id', true);
	
					if ($api_id) {
						$api_url = get_option('spreado_reward_api_url');
						$public_key = get_option('spreado_public_key');
						$private_key = get_option('spreado_private_key');
	
						// Get content data for comment
						$product_id = $comment->comment_post_ID;
						$content_data = array(
							'title' => get_the_title($product_id),
							'image' => wp_get_attachment_url(get_post_thumbnail_id($product_id)),
							'id' => $product_id,
							'info' => $comment->comment_content
						);
	
						$body = json_encode(array(
							'subscriberId' => $api_id,
							'contentTitle' => $content_data['title'],
							'contentImage' => $content_data['image'],
							'contentId' => $content_data['id'],
							'rewardType' => 'COMMENT',
							'rewardCategory' => 'ARTICLE',
							'contentInfo' => $content_data['info']
						));
	
						$response = wp_remote_post($api_url, array(
							'method' => 'POST',
							'headers' => array(
								'Content-Type' => 'application/json',
								'publicKey' => $public_key,
								'privateKey' => $private_key
							),
							'body' => $body
						));
	
						if (is_wp_error($response)) {
							wp_die('Reward API call failed: ' . $response->get_error_message());
						} else {
							$response_data = wp_remote_retrieve_body($response);
							// Handle response if needed
						}
					}
				}
			}
		}
	}
	

	
	// Add a function to handle user registration
	function register_user_to_spreado($user_id) {
		// Retrieve user information
		$user_info = get_userdata($user_id);
		$first_name = $user_info->first_name;
		$last_name = $user_info->last_name;
		$email = $user_info->user_email;
		$password = $user_info->user_pass; // Generate a random password
		// $country = get_user_meta($user_id, 'billing_country', true);

		// Prepare data for the Spreado API request
		$api_url = get_option('spreado_register_api_url');
		$public_key = get_option('spreado_public_key');
		$private_key = get_option('spreado_private_key');
		// $status = 'ACTIVE'; // Assuming a default status

		$body = json_encode(array(
			'firstName' => !empty($first_name) ? $first_name : ' ',
			'lastName' => !empty($last_name) ? $last_name : ' ',
			'country' => !empty($country) ? $country : ' ',
			'email' => $email,
			// 'password' => $password,
			// 'status' => $status
		));

		$response = wp_remote_post($api_url, array(
			'method' => 'POST',
			'headers' => array(
				'Content-Type' => 'application/json',
				'publicKey' => $public_key,
				'privateKey' => $private_key
			),
			'body' => $body
		));

		if (is_wp_error($response)) {
			// Handle error
			// error_log('Spreado API call failed: ' . $response->get_error_message());
			// wp_redirect(add_query_arg('csf_message', urlencode('Failed to register user with Spreado API.'), wp_get_referer()));
		} else {
			$response_data = json_decode(wp_remote_retrieve_body($response), true);

			if (isset($response_data['data']['_id'])) {
				$api_id = $response_data['data']['_id'];
				// Update user meta with API response
				update_user_meta($user_id, 'api_id', $api_id);
			} else {
				// Handle API response error
				// error_log('Spreado API response does not contain _id field.');
				// wp_redirect(add_query_arg('csf_message', urlencode('Spreado API response does not contain _id field.'), wp_get_referer()));
			}
		}
	}
	// Function to register existing users on Spreado if they don't have API ID
    public function register_existing_users_to_spreado() {
        // Get all users
        $users = get_users();

        // Iterate through users
        foreach ($users as $user) {
            // Check if API ID exists
            $api_id = get_user_meta($user->ID, 'api_id', true);

            // If API ID doesn't exist, register user on Spreado
            if (empty($api_id)) {
                $this->register_user_to_spreado($user->ID);
            }
        }
    }
	// Handle form submission and register user

	// function csf_register_user() {
	// 	if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['action']) && $_POST['action'] === 'csf_register_user') {
	// 		$username = sanitize_user($_POST['username']);
	// 		$first_name = sanitize_text_field($_POST['first_name']);
	// 		$last_name = sanitize_text_field($_POST['last_name']);
	// 		$country = sanitize_text_field($_POST['country']);
	// 		$email = sanitize_email($_POST['email']);
	// 		$password = $_POST['password'];
	
	// 		$errors = new WP_Error();
	
	// 		if (username_exists($username) || !validate_username($username)) {
	// 			$errors->add('username_error', 'Invalid or existing username');
	// 		}
	
	// 		if (!is_email($email) || email_exists($email)) {
	// 			$errors->add('email_error', 'Invalid or existing email');
	// 		}
	
	// 		if (empty($password)) {
	// 			$errors->add('password_error', 'Password cannot be empty');
	// 		}
	
	// 		if ($errors->get_error_code()) {
	// 			wp_redirect(add_query_arg('csf_message', urlencode(implode(', ', $errors->get_error_messages())), wp_get_referer()));
	// 			exit;
	// 		} else {
	// 			// Prepare data for the API request
	// 			// $api_url = 'https://api-qa.pact.synavos.net/subscriber';
	// 			$api_url = get_option('spreado_register_api_url');
	// 			$public_key = get_option('spreado_public_key');
	// 			$private_key = get_option('spreado_private_key');
	// 			$status = 'ACTIVE'; // Assuming a default status
	
	// 			$body = json_encode(array(
	// 				'firstName' => $first_name,
	// 				'lastName' => $last_name,
	// 				'country' => $country,
	// 				'email' => $email,
	// 				'password' => $password,
	// 				'status' => $status
	// 			));
	
	// 			$response = wp_remote_post($api_url, array(
	// 				'method' => 'POST',
	// 				'headers' => array(
	// 					'Content-Type' => 'application/json',
	// 					'publicKey' => $public_key,
	// 					'privateKey' => $private_key
	// 				),
	// 				'body' => $body
	// 			));
	// 			// Create the user
	// 			$user_id = wp_create_user($username, $password, $email);
	// 			if (is_wp_error($response)) {
	// 				wp_redirect(add_query_arg('csf_message', urlencode($response->get_error_message()), wp_get_referer()));
	// 				exit;
	// 			} 
	// 			else {
	// 				$response_data = json_decode(wp_remote_retrieve_body($response), true);
	
	// 				if (isset($response_data['data']['_id'])) {
	// 					$api_id = $response_data['data']['_id'];
	
	// 					if (!is_wp_error($user_id)) {
	// 						// Update user meta
	// 						update_user_meta($user_id, 'first_name', $first_name);
	// 						update_user_meta($user_id, 'last_name', $last_name);
	// 						update_user_meta($user_id, 'country', $country);
	// 						update_user_meta($user_id, 'api_id', $api_id);
	
	// 						wp_redirect(wp_login_url());

	// 						// wp_redirect(add_query_arg('csf_message', urlencode('Registration successful. API ID: ' . $api_id), wp_get_referer()));
	// 						exit;
	// 					} else {
	// 						wp_redirect(add_query_arg('csf_message', urlencode($user_id->get_error_message()), wp_get_referer()));
	// 						exit;
	// 					}
	// 				} else {
	// 					// wp_redirect(add_query_arg('csf_message', urlencode('API response does not contain _id field.'), wp_get_referer()));
	// 					exit;
	// 				}
	// 			}
	// 		}
	// 	}
	// }
	
	
	// Add a custom user meta field to the user profile edit screen
	function csf_show_api_id_field($user) {
		?>
		<h3><?php _e("Additional Information", "blank"); ?></h3>
	
		<table class="form-table">
			<tr>
				<th><label for="api_id"><?php _e("API ID"); ?></label></th>
				<td>
					<input type="text" name="api_id" id="api_id" value="<?php echo esc_attr(get_user_meta($user->ID, 'api_id', true)); ?>" class="regular-text" readonly/><br />
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
	
	function add_signup_link_to_login_page() {
		?>
		<p class="signup-link message">Don't have an account? <a href="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'partials/signup.php' ); ?>">Sign up</a></p>
		<?php
	}
	
	
}
