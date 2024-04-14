<?php

/*
Plugin Name: LearnWebCode
Description: A truly amazing plugin.
Version: 1.0.0
Author: ict4today
Author URI: https://ict4today.com
*/
class WordCountAndTimePlugin {
    function __construct() {
        //'admin_menu' hook is used to to add an option in admin panel
        add_action('admin_menu',  array($this, 'adminPage'));

        //Used to add any input fields in any pages in admin
        add_action('admin_init',  array($this, 'settings'));

        //
        add_filter('the_content', array($this, 'ifWrap'));
    }
    function ifWrap($content) {
        if(is_single() && is_main_query()) {
            return $this->createHtml($content);
        }
        return $content;
    }
    
    function createHtml($content) {
        $html = "<h2>" . get_option("wcp_headline") . "</h2>";
        if(get_option( 'wcp_wordcount', '1') == '1') {
            $wordCount = str_word_count(strip_tags($content));
            $html .= '<p>This post has '.$wordCount.' words</p>';
        }
        if(get_option( 'wcp_location', '0') == '0') {
            return $html.$content;
        }
        return $content.$html;
    }

    function settings() {
        /*
        * add_settings_section( 'section_id', 'Section Title', 'render_section_callback', 'my_settings_page' );
        * A group of input fields will have a section
        */
        add_settings_section( 'wcp_first_section', null, null, 'word-count-settings-page');

        /*
        * add_settings_field( 'field_id', 'Field Label', 'render_field_callback', 'my_settings_page', 'section_id' );
        * Adds a new input field to the section
        */
        add_settings_field( 'wcp_location', 'Display Location', array($this, 'locationHtml'), 'word-count-settings-page', 'wcp_first_section');
        /* 
        * register_setting( 'my_settings_group', 'my_setting_name', 'sanitize_callback' );
        * Registers new settings to WordPress.
        */
        register_setting('wordcountplugin', 'wcp_location', array('sanitize_callback' => array($this, 'sanitizeLocation'), 'default' => '0'));

        add_settings_field( 'wcp_headline', 'Headline Text', array($this, 'headlineHtml'), 'word-count-settings-page', 'wcp_first_section');
        register_setting('wordcountplugin', 'wcp_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post statistics'));

        add_settings_field( 'wcp_wordcount', 'Wordcount Text', array($this, 'wordcountHtml'), 'word-count-settings-page', 'wcp_first_section');
        register_setting('wordcountplugin', 'wcp_wordcount', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post statistics'));

        add_settings_field( 'wcp_wordcount', 'Wordcount Text', array($this, 'wordcountHtml'), 'word-count-settings-page', 'wcp_first_section');
        register_setting('wordcountplugin', 'wcp_wordcount', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post statistics'));
    }

    function sanitizeLocation($input) {
        if($input != '0' && $input != '1') {
            add_settings_error( 'wcp_location', 'wcp_location_error', 'Display location must be either beginning or end.' );
            return get_option( 'wcp_location');
        }
        return $input;
    }

    function locationHtml() { ?>
        <select name="wcp_location">
            <option value="0" <?php selected( get_option( 'wcp_location' ), '0' ); ?>>Beginning of post</option>
            <option value="1" <?php selected( get_option( 'wcp_location' ), '1' ); ?>>End of post</option>
        </select>
    <?php }

    function headlineHtml() { ?>
        <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option( 'wcp_headline')); ?>"/>
    <?php }

    function wordcountHtml() { ?>
        <input type="checkbox" name="wcp_wordcount" value="1" <?php checked(get_option( 'wcp_wordcount'), '1'); ?>/>
    <?php }
    
    function adminPage() {
        //add_options_page($page_title, $menu_title, $capability, $menu_slug, $callback = ”, int $position = null )
        add_options_page( 'Word Count Settings', 'Word Count', 'manage_options', 'word-count-settings-page', array($this, 'ourHtml'));
        //After executing this function a menu will be added in Settings
    }
    //This code will be visible when the menu item is clicked
    function ourHtml() {?>
        <div class="wrap">
            <h1>Word Count Settings</h1>
            <form action="options.php" method="POST">
                <?php
                    //First parameter of register_setting ie.my_setting_group
                    settings_fields( 'wordcountplugin' );
                    //menu_slug
                    do_settings_sections( "word-count-settings-page" );
                    submit_button();
                ?>
            </form>
        </div>
    <?php }
}
$wordCountAndTimePlugin = new WordCountAndTimePlugin();
