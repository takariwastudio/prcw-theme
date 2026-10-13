<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$media_position = get_field( 'media_position' ) ?: 'right';
$media_type      = get_field( 'media_type' ) ?: 'image';
$image           = get_field( 'image' );
$video_url       = get_field( 'video_url' );
$large_title     = get_field( 'large_title' );
$title           = get_field( 'title' );
$paragraph       = get_field( 'paragraph' );
$button_label    = get_field( 'button_label' );
$button_url      = get_field( 'button_url' );
$top_heading    = get_field( 'top_heading' );
$rich_paragraph = get_field( 'rich_paragraph' );
$content_align       = get_field( 'content_align' ) ?: 'left';
$background_override = get_field( 'background_override' );
$row_width = get_field( 'row_width' ) ?: 'full';

$wrapper_classes = 'prcw-media-text prcw-media-text--media-' . esc_attr( $media_position ) . ' prcw-media-text--align-' . esc_attr( $content_align );

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => $wrapper_classes,
) );

$row_style = $background_override ? ' style="background: ' . esc_attr( $background_override ) . ';"' : '';
?>
<div <?php echo $wrapper_attributes; ?>>

  <?php if ( $top_heading ) : ?>
    <h2 class="prcw-media-text__top-heading"><?php echo esc_html( $top_heading ); ?></h2>
  <?php endif; ?>

  <div class="prcw-media-text__row prcw-media-text__row--<?php echo esc_attr( $row_width ); ?>"<?php echo $row_style; ?>>

  <div class="prcw-media-text__media">
    <?php if ( 'video' === $media_type && $video_url ) : ?>
      <div class="prcw-media-text__video-wrap">
        <?php echo wp_oembed_get( $video_url ); ?>
      </div>
    <?php elseif ( 'image' === $media_type && $image ) : ?>
      <img class="prcw-media-text__image" src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>">
    <?php endif; ?>
  </div>

  <div class="prcw-media-text__content">
    <?php if ( $title ) : ?>
      <h2 class="prcw-media-text__title <?php echo $large_title ? 'prcw-media-text__title--large' : ''; ?>">
        <?php echo esc_html( $title ); ?>
      </h2>
    <?php endif; ?>

    <?php if ( $rich_paragraph ) : ?>
  <div class="prcw-media-text__paragraph">
    <?php echo wp_kses_post( $rich_paragraph ); ?>
  </div>
<?php elseif ( $paragraph ) : ?>
  <div class="prcw-media-text__paragraph">
    <?php echo wp_kses_post( wpautop( $paragraph ) ); ?>
  </div>
<?php endif; ?>

    <?php if ( $button_label && $button_url ) : ?>
      <a class="prcw-media-text__button" href="<?php echo esc_url( $button_url ); ?>">
        <?php echo esc_html( $button_label ); ?>
      </a>
    <?php endif; ?>
  </div>
</div>
</div>
