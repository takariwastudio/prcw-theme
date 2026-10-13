<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$image         = get_field( 'image' );
$image_width   = get_field( 'image_width' ) ?: 450;
$sections      = get_field( 'sections' );
$bottom_logo   = get_field( 'bottom_logo' );
$bottom_url    = get_field( 'bottom_logo_url' );

if ( empty( $sections ) ) {
    return;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-sticky-sections' ) );
?>
<div <?php echo $wrapper_attributes; ?>>

  <?php if ( $image ) : ?>
    <div class="prcw-sticky-sections__media">
      <img
        class="prcw-sticky-sections__image"
        style="--image-width: <?php echo esc_attr( $image_width ); ?>px;"
        src="<?php echo esc_url( $image['url'] ); ?>"
        alt="<?php echo esc_attr( $image['alt'] ); ?>"
      >
    </div>
  <?php endif; ?>

  <div class="prcw-sticky-sections__content">
    <?php foreach ( $sections as $section ) :
      $title     = $section['title'];
      $paragraph = $section['paragraph'];
    ?>
      <div class="prcw-sticky-sections__section">
        <?php if ( $title ) : ?>
          <h2 class="prcw-sticky-sections__title"><?php echo esc_html( $title ); ?></h2>
        <?php endif; ?>
        <?php if ( $paragraph ) : ?>
          <div class="prcw-sticky-sections__paragraph"><?php echo wp_kses_post( wpautop( $paragraph ) ); ?></div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>

    <?php if ( $bottom_logo ) : ?>
      <div class="prcw-sticky-sections__banner">
        <?php if ( $bottom_url ) : ?><a href="<?php echo esc_url( $bottom_url ); ?>"><?php endif; ?>
          <img src="<?php echo esc_url( $bottom_logo['url'] ); ?>" alt="<?php echo esc_attr( $bottom_logo['alt'] ); ?>">
        <?php if ( $bottom_url ) : ?></a><?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

</div>
