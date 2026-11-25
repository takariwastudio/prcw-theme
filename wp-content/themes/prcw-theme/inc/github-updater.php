<?php
/**
 * Actualizador automático del theme desde GitHub Releases.
 *
 * Consulta el último Release publicado en GitHub que contenga el asset compilado
 * `prcw-theme.zip` y habilita la actualización a 1 clic en WP Admin > Apariencia > Temas.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'PRCW_GITHUB_UPDATER_TRANSIENT', 'prcw_github_latest_release' );

/**
 * Repositorio GitHub en formato owner/repo.
 */
function prcw_get_github_repo() {
    $repo = defined( 'PRCW_GITHUB_REPO' ) && PRCW_GITHUB_REPO
        ? PRCW_GITHUB_REPO
        : 'takariwastudio/prcw-theme';

    return apply_filters( 'prcw_github_repo', $repo );
}

/**
 * Token opcional de GitHub (solo necesario si el repositorio pasa a privado o para evitar rate limits).
 */
function prcw_get_github_token() {
    if ( defined( 'PRCW_GITHUB_TOKEN' ) && PRCW_GITHUB_TOKEN ) {
        return trim( (string) PRCW_GITHUB_TOKEN );
    }

    if ( function_exists( 'get_field' ) ) {
        $acf_token = get_field( 'github_token', 'option' );
        if ( is_string( $acf_token ) && '' !== trim( $acf_token ) ) {
            return trim( $acf_token );
        }
    }

    $opt_token = get_option( 'options_github_token' );
    if ( is_string( $opt_token ) && '' !== trim( $opt_token ) ) {
        return trim( $opt_token );
    }

    return trim( (string) apply_filters( 'prcw_github_token', '' ) );
}

/**
 * Obtiene y cachea los datos del último Release de GitHub con asset .zip compilado.
 *
 * @param bool $force Ignorar caché (por ejemplo al pulsar "Comprobar de nuevo" en Escritorio > Actualizaciones).
 * @return array|null
 */
function prcw_get_github_latest_release( $force = false ) {
    if ( ! $force ) {
        $cached = get_site_transient( PRCW_GITHUB_UPDATER_TRANSIENT );
        if ( 'none' === $cached ) {
            return null;
        }
        if ( is_array( $cached ) && ! empty( $cached['version'] ) && ! empty( $cached['package'] ) ) {
            return $cached;
        }
    }

    $repo  = prcw_get_github_repo();
    $token = prcw_get_github_token();
    $url   = sprintf( 'https://api.github.com/repos/%s/releases/latest', $repo );

    $headers = array(
        'Accept'               => 'application/vnd.github+json',
        'User-Agent'           => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url( '/' ),
        'X-GitHub-Api-Version' => '2022-11-28',
    );

    if ( '' !== $token ) {
        $headers['Authorization'] = 'Bearer ' . $token;
    }

    $response = wp_remote_get(
        $url,
        array(
            'timeout' => 10,
            'headers' => $headers,
        )
    );

    if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
        set_site_transient( PRCW_GITHUB_UPDATER_TRANSIENT, 'none', 5 * MINUTE_IN_SECONDS );
        return null;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( ! is_array( $body ) || empty( $body['tag_name'] ) || ! empty( $body['draft'] ) || ! empty( $body['prerelease'] ) ) {
        set_site_transient( PRCW_GITHUB_UPDATER_TRANSIENT, 'none', 5 * MINUTE_IN_SECONDS );
        return null;
    }

    $version = ltrim( trim( (string) $body['tag_name'] ), 'vV' );
    if ( '' === $version ) {
        set_site_transient( PRCW_GITHUB_UPDATER_TRANSIENT, 'none', 5 * MINUTE_IN_SECONDS );
        return null;
    }

    // Busca exclusivamente el paquete compilado prcw-theme.zip (con build/ incluido).
    // Nunca usamos zipball_url porque el repo raíz anida wp-content/themes/prcw-theme/ y no incluye build/.
    $package_url = '';
    if ( ! empty( $body['assets'] ) && is_array( $body['assets'] ) ) {
        foreach ( $body['assets'] as $asset ) {
            $asset_name = isset( $asset['name'] ) ? (string) $asset['name'] : '';
            if ( 'prcw-theme.zip' === $asset_name || str_ends_with( strtolower( $asset_name ), '.zip' ) ) {
                if ( '' !== $token && ! empty( $asset['url'] ) ) {
                    // En repositorios privados la descarga requiere el endpoint API del asset con Accept: application/octet-stream
                    $package_url = (string) $asset['url'];
                } elseif ( ! empty( $asset['browser_download_url'] ) ) {
                    $package_url = (string) $asset['browser_download_url'];
                }
                if ( '' !== $package_url ) {
                    break;
                }
            }
        }
    }

    if ( '' === $package_url ) {
        set_site_transient( PRCW_GITHUB_UPDATER_TRANSIENT, 'none', 5 * MINUTE_IN_SECONDS );
        return null;
    }

    $release_data = array(
        'version'     => $version,
        'package'     => $package_url,
        'details_url' => ! empty( $body['html_url'] ) ? (string) $body['html_url'] : sprintf( 'https://github.com/%s/releases', $repo ),
    );

    set_site_transient( PRCW_GITHUB_UPDATER_TRANSIENT, $release_data, 15 * MINUTE_IN_SECONDS );

    return $release_data;
}

