<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$heading    = get_field( 'heading' );
$subheading = get_field( 'subheading' );
$form       = get_field( 'form' );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-form-section' ) );
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="prcw-form-section__inner">

    <?php if ( $heading ) : ?>
      <h2 class="prcw-form-section__heading"><?php echo esc_html( $heading ); ?></h2>
    <?php endif; ?>

    <?php if ( $subheading ) : ?>
      <p class="prcw-form-section__subheading"><?php echo esc_html( $subheading ); ?></p>
    <?php endif; ?>

    <?php if ( $form ) : ?>
      <div class="prcw-form-section__card">
        <?php echo do_shortcode( '[contact-form-7 id="' . intval( $form->ID ) . '"]' ); ?>
      </div>
    <?php endif; ?>

  </div>
</div>