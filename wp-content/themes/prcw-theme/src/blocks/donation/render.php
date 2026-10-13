<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

wp_enqueue_script( 'wc-single-product' );
wp_enqueue_script( 'wc-add-to-cart' );

$logo          = get_field( 'logo' );
$product_field = get_field( 'product' );
$payment_text  = get_field( 'payment_text' ) ?: 'YOU CAN DONATE THROUGH PAYPAL, STRIPE OR ATH MÓVIL!';
$intro_text = get_field( 'intro_text' );
$stat_image = get_field( 'stat_image' );
$stat_text  = get_field( 'stat_text' );

$card_classes = 'prcw-donation__card';
if ( $stat_image ) {
    $card_classes .= ' prcw-donation__card--with-stat';
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-donation' ) );
?>
<div <?php echo $wrapper_attributes; ?>>

  <?php if ( $intro_text ) : ?>
    <div class="prcw-donation__intro"><?php echo wp_kses_post( wpautop( $intro_text ) ); ?></div>
  <?php endif; ?>

  <div class="<?php echo esc_attr( $card_classes ); ?>">

    <?php if ( $stat_image ) : ?>
      <div class="prcw-donation__stat">
        <img src="<?php echo esc_url( $stat_image['url'] ); ?>" alt="">
        <?php if ( $stat_text ) : ?>
          <div class="prcw-donation__stat-overlay"></div>
          <p class="prcw-donation__stat-text"><?php echo esc_html( $stat_text ); ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if ( $logo ) : ?>
      <div class="prcw-donation__logo">
        <img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>">
      </div>
    <?php endif; ?>

    <div class="prcw-donation__form-wrap">
      <div class="prcw-donation__form">
        <p class="prcw-donation__label">DONATION</p>
        <?php
        if ( $product_field ) :
          $wc_product = wc_get_product( $product_field );
          if ( $wc_product ) :
            global $product;
            $product = $wc_product;
            woocommerce_template_single_add_to_cart();
          endif;
        endif;
        ?>
      </div>

      <?php if ( $payment_text ) : ?>
        <p class="prcw-donation__payment-text"><?php echo esc_html( $payment_text ); ?></p>
      <?php endif; ?>
    </div>

  </div>

  <div class="prcw-donation__modal">
    <div class="prcw-donation__modal-overlay"></div>
    <div class="prcw-donation__modal-box">
      <p class="prcw-donation__modal-text">¡Gracias! Tu donación se agregó al carrito.</p>
      <div class="prcw-donation__modal-actions">
        <a class="prcw-donation__modal-btn prcw-donation__modal-btn--primary" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
          Ir al carrito
        </a>
        <button type="button" class="prcw-donation__modal-btn prcw-donation__modal-btn--secondary prcw-donation__modal-close">
          Seguir navegando
        </button>
      </div>
    </div>
  </div>
</div>