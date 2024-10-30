<?php

/* Backup History for web code and database
 * Option to run backup now
 */
class Bountysite_History {

    public static function display_page()
    {
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
                    'Sitename'      => Bountysite::get_sitename(),
                    'Authorization'  => $api_key
                ],
                'body'      => [
                    'limit'     => $limit
                ]

            ];

            /*
             * Handle RunBackupNow  if requested
             */
            if ( ( isset( $_REQUEST['RunBackupNow'] ) )  &&
                ( isset( $_REQUEST['_token'] ) ) )
            {
                $nonce = Bountysite::sanitize_input( mb_substr( $_REQUEST['_token'] , 0 , BOUNTYSITE_MAX_API_KEY_LEN ), '/\w/' );
                if ( wp_verify_nonce( $nonce  , 'bountysite' ) )
                {
                    $api_url .= '/Backup/RunBackupNow' ;
                    $result = Bountysite::make_api_call( $api_url, $args ) ;
                    if ( 'verified_ok' == $result )
                    {
                        Bountysite::print_notification('Backup scheduled successfully.', 'success');
                    }else{
                        Bountysite::print_notification(
                            'Error while scheduling backup ' . $result ,
                            'error'
                        );
                    }
                }
            }

            // Fetch history of code and database
            $web_history = self::make_history_api('web', $args );
            $db_history = self::make_history_api('database', $args );

            $runbackupnow_link = add_query_arg( [
                'RunBackupNow'  => true,
                '_token'        => wp_create_nonce( 'bountysite' )
                ]);

?>
<br/>
<div class="col-md-12"><div class="panel-group"><div class="panel panel-default">
    <div class="panel-heading">
        <span>
            <h4>BountySite Backup History
                <a href="<?php echo $runbackupnow_link ?>">
                    <button class="btn btn-primary btn-md pull-right" style="margin-top:-8px;">RunBackupNow</button>
                </a>
            </h4>
        </span>
    </div>
    <div class="panel-body">
        <ul class="nav nav-pills">
            <li class="active"><a data-toggle="pill"  href="#webbackuphistory">Code</a></li>
            <li><a data-toggle="pill"  href="#databasebackuphistory">Database</a></li>
        </ul>
        <div class="tab-content">
            <div id="webbackuphistory" class="tab-pane fade in active" >
<?php self::print_history_table( $web_history ); ?>

            </div>

            <div id="databasebackuphistory" class="tab-pane fade " >
<?php self::print_history_table( $db_history ); ?>
            </div>
        </div>
    </div>
</div></div></div>
<?php
        }
    }
    // end of function display_page

    /* Make api call to BSAPI and fetch backup history
     * */
    private static function make_history_api( $backup_type , $args) {
        $args['body']['backup_type'] = $backup_type;
        $bountysite = get_option('bountysite');
        $api_url = $bountysite['api_url'] . '/Backup/History' ;
        $result = Bountysite::make_api_call( $api_url , $args, $get_data = 1 );
        if ( ! ( is_array($result) )  )
        {
            Bountysite::print_notification(
                'Unable to fetch ' . $backup_type . ' backup history : ' . $result,
                'error'
            );
            return [];
        }
        else
        {
            return $result['history'];
        }
    }


    /*
     * Print table with data
     */
    private static function print_history_table($history) {
        // If no history , $history would be a string
        if ( ( ! is_array( $history ) ) || ( empty( $history ) )  )
        {
            return ;
        }
?>
                <table class="table table-hover">
                    <tr></tr>
                    <th>Bytes Backed </th>
                    <th>Start Time </th>
                    <th>Time Taken </th>
                    <th>Commit Time </th>
<?php
            foreach ( $history as $iter) {
                echo '<tr>';
                echo '<td>' . esc_attr( $iter['bytes_backed']) . '</td>' ;
                echo '<td>' . esc_attr( $iter['start_time']) . '</td>' ;
                echo '<td>' . esc_attr( $iter['time_taken']) . '</td>' ;
                echo '<td>' . esc_attr( $iter['commit_time']) . '</td>' ;
                echo '</tr>';
            }
?>
                </table>
<?php

    }
}

?>
