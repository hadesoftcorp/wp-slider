<?php
/**
* Plugin Name: Slide HD
* Plugin URI: https://www.your-site.com/
* Description: Untuk menampilkan gambar bergeser ke samping kanan atau ke samping kiri secara otomatis atau secara manual dengan cara swipe ke kiri atau swipe ke kanan
* Version: 0.1
* Author: Muh. Samsul Huda
* Author URI: https://www.your-site.com/
**/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SLIDE_HD_DEBUG', false );
define( 'SLIDE_HD_PLUGIN_VERSION', '0.1' );

// Load plugin class files.
require_once 'library/class-slide-hd.php';
require_once 'library/class-slide-hd-post-type.php';
require_once 'library/class-slide-hd-form-control.php';

function slide_hd() {
	$instance = Slide_Hd::instance( __FILE__, SLIDE_HD_PLUGIN_VERSION );
	
	return $instance;
}

slide_hd();

slide_hd()->register_post_type( 'slide-hd', __('Slide HDs', 'slide-hd'), __('Slide HD', 'slide-hd'), 'This is plugins created by Samsul Huda.', array(
	'labels'	=> array(
		'name'               => __('Slide HDs', 'slide-hd'),
		'singular_name'      => __('Slide HD', 'slide-hd'),
		'name_admin_bar'     => __('Slide HD', 'slide-hd'),
		'add_new'            => _x( 'Tambah Slide', 'slide-hd', 'slide-hd' ),
		'add_new_item'       => __( 'Tambah Slide', 'slide-hd' ),
		'edit_item'          => __( 'Edit Slide', 'slide-hd' ),
		'new_item'           => sprintf( __( 'Tambah %s', 'slide-hd' ), __('Slide HD', 'slide-hd') ),
		'all_items' 		 => __( 'Daftar Slide', 'slide-hd' ),
		'view_item'          => __( 'View Slide', 'slide-hd' ),
		'search_items'       => __( 'Search Slide', 'adslidemin-hd' ),
		'not_found'          => __( 'Slide Tidak Ditemukan', 'slide-hd' ),
		'not_found_in_trash' => __( 'Sampah Kosong', 'slide-hd' ),
		'parent_item_colon'  => sprintf( __( 'Parent %s' ), __('Slide HD', 'slide-hd') ),	
		'menu_name' => 'Slide HD',
	),
	'public'    => true,
	'publicly_queryable' => true,
	'exclude_from_search' => true, // Check if this is legit
	'menu_icon' => 'dashicons-excerpt-view'
) );


