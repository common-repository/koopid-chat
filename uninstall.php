<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
  exit();

delete_option('koopid_options');
delete_option('koopid_plugin_ver');

?>
