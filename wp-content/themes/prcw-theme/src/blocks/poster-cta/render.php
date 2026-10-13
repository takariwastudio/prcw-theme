<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$title        = get_field( 'title' );
$image        = get_field( 'image' );
$button_label = get_field( 'button_label' ) ?: 'GET TICKETS NOW!';
$button_url   = get_field( 'button_url' );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-poster-cta' ) );
?>
<div <?php echo $wrapper_attributes; ?>>

  <?php if ( $title ) : ?>
    <h1 class="prcw-poster-cta__title"><?php echo esc_html( $title ); ?></h1>
  <?php endif; ?>

  <?php if ( $image ) : ?>
    <div class="prcw-poster-cta__image">
      <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>">
    </div>
  <?php endif; ?>

  <?php if ( $button_label && $button_url ) : ?>
    <a class="prcw-poster-cta__button" href="<?php echo esc_url( $button_url ); ?>">
      <?php echo esc_html( $button_label ); ?>
    </a>
  <?php endif; ?>

</div>
