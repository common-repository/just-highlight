<?php
/*
Plugin Name: Just Highlight
Plugin URI: http://jh.sigalitam.com/
Description: Just highlight is a WordPress plugin for highlighting text in your posts or pages, and easily create beautiful posts that highlight what really matters to your readers.
Version: 1.0.3
Author: Sigalitam
Author URI: http://sigalitam.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: just-highlight
*/

add_action( 'admin_enqueue_scripts', 'sigijh_load_actions_script' );
function sigijh_load_actions_script() {
    wp_enqueue_script('sigijh_actions_script', plugins_url( '/js/actions.js', __FILE__ ), array('jquery'));
}

add_action( 'admin_enqueue_scripts', 'sigijh_is_gutenberg_editor' );
function sigijh_is_gutenberg_editor() {
    global $current_screen;
    $current_screen = get_current_screen();
    if ( method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor() ) {
        //echo 'gutenberg editor';
    } else {
        //OLD EDITOR TINYMCE 
        add_action('admin_head', 'sigijh_add_editor_button');
        function sigijh_add_editor_button() {
            global $typenow;
            if (!current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
                return;
            }
            if( ! in_array( $typenow, array( 'post', 'page' ) ) ){
                return;
            }
            if ( get_user_option('rich_editing') == 'true') {
                add_filter("mce_external_plugins", "sigijh_add_tinymce_plugin", 999);
                add_filter('mce_buttons', 'sigijh_register_editor_button', 999);
            }
        }
        function sigijh_add_tinymce_plugin($plugin_array) {
            $plugin_array['sigijh_tc_button'] = plugins_url( '/js/highlight.js', __FILE__ );
            return $plugin_array;
        }
        function sigijh_register_editor_button($buttons) {
            array_push($buttons, "sigijh_tc_button");
            return $buttons;
        }
    }      
}

// Gutenberg EDITOR
add_action('enqueue_block_editor_assets', 'sigijh_gutenberg_editor_button');
function sigijh_gutenberg_editor_button() {
	wp_enqueue_script('sigijh-gutenberg-button-js',plugins_url( '/js/g_highlight.js', __FILE__ ),array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ),'1.0',true);
	wp_enqueue_style('sigijh-gutenberg-button-editor-css', plugins_url( '/css/g_styles.css', __FILE__ ), array( 'wp-edit-blocks' ));
}
 
 // WP FRONT STYLE
function sigijh_front_end_style() {
    wp_enqueue_style('sigijh-front_css', plugins_url('/css/style.css', __FILE__));
    wp_enqueue_script( 'sigijh-front_js', plugins_url('/js/animation.js', __FILE__), array ( 'jquery' ), 1.1, true);
    $optionsValues = array(
        'colorSelect' => get_option('sigijh_color_select'),
        'animationSpeed' => get_option('sigijh_animation_speed'),
        'animationActive' => get_option('sigijh_animation_active'),
        'animationScroll' => get_option('sigijh_animation_scroll'),
    );
    wp_localize_script( 'sigijh-front_js', 'optionsValues', $optionsValues ); 
}
add_action('wp_enqueue_scripts', 'sigijh_front_end_style');

 // WP ADMIN STYLE
