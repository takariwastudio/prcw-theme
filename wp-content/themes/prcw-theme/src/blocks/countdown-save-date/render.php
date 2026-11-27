<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'get_field' ) ) {
    return;
}

$eyebrow           = get_field( 'eyebrow' ) ?: 'SAVE THE DATE';
$title             = get_field( 'title' ) ?: 'PUERTO RICO COCKTAIL WEEK 2027';
$description       = get_field( 'description' ) ?: 'Get ready for a week of world-class cocktails, educational seminars, pop-ups, and unforgettable hospitality experiences across Puerto Rico.';
$date_badge        = get_field( 'date_badge' ) ?: 'NOVEMBER 2027 • SAN JUAN, PUERTO RICO';
$start_raw         = get_field( 'start_datetime' ) ?: '2027-11-07 18:00:00';
$end_raw           = get_field( 'end_datetime' ) ?: '2027-11-13 23:00:00';
$calendar_title    = get_field( 'calendar_title' ) ?: $title;
$calendar_location = get_field( 'calendar_location' ) ?: 'San Juan, Puerto Rico';
$calendar_details  = get_field( 'calendar_details' ) ?: 'Puerto Rico Cocktail Week 2027 — Stay tuned for the official schedule, participating bars, and tickets at ' . home_url( '/' );
$google_label      = get_field( 'google_button_label' ) ?: 'ADD TO GOOGLE CALENDAR';
$ics_label         = get_field( 'ics_button_label' ) ?: 'APPLE / OUTLOOK (.ICS)';
$live_message      = get_field( 'live_message' ) ?: 'WE ARE LIVE! WELCOME TO PRCW 2027!';
$bg_image          = get_field( 'background_image' );

$pr_tz = new DateTimeZone( 'America/Puerto_Rico' );
$utc   = new DateTimeZone( 'UTC' );

try {
    $start_dt = new DateTimeImmutable( (string) $start_raw, $pr_tz );
} catch ( Exception $e ) {
    $start_dt = new DateTimeImmutable( '2027-11-07 18:00:00', $pr_tz );
}

try {
    $end_dt = new DateTimeImmutable( (string) $end_raw, $pr_tz );
} catch ( Exception $e ) {
    $end_dt = $start_dt->modify( '+6 days' );
}

if ( $end_dt <= $start_dt ) {
    $end_dt = $start_dt->modify( '+4 hours' );
}

$start_utc      = $start_dt->setTimezone( $utc );
$end_utc        = $end_dt->setTimezone( $utc );
$target_utc_iso = $start_utc->format( 'Y-m-d\TH:i:s\Z' );
$cal_start_str  = $start_utc->format( 'Ymd\THis\Z' );
$cal_end_str    = $end_utc->format( 'Ymd\THis\Z' );

// Cálculo inicial en servidor para que incluso sin JS o en el preview del editor muestre números reales
$diff_seconds = max( 0, $start_utc->getTimestamp() - time() );
$init_days    = (int) floor( $diff_seconds / 86400 );
$init_hours   = (int) floor( ( $diff_seconds % 86400 ) / 3600 );
$init_minutes = (int) floor( ( $diff_seconds % 3600 ) / 60 );
$init_seconds = (int) ( $diff_seconds % 60 );

// URL para Google Calendar (RFC 3986)
$google_cal_url = 'https://calendar.google.com/calendar/render?' . http_build_query(
    array(
        'action'   => 'TEMPLATE',
        'text'     => $calendar_title,
        'dates'    => $cal_start_str . '/' . $cal_end_str,
        'details'  => $calendar_details,
        'location' => $calendar_location,
        'ctz'      => 'America/Puerto_Rico',
    ),
    '',
    '&',
    PHP_QUERY_RFC3986
);

// Escape RFC 5545 para archivo .ics universal (iPhone / macOS / Outlook)
$ics_escape = static function ( $value ) {
    $value = str_replace( array( "\\", "\r\n", "\n", "\r", ';', ',' ), array( "\\\\", "\\n", "\\n", "\\n", "\\;", "\\," ), (string) $value );
    return $value;
};

$ics_host  = wp_parse_url( home_url(), PHP_URL_HOST ) ?: 'prcocktailweek.com';
$ics_uid   = 'prcw-' . md5( $cal_start_str . $calendar_title ) . '@' . $ics_host;
$ics_stamp = gmdate( 'Ymd\THis\Z' );
$ics_body  = implode(
    "\r\n",
    array(
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//Puerto Rico Cocktail Week//PRCW Theme//EN',
        'CALSCALE:GREGORIAN',
        'METHOD:PUBLISH',
        'BEGIN:VEVENT',
        'UID:' . $ics_uid,
        'DTSTAMP:' . $ics_stamp,
        'DTSTART:' . $cal_start_str,
        'DTEND:' . $cal_end_str,
        'SUMMARY:' . $ics_escape( $calendar_title ),
        'DESCRIPTION:' . $ics_escape( $calendar_details ),
        'LOCATION:' . $ics_escape( $calendar_location ),
        'URL:' . esc_url_raw( home_url( '/' ) ),
        'END:VEVENT',
        'END:VCALENDAR',
    )
);
$ics_data_uri = 'data:text/calendar;charset=utf-8,' . rawurlencode( $ics_body );
$ics_filename = sanitize_title( $calendar_title ?: 'prcw-save-the-date' ) . '.ics';

