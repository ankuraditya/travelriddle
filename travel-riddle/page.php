<?php
get_header();

while (have_posts()) :
    the_post();
    $slug = get_post_field('post_name', get_the_ID());
?>
<header class="page-hero page-hero--<?php echo esc_attr($slug); ?>">
  <div class="wrap">
    <p class="eyebrow"><?php echo 'about' === $slug ? esc_html__('Our story', 'travel-riddle') : ('contact' === $slug ? esc_html__('Start a conversation', 'travel-riddle') : ('destinations' === $slug ? esc_html__('Choose your horizon', 'travel-riddle') : esc_html__('Travel Riddle', 'travel-riddle'))); ?></p>
    <h1><?php the_title(); ?></h1>
    <?php if ('contact' === $slug) : ?><p class="page-hero__intro"><?php esc_html_e('Ideas, questions and stories from the road are always welcome.', 'travel-riddle'); ?></p><?php endif; ?>
    <?php if ('destinations' === $slug) : ?><p class="page-hero__intro"><?php esc_html_e('Explore wild landscapes, living cities and coastlines that invite you to stay a little longer.', 'travel-riddle'); ?></p><?php endif; ?>
  </div>
</header>

<?php if ('destinations' === $slug) :
    $image_map = ['mountains-and-wild-places' => 'story-mountains.png', 'cities-and-culture' => 'story-city.png', 'coasts-and-islands' => 'story-coast.png'];
    $description_map = [
        'mountains-and-wild-places' => __('High trails, open skies and landscapes that put everyday life back into perspective.', 'travel-riddle'),
        'cities-and-culture' => __('Street-level stories, local traditions and the character revealed beyond the landmarks.', 'travel-riddle'),
        'coasts-and-islands' => __('Salt-air escapes, quiet coves and places shaped by the rhythm of the water.', 'travel-riddle'),
    ];
    $terms = get_terms(['taxonomy' => 'destination', 'hide_empty' => false]);
    $terms = is_wp_error($terms) ? [] : $terms;
    $published_stories = array_sum(array_map(static fn($term): int => (int) $term->count, $terms));
?>
<main class="inner-page destinations-page">
  <section class="section destinations-intro"><div class="wrap destinations-intro__grid">
    <div class="destinations-intro__copy"><p class="eyebrow"><?php esc_html_e('Where will curiosity lead?', 'travel-riddle'); ?></p><div><?php the_content(); ?></div></div>
    <aside class="destination-atlas" aria-label="<?php esc_attr_e('Travel Riddle destination overview', 'travel-riddle'); ?>">
      <p class="destination-atlas__label"><?php esc_html_e('The Travel Riddle atlas', 'travel-riddle'); ?></p>
      <dl><div><dt><?php echo esc_html(count($terms)); ?></dt><dd><?php esc_html_e('Landscapes to explore', 'travel-riddle'); ?></dd></div><div><dt><?php echo esc_html($published_stories); ?></dt><dd><?php echo esc_html(_n('Published story', 'Published stories', $published_stories, 'travel-riddle')); ?></dd></div></dl>
      <p><?php esc_html_e('Made for travellers who want to understand a place—not simply pass through it.', 'travel-riddle'); ?></p>
    </aside>
  </div></section>
  <section class="section destination-directory"><div class="wrap">
    <div class="destination-directory__head"><div><p class="eyebrow"><?php esc_html_e('Browse by landscape', 'travel-riddle'); ?></p><h2><?php esc_html_e('Find your next story', 'travel-riddle'); ?></h2></div><p><?php esc_html_e('Choose the kind of place calling to you. Every collection grows as new field notes and first-hand perspectives are published.', 'travel-riddle'); ?></p></div>
    <div class="destination-collections">
      <?php foreach ($terms as $index => $term) :
          $image = $image_map[$term->slug] ?? 'intro-journey.png';
          $description = trim(wp_strip_all_tags(term_description($term)));
          $description = $description ?: ($description_map[$term->slug] ?? __('Travel inspiration, useful perspectives and memorable stories from the road.', 'travel-riddle'));
      ?>
        <a class="destination-collection" href="<?php echo esc_url(get_term_link($term)); ?>">
          <figure><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/' . $image); ?>" alt="<?php echo esc_attr(sprintf(__('Explore %s', 'travel-riddle'), $term->name)); ?>"></figure>
          <div class="destination-collection__body"><div class="destination-collection__meta"><span><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span><span><?php echo esc_html(sprintf(_n('%d story', '%d stories', $term->count, 'travel-riddle'), $term->count)); ?></span></div><h3><?php echo esc_html($term->name); ?></h3><p><?php echo esc_html(wp_trim_words($description, 22, '…')); ?></p><span class="destination-collection__link"><?php esc_html_e('Explore collection', 'travel-riddle'); ?> <b aria-hidden="true">&#8594;</b></span></div>
        </a>
      <?php endforeach; ?>
    </div>
  </div></section>
