<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Setup;

/**
 * Add <body> classes
 */
function body_class($classes) {
  // Add page slug if it doesn't exist
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }

  // Add class if sidebar is active
  if (Setup\display_sidebar()) {
    $classes[] = 'sidebar-primary';
  }

  return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\\body_class');

/**
 * Clean up the_excerpt()
 */
function excerpt_more() {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
}
add_filter('excerpt_more', __NAMESPACE__ . '\\excerpt_more');

/**
 * Addthis Sharing
 */
function addthis_sharing ($atts, $content = null) {
  ob_start();
  ?>
  <div class="addthis_inline_share_toolbox"></div>
  <?php
  $content = ob_get_contents();
  ob_end_clean();
  return $content;
}
add_shortcode('addthis', __NAMESPACE__ . '\\addthis_sharing');

/**
 * Remove Posts and Comments from menu
 */
function post_remove ()
{ 
   remove_menu_page('edit.php');
   remove_menu_page('edit-comments.php');
}
add_action('admin_menu', __NAMESPACE__ . '\\post_remove');
