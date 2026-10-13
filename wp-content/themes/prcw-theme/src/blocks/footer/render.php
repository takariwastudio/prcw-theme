<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$logo             = get_field( 'footer_logo', 'option' );
$copyright        = get_field( 'footer_copyright_text', 'option' );
$terms_label      = get_field( 'footer_terms_label', 'option' );
$terms_url        = get_field( 'footer_terms_url', 'option' );
$nl_heading       = get_field( 'footer_newsletter_heading', 'option' );
$nl_text          = get_field( 'footer_newsletter_text', 'option' );
$newsletter_form  = get_field( 'footer_mailchimp_shortcode', 'option' );
$privacy_url      = get_field( 'footer_privacy_url', 'option' );
$consent_text     = get_field( 'footer_consent_text', 'option' );
$fb_url           = get_field( 'footer_social_facebook', 'option' );
$ig_url           = get_field( 'footer_social_instagram', 'option' );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-footer' ) );
?>
<footer <?php echo $wrapper_attributes; ?>>

  <div class="prcw-footer__newsletter">
    <div class="prcw-footer__newsletter-inner">
      <?php if ( $nl_heading ) : ?>
        <h3 class="prcw-footer__nl-heading"><?php echo esc_html( $nl_heading ); ?></h3>
      <?php endif; ?>

      <?php if ( $nl_text ) : ?>
        <p class="prcw-footer__nl-text"><?php echo esc_html( $nl_text ); ?></p>
      <?php endif; ?>

      <div class="prcw-footer__form">
  <?php if ( $newsletter_form ) : ?>
    <?php echo do_shortcode( '[contact-form-7 id="' . intval( $newsletter_form->ID ) . '"]' ); ?>
  <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="prcw-footer__bar">
    <div class="prcw-footer__bar-inner">

      <div class="prcw-footer__bar-left">
        <?php if ( $logo ) : ?>
          <img class="prcw-footer__logo" src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>">
        <?php endif; ?>

        <?php if ( $copyright ) : ?>
          <span class="prcw-footer__copyright"><?php echo esc_html( $copyright ); ?></span>
        <?php endif; ?>

        <?php if ( $terms_label && $terms_url ) : ?>
          <a class="prcw-footer__terms" href="<?php echo esc_url( $terms_url ); ?>"><?php echo esc_html( $terms_label ); ?></a>
        <?php endif; ?>
      </div>

      <div class="prcw-footer__bar-right">
        <?php if ( $fb_url || $ig_url ) : ?>
          <ul class="prcw-footer__social">
            <?php if ( $fb_url ) : ?>
              <li><a href="<?php echo esc_url( $fb_url ); ?>" aria-label="Facebook"><span class="dashicons dashicons-facebook-alt"></span></a></li>
            <?php endif; ?>
            <?php if ( $ig_url ) : ?>
              <li><a href="<?php echo esc_url( $ig_url ); ?>" aria-label="Instagram"><span class="dashicons dashicons-instagram"></span></a></li>
            <?php endif; ?>
          </ul>
        <?php endif; ?>

        <?php if ( function_exists( 'pll_the_languages' ) ) : ?>
          <div class="prcw-footer__lang">
            <?php echo pll_the_languages( array( 'echo' => 0 ) ); ?>
          </div>
        <?php endif; ?>

        <button class="prcw-footer__scroll-top" aria-label="Volver arriba" onclick="window.scrollTo({top:0,behavior:'smooth'})">
          ↑
        </button>
      </div>

    </div>
  </div>

</footer>
