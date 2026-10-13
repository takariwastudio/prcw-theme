<?php
/**
 * Render del bloque Hero
 * Variables disponibles vía ACF: get_field()
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$titulo    = get_field( 'hero_titulo' );
$texto     = get_field( 'hero_texto' );
$imagen    = get_field( 'hero_imagen' );
$boton_texto = get_field( 'hero_boton_texto' );
$boton_url   = get_field( 'hero_boton_url' );

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'prcw-hero',
) );
?>

<section <?php echo $wrapper_attributes; ?>
  <?php if ( $imagen ) : ?>
    style="background-image:url('<?php echo esc_url( $imagen['url'] ); ?>');"
  <?php endif; ?>
>
    <div class="prcw-hero__content">
      <?php if ( $titulo ) : ?>
        <h1 class="prcw-hero__title"><?php echo esc_html( $titulo ); ?></h1>
      <?php endif; ?>

      <?php if ( $texto ) : ?>
        <p class="prcw-hero__text"><?php echo esc_html( $texto ); ?></p>
      <?php endif; ?>

      <?php if ( $boton_texto && $boton_url ) : ?>
        <a class="prcw-hero__button" href="<?php echo esc_url( $boton_url ); ?>">
          <?php echo esc_html( $boton_texto ); ?>
        </a>
      <?php endif; ?>
    </div>
</section>
