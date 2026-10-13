<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$links = get_field( 'quick_links' );

if ( empty( $links ) ) {
    return;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-quick-links' ) );
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="prcw-quick-links__inner">
    <?php foreach ( $links as $link ) :
      $heading     = $link['heading'];
      $button_text = $link['button_label'] ?: 'HERE';
      $button_url  = $link['button_url'];
    ?>
      <div class="prcw-quick-links__item">
        <div class="prcw-quick-links__divider"></div>

        <?php if ( $heading ) : ?>
          <h3 class="prcw-quick-links__heading"><?php echo esc_html( $heading ); ?></h3>
        <?php endif; ?>

        <?php if ( $button_url ) : ?>
          <a class="prcw-quick-links__button" href="<?php echo esc_url( $button_url ); ?>">
            <?php echo esc_html( $button_text ); ?>
          </a>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>
