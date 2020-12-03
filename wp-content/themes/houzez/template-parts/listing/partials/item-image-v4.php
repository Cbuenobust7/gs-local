<?php
global $post, $ele_thumbnail_size; 
$thumbnail_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : 'houzez-item-image-4';
?>
<a href="<?php echo esc_url(get_permalink()); ?>" class="hover-effect">
	<?php
	$featured_img_url = get_the_post_thumbnail_url($post->ID, $thumbnail_size);
    if( $featured_img_url != '' ) {
        	echo '<img class="img-fluid" src="'.esc_url($featured_img_url).'" alt="">';
    }else{
        houzez_image_placeholder( 'houzez-item-image-4' );
    }
	?>
</a><!-- hover-effect -->