<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<main id="main" class="standard-content section">
<?php if ( have_posts() ) : ?>
    <?php if ( is_home() ) : ?><h1>お知らせ</h1><?php elseif ( is_archive() ) : the_archive_title( '<h1>', '</h1>' ); endif; ?>
    <?php while ( have_posts() ) : the_post(); ?>
    <article <?php post_class(); ?>>
    <?php if ( is_singular() ) : ?><h1><?php the_title(); ?></h1><div class="entry-content"><?php the_content(); wp_link_pages(); ?></div>
    <?php else : ?><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php the_excerpt(); endif; ?>
    </article>
    <?php endwhile; the_posts_pagination(); ?>
<?php else : ?><h1>ページが見つかりませんでした</h1><p><a href="<?php echo esc_url( home_url( '/' ) ); ?>">トップページへ戻る</a></p><?php endif; ?>
</main>
<?php get_footer(); ?>
