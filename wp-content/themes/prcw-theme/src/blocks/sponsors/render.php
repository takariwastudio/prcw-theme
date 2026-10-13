<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$section_title = get_field( 'section_title' ) ?: 'OUR SPONSORS';
$sponsors       = get_field( 'sponsors' );

if ( empty( $sponsors ) ) {
    return;
}

// Agrupamos por nivel, respetando el orden en que aparecen las filas
$tiers = array();
foreach ( $sponsors as $sponsor ) {
    $tier_name = trim( $sponsor['tier'] ) ?: 'General';
    if ( ! isset( $tiers[ $tier_name ] ) ) {
        $tiers[ $tier_name ] = array();
    }
    $tiers[ $tier_name ][] = $sponsor;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-sponsors' ) );
$first = true;
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="prcw-sponsors__inner">
    <?php if ( $section_title ) : ?>
      <h3 class="prcw-sponsors__title"><?php echo esc_html( $section_title ); ?></h3>
    <?php endif; ?>

    <div class="prcw-sponsors__tabs">
      <?php foreach ( $tiers as $tier_name => $items ) : ?>
        <button
          class="prcw-sponsors__tab <?php echo $first ? 'is-active' : ''; ?>"
          data-tab="<?php echo esc_attr( sanitize_title( $tier_name ) ); ?>"
        >
          <?php echo esc_html( $tier_name ); ?>
        </button>
        <?php $first = false; ?>
      <?php endforeach; ?>
    </div>

    <?php $first = true; ?>
    <?php foreach ( $tiers as $tier_name => $items ) :
      $with_logo    = array_filter( $items, fn( $s ) => ! empty( $s['logo'] ) );
      $without_logo = array_filter( $items, fn( $s ) => empty( $s['logo'] ) );
    ?>
      <div
        class="prcw-sponsors__panel <?php echo $first ? 'is-active' : ''; ?>"
        data-tab="<?php echo esc_attr( sanitize_title( $tier_name ) ); ?>"
      >
        <?php if ( ! empty( $with_logo ) ) : ?>
          <div class="prcw-sponsors__grid">
            <?php foreach ( $with_logo as $sponsor ) :
              $logo = $sponsor['logo'];
              $url  = $sponsor['url'];
              $sponsor_name = $sponsor['name'] ?? '';

              $img_html = sprintf(
                '<img class="prcw-sponsors__logo" src="%s" alt="%s">',
                esc_url( $logo['url'] ),
                esc_attr( $sponsor_name )
              );
            ?>
              <div class="prcw-sponsors__card">
                <?php if ( $url ) : ?>
                  <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo $img_html; ?></a>
                <?php else : ?>
                  <?php echo $img_html; ?>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if ( ! empty( $without_logo ) ) : ?>
          <p class="prcw-sponsors__list">
            <?php
            $names = array_map( fn( $s ) => esc_html( $s['name'] ?? '' ), $without_logo );
            echo implode( ', ', $names );
            ?>
          </p>
        <?php endif; ?>
      </div>
      <?php $first = false; ?>
    <?php endforeach; ?>
  </div>
</div>
