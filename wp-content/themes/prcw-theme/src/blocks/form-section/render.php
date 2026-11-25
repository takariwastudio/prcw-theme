<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'get_field' ) ) {
    return;
}

$heading    = get_field( 'heading' );
$subheading = get_field( 'subheading' );
$form       = get_field( 'form' );
$form_html  = function_exists( 'prcw_render_cf7_form' ) ? prcw_render_cf7_form( $form ) : '';

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

    <?php if ( $form_html ) : ?>
      <div class="prcw-form-section__card">
        <?php echo $form_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered by CF7 shortcode ?>
      </div>
    <?php endif; ?>

  </div>
</div>