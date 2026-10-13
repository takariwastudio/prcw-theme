<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$button_label = get_field( 'button_label' ) ?: 'APPLY HERE';
$button_url   = get_field( 'button_url' );
$icon         = get_field( 'icon' ) ?: 'alert-circle';
$text         = get_field( 'text' );
$link_label   = get_field( 'link_label' );
$link_url     = get_field( 'link_url' );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-notice-cta' ) );
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="prcw-notice-cta__inner">

    <?php if ( $button_label && $button_url ) : ?>
      <a class="prcw-notice-cta__button" href="<?php echo esc_url( $button_url ); ?>">
        <?php echo esc_html( $button_label ); ?>
      </a>
    <?php endif; ?>

    <?php if ( $icon ) : ?>
      <span class="prcw-notice-cta__icon"><?php echo prcw_render_icon( $icon ); ?></span>
    <?php endif; ?>

    <?php if ( $text ) : ?>
      <div class="prcw-notice-cta__text"><?php echo wp_kses_post( wpautop( $text ) ); ?></div>
    <?php endif; ?>

    <?php if ( $link_label && $link_url ) : ?>
      <a class="prcw-notice-cta__link" href="<?php echo esc_url( $link_url ); ?>">
        <?php echo esc_html( $link_label ); ?>
      </a>
    <?php endif; ?>

  </div>
</div>