$has_bg             = is_array( $bg_image ) && ! empty( $bg_image['url'] );
$block_classes      = 'prcw-countdown' . ( $has_bg ? ' prcw-countdown--has-bg' : '' );
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $block_classes ) );
?>
<section <?php echo $wrapper_attributes; ?>
  data-target-utc="<?php echo esc_attr( $target_utc_iso ); ?>"
  <?php if ( $has_bg ) : ?>
    style="background-image:url('<?php echo esc_url( $bg_image['url'] ); ?>');"
  <?php endif; ?>
>
  <?php if ( $has_bg ) : ?>
    <div class="prcw-countdown__overlay"></div>
  <?php endif; ?>

  <div class="prcw-countdown__inner">

    <?php if ( $eyebrow ) : ?>
      <p class="prcw-countdown__eyebrow">
        <?php if ( function_exists( 'prcw_render_icon' ) ) : ?>
          <span class="prcw-countdown__eyebrow-icon"><?php echo prcw_render_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
        <?php endif; ?>
        <span><?php echo esc_html( $eyebrow ); ?></span>
      </p>
    <?php endif; ?>

    <?php if ( $title ) : ?>
      <h2 class="prcw-countdown__title"><?php echo esc_html( $title ); ?></h2>
    <?php endif; ?>

    <?php if ( $date_badge ) : ?>
      <div class="prcw-countdown__badge">
        <?php if ( function_exists( 'prcw_render_icon' ) ) : ?>
          <span class="prcw-countdown__badge-icon"><?php echo prcw_render_icon( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
        <?php endif; ?>
        <span><?php echo esc_html( $date_badge ); ?></span>
      </div>
    <?php endif; ?>

    <?php if ( $description ) : ?>
      <div class="prcw-countdown__description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
    <?php endif; ?>

    <div class="prcw-countdown__timer<?php echo 0 === $diff_seconds ? ' is-complete' : ''; ?>" role="timer" aria-live="polite">
      <div class="prcw-countdown__unit">
        <span class="prcw-countdown__number" data-unit="days"><?php echo esc_html( $init_days >= 100 ? (string) $init_days : str_pad( (string) $init_days, 2, '0', STR_PAD_LEFT ) ); ?></span>
        <span class="prcw-countdown__label">DAYS</span>
      </div>
      <div class="prcw-countdown__unit">
        <span class="prcw-countdown__number" data-unit="hours"><?php echo esc_html( str_pad( (string) $init_hours, 2, '0', STR_PAD_LEFT ) ); ?></span>
        <span class="prcw-countdown__label">HOURS</span>
      </div>
      <div class="prcw-countdown__unit">
        <span class="prcw-countdown__number" data-unit="minutes"><?php echo esc_html( str_pad( (string) $init_minutes, 2, '0', STR_PAD_LEFT ) ); ?></span>
        <span class="prcw-countdown__label">MINUTES</span>
      </div>
      <div class="prcw-countdown__unit">
        <span class="prcw-countdown__number" data-unit="seconds"><?php echo esc_html( str_pad( (string) $init_seconds, 2, '0', STR_PAD_LEFT ) ); ?></span>
        <span class="prcw-countdown__label">SECONDS</span>
      </div>
    </div>

    <?php if ( $live_message ) : ?>
      <p class="prcw-countdown__live<?php echo 0 === $diff_seconds ? ' is-visible' : ''; ?>">
        <?php echo esc_html( $live_message ); ?>
      </p>
    <?php endif; ?>

    <div class="prcw-countdown__actions">
      <a
        class="prcw-countdown__btn prcw-countdown__btn--primary"
        href="<?php echo esc_url( $google_cal_url ); ?>"
        target="_blank"
        rel="noopener noreferrer"
      >
        <?php if ( function_exists( 'prcw_render_icon' ) ) : ?>
          <span class="prcw-countdown__btn-icon"><?php echo prcw_render_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
        <?php endif; ?>
        <span><?php echo esc_html( $google_label ); ?></span>
      </a>

      <a
        class="prcw-countdown__btn prcw-countdown__btn--secondary"
        href="<?php echo esc_attr( $ics_data_uri ); ?>"
        download="<?php echo esc_attr( $ics_filename ); ?>"
        data-ics="<?php echo esc_attr( $ics_body ); ?>"
      >
        <?php if ( function_exists( 'prcw_render_icon' ) ) : ?>
          <span class="prcw-countdown__btn-icon"><?php echo prcw_render_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
        <?php endif; ?>
        <span><?php echo esc_html( $ics_label ); ?></span>
      </a>
    </div>

  </div>
</section>
