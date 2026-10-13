<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$slides = get_field( 'slides' );

if ( empty( $slides ) ) {
    return;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-hero-slider' ) );
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="swiper">
    <div class="swiper-wrapper">
      <?php foreach ( $slides as $slide ) :
        $layout   = $slide['layout'] ?: 'centered';
        $bg       = $slide['background_image'];
        $title    = $slide['title'];
        $subtitle = $slide['subtitle'];
        $btn_text = $slide['button_label'];
        $btn_url  = $slide['button_url'];
        $side_img = $slide['side_image'];
	$title_color = $slide['title_color'] ?: '#494848';
      ?>
        <div class="swiper-slide prcw-hero-slider__slide prcw-hero-slider__slide--<?php echo esc_attr( $layout ); ?>"
          <?php if ( $bg ) : ?>
            style="background-image:url('<?php echo esc_url( $bg['url'] ); ?>');"
          <?php endif; ?>
        >
          <div class="prcw-hero-slider__overlay"></div>

          <?php if ( 'image_left' === $layout ) : ?>

            <div class="prcw-hero-slider__content prcw-hero-slider__content--split">
              <?php if ( $side_img ) : ?>
                <div class="prcw-hero-slider__side-image">
                  <img src="<?php echo esc_url( $side_img['url'] ); ?>" alt="<?php echo esc_attr( $side_img['alt'] ); ?>">
                </div>
              <?php endif; ?>

              <div class="prcw-hero-slider__text prcw-hero-slider__text--left">
		<?php if ( $title ) : ?><h2 class="prcw-hero-slider__title" style="color: <?php echo esc_attr( $title_color ); ?>;"><?php echo esc_html( $title ); ?></h2><?php endif; ?>
                <?php if ( $subtitle ) : ?><p class="prcw-hero-slider__subtitle"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
                <?php if ( $btn_text && $btn_url ) : ?>
                  <a class="prcw-hero-slider__button" href="<?php echo esc_url( $btn_url ); ?>"><?php echo esc_html( $btn_text ); ?></a>
                <?php endif; ?>
              </div>
            </div>

          <?php else : ?>

            <div class="prcw-hero-slider__content prcw-hero-slider__content--centered">
              <div class="prcw-hero-slider__text prcw-hero-slider__text--center">
		<?php if ( $title ) : ?><h2 class="prcw-hero-slider__title" style="color: <?php echo esc_attr( $title_color ); ?>;"><?php echo esc_html( $title ); ?></h2><?php endif; ?>
                <?php if ( $subtitle ) : ?><p class="prcw-hero-slider__subtitle"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
                <?php if ( $btn_text && $btn_url ) : ?>
                  <a class="prcw-hero-slider__button" href="<?php echo esc_url( $btn_url ); ?>"><?php echo esc_html( $btn_text ); ?></a>
                <?php endif; ?>
              </div>
            </div>

          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <button class="prcw-hero-slider__prev" aria-label="Anterior">
    <img src="<?php echo esc_url( PRCW_THEME_URI . '/assets/images/arrow-next.svg' ); ?>" alt="">
  </button>
  <button class="prcw-hero-slider__next" aria-label="Siguiente">
    <img src="<?php echo esc_url( PRCW_THEME_URI . '/assets/images/arrow-prev.svg' ); ?>" alt="">
  </button>
</div>
