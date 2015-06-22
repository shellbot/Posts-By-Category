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
        add_shortcode( 'sb_category_posts', array( $this, 'posts_shortcode' ) );
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