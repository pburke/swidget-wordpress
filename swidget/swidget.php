<?php
/******************************************************************************
Plugin Name: Swidget
Plugin URI: http://sales.carnegiemuseums.org
Description: Siriusware Widget
Version: 0.1
Author: Carnegie Museums of Pittsburgh
Author URI: http://www.carnegiemuseums.org
License: GPLv2 or later
******************************************************************************/

//Initialize
if( ! function_exists('swidget_shortcodes_init') ){
  function swidget_scripts_init()
  {
    wp_enqueue_script('swidget-script',"https://sales.carnegiemuseums.org/widget/ecommerce-widget.js",array( 'jquery' ));
  }
  function swidget_shortcodes_init()
  {
    init_checkout();
  }
  add_action('init', 'swidget_shortcodes_init');
  add_action('wp_enqueue_scripts', 'swidget_scripts_init');
}

//The checkout widget
function init_checkout()
{
  function swidget_checkout($atts = [], $content = null, $tag='')
  {
    // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $co_atts = shortcode_atts([
      "site" => null,
      "item" => null
    ], $atts, $tag);
    //Start Output
    $site = esc_html($co_atts["site"]);
    $item = esc_html($co_atts["item"]);
    $class = "swidget_$site_$item";

    $out = <<<EOT
  <script>
    jQuery( document ).ready(function(){
      jQuery(".$class").swQuickCheckout($site, $item);
    });
  </script>
  <div class="swidget-holder $class"></div>
EOT;

    return $out;
  }



  add_shortcode('swcheckout', 'swidget_checkout');
}
