<?php get_header(); while (have_posts()) : the_post();
$categories = get_the_category();
$category_names = wp_list_pluck($categories, 'name');
$related = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 3,
    'post__not_in' => [get_the_ID()],
    'ignore_sticky_posts' => true,
]);
?>
<header class="story-hero">
  <div class="wrap story-hero__inner">
    <nav class="story-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo esc_url(home_url('/stories/')); ?>"><?php esc_html_e('The journal', 'travel-riddle'); ?></a><span aria-hidden="true">/</span><span><?php echo esc_html($category_names[0] ?? __('Story', 'travel-riddle')); ?></span></nav>
    <p class="eyebrow"><?php echo esc_html(implode(' · ', $category_names)); ?></p>
    <h1><?php the_title(); ?></h1>
    <div class="story-meta">
      <span><?php printf(esc_html__('By %s', 'travel-riddle'), esc_html(get_the_author())); ?></span>
      <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>
      <span><?php printf(esc_html__('%d min read', 'travel-riddle'), travel_riddle_reading_time()); ?></span>
    </div>
  </div>
</header>

<?php if (has_post_thumbnail()) : ?>
<figure class="story-feature">
  <div class="story-feature__frame"><?php the_post_thumbnail('full', ['loading' => 'eager', 'fetchpriority' => 'high']); ?></div>
  <?php if (get_the_post_thumbnail_caption()) : ?><figcaption><?php echo esc_html(get_the_post_thumbnail_caption()); ?></figcaption><?php endif; ?>
</figure>
<?php endif; ?>

<section class="story-reading">
  <div class="wrap story-layout">
    <aside class="story-rail" aria-label="Story details">
      <p class="story-rail__label"><?php esc_html_e('Field notes', 'travel-riddle'); ?></p>
      <dl>
        <div><dt><?php esc_html_e('Published', 'travel-riddle'); ?></dt><dd><time datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"><?php echo esc_html(get_the_date('M j, Y')); ?></time></dd></div>
        <div><dt><?php esc_html_e('Reading time', 'travel-riddle'); ?></dt><dd><?php printf(esc_html__('%d minutes', 'travel-riddle'), travel_riddle_reading_time()); ?></dd></div>
        <?php if ($categories) : ?><div><dt><?php esc_html_e('Journey', 'travel-riddle'); ?></dt><dd><?php foreach ($categories as $index => $category) : ?><?php echo $index ? ', ' : ''; ?><a href="<?php echo esc_url(get_category_link($category)); ?>"><?php echo esc_html($category->name); ?></a><?php endforeach; ?></dd></div><?php endif; ?>
      </dl>
      <a class="story-rail__back" href="<?php echo esc_url(home_url('/stories/')); ?>"><?php esc_html_e('All stories', 'travel-riddle'); ?></a>
    </aside>
    <article class="story-content">
      <?php the_content(); ?>
      <footer class="story-signoff" aria-label="End of story"><span aria-hidden="true">✦</span><p><?php esc_html_e('Keep following the road that makes you curious.', 'travel-riddle'); ?></p></footer>
    </article>
  </div>
</section>

<nav class="wrap story-navigation" aria-label="More stories">
  <?php previous_post_link('<div class="story-navigation__item story-navigation__prev"><span>' . esc_html__('Previous story', 'travel-riddle') . '</span>%link</div>', '%title'); ?>
  <?php next_post_link('<div class="story-navigation__item story-navigation__next"><span>' . esc_html__('Next story', 'travel-riddle') . '</span>%link</div>', '%title'); ?>
</nav>

<?php if ($related->have_posts()) : ?>
<section class="story-related">
  <div class="wrap">
    <div class="section-head"><div><p class="eyebrow"><?php esc_html_e('Continue exploring', 'travel-riddle'); ?></p><h2><?php esc_html_e('More stories from the road', 'travel-riddle'); ?></h2></div><a class="text-link" href="<?php echo esc_url(home_url('/stories/')); ?>"><?php esc_html_e('View the journal', 'travel-riddle'); ?></a></div>
    <div class="story-related__grid">
      <?php while ($related->have_posts()) : $related->the_post(); ?>
      <article class="story-related__card">
        <a class="story-related__image" href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) { the_post_thumbnail('travel-riddle-card'); } ?></a>
        <div><p class="eyebrow"><?php echo esc_html(implode(' · ', wp_list_pluck(get_the_category(), 'name'))); ?></p><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><a class="text-link" href="<?php the_permalink(); ?>"><?php esc_html_e('Read story', 'travel-riddle'); ?></a></div>
      </article>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
  </div>
</section>
<?php endif; ?>
<?php endwhile; get_footer(); ?>
