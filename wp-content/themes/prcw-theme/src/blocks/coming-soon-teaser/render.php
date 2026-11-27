<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'get_field' ) ) {
    return;
}

$badge_text      = get_field( 'badge_text' ) ?: 'COMING SOON';
$section_title   = get_field( 'section_title' ) ?: '2027 PHOTO GALLERY & LINEUP';
$description     = get_field( 'description' ) ?: 'Our official Week 2027 photo gallery, educational seminars, and participating bars will be unveiled here soon.';
$preview_images  = get_field( 'preview_images' );
$notify_heading  = get_field( 'notify_heading' ) ?: 'STAY TUNED FOR UPDATES';
$notify_text     = get_field( 'notify_text' ) ?: 'Subscribe to our newsletter and follow us on social media so you receive instant notifications as soon as new details and photos drop!';
$primary_label   = get_field( 'primary_button_label' ) ?: 'GET NOTIFIED';
$primary_url     = get_field( 'primary_button_url' ) ?: '#newsletter';
$secondary_label = get_field( 'secondary_button_label' );
$secondary_url   = get_field( 'secondary_button_url' );

// Si no puso link secundario manual pero hay Instagram configurado en Footer Settings, ofrecerlo fácil si llenó el texto del botón
if ( $secondary_label && empty( $secondary_url ) ) {
    $footer_ig = get_field( 'footer_social_instagram', 'option' );
    if ( $footer_ig ) {
        $secondary_url = $footer_ig;
    }
}

$cf7_form      = get_field( 'cf7_form' );
$cf7_form_html = function_exists( 'prcw_render_cf7_form' ) ? prcw_render_cf7_form( $cf7_form ) : '';

$valid_images = array();
if ( ! empty( $preview_images ) && is_array( $preview_images ) ) {
    foreach ( $preview_images as $row ) {
        $img = $row['image'] ?? null;
        if ( is_array( $img ) && ! empty( $img['url'] ) ) {
            $valid_images[] = array(
                'url'     => $img['url'],
                'alt'     => $img['alt'] ?? '',
                'caption' => $row['caption'] ?? '',
            );
        }
    }
}

$placeholder_cards = array(
    array(
        'icon'  => 'image',
        'label' => 'OFFICIAL PHOTOS',
    ),
    array(
        'icon'  => 'sparkles',
        'label' => 'SIGNATURE POP-UPS',
    ),
    array(
        'icon'  => 'star',
        'label' => 'GUEST BARTENDERS',
    ),
);

$is_hash_link       = str_starts_with( trim( (string) $primary_url ), '#' );
$primary_href       = $is_hash_link ? '#' . sanitize_html_class( ltrim( trim( (string) $primary_url ), '#' ) ) : esc_url( $primary_url );
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-coming-soon' ) );
?>
<section <?php echo $wrapper_attributes; ?>>
  <div class="prcw-coming-soon__inner">

    <div class="prcw-coming-soon__header">
      <?php if ( $badge_text ) : ?>
        <span class="prcw-coming-soon__badge">
          <?php if ( function_exists( 'prcw_render_icon' ) ) : ?>
            <span class="prcw-coming-soon__badge-icon"><?php echo prcw_render_icon( 'sparkles' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
          <?php endif; ?>
          <span><?php echo esc_html( $badge_text ); ?></span>
        </span>
      <?php endif; ?>

      <?php if ( $section_title ) : ?>
        <h2 class="prcw-coming-soon__title"><?php echo esc_html( $section_title ); ?></h2>
      <?php endif; ?>

      <?php if ( $description ) : ?>
        <div class="prcw-coming-soon__description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
      <?php endif; ?>
    </div>

    <div class="prcw-coming-soon__gallery">
      <?php if ( ! empty( $valid_images ) ) : ?>
        <?php foreach ( $valid_images as $item ) : ?>
          <div class="prcw-coming-soon__frame prcw-coming-soon__frame--photo">
            <img src="<?php echo esc_url( $item['url'] ); ?>" alt="<?php echo esc_attr( $item['alt'] ); ?>">
            <div class="prcw-coming-soon__frame-overlay">
              <span class="prcw-coming-soon__frame-tag">
                <?php echo esc_html( $item['caption'] ?: $badge_text ); ?>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else : ?>
        <?php foreach ( $placeholder_cards as $card ) : ?>
          <div class="prcw-coming-soon__frame prcw-coming-soon__frame--placeholder">
            <?php if ( function_exists( 'prcw_render_icon' ) ) : ?>
              <span class="prcw-coming-soon__frame-icon"><?php echo prcw_render_icon( $card['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
            <?php endif; ?>
            <span class="prcw-coming-soon__frame-title"><?php echo esc_html( $card['label'] ); ?></span>
            <span class="prcw-coming-soon__frame-sub"><?php echo esc_html( $badge_text ); ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="prcw-coming-soon__notify-card">
      <div class="prcw-coming-soon__notify-icon">
        <?php if ( function_exists( 'prcw_render_icon' ) ) : ?>
          <?php echo prcw_render_icon( 'bell' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php endif; ?>
      </div>

      <div class="prcw-coming-soon__notify-body">
        <?php if ( $notify_heading ) : ?>
          <h3 class="prcw-coming-soon__notify-heading"><?php echo esc_html( $notify_heading ); ?></h3>
        <?php endif; ?>

        <?php if ( $notify_text ) : ?>
          <p class="prcw-coming-soon__notify-text"><?php echo esc_html( $notify_text ); ?></p>
        <?php endif; ?>

        <?php if ( $cf7_form_html ) : ?>
          <div class="prcw-coming-soon__form">
            <?php echo $cf7_form_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered by CF7 shortcode ?>
          </div>
        <?php endif; ?>

        <?php if ( ( $primary_label && $primary_href ) || ( $secondary_label && $secondary_url ) ) : ?>
          <div class="prcw-coming-soon__actions">
            <?php if ( $primary_label && $primary_href ) : ?>
              <a class="prcw-coming-soon__btn prcw-coming-soon__btn--primary" href="<?php echo esc_attr( $primary_href ); ?>">
                <?php if ( function_exists( 'prcw_render_icon' ) ) : ?>
                  <span class="prcw-coming-soon__btn-icon"><?php echo prcw_render_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                <?php endif; ?>
                <span><?php echo esc_html( $primary_label ); ?></span>
              </a>
            <?php endif; ?>

            <?php if ( $secondary_label && $secondary_url ) : ?>
              <a
                class="prcw-coming-soon__btn prcw-coming-soon__btn--secondary"
                href="<?php echo esc_url( $secondary_url ); ?>"
                target="_blank"
                rel="noopener noreferrer"
              >
                <?php if ( function_exists( 'prcw_render_icon' ) ) : ?>
                  <span class="prcw-coming-soon__btn-icon"><?php echo prcw_render_icon( 'instagram' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                <?php endif; ?>
                <span><?php echo esc_html( $secondary_label ); ?></span>
              </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</section>
