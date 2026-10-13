<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$bg           = get_field( 'background_image' );
$logo         = get_field( 'logo_image' );
$logo_width   = get_field( 'logo_width' ) ?: 335;
$text         = get_field( 'text' );
$button_label = get_field( 'button_label' );
$button_url   = get_field( 'button_url' );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-banner-cta' ) );
?>
<div <?php echo $wrapper_attributes; ?>
  <?php if ( $bg ) : ?>
    style="background-image:url('<?php echo esc_url( $bg['url'] ); ?>');"
  <?php endif; ?>
>
  <div class="prcw-banner-cta__overlay"></div>

  <div class="prcw-banner-cta__content">
<?php if ( $logo ) : ?>
<img class="prcw-banner-cta__logo" style="--logo-width: <?php echo esc_attr( $logo_width ); ?>px;" src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>">
<?php endif; ?>
    <?php if ( $text ) : ?>
      <div class="prcw-banner-cta__text"><?php echo wp_kses_post( wpautop( $text ) ); ?></div>
    <?php endif; ?>

    <?php if ( $button_label && $button_url ) : ?>
      <a class="prcw-banner-cta__button" href="<?php echo esc_url( $button_url ); ?>">
        <?php echo esc_html( $button_label ); ?>
      </a>
    <?php endif; ?>
  </div>
</div>
