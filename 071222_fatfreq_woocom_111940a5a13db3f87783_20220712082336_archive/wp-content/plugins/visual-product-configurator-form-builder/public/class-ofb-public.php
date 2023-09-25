<?php

/**
* The public-facing functionality of the plugin.
*
* @link       http://www.orionorigin.com/
* @since      1.0.0
*
* @package    Ofb
* @subpackage Ofb/public
*/

/**
* The public-facing functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the public-facing stylesheet and JavaScript.
*
* @package    Ofb
* @subpackage Ofb/public
* @author     ORION <http://www.orionorigin.com/>
*/
class Ofb_Public {

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
	* @param      string $plugin_name       The name of the plugin.
	* @param      string $version    The version of this plugin.
	*/
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	* Register the stylesheets for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_styles() {
		wp_enqueue_style( 'vpc-ofb-public-css', plugin_dir_url( __FILE__ ) . 'css/ofb-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'validationEngine.jquery', plugin_dir_url( __FILE__ ) . 'css/validationEngine.jquery.css', array(), $this->version, 'all' );
	}

	/**
	* Register the JavaScript for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts() {
		wp_enqueue_script( 'vpc-ofb-public-js', plugin_dir_url( __FILE__ ) . 'js/ofb-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'jquery.validationEngine', plugin_dir_url( __FILE__ ) . 'js/jquery.validationEngine.js', array( 'jquery' ), false, false );
		$lang = get_locale();
		switch ( $lang ) {
			case 'fr_FR':
			wp_enqueue_script( 'jquery.validationEngine-fr', plugin_dir_url( __FILE__ ) . 'js/jquery.validationEngine-fr.js', array( 'jquery' ), '', false );
			break;
			case 'it_IT':
			wp_enqueue_script( 'jquery.validationEngine-it', plugin_dir_url( __FILE__ ) . 'js/languages/jquery.validationEngine-it.js', array( 'jquery' ), '', false );
			break;
			case 'en_EN':
			wp_enqueue_script( 'jquery.validationEngine-en', plugin_dir_url( __FILE__ ) . 'js/jquery.validationEngine-en.js', array( 'jquery' ), '', false );
			break;
			case 'de_DE':
			wp_enqueue_script( 'jquery.validationEngine-de', plugin_dir_url( __FILE__ ) . 'js/languages/jquery.validationEngine-de.js', array( 'jquery' ), '', false );
			break;
			case 'uk_UK':
			wp_enqueue_script( 'jquery.validationEngine-uk', plugin_dir_url( __FILE__ ) . 'js/languages/jquery.validationEngine-uk_UA', array( 'jquery' ), '', false );
			break;
			case 'es_ES':
				wp_enqueue_script( 'jquery.validationEngine-es', plugin_dir_url( __FILE__ ) . 'js/languages/jquery.validationEngine-es.js', array( 'jquery' ), '', false );
				break;

			case 'nl_NL':
				wp_enqueue_script('jquery.validationEngine-nl', plugin_dir_url(__FILE__) . 'js/languages/jquery.validationEngine-nl.js', array('jquery'), '', false);
				break;
			default:
			wp_enqueue_script( 'jquery.validationEngine-en', plugin_dir_url( __FILE__ ) . 'js/jquery.validationEngine-en.js', array( 'jquery' ), '', false );
			break;
		}
	}

	/**
	* add form builder datas
	* @param array $datas
	* @return boolean
	*/
	function add_vpc_ofb_data( $datas ) {
		$datas['isOfb'] = true;
		return $datas;
	}

	/**
	* save ofb files
	*/
	function save_ofb_files() {
		$files = $_POST['files'];
		// echo($files['filename']);
		$name                    = $_POST['name'];
		$ext                     = $_POST['ext'];
		$id                  = uniqid();
		$upload_dir      = wp_upload_dir();
		$generation_path = $upload_dir['basedir'];
		$generation_url  = $upload_dir['baseurl'];
		$final_file_path = $generation_path . '/VPC/file_' . $id . $ext;
		$final_file_url  = $generation_url . '/VPC/file_' . $id . $ext;
		if ( ! file_exists( $final_file_path ) ) {
			wp_mkdir_p( $generation_path . '/VPC/' );
		}
		move_uploaded_file( $_FILES['files']['tmp_name'], $final_file_path );
		$_SESSION['files_saved'][ $name ]      = $final_file_url;
		$_SESSION['files_saved_path'][ $name ] = $final_file_path;
		echo json_encode(
			array(
				'name' => $name,
				'url'  => $final_file_url,
			)
		);
		die();
	}

	/**
	* initialise ofb sessions
	*/
	function initialise_vpc_ofb_sessions() {
		if ( session_id() != ""  ) {
			@session_start();
		}
	}

}
