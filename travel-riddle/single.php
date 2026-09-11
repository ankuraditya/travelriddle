<?php get_header(); while (have_posts()) : the_post(); ?>
<header class="page-hero"><div class="wrap"><p class="eyebrow"><?php echo esc_html(implode(' · ', wp_list_pluck(get_the_category(), 'name'))); ?> · <?php echo esc_html(travel_riddle_reading_time()); ?> min read</p><h1><?php the_title(); ?></h1></div></header>
<?php if (has_post_thumbnail()) : ?><div class="wrap" style="margin-top:60px"><?php the_post_thumbnail('full', ['style' => 'width:100%;max-height:720px;object-fit:cover']); ?></div><?php endif; ?>
<article class="content-area"><?php the_content(); ?></article>
<?php endwhile; get_footer(); ?>