</main>

<?php elseif ('about' === $slug) : ?>
<main class="inner-page about-page">
  <section class="section about-story"><div class="wrap about-story__grid">
    <aside class="about-story__aside about-story__aside--founder"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/founder.png'); ?>" alt="<?php esc_attr_e('Founder of Travel Riddle', 'travel-riddle'); ?>"></aside>
    <article class="about-story__content"><?php the_content(); ?></article>
  </div></section>
</main>

<?php elseif ('contact' === $slug) : ?>
<main class="inner-page contact-page"><section class="section"><div class="wrap contact-layout">
  <div class="contact-copy"><p class="eyebrow"><?php esc_html_e('Contact Travel Riddle', 'travel-riddle'); ?></p><h2><?php esc_html_e('What would you like to share?', 'travel-riddle'); ?></h2><?php the_content(); ?><div class="contact-detail"><span><?php esc_html_e('Email', 'travel-riddle'); ?></span><a href="mailto:hello@travelriddle.com">hello@travelriddle.com</a></div></div>
  <div class="contact-panel">
    <?php if (isset($_GET['contact']) && 'sent' === sanitize_key(wp_unslash($_GET['contact']))) : ?><div class="form-notice form-notice--success" role="status"><?php esc_html_e('Thank you. Your message has been sent successfully.', 'travel-riddle'); ?></div><?php elseif (isset($_GET['contact']) && 'error' === sanitize_key(wp_unslash($_GET['contact']))) : ?><div class="form-notice form-notice--error" role="alert"><?php esc_html_e('Your message could not be sent. Please check the fields and try again.', 'travel-riddle'); ?></div><?php endif; ?>
    <form class="contact-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
      <input type="hidden" name="action" value="travel_riddle_contact"><?php wp_nonce_field('travel_riddle_contact', 'travel_riddle_nonce'); ?>
      <p class="form-trap" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>
      <div class="form-row"><p><label for="contact-name"><?php esc_html_e('Your name', 'travel-riddle'); ?></label><input id="contact-name" name="name" type="text" required autocomplete="name"></p><p><label for="contact-email"><?php esc_html_e('Email address', 'travel-riddle'); ?></label><input id="contact-email" name="email" type="email" required autocomplete="email"></p></div>
      <p><label for="contact-subject"><?php esc_html_e('Subject', 'travel-riddle'); ?></label><input id="contact-subject" name="subject" type="text" required></p>
      <p><label for="contact-message"><?php esc_html_e('Your message', 'travel-riddle'); ?></label><textarea id="contact-message" name="message" rows="7" required></textarea></p>
      <button class="button" type="submit"><?php esc_html_e('Send message', 'travel-riddle'); ?></button>
    </form>
  </div>
</div></section></main>

<?php else : ?>
<main class="inner-page"><article class="content-area"><?php the_content(); ?></article></main>
<?php endif; endwhile; get_footer(); ?>
