<?php
/******************************************************************************
Plugin Name: Swidget
Plugin URI: https://github.com/CMP-Studio/swidget-wordpress
Description: Siriusware Widget
Version: 0.10.002
Author: Carnegie Museums of Pittsburgh
Author URI: http://www.carnegiemuseums.org
License: GPLv2 or later
******************************************************************************/
require_once dirname( __FILE__ ) . '/settingsPage.php';

function getURL($path)
{
  $domain = "sales.carnegiemuseums.org";

  return "https://" . $domain . $path;
}

//Initialize
if( ! function_exists('swidget_shortcodes_init') ){
  function swidget_session_init()
  {
    if ((function_exists('session_status')  && session_status() !== PHP_SESSION_ACTIVE)
    || !session_id()) {
      session_start();
    }
  }
  function swidget_scripts_init()
  {
    wp_enqueue_script('swidget-script',getURL("/widget/ecommerce-widget.js"),array( 'jquery' ));
  }
  function swidget_shortcodes_init()
  {
    init_checkout();
    init_cart();
  }
  add_action('init', 'swidget_session_init', 1);
  add_action('init', 'swidget_shortcodes_init');
  add_action('wp_enqueue_scripts', 'swidget_scripts_init');
}

function getSettings()
{
  $jsonSettings = array();
  $swidgetMappings = array(
    "sw_date_format" => "dateFormat",
    "sw_low_qty" => "lowQty",
    "sw_display_product_name" => "displayName",
    "sw_open_tab" => "openInNewTab",
    "sw_msg_loading" => "messageLoading ",
    "sw_msg_expired" => "messageExpired",
    "sw_msg_low_qty" => "messageLowQty",
    "sw_msg_sold_out" => "messageSoldOut",
    "sw_msg_add_to_cart" => "messageAddToCart",
    "sw_msg_too_early" => "messageTooEarly",
    "sw_msg_offline_only" => "messageOffline",
    "sw_txt_free" => "txtFreeItem",
    "sw_txt_free_checkout" => "txtFreeCheckout",
    "sw_txt_fee" => "txtAdditionalFee",
    "sw_txt_checkout" => "txtCheckoutBtn",
    "sw_txt_add_to_cart" => "txtAddToCartBtn",
    "sw_txt_cart" => "txtCartCheckoutBtn",
    "sw_txt_discount" => "txtDiscount",
    "sw_txt_member_discount" => "txtMemberDisc",
  );

  foreach ($swidgetMappings as $key => $value) {
    if("" !== get_option($key, ""))
    {
      if($key == "sw_display_product_name" || $key == "sw_open_tab")
      {
        if(get_option($key) == "false")
        {
          $jsonSettings[$value] = false;
        }
        else if (get_option($key) == "true")
        {
          $jsonSettings[$value] = true;
        }
      }
      else if($key == "sw_low_qty")
      {
        $jsonSettings[$value] = intval(get_option($key));
      }
      else
      {
        $jsonSettings[$value] = get_option($key);
      }
    }
  }
  return json_encode($jsonSettings);
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
    $settings = getSettings();
    $class = "swidget_$site_$item";

    $out = <<<EOT
    <script>
    jQuery( document ).ready(function(){
      jQuery(".$class").swQuickCheckout($site, $item, $settings);
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
    $settings = getSettings();

    $cart = getCart($site);

    if(!isset($cart)) return "";

    $class = "swidget_cart_$cart";

    $out = <<<EOT
    <script>
    jQuery( document ).ready(function(){
      jQuery(".$class").swCart($cart, $settings);
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
    $settings = getSettings();
    $site = intval($co_atts["site"]);
    $item = intval($co_atts["item"]);
    $cart = getCart($site);

    if(!isset($cart)) return "";

    $class = "swidget_$site" . "_" . $item;

    $out = <<<EOT
    <script>
    jQuery( document ).ready(function(){
      jQuery(".$class").swAddToCart($cart, $site, $item, $settings);
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
  if(!isset($_SESSION[$name]))
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
    $cart = $_SESSION[$name];
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
  if(isset($_SESSION[$name]))
  {
    if(isset($_SESSION[$updateName]))
    {
      $lastUpdate = intval($_SESSION[$updateName]);
      if(abs($lastUpdate - time()) <= 100) return $_SESSION[$name];
    }
  }
  $_SESSION[$updateName] = time();
  $_SESSION[$name] = $cart;

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
