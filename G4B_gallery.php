<?php
/*
Plugin Name: G4B Photo Gallery
Plugin URI: http://geekforbrains.com/wordpress/g4b-photo-gallery-plugin-for-wordpress
Description: A simple yet powerful photo gallery plugin for Wordpress.
Version: 1.0
Author: Gavin Vickery
Author URI: http://geekforbrains.com

===========================================================================

Copyright 2008 G4B Photo Gallery (email: gdvickery@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

===========================================================================
*/


/**
 * A bunch of tasty constants
 */
define('G4B_GALLERY_NAME', 'G4B Gallery'); // The title displayed in menus etc.
define('G4B_GALLERY_VERSION', '1.0');
define('G4B_GALLERY_SEP', (get_option('permalink_structure') == '') ? '&amp;' : '?'); // Url GET seperator
define('G4B_GALLERY_HOOK', '{g4b_gallery}'); // The tag that hooks in gallery on pages and posts
define('G4B_GALLERY_UPLOAD_DIR', WP_CONTENT_DIR . '/uploads/g4b_gallery/');
define('G4B_GALLERY_UPLOAD_URL', WP_CONTENT_URL . '/uploads/g4b_gallery/');
define('G4B_GALLERY_URL', WP_CONTENT_URL . '/plugins/G4B_gallery/'); // Needed to get CSS
define('G4B_GALLERY_ALBUM_TBL', $wpdb->prefix . 'g4b_gallery_albums'); // Albums database table
define('G4B_GALLERY_PHOTO_TBL', $wpdb->prefix . 'g4b_gallery_photos'); // Photos database table


/**
 * Get functions
 */
require_once('G4B_gallery_functions.php');


/**
 * Register setup
 */
register_activation_hook(__FILE__, 'G4B_gallery_setup');
function G4B_gallery_setup()
{
    global $wpdb;
    if($wpdb->get_var('show tables like "' . G4B_GALLERY_ALBUM_TBL . '"') != G4B_GALLERY_ALBUM_TBL)
    {
        $setupSql = '
            CREATE TABLE ' . $wpdb->prefix . G4B_GALLERY_ALBUM_TBL . ' (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                photo_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                PRIMARY KEY (id)
            );
            CREATE TABLE ' . $wpdb->prefix . G4B_GALLERY_PHOTO_TBL . ' (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                album_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                thumbnail VARCHAR(255) NOT NULL,
                photo VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            );
        ';
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($setupSql);
        
        add_option('g4b_gallery_version', G4B_GALLERY_VERSION);
        add_option('g4B_gallery_thumb_width', '100');
        add_option('g4B_gallery_thumb_height', '100');
        add_option('g4B_gallery_photo_width', '500');
        add_option('g4B_gallery_photo_height', '0');
        
        @mkdir(G4B_GALLERY_UPLOAD_DIR);
    }
}


/**
 * Register removal
 */
register_deactivation_hook(__FILE__, 'G4B_gallery_remove');
function G4B_gallery_remove()
{
    global $wpdb;
    
    // First remove albums and photos, including files
    // $albums = G4B_gallery_get_albums();
    // if($albums)
    //     foreach($albums as $album)
    //        G4B_gallery_del_album($album['id'], true);
    
    // Then delete upload dir
    // @unlink(trim(G4B_GALLERY_UPLOAD_DIR, '/'));
    
    // And theeeen, delete the tables
    $wpdb->query('DROP TABLE ' . G4B_GALLERY_ALBUM_TBL);
    $wpdb->query('DROP TABLE ' . G4B_GALLERY_PHOTO_TBL);
    
    // Oh yea, and the options..
    delete_option('g4b_gallery_version');
    delete_option('g4B_gallery_thumb_width');
    delete_option('g4B_gallery_photo_width');
    delete_option('g4B_gallery_thumb_height');
    delete_option('g4B_gallery_photo_height');
}


/**
 * Menu for options page
 */
function G4B_gallery_options()
{
    add_options_page(G4B_GALLERY_NAME, G4B_GALLERY_NAME, 0, 'G4B_gallery/G4B_gallery_options.php');
}
add_action('admin_menu', 'G4B_gallery_options');


/**
 * Menu for manage page
 */
function G4B_gallery_manage()
{
    add_management_page(G4B_GALLERY_NAME, G4B_GALLERY_NAME, 0, 'G4B_gallery/G4B_gallery_manage.php');
}
add_action('admin_menu', 'G4B_gallery_manage');


/**
 * Include gallery view
 */
function G4B_gallery_view()
{
    ob_start(); 
    require_once('G4B_gallery_view.php'); 
    $viewBuffer = ob_get_contents(); 
    ob_end_clean();
    return $viewBuffer;
}


/**
 * Add filter to parse gallery tag
 */
function G4B_gallery_filter($content)
{
    if(preg_match('/' . G4B_GALLERY_HOOK . '/', $content))
        $content = str_replace(G4B_GALLERY_HOOK, G4B_gallery_view(), $content);
    return $content;
}
add_filter('the_content', 'G4B_gallery_filter');


/**
 * Add gallery CSS file to site header
 */
/**
 * Load CSS into login and admin header
 */
function G4B_gallery_css()
{
    echo "<link rel='stylesheet' href='" . G4B_GALLERY_URL . "G4B_gallery.css" . "' type='text/css' media='all' />";
}
add_action('wp_head', 'G4B_gallery_css');
?>