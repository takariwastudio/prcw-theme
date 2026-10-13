<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$post_id     = get_the_ID();
$year        = get_field( 'year', $post_id );
$folder_path = get_field( 'dropbox_folder_path', $post_id );
$photos      = prcw_get_dropbox_photos( $folder_path ); // sin límite, galería completa

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-event-year-gallery' ) );
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="prcw-event-year-gallery__inner">

    <?php if ( $year ) : ?>
      <h1 class="prcw-event-year-gallery__title">PRCW <?php echo esc_html( $year ); ?></h1>
    <?php endif; ?>

    <?php if ( ! empty( $photos ) ) : ?>
      <div class="prcw-event-year-gallery__grid">
        <?php foreach ( $photos as $index => $photo_url ) : ?>
          <button
            type="button"
            class="prcw-event-year-gallery__photo"
            data-lightbox-index="<?php echo esc_attr( $index ); ?>"
          >
            <img src="<?php echo esc_url( $photo_url ); ?>" alt="">
          </button>
        <?php endforeach; ?>
      </div>

      <div class="prcw-event-year-gallery__lightbox">
        <button type="button" class="prcw-event-year-gallery__lightbox-close" aria-label="Close">&times;</button>
        <button type="button" class="prcw-event-year-gallery__lightbox-prev" aria-label="Previous">&larr;</button>
        <img class="prcw-event-year-gallery__lightbox-img" src="" alt="">
        <button type="button" class="prcw-event-year-gallery__lightbox-next" aria-label="Next">&rarr;</button>
      </div>

      <script type="application/json" class="prcw-event-year-gallery__data">
        <?php echo wp_json_encode( array_values( $photos ) ); ?>
      </script>
    <?php else : ?>
      <p class="prcw-event-year-gallery__empty">Photos coming soon!</p>
    <?php endif; ?>

  </div>
</div>