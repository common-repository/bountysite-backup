<?php

/*
 * Settings page 
 * Validation of options 
 */
class Bountysite_Settings {

    public static function register_settings() {
        register_setting(
            'bountysite',
            'bountysite',
            [
                __CLASS__,
                'validate_settings'
            ]
        );


        //Add bootstrap JS & CSS
        wp_enqueue_style('bountysite_bootstrap_css', BOUNTYSITE_STATIC_URL . 'css/bootstrap.min.css');
        wp_enqueue_script('bountysite_bootstrap_js', BOUNTYSITE_STATIC_URL . 'js/bootstrap.min.js');

    }

    // Validate settings
    public static function validate_settings( $args ) {
        $output = array();

        // verify api_url
        if ( isset( $args['api_url'] ) && ( $args['api_url'] ) ) 
        {
            $args['api_url'] =  esc_attr ( mb_substr( $args['api_url'], 0, BOUNTYSITE_MAX_API_URL_LEN) );
            //$args['api_url'] = Bountysite::sanitize_input( $args['api_url'], '/[a-zA-Z0-9\-_\.:\//');
            if (  filter_var( $args['api_url'] , FILTER_VALIDATE_URL ) )  {
                $output['api_url'] = $args['api_url'];
            } else {
                add_settings_error('bountysite_settings', 'error', 'Invalid URL for API URL ', 'error');
                $output['api_url'] = '';
            }
        }
        else
        {
            add_settings_error('bountysite_settings', 'error', 'API URL mandatory parameter not provided' , 'error' );
            $output['api_url'] = '' ;
        }

        // verify api_key 
        if ( isset( $args['api_key'] ) && ( $args['api_key'] )  ) 
        {
            $args['api_key'] = esc_attr ( mb_substr( $args['api_key'], 0, BOUNTYSITE_MAX_API_KEY_LEN) );
            //$args['api_key'] =  mb_substr( $args['api_key'], 0, BOUNTYSITE_MAX_API_KEY_LEN) ;
            $args['api_key'] =  Bountysite::sanitize_input( $args['api_key'], '/[a-zA-Z0-9]/') ;
            $output['api_key'] = $args['api_key'] ;
            if ( $output['api_url'] ) { 
                // Verify
                $validate_status = self::validate_api_key($output['api_url'], $args['api_key'] );
                if ( 'verified_ok' != $validate_status ) 
                {
                    add_settings_error('bountysite_settings', 'error', esc_attr( $validate_status ) , 'error' );
                    $output['api_key'] = '';
                }
            }
        }
        else
        {
            add_settings_error('bountysite_settings', 'error', 'API key mandatory parameter not provided' , 'error' );
            $output['api_key'] = '' ;
        }


        if ( ( isset( $args['show_limit']) ) && ( $args['show_limit']) ) 
        {
            // Only consider 3 digit
            $args['show_limit'] = esc_attr( mb_substr( $args['show_limit'], 0, 3) ) ;
            $args['show_limit'] = Bountysite::sanitize_input( $args['show_limit'], '/[0-9]/'); 
            if ( ! ( (int) $args['show_limit'])  ) {
                $output['show_limit'] = BOUNTYSITE_DEFAULT_SHOW_LIMIT;
            }
            else
            {
                if ( BOUNTYSITE_MAX_SHOW_LIMIT < $args['show_limit'] )
                {
                    $output['show_limit'] = BOUNTYSITE_MAX_SHOW_LIMIT;
                }
                else
                {
                    $output['show_limit'] = $args['show_limit'];
                }
            }
        }else {
            $output['show_limit'] = BOUNTYSITE_DEFAULT_SHOW_LIMIT;
        }

        return $output ;
    }
    // end of validate_settings



    //Verify API KEY
    private static function validate_api_key($api_url,  $api_key ) 
    {
        $api_url .= '/Backup/ValidateAPIKey' ;
        
        // Generate headers
        $args = array(
            'headers' => [
                'Authorization' => $api_key,
                'Sitename'      => Bountysite::get_sitename()
            ]
        );
        return Bountysite::make_api_call( $api_url, $args);
    }


    //Settings page 
    public static function settings_page() {
        $bountysite = get_option('bountysite');

?>
<div class="wrap">
    <br/><br/>
    <div class="row">
        <div class="col-md-6 col-md-offset-3" >
            <form id="BountySite_form"  method="post" action="options.php">
<?php
        settings_fields('bountysite'); 
        do_settings_sections('bountysite');
        settings_errors();
?>
                <div class="form-group"><div class="panel panel-primary"><div class="panel-body">
                    <br/>
                    <img src="<?php echo BOUNTYSITE_STATIC_URL . "images/bountysite_biglogo.svg";?>" style="display:block;margin-left:auto;margin-right:auto"/>
                    <h4 class="text-center" style="color:#337ab7">Website backup, threat hunting and availabiity</h4>
                    <h3>Settings</h3>
                    <br/>
                    <label for="api_url"> API URL : </label>
                    <input  class="form-control" id="api_url" placeholder="Enter API URL" name="bountysite[api_url]" value="<?php echo esc_attr( $bountysite['api_url'] ) ; ?>">
                    <p class="help-block">This field is mandatory. Copy paste API URL from your BountySite control panel, for API URL of your account. Eg https://partnerbrand.bountysite.com</p>
                    <br/>
                    <label for="api_key"> API Key : </label>
                    <input type="password" class="form-control" id="api_key" placeholder="Enter API Key" name="bountysite[api_key]" value="<?php echo esc_attr( $bountysite['api_key'] ) ; ?>">
                    <p class="help-block">This field is mandatory. Copy paste API key from your BountySite control panel. This key is used to communicate to BountySite API. This is required everytime to make changes to this settings page. </p>
                    <hr style="size:0.5px;border-color:#337ab7;"/> <br><br>


                    <label for="show_limit">History/Revisions Show Limit</label>
                    <input type="text" class="form-control" id="show_limit" placeholder=<?php echo BOUNTYSITE_DEFAULT_SHOW_LIMIT; ?> name="bountysite[show_limit]" value="<?php echo esc_attr( $bountysite['show_limit'] ); ?>">
                    <p class="help-block">Number of entries, you like to see in History and Revisions table. Maximum value : 200.</p>
<?php submit_button( 'Save Settings', 'primary' );?>
                </div></div></div>
            </form>
        </div>
    </div>
</div>
<?php

    }
    // end of settings_page function 



}
// end of class Bountysite_Settings
?>
