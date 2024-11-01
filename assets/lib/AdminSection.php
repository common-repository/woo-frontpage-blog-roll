<?php
if ( ! class_exists( 'AdminSection' ) ) :
  abstract class AdminSection
  {
      const DASHBOARD = 0;
      const POSTS = 1;
      const MEDIA = 2;
      const LINKS = 3;
      const PAGES = 4;
      const COMMENTS = 5;
      const APPEARANCE = 6;
      const PLUGINS = 7;
      const USERS = 8;
      const TOOLS = 9;
      const SETTINGS = 10;
      
      public static function register($section = 0, $page_title, $menu_title, $capability = 'None', $menu_slug, $function) {
        switch($section) {
            case 0:
                add_dashboard_page( $page_title, $menu_title, $capability, $menu_slug, $function);
                break;
            case 1:
                add_posts_page( $page_title, $menu_title, $capability, $menu_slug, $function);
                break;
            case 2:
                add_media_page( $page_title, $menu_title, $capability, $menu_slug, $function);
                break;
            case 3:
                add_links_page( $page_title, $menu_title, $capability, $menu_slug, $function);
                break;
            case 4:
                add_pages_page( $page_title, $menu_title, $capability, $menu_slug, $function);
                break;
            case 5:
                add_comments_page( $page_title, $menu_title, $capability, $menu_slug, $function);
                break;
            case 6:
                //add_submenu_page('themes.php', $page_title, $menu_title, $capability, $menu_slug, $function);
                add_theme_page( $page_title, $menu_title, $capability, $menu_slug, $function );
                break;
            case 7:
                add_plugins_page( $page_title, $menu_title, $capability, $menu_slug, $function);
                break;
            case 8:
                add_users_page( $page_title, $menu_title, $capability, $menu_slug, $function);
                break;
            case 9:
                add_management_page( $page_title, $menu_title, $capability, $menu_slug, $function );
                break;
            case 10:
                add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
                break;
        }
      }
  }
endif; 
?>