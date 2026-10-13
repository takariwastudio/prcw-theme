<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$content_pieces = get_field( 'content_pieces' );

if ( empty( $content_pieces ) ) {
    return;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-rich-content' ) );
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="prcw-rich-content__inner">

    <?php foreach ( $content_pieces as $piece ) : ?>

      <?php if ( 'heading' === $piece['acf_fc_layout'] ) :
        $text  = $piece['text'];
        $size  = $piece['size'] ?: 'lg';
        $color = $piece['color'] ?: 'gray';
        if ( ! $text ) continue;
      ?>
        <h2 class="prcw-rich-content__heading prcw-rich-content__heading--<?php echo esc_attr( $size ); ?> prcw-rich-content__heading--<?php echo esc_attr( $color ); ?>">
          <?php echo esc_html( $text ); ?>
        </h2>

      <?php elseif ( 'list' === $piece['acf_fc_layout'] ) :
        $items     = $piece['items'];
        $list_type = $piece['list_type'] ?: 'bullet';
        if ( ! $items ) continue;
        $lines = array_filter( array_map( 'trim', explode( "\n", $items ) ) );
        if ( empty( $lines ) ) continue;
        $tag = 'numbered' === $list_type ? 'ol' : 'ul';
      ?>
        <<?php echo $tag; ?> class="prcw-rich-content__list prcw-rich-content__list--<?php echo esc_attr( $list_type ); ?>">
          <?php foreach ( $lines as $line ) : ?>
            <li><?php echo esc_html( $line ); ?></li>
          <?php endforeach; ?>
        </<?php echo $tag; ?>>

      <?php elseif ( 'paragraph' === $piece['acf_fc_layout'] ) :
        $content = $piece['content'];
        if ( ! $content ) continue;
      ?>
        <div class="prcw-rich-content__paragraph">
          <?php echo wp_kses_post( $content ); ?>
        </div>

      <?php endif; ?>

    <?php endforeach; ?>

  </div>
</div>