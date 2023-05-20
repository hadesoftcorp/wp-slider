<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Slide_Hd_Form_Control {

	public $parent = null;
    public $id;
    public $type;
    public $name;
    public $hasDependents;
    public $prefix;
    public $suffix;
    public $label;
    public $description;
    public $value;
    public $show_labels;
    public $min_labels;
    public $field_counter;
    public $template;
    public $fieldset;
    public $options;
    public $input_attrs;
    public $class;
    
	public function __construct( $id, $parent, $repeatable, $args = array(), $value, $field_counter = '' ) {
		$keys = array_keys( get_object_vars( $this ) );
		
        foreach ( $keys as $key ) {
            if ( isset( $args[ $key ] ) ) {
                $this->$key = $args[ $key ];
            }
        }
        
		$this->id = $id;
		$this->parent = $parent;
		$this->repeatable = $repeatable;
		$this->value = $value;
		$this->field_counter = $field_counter;
		
		$this->render();
	}

    protected function render() {
        $id    = 'customize-control-' . str_replace( array( '[', ']' ), array( '-', '' ), $this->id );
        $class = 'customize-control customize-control-' . $this->type;
 
        $this->render_content();
    }
    
	public function render_content() {
		$html 		= '';
		$field_name = '';
		
		$field_name  .= $this->id;
        $field_class = 'otb-form-control otb-form-control-' . $this->type . ' ' . $this->id . ' ' . $this->class;

		if ( $this->hasDependents ) {
			$field_class .= 'has-dependents'; 
        }
        
		if ( $this->field_counter ) {
			$field_name .= $this->field_counter;
		}
        
		if ( $this->repeatable ) {
			$field_name .= '[]';
		}
		
		if ( $this->label ) {
			$html .= '<label for="' . $this->id . '">' . $this->label . '</label>';
		}
		
		switch( $this->type ) {

			case 'media_upload':
				global $_wp_additional_image_sizes;
				global $post;
				$image_id 	   = get_post_meta( $post->ID, 'slide-hd', true );
				if (isset($image_id[0])) {
					$image_id 		   = $image_id[0][$this->id];
				}else{
					$image_id = 0;
				}
				$has_image		   	  = false;
				$media_uploader_class = '';

				
				if ( $image_id && get_post( $image_id ) ) {
					$has_image = true;
					$media_uploader_class = 'has-img';
				}
				
				$html .= '<div class="media-uploader ' .$media_uploader_class. '">';
				$html .= '<p class="hide-if-no-js"><a style="width: 100%; text-align: center; line-height: 100px; font-size: 20px; text-transform: uppercase;" title="' . esc_attr__( 'Upload Image', 'admin-hd' ) . '" href="javascript:;" class="button upload" data-uploader_title="' . esc_attr__( 'Upload Image', 'admin-hd' ) . '" data-uploader_button_text="' . esc_attr__( 'Upload Image', 'admin-hd' ) . '">' . esc_html__( 'Unggah Gambar', 'admin-hd' ) . '</a></p>';				
				$html .= '<input type="hidden" name="' . esc_attr( $field_name ) . '" data-field-id="' .$this->id. '" value="' . esc_attr( $image_id ) . '" />';
				
				$html .= '<div class="preview" style="width: 100%; max-width: 100%!important;">';
				$html .= '<div class="delete icon">';
				$html .= '<div class="media-modal-close" style="width: 24px; height: 23px; top: 1px;"><span class="media-modal-icon"><span class="screen-reader-text">Tutup dialog</span></span></div>';		
				$html .= '</div>';
				
				if ( $has_image ) {
					$thumbnail_html = wp_get_attachment_image( $image_id, 'full' );

					if ( !empty( $thumbnail_html ) ) {
						$html .= $thumbnail_html;
					}
				
				} else {
					$html .= '<img src="" />';
				}
				
				$html .= '<style>div.preview > img{width:100%!important;}</style>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
			break;
			
			case 'repeatable_fieldset':
				ob_start();
				include ( super_simple_slider()->assets_dir .'/template-parts/'. $this->template );
				$html .= ob_get_clean();
			break;
		}
		
		if ( $this->description ) {
			$html .= '<div class="otb-form-control-description otb-form-control-description-' .$this->type. ' ' .$this->id. '-description">' . $this->description . '</div>';
		}
		
		echo $html;
	}
}
