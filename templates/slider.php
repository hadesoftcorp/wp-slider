<?php

if ( get_post_status() != 'publish' || get_post_type() != 'slide-hd' ) {
	return;
}

$post = get_post_meta( get_the_ID(), 'slide-hd', true );
$image_id = $post[0]['slide_hd_gambar'];
$url = $post['slide_hd_alamat'];

$image = wp_get_attachment_image_src( $image_id, 'full' );

if ( !empty( $image ) ) {
	$u = (($url != '') ? 'data-location="'.$url.'"' : '');
	$c = (($url != '') ? 'cursor: pointer;' : '');

	$html='<div '.$u.' style="width: 100%; flex-shrink: 0; height: 100%; position: relative; flex: 1 0 auto; max-width: 100%; display: flex;">
										<div style="flex: 1 0 0px; max-width: 100%;">
											<div style="color: rgba(0,0,0,.8); cursor: pointer; position: relative; flex: 1 0 auto; max-width: 100%; display: flex; z-index: 0;">
												<div style="padding-bottom: 40.9836%; transition: padding-bottom .2s cubic-bezier(.25,.8,.5,1); flex: 1 0 0px;"></div>
												<div style="background-image: url('.apply_filters('translatesendiri', $image[0]).'); '.$c.' background-position: center center; background-color: rgba(0,0,0,.1); background-size: cover; background-repeat: no-repeat; z-index: -1; position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
												</div>
												<div style="margin-left: -100%; flex: 1 0 0px; max-width: 100%;"></div>
											</div>
										</div>
									</div>';

	echo $html;

}
?>

