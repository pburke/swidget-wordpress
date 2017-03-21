<?php
/******************************************************************************
Plugin Name: Swidget
Plugin URI: https://github.com/CMP-Studio/swidget-wordpress
Description: Siriusware Widget
Version: 0.7
Author: Carnegie Museums of Pittsburgh
Author URI: http://www.carnegiemuseums.org
License: GPLv2 or later
******************************************************************************/

function getURL($path)
{
  $domain = "sales.carnegiemuseums.org";

  return "https://" . $domain . $path;
}

//Initialize
if( ! function_exists('swidget_shortcodes_init') ){
  function swidget_scripts_init()
  {
    wp_enqueue_script('swidget-script',getURL("/widget/ecommerce-widget.js"),array( 'jquery' ));
  }
  function swidget_shortcodes_init()
  {
    setcookie("swidget_version", "0.7.000", time() + DAY_IN_SECONDS, "/", COOKIE_DOMAIN );
    init_checkout();
    init_cart();
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
    $site = intval($co_atts["site"]);
    $item = intval($co_atts["item"]);
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

//Cart based functions


function init_cart()
{
  function swidget_cart($atts = [], $content = null, $tag='')
  {
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $co_atts = shortcode_atts([
      "site" => null
    ], $atts, $tag);

    $site = intval($co_atts["site"]);

    $cart = getCart($site);

    if(!isset($cart)) return "";

    $class = "swidget_cart_$cart";

    $out = <<<EOT
    <script>
    jQuery( document ).ready(function(){
      jQuery(".$class").swCart($cart);
    });
    </script>
    <div class="swidget-cart-holder $class" data-cart="$cart"></div>
EOT;

    return $out;
  }

  function swidget_addtocart($atts = [], $content = null, $tag='')
  {
    // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $co_atts = shortcode_atts([
      "site" => null,
      "item" => null
    ], $atts, $tag);
    //Start Output
    $site = intval($co_atts["site"]);
    $item = intval($co_atts["item"]);
    $cart = getCart($site);

    if(!isset($cart)) return "";

    $class = "swidget_$site" . "_" . $item;

    $out = <<<EOT
    <script>
    jQuery( document ).ready(function(){
      jQuery(".$class").swAddToCart($cart, $site, $item);
    });
    </script>
    <div class="swidget-holder $class" data-cart="$cart"></div>
EOT;

    return $out;
  }

  add_shortcode('swcart', 'swidget_cart');
  add_shortcode('swaddtocart', 'swidget_addtocart');
}

//Helper functions
function getCart($site)
{
  $name = "swidget_cart_$site";
  if(!isset($_COOKIE[$name]))
  {
    //No cart: must create one
    $url = getURL("/api/v1/cart/create?site=$site");
    $result = curl_call($url);
    $json = json_decode($result);

    if($json->success)
    {
      return saveCart($name, $json->cart);
    }
  }
  else
  {
    $cart = $_COOKIE[$name];
    $url = getURL("/api/v1/cart/check?site=$site&cart=$cart&recreate=true");
    $result = curl_call($url);
    $json = json_decode($result);

    if($json->success)
    {
      if($json->valid)
      {
        //Cart is still valid
        return $cart;
      }
      else {
        if(isset($json->cart))
        {
          return saveCart($name, $json->cart);
        }
      }
    }
  }

  return null;
}

function saveCart($name, $cart)
{
  $updateName = $name . "_update";
  if(isset($_COOKIE[$name]))
  {
    if(isset($_COOKIE[$updateName]))
    {
      $lastUpdate = intval($_COOKIE[$updateName]);
      if(abs($lastUpdate - time()) <= 100) return $_COOKIE[$name];
    }
  }
  setcookie($updateName, time(), 0, "/", COOKIE_DOMAIN );
  setcookie($name, $cart, 0, "/", COOKIE_DOMAIN );
  //for the current page
  $_COOKIE[$updateName] = time();
  $_COOKIE[$name] = $cart;
  return $cart;
}

function curl_call($url)
{

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Wordpress Swidget");

  $result = curl_exec($ch);
  curl_close($ch);

  return $result;

}
