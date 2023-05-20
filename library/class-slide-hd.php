<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Slide_Hd {

	private static $_instance = null;
	public $file;
	public $version;
	public $assets_url;
	public $assets_dir;

	public function __construct( $file, $version ) {
		$this->file 	= $file;
		$this->version 	= $version;
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/', $this->file ) ) );
		$this->assets_dir = trailingslashit( dirname($file) );
		add_action( 'admin_enqueue_scripts', array( $this, 'init_allassets' ), 10, 1 );
		add_action( 'admin_menu', array( $this, 'manual_book' ) );
	}

	public static function instance( $file, $version ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	}

	public function init_allassets( $hook = '' ) {
		global $post;
		if ( ( $hook != 'post-new.php' && $hook != 'post.php' ) || ( $post && $post->post_type !== 'slide-hd' ) ) {
			return;
	    }

		wp_enqueue_media();
		wp_register_script('slide-admin-js', esc_url( $this->assets_url ) . 'assets/js/admin.js', array( 'jquery' ), $this->version, true);
		wp_enqueue_script('slide-admin-js');
		wp_register_style('slide-admin-css', esc_url( $this->assets_url ) . 'assets/css/admin.css', array(), $this->version);
		wp_enqueue_style('slide-admin-css');
	}

	public function manual_book() {
		add_submenu_page(
			'edit.php?post_type=slide-hd',
			__( '<span class="premium-link" style="color: #f18500;">Manual Book</span>', 'super-simple-slider' ),
			__( '<span class="premium-link" style="color: #f18500;">Manual Book</span>', 'super-simple-slider' ),
			'manage_options',
			'terserah',
			array( $this, 'view_manual_book'),
			null
		);
	}

	public function view_manual_book() {
		$dir        = dirname( __FILE__ );
		$assets_dir = trailingslashit( $dir );
		include( $assets_dir . '../templates/content.php' );
	}

	public function register_post_type( $post_type = '', $plural = '', $single = '', $description = '', $options = array() ) {

		if ( ! $post_type || ! $plural || ! $single ) {
			return false;
		}

		$post_type = new Slide_Hd_Post_Type( slide_hd(), $post_type, $plural, $single, $description, $options );

		$this->post_type = $post_type;
		
		return $post_type;
	}
}