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
    <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <span class="brand-mark" aria-hidden="true">体</span>
        <span><b><?php echo esc_html( $lp['general']['site_name'] ); ?></b><small><?php echo esc_html( $lp['general']['tagline'] ); ?></small></span>
    </a>
    <nav aria-label="ページ内メニュー">
        <a href="<?php echo esc_url( ok_lp_anchor( 'lessons' ) ); ?>">教室について</a>
        <a href="<?php echo esc_url( ok_lp_anchor( 'schedule' ) ); ?>">料金・日程</a>
        <a href="<?php echo esc_url( ok_lp_anchor( 'trial' ) ); ?>">体験申込み</a>
    </nav>
</header>
