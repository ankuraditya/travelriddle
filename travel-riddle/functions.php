<?php
if (!defined('ABSPATH')) { exit; }

define('TRAVEL_RIDDLE_VERSION', '1.5.1');

function travel_riddle_setup(): void {
    load_theme_textdomain('travel-riddle', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('custom-logo', ['height' => 160, 'width' => 340, 'flex-height' => true, 'flex-width' => true]);
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    register_nav_menus(['primary' => __('Primary navigation', 'travel-riddle'), 'footer' => __('Footer navigation', 'travel-riddle')]);
    add_image_size('travel-riddle-card', 960, 720, true);
}
add_action('after_setup_theme', 'travel_riddle_setup');

function travel_riddle_register_content(): void {
    register_taxonomy('destination', ['post'], [
        'labels' => ['name' => __('Destinations', 'travel-riddle'), 'singular_name' => __('Destination', 'travel-riddle')],
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'destination', 'hierarchical' => true],
    ]);
}
add_action('init', 'travel_riddle_register_content');

function travel_riddle_assets(): void {
    wp_enqueue_style('travel-riddle', get_stylesheet_uri(), [], TRAVEL_RIDDLE_VERSION);
    wp_enqueue_script('travel-riddle', get_template_directory_uri() . '/assets/js/site.js', [], TRAVEL_RIDDLE_VERSION, true);
}
add_action('wp_enqueue_scripts', 'travel_riddle_assets');

function travel_riddle_fallback_menu(): void {
    $items = [
        home_url('/') => __('Home', 'travel-riddle'),
        home_url('/destinations/') => __('Destinations', 'travel-riddle'),
        home_url('/stories/') => __('Stories', 'travel-riddle'),
        home_url('/about/') => __('About', 'travel-riddle'),
        home_url('/contact/') => __('Contact', 'travel-riddle'),
    ];
    echo '<ul class="nav-list">';
    foreach ($items as $url => $label) {
        printf('<li><a href="%s">%s</a></li>', esc_url($url), esc_html($label));
    }
    echo '</ul>';
}

function travel_riddle_reading_time(int $post_id = 0): int {
    $content = get_post_field('post_content', $post_id ?: get_the_ID());
    return max(1, (int) ceil(str_word_count(wp_strip_all_tags($content)) / 220));
}

function travel_riddle_excerpt(int $words = 24): string {
    $excerpt = trim(get_the_excerpt());
    if (str_contains($excerpt, 'class=') || strlen($excerpt) < 24) {
        $excerpt = wp_strip_all_tags(get_post_field('post_content', get_the_ID()));
    }
    return wp_trim_words($excerpt, $words, '…');
}

function travel_riddle_seo_description(): string {
    if (is_singular()) {
        $description = has_excerpt() ? get_the_excerpt() : wp_strip_all_tags(get_post_field('post_content', get_queried_object_id()));
        return wp_trim_words($description, 28, '…');
    }
    if (is_category() || is_tag() || is_tax()) {
        return wp_strip_all_tags(term_description()) ?: sprintf(__('Explore travel stories and practical inspiration about %s from Travel Riddle.', 'travel-riddle'), single_term_title('', false));
    }
    return get_bloginfo('description') ?: __('Thoughtful travel stories, destination inspiration and practical insight for curious travelers.', 'travel-riddle');
}

function travel_riddle_seo_head(): void {
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) { return; }
    $description = travel_riddle_seo_description();
    $canonical = is_singular() ? get_permalink() : home_url(wp_unslash($_SERVER['REQUEST_URI'] ?? '/'));
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr(wp_get_document_title()) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($canonical) . '">' . "\n";
    echo '<meta property="og:type" content="' . (is_singular('post') ? 'article' : 'website') . '">' . "\n";
    if (is_singular() && has_post_thumbnail()) { echo '<meta property="og:image" content="' . esc_url(get_the_post_thumbnail_url(null, 'full')) . '">' . "\n"; }
}
add_action('wp_head', 'travel_riddle_seo_head', 2);

