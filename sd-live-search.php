<?php
/*
  Plugin Name: SD Live Search
  Plugin URI: http:swapnildhanrale.com
  Description: Easy to Search live within site by adding simple shortcode.
  Version: 1.0.2
  Author: Swapnil dhanrale
  Author URI: https://profiles.wordpress.org/swapnild
  Text Domain: text-domain
*/

define( 'SD_SEARCH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
//define( 'SD_SEARCH_PLUGIN_DIR', plugins_url( '/', __FILE__ ) );

if ( !class_exists( 'ult_live_search' ) ) {

    class ult_live_search {

      function __construct() {

        add_action( 'wp_enqueue_scripts', array( $this, 'imedica_child_enqueue_styles' ) );
        add_action('wp_head',array( $this,'custom_search_script') );
        add_filter( 'style_loader_src', array( $this, 'imedica_remove_query_strings' ), 10, 2 ) ;
        add_filter( 'script_loader_src', array( $this, 'imedica_remove_query_strings' ), 10, 2 );

        add_filter( 'clean_url', array( $this, 'imedica_defer_parsing_js' ), 10, 1 );

        add_shortcode('wpbsearch', array( $this, 'get_search_test_form' ) );

        add_shortcode('category_name', array( $this, 'get_the_category_name' ) );
        add_filter('template_include',array( $this, 'my_custom_search_template' ) );

      }

      /* Custom ult-live -search Script in head */
      function custom_search_script() {
        $output="<script>
            jQuery(document).ready(function($) {
                 jQuery('#ult_searchform input[name=s]').liveSearch({url: '/?s='});
            });
        </script>";
        echo $output;
      }

      /*Page loading time optimization*/
      function imedica_child_enqueue_styles() {
        wp_enqueue_style( 'imedica-parent-style', plugins_url( '/style.css', __FILE__ ) );
        wp_enqueue_script( 'script-name', plugins_url( '/js/live-search.js', __FILE__ ) , array(), '1.0.0', true );
      }

      // Remove query strings from urls
      function imedica_remove_query_strings( $src ) {
        $src = remove_query_arg( array( 'v', 'ver', 'rev', 'bg_color', 'sensor' ), $src );
        return $src;
      }

      // Defer parsing of JavaScripts
      function imedica_defer_parsing_js( $url ) {
        if ( ! is_admin() ) {
          if ( false === strpos( $url, '.js' ) ) {
            return $url;
          }
          if ( strpos( $url, 'jquery.js' ) ) {
            return $url;
          }
          if ( strpos( $url, 'slick.min.js' ) ) {
            return $url;
          }
          if ( strpos( $url, 'custom.js' ) ) {
            return $url;
          }
          if ( strpos( $url, 'functions.min.js' ) ) {
            return $url;
          }
          if ( strpos( $url, 'jquery.bxslider.min.js' ) ) {
            return $url;
          }
          return "$url' defer='defer";
        } else {
          return $url;
        }
      }

      function get_search_test_form( $form ) {
        $form = '<form role="search" method="get" id="ult_searchform" action="' . home_url( "/" ) . '" >
        <div><label class="screen-reader-text" for="s">' . __("Search for:") . '</label>
        <input type="text" placeholder="Enter a search term" Required="required"  value="' . get_search_query() . '" name="s" id="s" />
        <input type="submit" id="ult_searchsubmit" value="'. esc_attr__("Search") .'" />
        </div>
        </form>';
        return $form;
      }

      function get_the_category_name( $category ){ ?>
        <div class="entry-title">
          <?php 

          //  Get all Parent Categories - Posts
          $pc = array(
            'parent'                   => 0,
            'orderby'                  => 'name',
            'hide_empty'               => 1,
            'pad_counts'               => false
          );

          // Main Category
          $categories = get_terms('category', $pc);  //or use your custom taxonomy name
          if( !empty($categories) && is_array($categories) ){
            echo '<ul class="bsf-main blog-grid-masonry">'; 
            foreach($categories as $cat){
              echo '<li class="bsf-list post-item"><i class="fa fa-folder folder-icon"></i><a href="'.get_category_link( $cat->term_id ).'">'.$cat->name.'</a>';
              $arg2 = array(
                  'child_of'    => $cat->term_id,
                  'orderby'     => 'name',
                  'hide_empty'  => 1,
                  'pad_counts'  => false 
              );

              //  Get all post from - Sub Category
              $args = array( 'cat' => $cat->term_id );
              $the_query = new WP_Query( $args );
              if ( $the_query->have_posts() ) {
                echo '<ul>';
                while ( $the_query->have_posts() ) { $the_query->the_post(); ?>
                  <li class="bsf-list1"><i class="fa fa-file-text post-icon"></i><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></li>
                <?php }
                echo '</ul><!-- sub category posts -->';
              }

              // Sub Category
              $sub_categories = get_terms('category', $arg2); //or use your custom taxonomy name
              if(!empty($sub_categories) && is_array($sub_categories)){
                echo '<ul class="bsf-sub">';       
                foreach($sub_categories as $scat){
                    echo '<li class="bsf-sub-list"><i class="fa fa-folder sub-folder-icon"></i>'.$scat->name;

                    //  Get all post from - Sub Category
                    $args = array( 'cat' => $scat->term_id );
                    $the_query = new WP_Query( $args );
                    if ( $the_query->have_posts() ) {
                      echo '<ul>';
                      while ( $the_query->have_posts() ) { $the_query->the_post(); ?>
                        <li class="bsf-list1"><i class="fa fa-file-text post-icon"></i><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></li>
                      <?php }
                      echo '</ul><!-- sub category posts -->';
                    }

                echo '</li><!-- sub category single -->';
                }
              echo '</ul><!-- sub category list -->';
            }
              echo '</li><!-- main category single -->';
            }
          echo '</ul><!-- main category list -->';
          }
          ?>
        </div>
      <?php
      }

      function my_custom_search_template($template){
      
          if ( is_search() ) {
             $t = require_once('ult-search.php');
             if ( ! empty($t) ) $template = $t;
          }
          return $template;
      }

      
    }

    new ult_live_search;
}
