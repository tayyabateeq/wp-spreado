<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://synavos.com
 * @since      1.0.0
 *
 * @package    Spreado
 * @subpackage Spreado/public/partials
 */
?>
<?php
// Include WordPress core files
$path = $_SERVER['DOCUMENT_ROOT'];
include_once $path . '/wp-load.php';
class Spreado_Public_Signup{
    public function __construct(){

    }

    // Function to display the signup form
    function csf_signup_form () {
        $form_image = plugin_dir_url( __FILE__ ) . 'w-logo-blue.png';
        if(!is_user_logged_in()){
            ob_start();
            ?>
            <div class="signup-form-container">
                <div class="register-form-image">
                    <img src="<?php echo esc_url($form_image); ?>" alt="WordPress Logo">
                </div>
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                    <p>
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" required>
                    </p>
                    <p>
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" id="first_name" required>
                    </p>
                    <p>
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" required>
                    </p>
                    <p>
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                    </p>
                    <p>
                        <label for="country">Country</label>
                        <input type="text" name="country" id="country" required>
                    </p>
                    <p>
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required>
                    </p>
                    <p>
                        <input type="hidden" name="action" value="csf_register_user">
                        <input type="submit" value="Sign Up" class="button button-primary button-large">
                    </p>
                    <p>
                        Already have an account? <a href="<?php echo esc_url(wp_login_url()); ?>">Log in here</a>.
                    </p>
                </form>
            </div>
            <?php
            // Display any error or success messages
            if (isset($_GET['csf_message'])) {
                echo '<div class="csf-message">' . esc_html($_GET['csf_message']) . '</div>';
            }
        }
    }
}
// Instantiate the class to make the function available
$spreado_signup = new Spreado_Public_Signup();
$spreado_signup->csf_signup_form();  

?>

<style>
    body{
        background: #f0f0f1;
        min-width: 0;
        color: #3c434a;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        font-size: 13px;
        line-height: 1.4;
    }
    .signup-form-container {
        max-width: 400px;
        margin: auto;
        padding: 20px;
        margin-top: 50px;
    }
    .signup-form-container form{
        max-width: 400px;
        margin: auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
        margin-top: 20px;
    }
    /* Style for form labels */
    .signup-form-container label {
        display: block;
        margin-bottom: 5px;
    }

    /* Style for form inputs */
    .signup-form-container input[type="text"],
    .signup-form-container input[type="email"],
    .signup-form-container input[type="password"] {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 3px;
        box-sizing: border-box;
    }

    /* Style for form submit button */
    .signup-form-container input[type="submit"] {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 3px;
        background-color: #0073aa;
        color: #fff;
        cursor: pointer;
    }

    /* Style for error or success messages */
    .signup-form-container .csf-message {
        margin-top: 10px;
        padding: 10px;
        border-radius: 3px;
    }
    .register-form-image{
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>