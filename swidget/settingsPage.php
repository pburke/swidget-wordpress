<?php


function getMessageSettings()
{
  return array(
    "sw_msg_loading" => "Loading",
    "sw_msg_too_early" => "Not yet on sale",
    "sw_msg_offline_only" => "Offline Sales Only",
    "sw_msg_expired" => "Expired",
    "sw_msg_low_qty" => "Low Quantity",
    "sw_msg_sold_out" => "Sold Out",
    "sw_msg_add_to_cart" => "Add to Cart"
  );
}

function getTextSettings()
{
  return array (
    "sw_txt_free" => "Free Item",
    "sw_txt_fee" => "Additional Fee",
    "sw_txt_checkout" => "Checkout Button (Quick)",
    "sw_txt_cart" => "Checkout Button (Cart)",
    "sw_txt_free_checkout" => "Checkout Button (Free item)",
    "sw_txt_add_to_cart" => "Add To Cart Button",
    "sw_txt_discount" => "Discount",
    "sw_txt_member_discount" => "Member Discount",
  );
}

add_action('admin_menu', function() {
  add_options_page( 'Swidget Wordpress Config', 'Swidget Wordpress', 'manage_options', 'swidget', 'generate_swidget_config_page' );
});

add_action( 'admin_init', function() {
  register_setting( 'swidget-wordpress', 'sw_date_format' );
  register_setting( 'swidget-wordpress', 'sw_low_qty' );
  register_setting( 'swidget-wordpress', 'sw_display_product_name' );
  register_setting( 'swidget-wordpress', 'sw_open_tab' );
//Messages
$msgSettings = getMessageSettings();
foreach ($msgSettings as $key => $value) {
  register_setting( 'swidget-wordpress', $key );
}
//Text
$txtSettings = getTextSettings();
  foreach ($txtSettings as $key => $value) {
    register_setting( 'swidget-wordpress', $key );
  }
});

function generate_swidget_config_page()
{
  $msgSettings = getMessageSettings();
  $txtSettings = getTextSettings();
  ?>
  <div class="wrap">
    <h1>Swidget Settings</h1>
    <p> See the <a href="https://github.com/CMP-Studio/swidget-wordpress/blob/master/readme.md">readme</a> for additional information</p>
   <form action="options.php" method="post">
     <?php
     settings_fields( 'swidget-wordpress' );
     do_settings_sections( 'swidget-wordpress' );
     ?>
     <table>
       <!-- date format -->
       <tr>
         <td>
           <label for="sw_date_format">Date Format</label>
         </td>
         <td>
           <input id="sw_date_format" name="sw_date_format" type="text" value="<?php echo esc_attr( get_option( 'sw_date_format' ) ); ?>" />
         </td>
       </tr>
       <!-- Low Qty -->
       <tr>
         <td>
           <label for="sw_low_qty">Low Qty</label>
         </td>
         <td>
           <input id="sw_low_qty" name="sw_low_qty" type="number" value="<?php echo esc_attr( get_option( 'sw_low_qty' ) ); ?>" />
         </td>
       </tr>
       <!-- Display Product Name -->
       <tr>
         <td>
           <label for="sw_display_product_name">Display Product Name</label>
         </td>
         <td>
           <label>
           <input id="sw_display_product_name" name="sw_display_product_name" type="radio" value = "true"  <?php echo esc_attr( get_option('sw_display_product_name') ) != 'false' ? 'checked="checked"' : ''; ?> /> Yes
         </label><br/>
         <label>
           <input id="sw_display_product_name" name="sw_display_product_name" type="radio" value = "false" <?php echo esc_attr( get_option('sw_display_product_name') ) == 'false' ? 'checked="checked"' : ''; ?>  /> No
         </label>
         </td>
       </tr>

       <!-- Open in new tab-->
       <tr>
         <td>
           <label for="sw_open_tab">Open checkout in:</label>
         </td>
         <td>
           <label>
           <input id="sw_open_tab" name="sw_open_tab" type="radio" value = "false"  <?php echo esc_attr( get_option('sw_open_tab') ) != 'true' ? 'checked="checked"' : ''; ?> /> Same Tab
         </label><br/>
         <label>
           <input id="sw_open_tab" name="sw_open_tab" type="radio" value = "true" <?php echo esc_attr( get_option('sw_open_tab') ) == 'true' ? 'checked="checked"' : ''; ?>  /> New Tab
         </label>
         </td>
       </tr>

       <tr>
         <td colspan="2"><h2>Messages</h2></td>
       </tr>
       <?php
       foreach ($msgSettings as $key => $value) {  ?>
         <!-- <?php echo $value; ?> -->
         <tr>
           <td>
             <label for="<?php echo $key; ?>"><?php echo $value; ?></label>
           </td>
           <td>
             <input id="<?php echo $key; ?>" name="<?php echo $key; ?>" type="text" value="<?php echo esc_attr( get_option( $key ) ); ?>" />
           </td>
         </tr>
         <?php } ?>

         <tr>
           <td colspan="2"><h2>Text Modification</h2></td>
         </tr>
         <?php
         foreach ($txtSettings as $key => $value) {  ?>
           <!-- <?php echo $value; ?> -->
           <tr>
             <td>
               <label for="<?php echo $key; ?>"><?php echo $value; ?></label>
             </td>
             <td>
               <input id="<?php echo $key; ?>" name="<?php echo $key; ?>" type="text" value="<?php echo esc_attr( get_option( $key ) ); ?>" />
             </td>
           </tr>
           <?php } ?>

     </table>
     <button name="save">Save</button>
   </form>
 </div>
  <?php
}
