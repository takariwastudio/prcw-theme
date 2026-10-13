<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$section_title = get_field( 'section_title' ) ?: 'THE CREW';
$group_photo    = get_field( 'group_photo' );
$members        = get_field( 'members' );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-crew' ) );
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="prcw-crew__inner">

    <?php if ( $section_title ) : ?>
      <h3 class="prcw-crew__title"><?php echo esc_html( $section_title ); ?></h3>
    <?php endif; ?>

    <?php if ( $group_photo ) : ?>
      <div class="prcw-crew__photo">
        <img src="<?php echo esc_url( $group_photo['url'] ); ?>" alt="<?php echo esc_attr( $group_photo['alt'] ); ?>">
      </div>
    <?php endif; ?>

    <?php if ( ! empty( $members ) ) : ?>
      <div class="prcw-crew__grid">
        <?php foreach ( $members as $member ) :
          $name          = $member['name'];
          $instagram     = $member['instagram'];
          $instagram_url = $member['instagram_url'];
        ?>
          <div class="prcw-crew__member">
            <?php if ( $name ) : ?>
              <p class="prcw-crew__name"><?php echo esc_html( $name ); ?></p>
            <?php endif; ?>

            <?php if ( $instagram ) : ?>
              <?php if ( $instagram_url ) : ?>
                <a class="prcw-crew__handle" href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener noreferrer">
                  <?php echo esc_html( $instagram ); ?>
                </a>
              <?php else : ?>
                <p class="prcw-crew__handle"><?php echo esc_html( $instagram ); ?></p>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</div>
