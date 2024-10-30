<?php

/*
 * View website revision history
 * */
class Bountysite_Revisions {

    public static function display_page(){
        $bountysite = get_option('bountysite');
        $api_key = $bountysite['api_key'];
        $api_url = $bountysite['api_url'];
        $limit = $bountysite['show_limit'];
        if (  ( ! $api_key ) || ( ! $api_url  ) )
        {
            Bountysite::print_notification('No API key/URL set. Kindly configure API Key/URL in BountySite Settings.', 'error');
        }
        else
        {
            $args = [
                'timeout'   => BOUNTYSITE_API_TIMEOUT,
                'headers'   => [
                    'Sitename'      => Bountysite::get_sitename() ,
                    'Authorization'  => $api_key
                ],
                'body'      => [
                    'limit'     => $limit
                ]
            ];
            // Check if requested for restore
            if ( ( isset( $_REQUEST['backup_type'])) &&
                ( isset( $_REQUEST['_token'] ) )  )
            {
                $nonce = Bountysite::sanitize_input( mb_substr( $_REQUEST['_token'] , 0, BOUNTYSITE_MAX_API_KEY_LEN ), '/\w/' );
                $backup_type = Bountysite::sanitize_input( mb_substr( $_REQUEST['backup_type'], 0, 10), '/[a-z]/') ;
                if ( ( ( 'web' == $backup_type ) || ( 'database' == $backup_type ) )  &&
                ( wp_verify_nonce( $nonce , 'bountysite_restore' ) ) )
                {
                    $restore_display = Bountysite::sanitize_input( mb_substr( $_REQUEST['display'], 0, BOUNTYSITE_MAX_API_KEY_LEN), '/[\w\s\-\.:]/' );
                    $restore_to_version = Bountysite::sanitize_input( mb_substr( $_REQUEST['version'], 0, BOUNTYSITE_MAX_API_KEY_LEN), '/\w/');
                    $args['body'] = [
                        'backup_type' => $backup_type,
                        'restore_display' => $restore_display,
                        'restore_to_version' => $restore_to_version,
                        'limit'     => $limit
                    ];
                    $api_url .= '/Backup/RestoreTo';
                    $result = Bountysite::make_api_call( $api_url , $args );
                    if ( 'verified_ok' == $result )
                    {
                        Bountysite::print_notification('Restore request successfully sent', 'success');
                    }
                    else
                    {
                        Bountysite::print_notification('Restore request failed : ' . $result , 'error');
                    }
                }
            }

            // fetch web revisions history
            $web_revisions = self::make_revision_api( 'web' , $args);

            // fetch db revisions history
            $db_revisions = self::make_revision_api( 'database' , $args);
?>
<br/>
<div class="col-md-12"><div class="panel-group"><div class="panel panel-default">
  <div class="panel-heading"><h4>BountySite Restore</h4></div>
    <div class="panel-body">
        <ul class="nav nav-pills">
            <li class="active"><a data-toggle="pill"  href="#web">Code</a></li>
            <li><a data-toggle="pill"  href="#database">Database</a></li>
        </ul>
        <div class="tab-content">
            <div id="web" class="tab-pane fade in active" >
<?php self::print_revision_table( $web_revisions , 'web');?>
            </div>
            <div id="database" class="tab-pane fade" >
<?php self::print_revision_table( $db_revisions , 'database' ) ;?>
            </div>
        </div>
    </div>
  </div>
</div></div></div>

<?php
        }
    }


    /*
     * Fetch Revisions data by BSAPI REST call
     */
    private static function make_revision_api( $backup_type, $args ) {
        $args['body']['backup_type'] = $backup_type;
        $bountysite = get_option('bountysite');
        $api_url = $bountysite['api_url'] .  '/Backup/Revisions';
        $result = Bountysite::make_api_call( $api_url , $args, $get_data = 1 );
        if ( ! ( is_array( $result ) ) )
        {
            Bountysite::print_notification(
                'Unable to fetch ' . $backup_type . '  revision history ' . $result,
                'error'
            );
            return [];
        }
        else
        {
            return $result['revisions'];
        }
    }


    /*
     * Print revisions data in table format
     */
    private static function print_revision_table( $revisions , $backup_type )
    {
        if ( ( ! is_array( $revisions ) ) || empty( $revisions ) )
        {
            return ;
        }
?>
<table class="table table-hover">
<tr>
<th>Author </th>
<th>Commit Time </th>
<th>Message </th>
<th>Actions </th>
</tr>
<?php
        foreach( $revisions as $iter )
        {
            echo '<tr>';
            echo '<td>' .esc_attr( $iter['author_name']) . '</td>' ;
            echo '<td>' .esc_attr( $iter['commit_date']) . '</td>' ;
            echo '<td>' . esc_attr($iter['message']) . '</td>' ;
            $restore_link = add_query_arg( [
                'version'   => $iter['hash'],
                'backup_type' => $backup_type,
                'display'   => $iter['commit_date'],
                '_token'    => wp_create_nonce('bountysite_restore')
            ]);

?>
<td>
    <a href= "<?php echo $restore_link ; ?>"><button class="btn btn-primary">Restore</button></a>
</td>
</tr>
<?php
        }
        echo '</table>';
    }
}

?>
