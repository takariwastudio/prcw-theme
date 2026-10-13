<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$section_title = get_field( 'section_title' ) ?: 'SERVICES';
$columns        = get_field( 'columns' );
$note           = get_field( 'note' );

if ( empty( $columns ) ) {
    return;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'prcw-services' ) );
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="prcw-services__inner">

    <?php if ( $section_title ) : ?>
      <h2 class="prcw-services__title"><?php echo esc_html( $section_title ); ?></h2>
    <?php endif; ?>

    <div class="prcw-services__columns">
      <?php foreach ( $columns as $column ) :
        $column_title = $column['column_title'];
        $items        = $column['items'];
      ?>
        <div class="prcw-services__column">
          <?php if ( $column_title ) : ?>
            <div class="prcw-services__column-header"><?php echo esc_html( $column_title ); ?></div>
          <?php endif; ?>

          <div class="prcw-services__column-body">
            <?php if ( ! empty( $items ) ) : ?>
              <?php foreach ( $items as $item ) :
                $icon  = $item['icon'];
                $label = $item['label'];
              ?>
                <div class="prcw-services__item">
                  <?php if ( $icon ) : ?>
                    <span class="prcw-services__icon"><?php echo prcw_render_icon( $icon ); ?></span>
                  <?php endif; ?>
                  <?php if ( $label ) : ?>
                    <span class="prcw-services__label"><?php echo esc_html( $label ); ?></span>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if ( $note ) : ?>
      <p class="prcw-services__note"><?php echo esc_html( $note ); ?></p>
    <?php endif; ?>

  </div>
</div>