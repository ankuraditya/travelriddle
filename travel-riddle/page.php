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
    <?php if ('about' === $slug) : ?><p class="page-hero__intro"><?php esc_html_e('Travel gives every journey a soul—and every destination a story worth understanding.', 'travel-riddle'); ?></p><?php endif; ?>
    <?php if ('contact' === $slug) : ?><p class="page-hero__intro"><?php esc_html_e('Ideas, questions and stories from the road are always welcome.', 'travel-riddle'); ?></p><?php endif; ?>
    <?php if ('destinations' === $slug) : ?><p class="page-hero__intro"><?php esc_html_e('Explore wild landscapes, living cities and coastlines that invite you to stay a little longer.', 'travel-riddle'); ?></p><?php endif; ?>
  </div>
</header>

<?php if ('destinations' === $slug) :
    $image_map = ['mountains-and-wild-places' => 'story-mountains.png', 'cities-and-culture' => 'story-city.png', 'coasts-and-islands' => 'story-coast.png'];
    $terms = get_terms(['taxonomy' => 'destination', 'hide_empty' => false]);
?>
<main class="inner-page destinations-page">
  <section class="section inner-intro"><div class="wrap inner-intro__grid"><p class="eyebrow"><?php esc_html_e('Where will curiosity lead?', 'travel-riddle'); ?></p><div><?php the_content(); ?></div></div></section>
  <section class="section destination-directory"><div class="wrap"><div class="section-head"><div><p class="eyebrow"><?php esc_html_e('Browse by landscape', 'travel-riddle'); ?></p><h2><?php esc_html_e('Find your next story', 'travel-riddle'); ?></h2></div><p><?php esc_html_e('Every collection grows as new field notes, guides and first-hand perspectives are published.', 'travel-riddle'); ?></p></div>
    <div class="destination-grid destination-grid--directory">
      <?php if (!is_wp_error($terms) && $terms) : foreach ($terms as $term) : $image = $image_map[$term->slug] ?? 'intro-journey.png'; ?>
        <a class="destination-card" href="<?php echo esc_url(get_term_link($term)); ?>"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/' . $image); ?>" alt=""><div><span><?php echo esc_html(sprintf(_n('%d published story', '%d published stories', $term->count, 'travel-riddle'), $term->count)); ?></span><h3><?php echo esc_html($term->name); ?></h3><b aria-hidden="true">&#8599;</b></div></a>
      <?php endforeach; endif; ?>
    </div>
  </div></section>
</main>

<?php elseif ('about' === $slug) : ?>
<main class="inner-page about-page">
  <section class="section about-story"><div class="wrap about-story__grid">
    <aside class="about-story__aside"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/intro-journey.png'); ?>" alt="<?php esc_attr_e('Traveler overlooking a mountain landscape', 'travel-riddle'); ?>"><p><?php esc_html_e('“Words and pictures may inspire a journey, but travel gives that journey a soul.”', 'travel-riddle'); ?></p></aside>
    <article class="about-story__content"><?php the_content(); ?></article>
  </div></section>
  <section class="founder-feature"><div class="wrap founder-feature__grid"><div class="founder-feature__portrait"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/founder.png'); ?>" alt="<?php esc_attr_e('MD. Hassan, founder of Travel Riddle', 'travel-riddle'); ?>"></div><div><p class="eyebrow"><?php esc_html_e('Meet the founder', 'travel-riddle'); ?></p><h2>MD. HASSAN</h2><p class="founder-feature__role"><?php esc_html_e('15+ years in the travel domain', 'travel-riddle'); ?></p><p><?php esc_html_e('His experience across destinations, traveler needs and the wider industry shapes Travel Riddle’s thoughtful, honest and practical editorial approach.', 'travel-riddle'); ?></p><a class="button" href="<?php echo esc_url(home_url('/stories/')); ?>"><?php esc_html_e('Read the latest stories', 'travel-riddle'); ?></a></div></div></section>
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
