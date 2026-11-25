<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'get_field' ) ) {
    return;
}

$image = get_field( 'image' );

if ( ! $image ) {
    return;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-full-bleed-image' ) );
?>
<div <?php echo $wrapper_attributes; ?>>
  <img
    class="prcw-full-bleed-image__img"
    src="<?php echo esc_url( $image['url'] ); ?>"
    alt="<?php echo esc_attr( $image['alt'] ); ?>"
  >
</div>