=== BountySite ===
Contributors: bountysite
Donate link: -
Tags: backup, security
Requires at least: 4.9
Tested up to: 5.2.2
Stable tag: -
Requires PHP: > 5.x
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

BountySite plugin lets you manage BountySite backup, without having to login to BountySite control panel. 


== Description ==

BountySite plugin allows BountySite users to manage their backups from within wordpress. This is done by making REST API to BountySite Backup API. Before using plugin, API ACLs(whitelisting of IPs) have to configured on BountySite control panel for security purposes. The plugin is useful post configuring backups on BountySite control panel.

BountySite uses FTP/SFTP/FTPS mode to transfer data, as configured within BountySite control panel. To configure your website backup refer https://www.bountysite.com/wordpress.html#configure.

BountySite plugin allows wordpress users to manage the following features:-
- Schedule Backup
- View Backup History of Code(web files) and database
- View Website change history
- Schedule a restore

== Installation ==

1. Download BountySite plugin from https://www.bountysite.com/downloads/wordpress-bountysite-plugin.zip or install from Wordpress plugin by searching bountysite as keyword
2. Activate the plugin through the 'Plugins' screen in WordPress. Click on Activate under BountySite plugin.
3. Before configuring BountySite settings, ensure that you have allowed to make API calls in BountySite control panel.

= Configuing ACL on BountySite Control Panel =
BountySite Backup API is blocked by default to public, for security reasons. 
Login to your BountySite control panel and go to API Key Page.
 - Choose corresponding sitename
 - Go to API key Page (top right navbar Settings dropdown -> API Key)
 - Add wordpress site's public IPaddress, as x.x.x.x/32 in Whitelisted IPs. 
 - Copy API URL  by clicking on Copy button. 
Login to wordpress admin page, and go to BountySite -> BountySite  settings page. 
 - Paste the API URL 
 - On BountySite control panel, copy API Key and paste in API Key
 - Set the maximum number of entries to show in one page, with "History/Revisions Show Limit"
 - Click on "Save Settings" button
BountySite plugin will automaticatlly validate API Key and confirm on successfull validation. If you get API Key and sitename mismatch, check if you have copied API key from the same site as your wordpress site. 


== Usage ==


a) Schedule Backup
- From Wordpress admin panel
- Menu : BountySite -> BountySite Backup History
- Top right side, button, named "RunBackupNow" to schedule a backup
- Code(web files) and database is shceduled for download via mode(FTP/SFTP/SFTP) configured in BountySite control panel
- BountySite backup history page will confirm backup post completion of the backup process


b) View Backup History of Code(web files) and database
- From Wordpress admin panel
- Menu : BountySite -> BountySite Backup History
- Note the tabs named Code and Database
- Tab Code shows web file backup history
- Tab database show database file backup history
- Bytes backed shows total bytes backed
- On incremental backup Bytes backed shows differential bytes of data transfered
- Start time column shows when the backup was started post backup queueing
- Time taken shows the total time taken in seconds from start to end of backup
- Commit Time shows the time in GMT when the file was acutally commited, which is used as reference for restores


c) View Website change history
- Fom Wordpress admin panel
- Menu : BountySite -> BountySite Backup Revisions 
- Revisions page shows history of site changes. This is different from Backup History, cause every backup may not have a change in Code(web files) or database.
- Note the tabs named Code and Database, for web files and db file respectively

d) Schedule a restore
- Fom Wordpress admin panel
- Menu : BountySite -> BountySite Backup Revisions 
- Restore Code or Database, by choosing corresponding tab
- Click on restore button corresponding to the commit time(snapshot), you want to restore to

== Frequently Asked Questions ==

= Why do I need this plugin? =
The plugin gives you a history of file changes, that your site has undergone. It also provides with details of when backup was run, recently. 
You can also schedule an immediate backup, after making a new blog/post.

= What is the difference between Backup History and Revision? =
Backup History shows when the backup was run on your site, and Backup Revisions shows when changes have been recorded. Backups only copy incremental(ie. only file modification) changes. So, every backup may not necessarily have any changes. In this case, there will be an entry in Backup History, but no entry in Backup Revision. 

== Changelog ==

Added API URL as config option, instead of hardcoded value. 

== Upgrade Notice ==

First version. 

== Screenshots ==

1. Settings page - Set API URL and Key from BountySite control panel. Show limit is the number of latest entries to be seen on page. Older ones are not shown. 

2. BountySite Backup History - Shows history of backups run on your site. 
 a. Choose between Code and Database, to see backup history in files and databases directly. 
 b. Schedule a backup immediately for website (Code and Database)
 c. Click on View Changes button to see list of files that have been modified/added/deleted for a given backup job

3. BountySite Restore - Shows change history of your site over time. 
 a. Choose between Code and Database, to see 
 b. Choose corresponding snapshot/revision time and schedule a restore
 
 
Hope you find the plugin useful! More features coming soon.
- Give details of files backed up for a snapshot/revision