function travel_riddle_schema(): void {
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) { return; }
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            ['@type' => 'WebSite', '@id' => home_url('/#website'), 'url' => home_url('/'), 'name' => get_bloginfo('name'), 'description' => travel_riddle_seo_description()],
            ['@type' => 'Organization', '@id' => home_url('/#organization'), 'name' => 'Travel Riddle', 'url' => home_url('/'), 'founder' => ['@type' => 'Person', 'name' => 'MD. Hassan', 'jobTitle' => 'Founder and Travel Storyteller']],
        ],
    ];
    if (is_singular('post')) {
        $schema['@graph'][] = ['@type' => 'BlogPosting', 'headline' => get_the_title(), 'datePublished' => get_the_date(DATE_W3C), 'dateModified' => get_the_modified_date(DATE_W3C), 'mainEntityOfPage' => get_permalink(), 'author' => ['@type' => 'Person', 'name' => get_the_author()], 'publisher' => ['@id' => home_url('/#organization')]];
    }
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}
add_action('wp_head', 'travel_riddle_schema', 30);

function travel_riddle_seed_attachment(string $filename, string $title): int {
    $existing = get_posts(['post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_travel_riddle_source', 'meta_value' => $filename, 'posts_per_page' => 1, 'fields' => 'ids']);
    if ($existing) { return (int) $existing[0]; }
    $source = get_template_directory() . '/assets/images/' . $filename;
    if (!is_readable($source)) { return 0; }
    $upload = wp_upload_bits($filename, null, file_get_contents($source));
    if (!empty($upload['error'])) { return 0; }
    $type = wp_check_filetype($upload['file']);
    $id = wp_insert_attachment(['post_mime_type' => $type['type'], 'post_title' => $title, 'post_status' => 'inherit'], $upload['file']);
    if (is_wp_error($id)) { return 0; }
    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $upload['file']));
    update_post_meta($id, '_travel_riddle_source', $filename);
    update_post_meta($id, '_wp_attachment_image_alt', $title);
    return (int) $id;
}

function travel_riddle_seed_page(string $title, string $slug, string $content): int {
    $existing = get_page_by_path($slug, OBJECT, 'page');
    if ($existing) { return (int) $existing->ID; }
    $id = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug, 'post_content' => $content]);
    return is_wp_error($id) ? 0 : (int) $id;
}

