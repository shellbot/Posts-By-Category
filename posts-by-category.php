<?php
/*
Plugin Name: Posts By Category
Plugin URI: http://codebyshellbot.com/wordpress-plugins/posts-by-category/
Description: Display posts from a particular category or tag in a variety of ways.
Version: 1.0.0
Author: Shellbot
Author URI: http://codebyshellbot.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class sb_posts_by_category {

    function __construct() {
      add_action('admin_notices', array( $this, 'show_admin_notice' ) );
      add_action('admin_init', array( $this, 'dismiss_admin_notice' ) );
      add_filter( 'plugin_action_links', array( $this, 'add_extra_links' ), 10, 5 );
      add_shortcode( 'sb_category_posts', array( $this, 'posts_shortcode' ) );
    }

    /* Display a notice that can be dismissed */

    function show_admin_notice() {

      if ( current_user_can( 'install_plugins' ) ) {

        global $current_user ;
          $user_id = $current_user->ID;
          /* Check that the user hasn't already clicked to ignore the message */
      	if ( ! get_user_meta($user_id, 'pbc_ignore_admin_notice') ) {
          echo '<div class="updated"><p>';
          printf(__('This Posts by Category plugin is <a href="%1$s" target="_blank">supported through Patreon</a>. If you find it useful, please consider a small donation. Thanks! | <a href="%2$s">Hide Notice</a>'), 'http://patreon.com/shellbot', '?pbc_admin_notice_ignore=0');
          echo "</p></div>";
      	}

      }

    }

    function dismiss_admin_notice() {

    	global $current_user;
      $user_id = $current_user->ID;

      /* If user clicks to ignore the notice, add that to their user meta */
      if ( isset($_GET['pbc_admin_notice_ignore']) && '0' == $_GET['pbc_admin_notice_ignore'] ) {
        add_user_meta($user_id, 'pbc_ignore_admin_notice', 'true', true);
    	}

    }

    /* Support & donate links ----------------------------------------------- */

    function add_extra_links( $actions, $plugin_file ) {
        static $plugin;

        if (!isset($plugin))
          	$plugin = plugin_basename(__FILE__);

        if ($plugin == $plugin_file) {

        		$donate_link = array('donate' => '<a href="http://patreon.com/shellbot" target="_blank">' . __('Donate', 'shellbotics') . '</a>');
        		$support_link = array('support' => '<a href="http://wordpress.org/support/plugin/posts-by-category" target="_blank">' . __('Support', 'posts-by-category') . '</a>');

            $actions = array_merge($donate_link, $actions);
            $actions = array_merge($support_link, $actions);

        }

      	return $actions;
    }


    /* Shortcode ------------------------------------------------------------ */

    function posts_shortcode( $atts ) {

        extract( shortcode_atts(array(
            'title' => '',
            'type' => 'cat',
            'cat' => '1',
            'tag' => '1',
            'show' => '-1',
            'group_by' => 'none',
            'show_image' => 'no',
            'show_author' => 'no',
            'cols' => '1',
        ), $atts ) );

        //Build a proper query
        $query = array (
            'posts_per_page' => $show,
        );

        if( $type == 'tag' ) {
            $query['tag_id'] = $tag;
        } else {
            $query['cat'] = $cat;
        }
        //var_dump($query);

        //Run query
        $posts = get_posts( $query );

        if( !empty( $title ) ) {
            $output = '<h3>' . $title .'</h3>';
        }

        //Group posts if necessary
        switch( $group_by ) {
            case 'year':
                $years = $this->group_by_year( $posts );
                foreach( $years as $year => $posts ) {
                    $output .= '<h4>' . $year . '</h4>';
                    $output .= $this->output_posts( $posts );
                }
                break;
            case 'month':
                $months = $this->group_by_month( $posts );
                foreach( $months as $month => $posts ) {
                    $output .= '<h4>' . $month . '</h4>';
                    $output .= $this->output_posts( $posts );
                }
                break;
            case 'letter':
                $letters = $this->group_by_letter( $posts );
                foreach( $letters as $letter => $posts ) {
                    $output .= '<h4>' . $letter . '</h4>';
                    $output .= $this->output_posts( $posts );
                }
                break;
            default:
                $output .= $this->output_posts( $posts );
        }

        return $output;

    }


    /* Grouping stuff ------------------------------------------------------- */

    function group_by_year( $ungrouped ) {

        $grouped = array();

        foreach( $ungrouped as $post ) {
            $datetime_bits = explode( ' ', $post->post_date );
            $date_bits = explode( '-', $datetime_bits['0'] );
            $year = $date_bits['0'];

                $grouped[$year][] = $post;
        }

        return $grouped;

    }

    function group_by_month( $ungrouped ) {

        $grouped = array();

        foreach( $ungrouped as $post ) {
            $datetime_bits = explode( ' ', $post->post_date );
            $date_bits = explode( '-', $datetime_bits['0'] );
            $year = $date_bits['0'];
            $month = $date_bits['1'];

            $group = date( 'F', mktime( 0, 0, 0, $month, 10 ) ) . ' ' . $year;

                $grouped[$group][] = $post;

        }

        return $grouped;

    }

    function group_by_letter( $ungrouped ) {

        $grouped = array();

        foreach( $ungrouped as $post ) {

            $letter = substr( $post->post_title, 0, 1 );

            $grouped[$letter][] = $post;

        }

        ksort( $grouped );

        return $grouped;

    }

    /* Output stuff --------------------------------------------------------- */

    function output_posts( $posts ) {

        $post_list = '';

        $post_list .= '<ul>';

        foreach( $posts as $post ) {
            $post_list .= '<li><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></li>';
        }

        $post_list .= '</ul>';

        return $post_list;

    }

}

$sbpbc = new sb_posts_by_category();
