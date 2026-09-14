<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#main"><?php esc_html_e('Skip to content', 'travel-riddle'); ?></a>
<header class="site-header">
  <div class="site-header__inner wrap">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php esc_attr_e('Travel Riddle home', 'travel-riddle'); ?>">
      <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/travel-riddle-logo-transparent.png'); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
    </a>
    <nav class="main-nav" id="site-navigation" aria-label="<?php esc_attr_e('Primary navigation', 'travel-riddle'); ?>">
      <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'menu_class' => 'nav-list', 'fallback_cb' => 'travel_riddle_fallback_menu']); ?>
    </nav>
    <div class="header-actions">
      <a class="text-link" href="<?php echo esc_url(home_url('/?s=')); ?>"><?php esc_html_e('Search', 'travel-riddle'); ?></a>
      <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-navigation" aria-label="<?php esc_attr_e('Open menu', 'travel-riddle'); ?>"><span></span><span></span></button>
    </div>
  </div>
</header>
<main id="main">