function sigijh_admin_style() {
    wp_enqueue_style('sigijh-admin_css', plugins_url('/css/admin-style.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'sigijh_admin_style');

// WP COLOR PICKER
add_action( 'admin_enqueue_scripts', 'sigijh_color_picker' );
function sigijh_color_picker( $hook_suffix ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'sigijh-script-handle', plugins_url('/js/colorpicker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}

// WP OLD EDITOR STYLE
function sigijh_editor_style() {
    add_editor_style( plugins_url('/css/editor-style.css', __FILE__)  );
}
add_action( 'init', 'sigijh_editor_style' );

function sigijh_theme_editor_dynamic_styles( $mceInit ) {
    $options = sigijh_get_current_options();
    $styles = '.sigijh_hlt { background-color: '.$options['sigijh_color_select'].'}';
    if ( isset( $mceInit['content_style'] ) ) {
        $mceInit['content_style'] .= ' ' . $styles . ' ';
    } else {
        $mceInit['content_style'] = $styles . ' ';
    }
    return $mceInit;
}
add_filter('tiny_mce_before_init','sigijh_theme_editor_dynamic_styles');

// WP GUTENBERG STYLE
function sigijh_editor_style_gutenberg(){
    $options = sigijh_get_current_options();
    $styles = '<style> .sigijh_hlt { background-color: '.$options['sigijh_color_select'].' !important}</style>';
    echo $styles;
}
add_action( 'admin_head', 'sigijh_editor_style_gutenberg' );

// UNINSTALL PLUGIN
function sigijh_uninstall_plugin() {
	$options_removed = false;
	try {
        delete_option('sigijh_color_select');
        delete_option('sigijh_animation_speed');
        delete_option('sigijh_animation_active');
        delete_option('sigijh_animation_scroll');
        unregister_setting( 'sigijh_plugin_options', 'sigijh_color_select' );
        unregister_setting( 'sigijh_plugin_options', 'sigijh_animation_speed' );
        unregister_setting( 'sigijh_plugin_options', 'sigijh_animation_active' );
        unregister_setting( 'sigijh_plugin_options', 'sigijh_animation_scroll' );
		$options_removed = true;
	} catch( Exception $e ) {}
	return $options_removed;
}
register_uninstall_hook( __FILE__, 'sigijh_uninstall_plugin' );

// ADMIN PAGE
add_action( 'admin_menu', 'sigijh_highlight_menu' );
function sigijh_highlight_menu() {
	add_options_page( 'Highlight Options', 'Just Highlight', 'manage_options', 'just-highlight', 'sigijh_highlight_options' );
}

add_action( 'admin_init', 'sigijh_register_options' );
function sigijh_register_options() {
    register_setting( 'sigijh_plugin_options', 'sigijh_color_select' );
    register_setting( 'sigijh_plugin_options', 'sigijh_animation_speed' );
    register_setting( 'sigijh_plugin_options', 'sigijh_animation_active' );
    register_setting( 'sigijh_plugin_options', 'sigijh_animation_scroll' );
}

if (isset($_GET['resetall']) ) {
    if(isset($_COOKIE["sigijh_resetall"])){   
        update_option( 'sigijh_color_select', '#ff0' );
        update_option( 'sigijh_animation_speed', 'Normal' );
        update_option( 'sigijh_animation_active', 'Yes' );
        update_option( 'sigijh_animation_scroll', 'Yes' ); 
        setcookie('sigijh_resetall', '', time()-3600);
    }
}

function sigijh_highlight_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    $options = sigijh_get_current_options();

    echo ('<div class="wrap">
        <h2>Just Highlight Options</h2>
        <form action="options.php" method="post">');
            settings_fields( 'sigijh_plugin_options' );
            echo (' <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="sigijh_color_select">Highlight Color</label></th>
                        <td>
                            <input type="text" name="sigijh_color_select" value="'.$options['sigijh_color_select'].'" class="sigijh-color-field" data-default-color="#ff0" />
                            <p class="description">Choose the highlight background color.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="sigijh_animation_speed">Animation Speed</label></th>
                        <td>
                            <select name="sigijh_animation_speed">');
                            $speed_options = array( 'Slow', 'Normal', 'Fast', 'Very fast' );
                            $output = '';
                            for( $i=0; $i<count($speed_options); $i++ ) {
                                $output .= '<option value="'.$speed_options[$i].'" '.($options['sigijh_animation_speed'] == $speed_options[$i] ? 'selected="selected"' : '' ) . '>'. $speed_options[$i].'</option>';
                            }
                            echo $output;
                            echo ('</select>
                            <p class="description">Choose the highlight speed animation.</p>
                        </td>
                    </tr>
                    

                    <tr>
                        <th scope="row"><label for="sigijh_animation_scroll">On Scroll Animation</label></th>
                        <td>');
                            $scroll_options = array( 'Yes', 'No');
                            $output = '';
                            for( $i=0; $i<count($scroll_options); $i++ ) {
                                $output .= '<input type="radio" name="sigijh_animation_scroll" value="'.$scroll_options[$i].'" '.($options['sigijh_animation_scroll'] == $scroll_options[$i] ? 'checked' : '' ) . '>'. $scroll_options[$i] .' &nbsp &nbsp ';
                            }
                            echo $output;
                            echo ('<p class="description">Choose YES / NO to activate or disable on scroll animation.<br>
                            <b>Note:</b> If you choose NO all animation will start at once.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="sigijh_animation_active">Show Animation</label></th>
                        <td>');
                            $active_options = array( 'Yes', 'No');
                            $output = '';
                            for( $i=0; $i<count($active_options); $i++ ) {
                                $output .= '<input type="radio" name="sigijh_animation_active" value="'.$active_options[$i].'" '.($options['sigijh_animation_active'] == $active_options[$i] ? 'checked' : '' ) . '>'. $active_options[$i] .' &nbsp &nbsp ';
                            }
                            echo $output;
                            echo ('<p class="description">Choose YES / NO to activate or disable the highlight animation.</p>
                        </td>
                    </tr>

                </tbody>
            </table>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" >'); 
                $url = admin_url( "options-general.php?page=".$_GET["page"] );
                
                echo (' &nbsp &nbsp <a id="sigijh_resetall" href="'.$url . "&resetall=1".'">Reset All</a>
            </p>
        </form>
    </div>
    ');
}

function sigijh_get_current_options() {
    $savedOptions = array();
    try {
        $savedOptions = array(
            'sigijh_color_select' => get_option('sigijh_color_select') ? get_option('sigijh_color_select') : '#ff0',
            'sigijh_animation_speed' => get_option('sigijh_animation_speed') ? get_option('sigijh_animation_speed') : 'Normal',
            'sigijh_animation_active' => get_option('sigijh_animation_active') ? get_option('sigijh_animation_active') : 'Yes',
            'sigijh_animation_scroll' => get_option('sigijh_animation_scroll') ? get_option('sigijh_animation_scroll') : 'Yes',
        );
    } catch ( Exception $e) {}
    return $savedOptions;
}