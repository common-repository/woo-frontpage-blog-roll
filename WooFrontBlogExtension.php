<?php
/*
Plugin Name: Woo Frontpage Blog Roll
Plugin URI: https://frametagmedia.com.au/pluginpages/woo-frontpage-blog-roll/
Description: Woo Commerce extension plugin that allows a blog roll to be placed on the front page. Requires a theme that is compatible with homepage control.
Version: 1.0
Author: Frametag Media
Author URI: https://frametagmedia.com.au
License: GNU GPLv2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once("assets/lib/AdminSection.php");
require_once("assets/lib/PluginOptionsPage.php");

$wooFrontBlog_opts = array(
			array(
					"type" => "section",
					"name" => "Plugin Settings"
			),
			array( "type" => "open" ),
			array(
					"type" => "text",
					"name" => "Section title",
					"desc" => "Title to show before the blogroll. Leave empty to show none",
					"id" => $extension_alias . "_title",
					"std" => "Latest news:"
			),
			array(
					"type" => "number",
					"name" => "Show entries",
					"desc" => "How many entries to show",
					"id" => $extension_alias . "_number",
					"std" => 4
			),
			array(
					"type" => "checkbox",
					"name" => "Vertical",
					"desc" => "If checked the frontpage sections will be arranged vertically. The default arrangement is horizontal",
					"id" => $extension_alias . "_vertical",
					"std" => false
			),
			array( "type" => "close" )
	);
$wooFrontBlog_extension_name = "woo Frontpage Blog Roll";
$wooFrontBlog_extension_alias = "wooFrontBlogextension";
$wooFrontBlog_extension_path = plugin_dir_path(__FILE__);
$wooFrontBlog_extension_URL = trailingslashit(get_bloginfo('wpurl')) . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));
$wooFrontBlog_optionsPage = new PluginOptionsPage(AdminSection::SETTINGS, $wooFrontBlog_extension_name, $wooFrontBlog_extension_alias, $wooFrontBlog_opts);

register_activation_hook(__FILE__, array($wooFrontBlog_optionsPage, 'install'));
register_deactivation_hook(__FILE__, array($wooFrontBlog_optionsPage, 'uninstall'));

//Plugin part
$wooFrontBlog_optionsPage->menu("Blogroll extension");

if (!function_exists('wooFrontBlog_get_excerpt')):
    function wooFrontBlog_get_excerpt($post)
    {
        if ($post["post_excerpt"]) return $post["post_excerpt"];

        $text = strip_shortcodes( $post['post_content'] );

        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = strip_tags($text);
        $excerpt_length = apply_filters('excerpt_length', 55);
        $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
        $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
        if ( count($words) > $excerpt_length ) {
                array_pop($words);
                $text = implode(' ', $words);
                $text = $text . $excerpt_more;
        } else {
                $text = implode(' ', $words);
        }

        return apply_filters('wp_trim_excerpt', $text, $post['post_content'] );
    }
endif;

if ( ! function_exists( 'storefront_homepage_latest_posts' ) ):
	function storefront_homepage_latest_posts()
	{
		global $wooFrontBlog_extention_alias;
		$title =  get_option($wooFrontBlog_extention_alias . "_title");
		$number =  get_option($wooFrontBlog_extention_alias . "_number");
		$isVertical =  get_option($wooFrontBlog_extention_alias . "_vertical");
		$args = array(
			'numberposts' => $number,
			'orderby' => 'post_date',
			'order' => 'DESC',
			'post_type' => 'post',
			'post_status' => 'publish'
		);
		$recent_posts = wp_get_recent_posts( $args, ARRAY_A );

		echo '<section class="storefront-blogroll-section">';
		if ($title != ""):
			echo '<h2 class="section-title">'.$title.'</h2>';
		endif;
			foreach( $recent_posts as $recent ){
				echo '<li class="news_item"><a href="' . get_permalink($recent["ID"]) . '">' .   $recent["post_title"].'</a> ('.mysql2date('M j, Y', $recent["post_date"]).')<p class="post">'. wooFrontBlog_get_excerpt($recent).'</p> </li> ';
			}
		echo '</section>';
	}
endif;

if (!function_exists('wooFrontBlog_assets')):
    function wooFrontBlog_assets()
    {
        global $wooFrontBlog_extension_URL;
        wp_enqueue_style('wooFrontBlog_style', $wooFrontBlog_extension_URL . '/assets/css/style.css');
    }
endif;


add_action( 'homepage', 'storefront_homepage_latest_posts', 71 );
add_action('wp_enqueue_scripts', 'wooFrontBlog_assets');

?>