/**
 * Inyecta la actualización disponible en el transient nativo de WordPress.
 */
function prcw_check_github_theme_update( $transient ) {
    if ( ! is_object( $transient ) ) {
        return $transient;
    }

    $stylesheet = get_template();
    $theme      = wp_get_theme( $stylesheet );
    if ( ! $theme->exists() ) {
        return $transient;
    }

    $current_version = (string) $theme->get( 'Version' );
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Solo lectura de bandera nativa de update-core.php
    $force_check     = ! empty( $_GET['force-check'] );
    $release         = prcw_get_github_latest_release( $force_check );

    if ( ! $release || empty( $release['version'] ) || empty( $release['package'] ) ) {
        return $transient;
    }

    $item = array(
        'theme'        => $stylesheet,
        'new_version'  => $release['version'],
        'url'          => $release['details_url'],
        'package'      => $release['package'],
        'requires'     => '6.4',
        'requires_php' => '8.0',
    );

    if ( version_compare( $release['version'], $current_version, '>' ) ) {
        if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
            $transient->response = array();
        }
        $transient->response[ $stylesheet ] = $item;
    } else {
        if ( ! isset( $transient->no_update ) || ! is_array( $transient->no_update ) ) {
            $transient->no_update = array();
        }
        $transient->no_update[ $stylesheet ] = $item;
    }

    return $transient;
}
add_filter( 'pre_set_site_transient_update_themes', 'prcw_check_github_theme_update' );

/**
 * Si hay token configurado y la descarga apunta al endpoint API de assets de GitHub,
 * adjunta las cabeceras requeridas para descargar el binario (soporte de repositorios privados).
 */
function prcw_github_asset_http_args( $args, $url ) {
    $repo       = prcw_get_github_repo();
    $api_prefix = sprintf( 'https://api.github.com/repos/%s/releases/assets/', $repo );

    if ( str_starts_with( (string) $url, $api_prefix ) ) {
        $token = prcw_get_github_token();
        if ( '' !== $token ) {
            if ( ! isset( $args['headers'] ) || ! is_array( $args['headers'] ) ) {
                $args['headers'] = array();
            }
            $args['headers']['Authorization'] = 'Bearer ' . $token;
            $args['headers']['Accept']        = 'application/octet-stream';
        }
    }

    return $args;
}
add_filter( 'http_request_args', 'prcw_github_asset_http_args', 10, 2 );

/**
 * Garantiza que la carpeta extraída del .zip coincida exactamente con el slug del theme (prcw-theme),
 * incluso si el archivo fue comprimido manualmente con sufijo de versión.
 */
function prcw_normalize_upgraded_theme_dir( $source, $remote_source, $upgrader, $hook_extra ) {
    $stylesheet = get_template();

    if ( empty( $hook_extra['theme'] ) || $hook_extra['theme'] !== $stylesheet ) {
        return $source;
    }

    global $wp_filesystem;
    if ( ! $wp_filesystem ) {
        return $source;
    }

    $expected_dir = trailingslashit( $remote_source ) . $stylesheet;
    if ( untrailingslashit( $source ) === untrailingslashit( $expected_dir ) ) {
        return $source;
    }

    if ( $wp_filesystem->move( $source, $expected_dir, true ) ) {
        return trailingslashit( $expected_dir );
    }

    return $source;
}
add_filter( 'upgrader_source_selection', 'prcw_normalize_upgraded_theme_dir', 10, 4 );

/**
 * Limpia la caché del actualizador tras completar una actualización de temas.
 */
function prcw_clear_github_updater_cache( $upgrader, $options ) {
    if ( ! empty( $options['type'] ) && 'theme' === $options['type'] ) {
        delete_site_transient( PRCW_GITHUB_UPDATER_TRANSIENT );
    }
}
add_action( 'upgrader_process_complete', 'prcw_clear_github_updater_cache', 10, 2 );
