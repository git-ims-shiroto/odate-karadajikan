<?php defined( 'ABSPATH' ) || exit; $lp = ok_lp_content(); ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">本文へ移動</a>
<header class="site-header">
    <a class="brand" href="<?php echo esc_url( ok_lp_is_landing() ? '#top' : home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( $lp['general']['site_name'] ); ?> トップへ">
        <span class="brand-mark"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo.png' ) ); ?>" alt="ひめトレ®" width="160" height="160"></span>
        <span><b><?php echo esc_html( $lp['general']['site_name'] ); ?></b><small><?php echo esc_html( $lp['general']['tagline'] ); ?></small></span>
    </a>
    <nav aria-label="ページ内メニュー">
        <a href="<?php echo esc_url( ok_lp_anchor( 'lessons' ) ); ?>">教室について</a>
        <a href="<?php echo esc_url( ok_lp_anchor( 'schedule' ) ); ?>">料金・日程</a>
        <a href="<?php echo esc_url( ok_lp_anchor( 'trial' ) ); ?>">体験申込み</a>
    </nav>
</header>
