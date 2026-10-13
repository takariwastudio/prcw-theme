<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$background_image = get_field( 'background_image' );
$featured_photo    = get_field( 'featured_photo' );
$form_heading      = get_field( 'form_heading' ) ?: 'LEAVE YOUR INFORMATION HERE!';
$form_field        = get_field( 'form' );
$contacts          = get_field( 'contacts' );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-contact-section' ) );
?>
<div <?php echo $wrapper_attributes; ?>
  <?php if ( $background_image ) : ?>
    style="background-image:url('<?php echo esc_url( $background_image['url'] ); ?>');"
  <?php endif; ?>
>
  <div class="prcw-contact-section__overlay"></div>

  <div class="prcw-contact-section__inner">

    <?php if ( $featured_photo ) : ?>
      <div class="prcw-contact-section__photo">
        <img src="<?php echo esc_url( $featured_photo['url'] ); ?>" alt="">
      </div>
    <?php endif; ?>

    <div class="prcw-contact-section__card">

      <?php if ( $form_heading ) : ?>
        <h2 class="prcw-contact-section__form-heading"><?php echo esc_html( $form_heading ); ?></h2>
      <?php endif; ?>

      <?php if ( $form_field ) : ?>
        <div class="prcw-contact-section__form">
          <?php echo do_shortcode( '[contact-form-7 id="' . intval( $form_field->ID ) . '"]' ); ?>
        </div>
      <?php endif; ?>

      <?php if ( ! empty( $contacts ) ) : ?>
        <div class="prcw-contact-section__contacts">
          <?php foreach ( $contacts as $contact ) :
            $c_heading = $contact['heading'];
            $c_links   = $contact['social_links'];
          ?>
            <div class="prcw-contact-section__contact">
              <?php if ( $c_heading ) : ?>
                <h3 class="prcw-contact-section__contact-heading"><?php echo esc_html( $c_heading ); ?></h3>
              <?php endif; ?>

              <?php if ( ! empty( $c_links ) ) : ?>
                <div class="prcw-contact-section__contact-links">
                  <?php foreach ( $c_links as $link ) :
                    $l_icon = $link['icon'];
                    $l_url  = $link['url'];
                    if ( ! $l_url ) {
                      continue;
                    }
                  ?>
                    <a class="prcw-contact-section__contact-link" href="<?php echo esc_url( $l_url ); ?>" target="_blank" rel="noopener noreferrer">
                      <?php echo prcw_render_icon( $l_icon ); ?>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>

  </div>
</div>