<?php


/* Bountysite class */

class Bountysite {

    // register settings
    // add menu items
    public function __construct() {
        // Register settings
        add_action(
            'admin_init',
            [
                'Bountysite_Settings',
                'register_settings'
            ]
        );


        // Add menu items for settings, Backup History, Backup Revisions
        add_action(
            'admin_menu',
            [
                'Bountysite',
                'add_menu_items'
            ]
        );

    }

    // Initiate new instance
    public static function instance() {
        new self();


    }

    // Get current site hostname
    // Mostly used to for sending Sitename header in BSAPI calls
    public static function get_sitename()
    {
        $parse_url = wp_parse_url(site_url());
        if ( array_key_exists( 'host', $parse_url ) )
        {
            return str_replace('www.', '', $parse_url['host'] );
        }
        else
        {
            return "";
        }
    }



    // Activation Hook
    public static function activation_hook() {
        add_option(
            'bountysite',
            [
                'api_url'       => '',
                'api_key'       => '',
                'show_limit'    => BOUNTYSITE_DEFAULT_SHOW_LIMIT
            ]
        );




    }


    //deactivation hook
    /*
    public static function deactivation_hook() {

    }
     */

    //uninstall hook
    public static function uninstall_hook() {

        // delete option
        delete_option('bountysite');
    }


    // Add menu items
    public static function add_menu_items() {
        add_menu_page(
            'bountysite',
            'BountySite',
            'manage_options',
            'bountysite_settings',
            [
                'Bountysite_Settings',
                'settings_page'
            ],
            BOUNTYSITE_STATIC_URL . "/images/bountysite_menu.png"
        );

        add_submenu_page(
            'bountysite_settings',
            'Backup History',
            'BountySite Backup History',
            'manage_options',
            'bountysite_history',
            [
                'Bountysite_History',
                'display_page'
            ]
        );

        add_submenu_page(
            'bountysite_settings',
            'Backup Revisions',
            'BountySite Backup Revisions',
            'manage_options',
            'bountysite_revisions',
            [
                'Bountysite_Revisions',
                'display_page'
            ]
        );

    }


    /*
     *  SanitizeInput : Remove characters that dont match regex
     *  return stripped input
     */
    public static function sanitize_input( $input, $regex )
    {
        $output = "";
        for ($i = 0; $i < strlen( $input ) ; $i++ )
        {
            if ( preg_match( $regex, $input[$i] ) ) {
                $output .= $input[$i];
            }
        }
        return $output;
    }


    /*
     * make_api_call : REST API call BSAPI and fetch results
     *
     * $api_url - BSAPI URL to call
     * $args - headers sent in BSAPI REST
     * $get_data - 1 - whether to fetch data to be shown
     *             0 - only result status
     *
     * BSAPI always returns JSON
     * status - returns HTTP code of status
     *
     * Returns string if there is any error, irrespective of $get_data
     */
    public static function make_api_call( $api_url, $args, $get_data = 0)
    {
        // make api call
        if ( 1 == BOUNTYSITE_DEBUG ) {
            // local testing 
            $args['sslverify'] = false;
        }
        $response = wp_remote_get( $api_url, $args);
        $http_response_code = wp_remote_retrieve_response_code( $response );
        if ( 200 != $http_response_code )
        {
            return 'API call returned with status code ' . (string) $http_response_code;
        }

        $api_output = json_decode( wp_remote_retrieve_body( $response ) , $assoc = true );
        if ( ! $api_output )
        {
            return 'API call JSON decode error' ;
        }

        if  ( is_array( $api_output ) )
        {
            if ( ! array_key_exists( 'status', $api_output ) )
            {
                return 'Could not find required values from JSON' ;
            }

            if ( 200 != $api_output['status'] ) {
                if   ( array_key_exists( 'msg', $api_output ) ) {
                    return $api_output['msg'] ;
                } else {
                    return 'Non 200 status, but no message to display';
                }
            }


            if ( 0 == $get_data )
            {
                return 'verified_ok';
            }

            else
            {
                return $api_output;
            }
        }
        else
        {
            return 'Invalid JSON reply from server';
        }


        return 'Unable to verify response status';
    }

    /*
     * print_notification : Notify message on top, using bootstrap css
     */
    public static function print_notification($msg, $status)
    {
        if ( 'error' == $status ) {
            $class = 'alert-danger';
        }
        else if ( 'success' == $status ) {
            $class = 'alert-success';
        }
?>
<br/>
<div class="alert <?php echo $class;?> alert-dismissible">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong><?php echo esc_attr($status); ?></strong> <?php echo esc_attr($msg); ?>
</div>
    <?php
    }


}

?>