function travel_riddle_seed_site(): void {
    if (get_option('travel_riddle_seeded_v1')) { return; }

    $about = '<p class="intro-lead">At Travel Riddle, we believe nature was created for us to see, experience, appreciate and learn from.</p><p>Every landscape, sunset and unfamiliar street has something to show us if we take the time to notice. Words and pictures may inspire a journey, but travel gives that journey a soul.</p><h2>Why Travel Riddle exists</h2><p>There is something special about standing in a place you have only seen in photographs. Feeling the air, hearing the sounds, meeting the people and experiencing the place yourself creates memories that pictures alone never can.</p><p>We do not simply tell you where to go. We help you understand why a place is worth visiting and whether it is right for you. Through travel stories, ideas and practical insight, we hope to make your choices easier and your journeys more meaningful.</p><h2>Meet our founder</h2><p><strong>MD. Hassan</strong> brings more than 15 years of experience in the travel domain. His knowledge of destinations, traveler needs and the industry shapes Travel Riddle’s honest, experience-led editorial approach.</p><p>Do not wait for the perfect time to travel. Make travel a part of your perfect life.</p>';
    $contact = '<p class="intro-lead">Have a destination idea, editorial question or collaboration proposal? We would be pleased to hear from you.</p><h2>Editorial and general enquiries</h2><p>Email: <a href="mailto:hello@travelriddle.com">hello@travelriddle.com</a></p><p>Please include a clear subject and relevant details. We aim to respond to genuine enquiries within three working days.</p><h2>Share a travel story</h2><p>If you have first-hand experience that could help other travelers, tell us where you went, when you visited and what made the journey memorable.</p>';
    $privacy = '<p>Travel Riddle respects your privacy. This policy explains the information collected through this website, how it is used and the choices available to you.</p><h2>Information we collect</h2><p>We may collect information you submit through contact or subscription forms, along with limited technical information used for security, analytics and site performance.</p><h2>Analytics and cookies</h2><p>We may use Google Site Kit and connected Google services to understand aggregated website traffic. You can control cookies through your browser settings.</p><h2>Contact</h2><p>For privacy questions, email <a href="mailto:hello@travelriddle.com">hello@travelriddle.com</a>.</p>';
    $disclaimer = '<p>Travel Riddle publishes travel information for general editorial and inspirational purposes. Conditions, prices, schedules, entry requirements and local rules can change.</p><p>Readers should verify important information with official sources and make decisions appropriate to their circumstances. Travel Riddle is not responsible for losses arising from reliance on outdated or incomplete information.</p><h2>Editorial independence</h2><p>Sponsored, gifted or affiliate-supported content will be clearly disclosed. Opinions remain those of the author.</p>';
    $editorial = '<p>Travel Riddle is committed to useful, honest and experience-led travel publishing.</p><h2>Our standards</h2><p>We distinguish first-hand experience from research, verify important practical claims, credit sources where appropriate and correct material errors transparently.</p><h2>Updates and corrections</h2><p>Travel information changes frequently. Articles may be reviewed and updated as new information becomes available. To report a correction, email <a href="mailto:hello@travelriddle.com">hello@travelriddle.com</a>.</p><h2>Commercial content</h2><p>Partnerships do not purchase positive coverage. Advertising, sponsorship and affiliate relationships will be disclosed clearly.</p>';

    $home_id = travel_riddle_seed_page('Home', 'home', '');
    $stories_id = travel_riddle_seed_page('Travel Stories', 'stories', '');
    travel_riddle_seed_page('Destinations', 'destinations', '<p class="intro-lead">Explore places through stories, local insight and practical travel inspiration.</p><p>Use the destination archives to discover articles by country, region and city as Travel Riddle grows.</p>');
    travel_riddle_seed_page('About Us', 'about', $about);
    travel_riddle_seed_page('Contact', 'contact', $contact);
    travel_riddle_seed_page('Privacy Policy', 'privacy-policy', $privacy);
    travel_riddle_seed_page('Disclaimer', 'disclaimer', $disclaimer);
    travel_riddle_seed_page('Editorial Policy', 'editorial-policy', $editorial);

    update_option('show_on_front', 'page');
    update_option('page_on_front', $home_id);
    update_option('page_for_posts', $stories_id);
    update_option('blogdescription', 'Every destination has a story. Every story has a mystery.');
    update_option('timezone_string', 'Asia/Kolkata');
    update_option('default_comment_status', 'closed');
    update_option('permalink_structure', '/%postname%/');

    $category_ids = [];
    foreach (['Travel Guides', 'Culture', 'Adventure', 'Food and Local Life'] as $category) {
        $term = term_exists($category, 'category');
        if (!$term) { $term = wp_insert_term($category, 'category'); }
        if (!is_wp_error($term)) { $category_ids[$category] = (int) (is_array($term) ? $term['term_id'] : $term); }
    }
    $destination_ids = [];
    foreach (['Mountains and Wild Places', 'Cities and Culture', 'Coasts and Islands'] as $destination) {
        $term = term_exists($destination, 'destination');
        if (!$term) { $term = wp_insert_term($destination, 'destination'); }
        if (!is_wp_error($term)) { $destination_ids[$destination] = (int) (is_array($term) ? $term['term_id'] : $term); }
    }

    $stories = [
        ['Hidden Corners and Unforgettable Light', 'hidden-corners-unforgettable-light', 'story-mountains.png', 'Travel Guides', 'Mountains and Wild Places', '<p class="intro-lead">The stories we bring home are often found beyond the itinerary.</p><p>In the mountains, distance changes our sense of scale. Trails become invitations, early light turns familiar rock into something extraordinary, and every quiet viewpoint rewards the decision to keep walking.</p><h2>Travel slowly enough to notice</h2><p>The most memorable journeys leave space for weather, conversation and surprise. Choose fewer stops, start early and let the landscape set the pace.</p><h2>Practical perspective</h2><p>Carry layers, water and a reliable offline map. Respect trail conditions and local guidance, and leave every place as you found it.</p>'],
        ['The City After Sunset', 'the-city-after-sunset', 'story-city.png', 'Culture', 'Cities and Culture', '<p class="intro-lead">Some cities reveal their real character after the day’s itinerary ends.</p><p>As shopfronts glow and tables fill, old streets become places of conversation. This is the hour to walk without a checklist, follow the sound of a busy café and notice how local life moves.</p><h2>Look beyond the landmarks</h2><p>Architecture provides the setting, but people create the atmosphere. Support independent businesses, learn a few local phrases and make room for an unplanned evening.</p>'],
        ['The Coast That Time Forgot', 'the-coast-that-time-forgot', 'story-coast.png', 'Adventure', 'Coasts and Islands', '<p class="intro-lead">A quiet path, clear water and an unhurried afternoon can be the whole reason to travel.</p><p>Beyond the busiest beaches are coves reached on foot, fishing villages that still keep their rhythm and coastlines best experienced without a schedule.</p><h2>Go gently</h2><p>Bring only what you need, protect fragile shorelines and choose local operators who care for the places they share.</p>'],
    ];
    foreach ($stories as $index => $story) {
        $existing = get_page_by_path($story[1], OBJECT, 'post');
        $post_id = $existing ? (int) $existing->ID : (int) wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_title' => $story[0], 'post_name' => $story[1], 'post_content' => $story[5], 'post_excerpt' => wp_strip_all_tags(strtok($story[5], '</p>'))]);
        if ($post_id) {
            if (!empty($category_ids[$story[3]])) { wp_set_post_categories($post_id, [$category_ids[$story[3]]]); }
            if (!empty($destination_ids[$story[4]])) { wp_set_object_terms($post_id, [$destination_ids[$story[4]]], 'destination'); }
            $image_id = travel_riddle_seed_attachment($story[2], $story[0]);
            if ($image_id) { set_post_thumbnail($post_id, $image_id); }
        }
    }
    $hello = get_post(1);
    if ($hello && 'Hello world!' === $hello->post_title) { wp_update_post(['ID' => 1, 'post_status' => 'draft']); }

    $menu_name = 'Primary Navigation';
    $menu = wp_get_nav_menu_object($menu_name);
    $menu_id = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu($menu_name);
    if ($menu_id && !wp_get_nav_menu_items($menu_id)) {
        foreach (['Home' => 'home', 'Destinations' => 'destinations', 'Stories' => 'stories', 'About' => 'about', 'Contact' => 'contact'] as $label => $slug) {
            $page = get_page_by_path($slug);
            if ($page) { wp_update_nav_menu_item($menu_id, 0, ['menu-item-title' => $label, 'menu-item-object' => 'page', 'menu-item-object-id' => $page->ID, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish']); }
        }
    }
    set_theme_mod('nav_menu_locations', ['primary' => $menu_id, 'footer' => $menu_id]);

    $admin = get_user_by('id', 1);
    if ($admin) { wp_update_user(['ID' => 1, 'display_name' => 'MD. Hassan', 'description' => 'Founder of Travel Riddle with more than 15 years of experience in the travel domain.']); }
    flush_rewrite_rules();
    update_option('travel_riddle_seeded_v1', current_time('mysql'));
}
add_action('admin_init', 'travel_riddle_seed_site');

function travel_riddle_update_inner_pages(): void {
    if (get_option('travel_riddle_inner_pages_v2')) { return; }

    $about = '<p class="intro-lead">At Travel Riddle, we believe nature was created for us to see, experience, appreciate, learn from and perhaps even heal through.</p><p>Every landscape, sunset and unfamiliar street has something to show us—if we take the time to notice and appreciate it.</p><h2>Travel gives a journey its soul</h2><p>Words and pictures may inspire a journey, but there is something special about standing in a place you have only seen in photographs. Feeling the air, hearing the sounds, meeting the people and experiencing the place yourself can create memories that pictures alone never can.</p><p>Travel does not ask your age, where you come from or how much you have. The desire to explore is something we all share. Sometimes, the farther we travel from what is familiar, the closer we come to discovering who we truly are.</p><h2>Why Travel Riddle exists</h2><p>We do not simply tell you where to go. We help you understand why a place is worth visiting and whether it is right for you.</p><p>Through travel stories, ideas, insights and inspiration, we hope to make your travel choices easier and your journeys more interesting and meaningful.</p><blockquote>“Sunsets are proof that endings can be beautiful.”</blockquote><p>If something we share inspires you to take a journey, discover a new place, see the world differently or simply create a beautiful memory, then we have achieved something meaningful.</p><p>It would be both a privilege and a pleasure to know that Travel Riddle has made even the smallest difference in your life.</p><p><strong>Do not wait for the perfect time to travel. Make travel a part of your perfect life.</strong></p>';
    $destinations = '<p class="intro-lead">The world is full of places that change us in ways we do not expect.</p><p>Explore destination collections shaped by stories, local insight and practical inspiration. Choose a landscape below to discover the articles connected to it.</p>';
    $contact = '<p class="intro-lead">Have a destination idea, editorial question or collaboration proposal? We would be pleased to hear from you.</p><p>Use the form to contact the editorial team, suggest a place or share first-hand travel experience that could help another curious traveler.</p>';

    foreach (['about' => $about, 'destinations' => $destinations, 'contact' => $contact] as $slug => $content) {
        $page = get_page_by_path($slug, OBJECT, 'page');
        if ($page) { wp_update_post(['ID' => $page->ID, 'post_content' => $content]); }
    }
    update_option('travel_riddle_inner_pages_v2', current_time('mysql'));
}
add_action('admin_init', 'travel_riddle_update_inner_pages');

function travel_riddle_apply_client_about_copy(): void {
    if (get_option('travel_riddle_client_about_v3')) { return; }
    $about = '<p class="intro-lead">At Travel Riddle, we believe nature was created for us to see, experience, appreciate, learn from, and perhaps even heal through. Every landscape, sunset, and unfamiliar street has something to show us—if we take the time to notice and appreciate it.</p><p>Words and pictures may inspire a journey, but travel gives that journey a soul.</p><p>There is something special about standing in a place you have only seen in photographs. Feeling the air, hearing the sounds, meeting the people, and experiencing the place yourself can create memories that pictures alone never can.</p><p>Travel does not ask your age, where you come from, or how much you have. The desire to explore is something we all share. And sometimes, the farther we travel from what is familiar, the closer we come to discovering who we truly are.</p><p><strong>Don\'t wait for the perfect time to travel. Make travel a part of your perfect life.</strong></p><p>At Travel Riddle, we don\'t simply tell you where to go. We help you understand why a place is worth visiting and whether it is right for you.</p><p>Through travel stories, ideas, insights, and inspiration, we hope to make your travel choices easier and your journeys more interesting and meaningful.</p><blockquote>“Sunsets are proof that endings can be beautiful.”</blockquote><p>If something we share inspires you to take a journey, discover a new place, see the world differently, or simply create a beautiful memory, then we have achieved something meaningful.</p><p>It would be both a privilege and a pleasure to know that Travel Riddle has made even the smallest difference in your life.</p>';
    $page = get_page_by_path('about', OBJECT, 'page');
    if ($page) { wp_update_post(['ID' => $page->ID, 'post_content' => $about]); }
    update_option('travel_riddle_client_about_v3', current_time('mysql'));
}
add_action('admin_init', 'travel_riddle_apply_client_about_copy', 20);

function travel_riddle_handle_contact(): void {
    $redirect = home_url('/contact/');
    if (!isset($_POST['travel_riddle_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['travel_riddle_nonce'])), 'travel_riddle_contact')) {
        wp_safe_redirect(add_query_arg('contact', 'error', $redirect)); exit;
    }
    if (!empty($_POST['website'])) { wp_safe_redirect(add_query_arg('contact', 'sent', $redirect)); exit; }

    $name = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $subject = sanitize_text_field(wp_unslash($_POST['subject'] ?? ''));
    $message = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));
    if (!$name || !is_email($email) || !$subject || !$message) {
        wp_safe_redirect(add_query_arg('contact', 'error', $redirect)); exit;
    }

    $body = "Name: {$name}\nEmail: {$email}\n\n{$message}";
    $sent = wp_mail(get_option('admin_email'), '[Travel Riddle] ' . $subject, $body, ['Reply-To: ' . $name . ' <' . $email . '>']);
    wp_safe_redirect(add_query_arg('contact', $sent ? 'sent' : 'error', $redirect)); exit;
}
add_action('admin_post_nopriv_travel_riddle_contact', 'travel_riddle_handle_contact');
add_action('admin_post_travel_riddle_contact', 'travel_riddle_handle_contact');
