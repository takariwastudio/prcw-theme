<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$last_events_heading = get_field( 'last_events_heading' ) ?: 'LAST EVENTS';
$split                = prcw_get_event_years_split();
$active_years         = $split['active'];
$archive_years        = $split['archive'];

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-events-archive' ) );
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="prcw-events-archive__inner">

    <?php if ( ! empty( $active_years ) ) : ?>
      <div class="prcw-events-archive__tabs">
        <?php foreach ( $active_years as $index => $post ) :
          $year = get_field( 'year', $post->ID );
        ?>
          <button
            type="button"
            class="prcw-events-archive__tab <?php echo 0 === $index ? 'is-active' : ''; ?>"
            data-target="year-<?php echo esc_attr( $year ); ?>"
          >
            <?php echo esc_html( $year ); ?>
          </button>
        <?php endforeach; ?>
      </div>

      <?php foreach ( $active_years as $index => $post ) :
        $year        = get_field( 'year', $post->ID );
        $folder_path = get_field( 'dropbox_folder_path', $post->ID );
        $photos      = prcw_get_dropbox_photos( $folder_path, 6 );
        $permalink   = get_permalink( $post->ID );
      ?>
        <div
          class="prcw-events-archive__panel <?php echo 0 === $index ? 'is-active' : ''; ?>"
          id="year-<?php echo esc_attr( $year ); ?>"
        >
          <div class="prcw-events-archive__grid">
            <?php if ( ! empty( $photos ) ) : ?>
              <?php foreach ( $photos as $photo_url ) : ?>
                <div class="prcw-events-archive__photo">
                  <img src="<?php echo esc_url( $photo_url ); ?>" alt="">
                </div>
              <?php endforeach; ?>
            <?php else : ?>
              <?php for ( $i = 0; $i < 6; $i++ ) : ?>
                <div class="prcw-events-archive__photo prcw-events-archive__photo--placeholder"></div>
              <?php endfor; ?>
            <?php endif; ?>
          </div>

          <div class="prcw-events-archive__view-more">
            <a href="<?php echo esc_url( $permalink ); ?>" class="prcw-events-archive__button">
              View More
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if ( ! empty( $archive_years ) ) : ?>
      <h2 class="prcw-events-archive__last-heading"><?php echo esc_html( $last_events_heading ); ?></h2>

      <div class="prcw-events-archive__list">
        <?php foreach ( $archive_years as $post ) :
          $year        = get_field( 'year', $post->ID );
          $description = get_field( 'description', $post->ID );
          $bg          = get_field( 'archive_background', $post->ID );
          $url         = get_field( 'archive_dropbox_url', $post->ID );
        ?>
          <div class="prcw-events-archive__item">
            <div
              class="prcw-events-archive__item-bg"
              <?php if ( $bg ) : ?>
                style="background-image:url('<?php echo esc_url( $bg['url'] ); ?>');"
              <?php endif; ?>
            ></div>

            <div class="prcw-events-archive__item-content">
              <?php if ( $description ) : ?>
                <p class="prcw-events-archive__item-desc"><?php echo esc_html( $description ); ?></p>
              <?php endif; ?>

              <?php if ( $url ) : ?>
                <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" class="prcw-events-archive__button">
                  View More
                </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</div>