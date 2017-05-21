<?php
add_action('admin_menu', function() {
  add_options_page( 'Swidget Wordpress Config', 'Swidget Wordpress', 'manage_options', 'swidget', 'generate_swidget_config_page' );
});

add_action( 'admin_init', function() {

});

function generate_swidget_config_page()
{
  ?>
    <h1>Test</h1>
  <?php
}
