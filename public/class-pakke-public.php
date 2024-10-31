<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       cwa.mx
 * @since      1.0.0
 *
 * @package    Pakke
 * @subpackage Pakke/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Pakke
 * @subpackage Pakke/public
 * @author     CWA <info@cwa.mx>
 */
class Pakke_Public {

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
		 * defined in Pakke_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pakke_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pakke-public.css', array(), $this->version, 'all' );

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
		 * defined in Pakke_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pakke_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pakke-public.js', array( 'jquery' ), $this->version, false );

	}
    public function register_shortcodes() {
        add_shortcode( 'pakke_login_form', array( $this, 'pakke_login_form' ) );

    }
    function pakke_login_form() {

        ob_start();
        ?>

        <div class="container">
            <form  action="" method="post" id="pakke-login-form" class="pakke-forms">
                <label for="pakke-login-email">Email</label>
                <input type="text" name="pakke-login-email" id="pakke-login-email">
                <br>
                <label for="pakke-login-pass">Contraseña</label>
                <input type="password" name="pakke-login-pass" id="pakke-login-pass">
                <button type="button" class="btn_pakke" id="btn_pakke_login" > Acceder</button>


            </form>
        </div>

        <?php


        return ob_get_clean();
    }


}
