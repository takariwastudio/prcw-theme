<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$eyebrow         = get_field( 'eyebrow' );
$highlight_text  = get_field( 'highlight_text' );
$body_content    = get_field( 'body_content' );
$closing_heading = get_field( 'closing_heading' );
$eyebrow_color   = get_field( 'eyebrow_color' );
$eyebrow_size    = get_field( 'eyebrow_size' ) ?: 'normal';
$text_align      = get_field( 'text_align' ) ?: 'center';

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-content-section' ) );

$inner_classes = 'prcw-content-section__inner prcw-content-section__inner--align-' . esc_attr( $text_align );
$eyebrow_classes = 'prcw-content-section__eyebrow prcw-content-section__eyebrow--' . esc_attr( $eyebrow_size );
$eyebrow_style = $eyebrow_color ? ' style="color: ' . esc_attr( $eyebrow_color ) . ';"' : '';
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="<?php echo esc_attr( $inner_classes ); ?>">

    <?php if ( $eyebrow ) : ?>
  <h2 class="<?php echo esc_attr( $eyebrow_classes ); ?>"<?php echo $eyebrow_style; ?>><?php echo esc_html( $eyebrow ); ?></h2>
<?php endif; ?>

    <?php if ( $highlight_text ) : ?>
      <div class="prcw-content-section__highlight"><?php echo wp_kses_post( wpautop( $highlight_text ) ); ?></div>
    <?php endif; ?>

    <?php if ( $body_content ) : ?>
      <div class="prcw-content-section__body"><?php echo wp_kses_post( $body_content ); ?></div>
    <?php endif; ?>

    <?php if ( $closing_heading ) : ?>
      <h3 class="prcw-content-section__closing"><?php echo esc_html( $closing_heading ); ?></h3>
    <?php endif; ?>

  </div>
</div>