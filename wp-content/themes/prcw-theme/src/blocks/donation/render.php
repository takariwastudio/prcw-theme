<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'get_field' ) ) {
    return;
}

$wc_active = class_exists( 'WooCommerce' )
    && function_exists( 'wc_get_product' )
    && function_exists( 'woocommerce_template_single_add_to_cart' )
    && function_exists( 'wc_get_cart_url' );

if ( $wc_active ) {
    wp_enqueue_script( 'wc-single-product' );
    wp_enqueue_script( 'wc-add-to-cart' );
}

$logo          = get_field( 'logo' );
$product_field = get_field( 'product' );
if ( empty( $product_field ) && function_exists( 'prcw_get_tip_jar_product_id' ) ) {
    $product_field = prcw_get_tip_jar_product_id();
}
$payment_text  = get_field( 'payment_text' ) ?: 'YOU CAN DONATE THROUGH PAYPAL, STRIPE OR ATH MÓVIL!';
$intro_text    = get_field( 'intro_text' );
$stat_image    = get_field( 'stat_image' );
$stat_text     = get_field( 'stat_text' );

$has_stat_image = is_array( $stat_image ) && ! empty( $stat_image['url'] );
$card_classes   = 'prcw-donation__card';
if ( $has_stat_image ) {
    $card_classes .= ' prcw-donation__card--with-stat';
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-donation' ) );
?>
<div <?php echo $wrapper_attributes; ?>>

  <?php if ( $intro_text ) : ?>
    <div class="prcw-donation__intro"><?php echo wp_kses_post( wpautop( $intro_text ) ); ?></div>
  <?php endif; ?>

  <div class="<?php echo esc_attr( $card_classes ); ?>">

    <?php if ( $has_stat_image ) : ?>
      <div class="prcw-donation__stat">
        <img src="<?php echo esc_url( $stat_image['url'] ); ?>" alt="<?php echo esc_attr( $stat_image['alt'] ?? '' ); ?>">
        <?php if ( $stat_text ) : ?>
          <div class="prcw-donation__stat-overlay"></div>
          <p class="prcw-donation__stat-text"><?php echo esc_html( $stat_text ); ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if ( is_array( $logo ) && ! empty( $logo['url'] ) ) : ?>
      <div class="prcw-donation__logo">
        <img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ?? '' ); ?>">
      </div>
    <?php endif; ?>

    <div class="prcw-donation__form-wrap">
      <div class="prcw-donation__form">
        <p class="prcw-donation__label">DONATION</p>
        <?php
        if ( $wc_active && $product_field ) :
          $wc_product = wc_get_product( $product_field );
          if ( $wc_product ) :
            global $product;
            $previous_product = $product ?? null;
            $product          = $wc_product;
            woocommerce_template_single_add_to_cart();
            $product          = $previous_product;
          endif;
        elseif ( is_admin() && ! $wc_active ) :
          echo '<p>' . esc_html__( 'WooCommerce debe estar activo para mostrar el formulario de donación.', 'prcw-theme' ) . '</p>';
        endif;
        ?>
      </div>

      <?php if ( $payment_text ) : ?>
        <p class="prcw-donation__payment-text"><?php echo esc_html( $payment_text ); ?></p>
      <?php endif; ?>
    </div>

  </div>

  <?php if ( $wc_active ) : ?>
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
  <?php endif; ?>
</div>