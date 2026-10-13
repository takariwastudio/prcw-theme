<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

wp_enqueue_script( 'wc-add-to-cart' );

$section_title = get_field( 'section_title' ) ?: 'PRE-SALE (ALL ACCESS)';
$tickets        = get_field( 'tickets' );

if ( empty( $tickets ) ) {
    return;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-ticket-pricing' ) );
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="prcw-ticket-pricing__inner">

    <?php if ( $section_title ) : ?>
      <h2 class="prcw-ticket-pricing__title"><?php echo esc_html( $section_title ); ?></h2>
    <?php endif; ?>

    <div class="prcw-ticket-pricing__bg">
      <div class="prcw-ticket-pricing__grid">
        <?php foreach ( $tickets as $ticket ) :
          $product_field = $ticket['product'];
          $description   = $ticket['description'];
          $button_label  = $ticket['button_label'] ?: 'BUY NOW';

          if ( ! $product_field ) {
            continue;
          }

          $wc_product = wc_get_product( $product_field );
          if ( ! $wc_product ) {
            continue;
          }

          $name           = $wc_product->get_name();
          $regular_price  = $wc_product->get_regular_price();
          $sale_price     = $wc_product->get_sale_price();
          $is_on_sale     = $wc_product->is_on_sale();
          $product_id     = $wc_product->get_id();
        ?>
          <div class="prcw-ticket-pricing__card">

            <?php if ( $name ) : ?>
              <h3 class="prcw-ticket-pricing__name"><?php echo esc_html( $name ); ?></h3>
            <?php endif; ?>

            <div class="prcw-ticket-pricing__price-box">
  <?php if ( $is_on_sale && $regular_price ) : ?>
    <p class="prcw-ticket-pricing__price-old"><del><?php echo wp_kses_post( wc_price( $regular_price ) ); ?></del></p>
    <p class="prcw-ticket-pricing__price-current"><?php echo wp_kses_post( wc_price( $sale_price ) ); ?> + IVU</p>
  <?php else : ?>
    <p class="prcw-ticket-pricing__price-current"><?php echo wp_kses_post( wc_price( $regular_price ) ); ?> + IVU</p>
  <?php endif; ?>
</div>

            <?php if ( $description ) : ?>
              <div class="prcw-ticket-pricing__description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
            <?php endif; ?>

            <form class="prcw-ticket-pricing__form cart" action="<?php echo esc_url( $wc_product->get_permalink() ); ?>" method="post">
              <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product_id ); ?>" class="prcw-ticket-pricing__button">
                <?php echo esc_html( $button_label ); ?>
              </button>
            </form>

          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

  <div class="prcw-ticket-pricing__modal">
    <div class="prcw-ticket-pricing__modal-overlay"></div>
    <div class="prcw-ticket-pricing__modal-box">
      <p class="prcw-ticket-pricing__modal-text">¡Listo! Tu ticket se agregó al carrito.</p>
      <div class="prcw-ticket-pricing__modal-actions">
        <a class="prcw-ticket-pricing__modal-btn prcw-ticket-pricing__modal-btn--primary" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
          Ir al carrito
        </a>
        <button type="button" class="prcw-ticket-pricing__modal-btn prcw-ticket-pricing__modal-btn--secondary prcw-ticket-pricing__modal-close">
          Seguir navegando
        </button>
      </div>
    </div>
  </div>
</div>