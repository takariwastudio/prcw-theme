<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$bg          = get_field( 'background_image' );
$title       = get_field( 'title' );
$title_color = get_field( 'title_color' ) ?: '#7a7a7a';

$bg_url = $bg ? $bg['url'] : PRCW_THEME_URI . '/assets/images/page-header-default.webp';

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-page-header' ) );
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="prcw-page-header__bg" style="background-image:url('<?php echo esc_url( $bg_url ); ?>');"></div>
  <div class="prcw-page-header__gradient"></div>

  <?php if ( $title ) : ?>
    <div class="prcw-page-header__content">
      <h1 class="prcw-page-header__title" style="color: <?php echo esc_attr( $title_color ); ?>;">
        <?php echo esc_html( $title ); ?>
      </h1>
    </div>
  <?php endif; ?>
</div>
