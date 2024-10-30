<?php

/*
Plugin Name: BountySite
Plugin URI: https://www.bountysite.com/wordpress.html
Description: A plugin to backup & restore your website on BountySite.
Author: bountysite
Version: 0.1.1
Author URI: https://www.bountysite.com
Text Domain: bountysite
*/

/*
Copyright (C) 2019 Bountysite

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

// Should not run directly
defined( 'ABSPATH' ) OR die();

// Load path
define( 'BOUNTYSITE_PLUGIN_FILE', __FILE__ );
define( 'BOUNTYSITE_PLUGIN_DIR', dirname( __FILE__ ));
define( 'BOUNTYSITE_STATIC_DIR', dirname( __FILE__ ) . '/admin/' );
define( 'BOUNTYSITE_STATIC_URL', plugin_dir_url( __FILE__ ) . '/admin/' );
define( 'BOUNTYSITE_INCLUDES_DIR', dirname( __FILE__ ) . "/includes/" );
define( 'BOUNTYSITE_MIN_WP', '4.9' );


// default options 
defined('BOUNTYSITE_DEBUG') OR define('BOUNTYSITE_DEBUG', 1 );
defined('BOUNTYSITE_API_TIMEOUT') OR define('BOUNTYSITE_API_TIMEOUT', 10);


define( 'BOUNTYSITE_MAX_API_KEY_LEN', 60);
define( 'BOUNTYSITE_MAX_API_URL_LEN', 255);
define( 'BOUNTYSITE_MAX_SHOW_LIMIT', 200);
define( 'BOUNTYSITE_DEFAULT_SHOW_LIMIT', 30);

/* on plugin load, create object */
require_once( BOUNTYSITE_INCLUDES_DIR . 'class-Bountysite.php' );
add_action(
    'plugins_loaded',
    [
        'Bountysite',
        'instance'
    ]
);

/* activation plugin */
register_activation_hook(
    __FILE__,
    [
        'Bountysite',
        'activation_hook'
    ]
);

/* deactivation plugin */
/*
register_deactivation_hook(
    __FILE__,
    [
        'Bountysite',
        'deactivation_hook'
    ]
);
 */


/* plugin uninstallation */
register_uninstall_hook(
    __FILE__,
    [
        'Bountysite',
        'uninstall_hook'
    ]
);

/* autoload */
spl_autoload_register('Bountysite_autoload');
function Bountysite_autoload($class) {
    if ( in_array(
        $class,
        [
            'Bountysite_Settings',
            'Bountysite_History',
            'Bountysite_Revisions'
        ] ) )  {
            require_once(
                sprintf( "%s/class-%s.php",
                    BOUNTYSITE_INCLUDES_DIR,
                    str_replace('_', '-', $class )
                )
            );
    }
}
?>
