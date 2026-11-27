<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'get_field' ) ) {
    return;
}

$section_title = get_field( 'section_title' ) ?: 'OFFICIAL BARS GUIDE';
$intro_text     = get_field( 'intro_text' );
$bars           = get_field( 'bars' );

if ( empty( $bars ) || ! is_array( $bars ) ) {
    return;
}

// Ciudades únicas, en el orden en que aparecen
$cities = array();
foreach ( $bars as $bar ) {
    $city = trim( (string) ( $bar['city'] ?? '' ) );
    if ( $city && ! in_array( $city, $cities, true ) ) {
        $cities[] = $city;
    }
}

// Datos para el mapa (usa lat/lng manuales o los extrae automáticamente del enlace de Google Maps)
$map_points = array();
foreach ( $bars as $bar ) {
    $lat      = $bar['lat'] ?? '';
    $lng      = $bar['lng'] ?? '';
    $maps_url = (string) ( $bar['maps_url'] ?? '' );

    if ( ( '' === $lat || '' === $lng || ! is_numeric( $lat ) || ! is_numeric( $lng ) ) && '' !== $maps_url ) {
        if ( preg_match( '/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $maps_url, $m ) ) {
            $lat = $m[1];
            $lng = $m[2];
        } elseif ( preg_match( '/@(-?\d+\.\d+),(-?\d+\.\d+)/', $maps_url, $m ) ) {
            $lat = $m[1];
            $lng = $m[2];
        } elseif ( preg_match( '/[?&]q=(-?\d+\.\d+),(-?\d+\.\d+)/', $maps_url, $m ) ) {
            $lat = $m[1];
            $lng = $m[2];
        }
    }

    if ( '' !== $lat && '' !== $lng && is_numeric( $lat ) && is_numeric( $lng ) ) {
        $map_points[] = array(
            'lat'  => floatval( $lat ),
            'lng'  => floatval( $lng ),
            'name' => sanitize_text_field( (string) ( $bar['name'] ?? '' ) ),
            'city' => sanitize_text_field( (string) ( $bar['city'] ?? '' ) ),
        );
    }
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-bars-directory' ) );
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="prcw-bars-directory__inner">

    <?php if ( $section_title ) : ?>
      <h1 class="prcw-bars-directory__title"><?php echo esc_html( $section_title ); ?></h1>
    <?php endif; ?>

    <?php if ( $intro_text ) : ?>
      <div class="prcw-bars-directory__intro"><?php echo wp_kses_post( wpautop( $intro_text ) ); ?></div>
    <?php endif; ?>

    <?php if ( ! empty( $cities ) ) : ?>
      <div class="prcw-bars-directory__filters">
        <button type="button" class="prcw-bars-directory__filter is-active" data-city="all">All</button>
        <?php foreach ( $cities as $city ) : ?>
          <button type="button" class="prcw-bars-directory__filter" data-city="<?php echo esc_attr( $city ); ?>">
            <?php echo esc_html( $city ); ?>
          </button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div
      class="prcw-bars-directory__map"
      data-points="<?php echo esc_attr( wp_json_encode( $map_points ) ); ?>"
    ></div>

    <div class="prcw-bars-directory__grid">
      <?php foreach ( $bars as $bar ) :
        $photo       = $bar['photo'] ?? null;
        $name        = $bar['name'] ?? '';
        $city        = $bar['city'] ?? '';
        $description = $bar['description'] ?? '';
        $phone       = $bar['phone'] ?? '';
        $social_url  = $bar['social_url'] ?? '';
        $maps_url    = $bar['maps_url'] ?? '';
      ?>
        <div class="prcw-bars-directory__card" data-city="<?php echo esc_attr( $city ); ?>">

          <?php if ( is_array( $photo ) && ! empty( $photo['url'] ) ) : ?>
            <div class="prcw-bars-directory__card-image">
              <img src="<?php echo esc_url( $photo['url'] ); ?>" alt="<?php echo esc_attr( $name ); ?>">
            </div>
          <?php endif; ?>

          <div class="prcw-bars-directory__card-content">
            <?php if ( $name ) : ?>
              <h3 class="prcw-bars-directory__card-name"><?php echo esc_html( $name ); ?></h3>
            <?php endif; ?>

            <?php if ( $city ) : ?>
              <p class="prcw-bars-directory__card-city"><?php echo esc_html( $city ); ?></p>
            <?php endif; ?>

            <?php if ( $description ) : ?>
              <p class="prcw-bars-directory__card-desc"><?php echo esc_html( $description ); ?></p>
            <?php endif; ?>

            <div class="prcw-bars-directory__card-links">
              <?php if ( $phone ) : ?>
                <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>" class="prcw-bars-directory__card-link">
                  📞 <?php echo esc_html( $phone ); ?>
                </a>
              <?php endif; ?>

              <?php if ( $social_url ) : ?>
                <a href="<?php echo esc_url( $social_url ); ?>" target="_blank" rel="noopener noreferrer" class="prcw-bars-directory__card-link">
                  Social
                </a>
              <?php endif; ?>

              <?php if ( $maps_url ) : ?>
                <a href="<?php echo esc_url( $maps_url ); ?>" target="_blank" rel="noopener noreferrer" class="prcw-bars-directory__card-link">
                  Directions
                </a>
              <?php endif; ?>
            </div>
          </div>

        </div>
      <?php endforeach; ?>
    </div>

  </div>
</div>