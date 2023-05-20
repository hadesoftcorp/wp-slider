<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Slide_Hd_Post_Type {

	private static $_instance = null;
	public $parent = null;
	public $post_type;
	public $description;
	public $options;
	public $repeatable_fieldset_settings;

	public function __construct( $parent, $post_type = '', $plural = '', $single = '', $description = '', $options = array() ) {
		$this->parent = $parent;
		$this->post_type   = $post_type;
		$this->description = $description;
		$this->options     = $options;
		$this->repeatable_fieldset_settings = array(
			'repeatable' => true,
			'fields' => array(
				'slide_hd_gambar' => array(
					'type'			=> 'media_upload',
					'class'			=> '',
					'description'	=> '',
				),
				'slide_hd_alamat' => array(
					'type'			=> 'text',
					'class'			=> '',
					'description'	=> '',
				),
			)
		);

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'admin_init', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'proses_simpan_data' ) );
		add_shortcode( 'slidehd', array( $this, 'show_shortcode_slide_hd' ) );
	}

	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	}

	public function register_post_type() {

		$args = array(
			'labels'                => apply_filters( $this->post_type . '_labels', $this->options ),
			'description'           => $this->description,
			'public'                => true,
			'publicly_queryable'    => true,
			'exclude_from_search'   => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'query_var'             => true,
			'can_export'            => true,
			'rewrite'               => false,
			'capability_type'       => 'post',
			'has_archive'           => false,
			'hierarchical'          => false,
			'show_in_rest'          => true,
			'rest_base'             => $this->post_type,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports'              => array( 'title' ),
			'menu_position'         => 50,
			'menu_icon'             => 'dashicons-admin-post',
		);

		$args = array_merge( $args, $this->options );

		register_post_type( $this->post_type, apply_filters( $this->post_type . '_register_args', $args, $this->post_type ) );
	}

	public function add_meta_boxes() {
		add_meta_box( 'slide-hd-gambar', 'Upload Slide', array( $this, 'slide_hd_gambar' ), 'slide-hd', 'normal', 'default' );
		add_meta_box( 'slide-hd-shortcode', 'Shortcode', array( $this, 'slide_hd_shortcode' ), 'slide-hd', 'side', 'high' );
		add_meta_box( 'slide-hd-alamat', 'Alamat Url', array( $this, 'slide_hd_alamat' ), 'slide-hd', 'normal', 'default' );
	}

	public function slide_hd_gambar() {
		global $post;
		$slide_settings = get_post_meta( $post->ID, 'slide-hd', true );
		wp_nonce_field( 'otb_repeater_nonce', 'otb_repeater_nonce' );
		?>
		
		<div class="otb-postbox-container">

			<table class="otb-panel-container multi sortable repeatable" width="100%" cellpadding="0" cellspacing="0" border="0">
				<tbody class="container">
		<?php $this->create_super_simple_form_control( 'slide_hd_gambar', $this->repeatable_fieldset_settings ); ?>
				</tbody>
			</table>
			
		</div>
		
	<?php
	}
	
	public function slide_hd_shortcode() {
		global $post;
	?>
		<div class="text-input-with-button-container copyable">
			<input name="slide_hd_shortcode" value="<?php esc_html_e( '[slidehd]' ); ?>" readonly />
			<div class="wp-menu-image2 dashicons-before dashicons-admin-page copy" aria-hidden="true" style="cursor: pointer; background-color: #f1f1f1; border-top: 1px solid rgba(0, 0, 0, 0.2); border-right: 1px solid rgba(0, 0, 0, 0.2); border-bottom: 1px solid rgba(0, 0, 0, 0.2); "><br></div>
			<style type="text/css">div.wp-menu-image2:before {color: rgb(82 199 22 / 60%)!important; padding: 7px 7px!important;}</style>
			<div class="message"><?php esc_html_e( 'Copied to clipboard', 'slide-hd' ); ?></div>
		</div>
		<style type="text/css">
		.inside > #edit-slug-box {
			display: none;
		}
		</style>
	<?php
	}

	public function slide_hd_alamat() {
		global $post;
		$v 	   = "";
		$alamat 	   = get_post_meta( $post->ID, 'slide-hd', true );
		if (isset($alamat)) {
			if (isset($alamat['slide_hd_alamat'])) {
				$v = $alamat['slide_hd_alamat'];
			}
		}
	?>
		<div class="text-input-with-button-container">
			<input name="slide_hd_alamat" style="border: 1px solid rgba(0, 0, 0, 0.2)!important; background-color: transparent;" value="<?= $v; ?>" placeholder="Paste link disini untuk klik url gambar. Cnth:https://facebook.com/" />
		</div>
	<?php
	}
	
	public function proses_simpan_data( $post_id ) {
		if ( !isset( $_POST['otb_repeater_nonce'] ) || !wp_verify_nonce( $_POST['otb_repeater_nonce'], 'otb_repeater_nonce' ) )
			return;
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( !current_user_can( 'edit_post', $post_id ) )
			return;

		$sss_old = get_post_meta( $post_id, 'slid-hd', true );
		$sss_new = array();
		
		$repeatable_fieldset_settings = $this->repeatable_fieldset_settings['fields'];

        foreach ( $repeatable_fieldset_settings as $name => $config ) {
			$values_array = wp_unslash( $_POST[ $name ] );

			if (is_array($values_array)) {
				for ( $i=0; $i<count( $values_array ); $i++ ) {
					$sss_new[$i][ $name ] = $this->sanitize_field( $values_array[$i], $config['type'] );
				}
			}else{
				$sss_new[ $name ] = $this->sanitize_field( $values_array, $config['type'] );
			}
        }
        
		if ( !empty( $sss_new ) && $sss_new != $sss_old ) {
			update_post_meta( $post_id, 'slide-hd', $sss_new );
		} elseif ( empty( $sss_new ) && $sss_old ) {
			delete_post_meta( $post_id, 'slide-hd', $sss_old );
		}
	}

	function show_shortcode_slide_hd( $atts ) {
	    
		wp_register_script('slide-frontend-js', esc_url( $this->parent->assets_url ) . 'assets/js/ppp.min.js', array( 'jquery' ), $this->parent->version, true);
		wp_enqueue_script('slide-frontend-js');
// 		wp_enqueue_script("jquery");
		
		$args = array(
	        'post_type' => 'slide-hd',
	        'posts_per_page' => -1
	    );
		$qq = new WP_Query($args);
		if ($qq->have_posts() ) : 
		    
			ob_start();
			echo '<div style="margin-top: 0; margin-bottom: 24px;">
						<div style="margin-top: 0px; overflow: hidden;">
							<div style="margin-left: auto; margin-right: auto; position: relative; overflow: hidden; list-style: none; padding: 0; z-index: 1;">';
			echo '<div id="sentence" style="transition-duration: 300ms; position: relative; width: 100%; height: 100%; z-index: 1; display: flex; transition-property: transform; box-sizing: content-box;">';
			$loop = 0;
		    while ( $qq->have_posts() ) : $qq->the_post();
		    	$loop++;
				include( $this->parent->assets_dir .'templates/slider.php' );
		    endwhile;
		    $loop = $loop-1;
		    echo '</div>';
		    echo '</div></div></div>';
		    echo '<script type="text/javascript">
                jQuery(document).ready(function($){
                    var start = null;
                    document.getElementById("sentence").addEventListener("touchstart",function(event){
                    
                        if(event.touches.length === 1){
                            start = event.touches.item(0).clientX;
                        }else{
                            start = null;
                        }
                         
                    });
                 
                    document.getElementById("sentence").addEventListener("touchend",function(event){
                     
                        var offset = 100;//at least 100px are a swipe
                        if(start){
                            var end = event.changedTouches.item(0).clientX;
                    
                            if(end > start + offset){
                                kurangsatu();
                            }else if(end < start - offset ){
                                tambahsatu();
                            }
                        }
                     
                    });
 
					var n = 0;
					var down = false;
					var titikAwal = 0;
					
					document.getElementById("sentence").onmousedown = function(event) {
						down = true;
						titikAwal = event.pageX;
					}

					document.getElementById("sentence").onmouseup = function(event) {
						down = false;
						if (titikAwal > event.pageX) {
							if (n == '.$loop.'){}else{
								tambahsatu();
								titikAwal = 0;
							}
						}
						else if (event.pageX > titikAwal) {
							if (n == 0){}else{
								kurangsatu();
								titikAwal = 0;
							}
						}
						else if (event.pageX == titikAwal) {
							if(this.children[n].dataset.location){
								window.location.href = this.children[n].dataset.location;
							}
						}
					}

					setInterval(function() {
						if (down == false) {
							tambahsatu();
						}
					 }, 10000);

					function tambahsatu(){
					    if (n=='.$loop.') {
					    	n=0;
					    }else{
						    n++;
						}
					    var ddd = n*getWidth();
					    document.getElementById("sentence").style.transform = "translate3d(-" + ddd + "px, 0px, 0px)";
					}

					function kurangsatu(){
					    if (n==0) {
					    	n='.$loop.';
					    }else{
						    n--;
						}
					    var ddd = n*getWidth();
					    document.getElementById("sentence").style.transform = "translate3d(-" + ddd + "px, 0px, 0px)";
					}

					function getWidth() {
					  return Math.max(
					    document.body.scrollWidth,
					    document.documentElement.scrollWidth,
					    document.body.offsetWidth,
					    document.documentElement.offsetWidth,
					    document.documentElement.clientWidth
					  );
					}
                });
                
					</script>';
		    wp_reset_postdata();

		    return ob_get_clean();	
		endif;
	}

	public function sanitize_field( $value, $type ) {
		switch( $type ) {
			case 'text':
				$value = sanitize_text_field( $value );
			break;
			case 'media_upload':
				$value = intval( $value );
			break;
		}
		return $value;
	}
	
	function getIfSet( &$var, $defaultValue ) {
		if(isset($var)) {
			return $var;
		} else {
			return $defaultValue;
		}
	}
	
	/* Utility function for creating form controls */
	public function create_super_simple_form_control( $id, $settings ) {
		global $post;
		
		$value = '';
		$formControl = null;
		
		$repeatable 	   = $this->getIfSet( $settings['repeatable'], false);
		$parent_field_type = $this->getIfSet($settings['type'], '');
		$field_counter 	   = $this->getIfSet($settings['field_counter'], '');
		$settings 		   = $settings['fields'][$id];
		$field_type 	   = $settings['type'];
		
		if ( ( $repeatable || $parent_field_type == 'repeatable_fieldset' ) && isset( $this->field[$id] ) ) {
			$value = $this->field[$id];
		} else if ( !$repeatable ) {
			$value = get_post_meta( $post->ID, $id, true );
		}

		if ( !is_numeric( $value ) && empty( $value ) && isset( $settings['default'] ) ) {		
			$value = $settings['default'];
		}
		
		$formControl = new Slide_Hd_Form_Control( $id, $this, $repeatable, $settings, $value, $field_counter );
		
		return $formControl;
	}

}