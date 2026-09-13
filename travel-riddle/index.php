<?php get_header(); ?>
<header class="page-hero page-hero--stories"><div class="wrap"><p class="eyebrow"><?php esc_html_e('Travel journal', 'travel-riddle'); ?></p><h1><?php echo is_archive() ? esc_html(get_the_archive_title()) : esc_html__('Stories', 'travel-riddle'); ?></h1></div></header>
<main class="stories-archive">
  <section class="section">
    <div class="wrap">
      <div class="stories-archive__intro"><p class="eyebrow"><?php esc_html_e('Latest field notes', 'travel-riddle'); ?></p><h2><?php esc_html_e('Stories for the curious traveller', 'travel-riddle'); ?></h2><p><?php esc_html_e('Places, people and practical perspectives—collected one meaningful journey at a time.', 'travel-riddle'); ?></p></div>
      <div class="story-index-list">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article class="story-index-card">
          <a class="story-index-card__image" href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) { the_post_thumbnail('travel-riddle-card'); } ?></a>
          <div class="story-index-card__body">
            <p class="eyebrow"><?php echo esc_html(implode(' · ', wp_list_pluck(get_the_category(), 'name'))); ?> · <?php echo esc_html(travel_riddle_reading_time()); ?> min read</p>
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <p><?php echo esc_html(wp_trim_words(wp_strip_all_tags(get_the_content()), 28, '…')); ?></p>
            <a class="text-link" href="<?php the_permalink(); ?>"><?php esc_html_e('Read story', 'travel-riddle'); ?></a>
          </div>
        </article>
        <?php endwhile; the_posts_pagination(); else : ?><p><?php esc_html_e('New stories are on their way.', 'travel-riddle'); ?></p><?php endif; ?>
      </div>
    </div>
  </section>
</main>
<?php get_footer(); ?>
