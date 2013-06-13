<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * ebb_lang.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 06/12/2013
*/

#Common language tags.
$lang['login'] = 'Login';
$lang['register'] = 'Register';
$lang['logout'] = 'Logout';
$lang['404'] = 'File does not exists.';
$lang['loggedinas'] = 'Logged In As:';
$lang['welcomeguest'] = 'Not Logged In';
$lang['yes'] = 'Yes';
$lang['no'] = 'No';
$lang['del'] = 'delete';
$lang['edit'] = 'Edit';
$lang['delete'] = 'Delete';
$lang['modify'] = 'Modify';
$lang['enable'] = 'Enable';
$lang['disable'] = 'Disable';
$lang['on'] = 'On';
$lang['off'] = 'Off';
$lang['submit'] = 'Submit'; //@todo try to reduce the usage of generic verbiage like submit.


//$lang['installadmin'] = 'the installation files are still active!!!! Your board is disabled until you remove the installation files!';
//$lang['install'] = 'This Board is currently under work, please come back later once all work here is completed.';
$lang['banned'] = 'The administrator of this board has banned you from accessing this board.';
$lang['suspended'] = 'The administrator of this board has suspended your access.';
$lang['emailban'] = 'This email address or email domain has been blocked. Please use another email address.';
$lang['usernameblacklisted'] = 'The username you wish to use has been blacklisted by the administrator. Please chose another username.';
$lang['username'] = 'Username';
$lang['pages'] = 'Pages';
$lang['next'] = 'Next';
$lang['prev'] = 'Previous';
$lang['spamwarn'] = 'SPAMMING ATTEMPT!';
$lang['nullspam'] = 'spam check is null.';
$lang['expiredsess'] = 'Your session has expired. Please re-login.';
$lang['invalidlogin'] = 'INVALID LOGIN METHOD!';
$lang['alreadyloggedin'] = 'You are already logged in.';
$lang['notloggedin'] = 'You must be logged in to continue';
$lang['ajaxerror'] = 'You can not access this page directly';
$lang['pagination_first'] = 'First';
$lang['pagination_last'] = 'Last';
$lang['reload'] = 'Reload';
$lang['formfail'] = 'Failed to Process Form.';
$lang['processingform'] = 'Processing Form';

$lang['inactivesessiontitle'] = 'Your session is about to expire!';
$lang['inactivenotice'] = 'You will be logged off in';
$lang['seconds'] = 'seconds';
$lang['continuesession'] = 'Do you want to continue your session?';

#buttons
$lang['btnlocked'] = 'Locked';
$lang['btndeletetopic'] = 'Delete Topic';
$lang['btnlocktopic'] = 'Lock Topic';
$lang['btnunlocktopic'] = 'Unlock Topic';
$lang['btndeletemessage'] = 'Delete Message';
$lang['btnquoteauthor'] = 'Quote Author';
$lang['btnpmauthor'] = 'PM Author';

$lang['close'] = 'Close';
$lang['error'] = 'Error';
$lang['info'] = 'Information';
$lang['accessdenied'] = 'You do not have permission to access this area.';
$lang['jsdisabled'] = 'JavaScript is disabled or not supported by your browser. Javascript is required to continue any further.';
#ID-checking language tags.
$lang['nobid'] = 'No Board ID was Located.';
$lang['notid'] = 'No Topic ID was Located.';
$lang['nopid'] = 'No Post ID was Located.';
$lang['noattachid'] = 'No Attachment ID was Located.';
$lang['nopmid'] = 'No PM ID was Located.';
$lang['nogid'] = 'No Group ID Was Located.';
$lang['nosid'] = 'No Style ID was Located.';
$lang['nosmid'] = 'No Smile ID was Located.';
$lang['nocensorid'] = 'No Censor ID was Located.';
$lang['invalidprofile'] = 'Invalid Profile ID entered.';
$lang['invalidaction'] = 'Invalid action request entered.';
$lang['invalidgid'] = 'Invalid GroupID provided.';
$lang['invalidpref'] = 'Invalid Preference Value defined.';
$lang['invaliduser'] = 'Invalid Username provided.';
#general error messages.
$lang['nonews'] = 'No News To Report On.';
$lang['groupstatus'] = 'GROUP STATUS ERROR!';
$lang['extlookup'] = 'No file extension was entered.';
$lang['noaction'] = 'No action was given by user.';
#success notifications.
$lang['deletetopicsuccess'] = 'Successfully Deleted Topic';
$lang['deletereplysuccess'] = 'Successfully Deleted Reply';
$lang['lockedtopicsuccess'] = 'Successfully Locked Topic';
$lang['unlockedtopicsuccess'] = 'Successfully Unlocked Topic';
$lang['applythemesuccess'] = 'New Theme Applied.';
$lang['applythemefail'] = 'Failed to apply new theme.';

#Menu-based language tags.
$lang['newpm'] = 'Unread Message(s)';
$lang['home'] = 'Home';
$lang['search'] = 'Search';
$lang['help'] = 'Help'; //TODO this is obsolete.
$lang['members'] = 'Memberlist';
$lang['uoptions'] = 'User Options';
$lang['viewprofile'] = 'View Profile';
#language tags for index.php
$lang['index'] = 'Index';
$lang['ticker_txt'] = 'Information Ticker';
$lang['newposts'] = 'New posts since last visit';
$lang['boards'] = 'Boardlist';
$lang['description'] = 'Description';
$lang['topics'] = 'Topics';
$lang['posts'] = 'Posts';
$lang['moderators'] = 'Moderators';
$lang['subboards'] = 'Sub-Boards';
$lang['lastposteddate'] = 'Last Posted Date';
$lang['Postedby'] = 'Posted by';
$lang['noposts'] = 'No Posts';
$lang['boardstatus'] = 'Board Status';
$lang['membernum'] = 'Members';
$lang['newestmember'] = 'Welcome our newest member';
$lang['iconguide'] = 'Icon Guide';
$lang['newpost'] = 'New Post';
$lang['oldpost'] = 'Old Post';
$lang['whosonline'] = 'Currently Online';
$lang['onlinekey'] = '[Key]<br /><b>Administrator</b><br /><i>Moderator</i><br />Member';
$lang['guestonline'] = 'guests are online currently.';
$lang['poweredby'] = 'Powered by';
#RSS language tags.
$lang['viewfeed'] = 'View RSS Feed for this board.';
$lang['latestposts'] = 'Latest Topics created.';
$lang['invalidopt'] = 'You entered an invalid choice, board may not exist anymore.';
#search.php language tags
$lang['keyword'] = 'Keyword';
$lang['selectsearchtype'] = 'Search for';
$lang['selboard'] = 'Select a Board';
$lang['noresults'] = 'Nothing was found based on what you requested.';
$lang['searchresults'] = 'Search Results';
$lang['result'] = 'Result(s)';
$lang['postedin'] = 'Posted In';
$lang['guesterror'] = 'You must be logged in to use this feature.';
$lang['quicksearch'] = 'Quick Search';
$lang['advsearch'] = 'Advanced Search';
$lang['flood'] = 'Please wait 20 seconds before searching again.';
$lang['relatedtopics'] = 'Related Topics';
$lang['nosimilar'] = 'No topic matches found.';
$lang['joindate'] = 'Join Date';
#Group Language tags
//$lang['grouplist'] = 'Grouplist';
//$lang['viewgroup'] = 'View Group Roster';
//$lang['groupmembers'] = 'Group Members';
//$lang['details'] = 'Group Details';
//$lang['groupname'] = 'Group Name';
//$lang['description'] = 'Group Description';
//$lang['groupstat'] = 'Group Status';
//$lang['open'] = 'Membership Open';
//$lang['closed'] = 'Membership Closed';
//$lang['nomembers'] = 'No users currently belong to this group.';
//$lang['notexist'] = 'This group does not exist!';
#Private Message Language Tags
$lang['pm'] = 'Private Messages';
$lang['pm_access_user'] = 'The user you wish to send a PM to does not have the rights to access this area.';
$lang['viewpm'] = 'View Message';
$lang['from'] = 'From';
$lang['to'] = 'To';
$lang['invalidfolder'] = 'You have requested an invalid folder name.';
$lang['inbox'] = 'Inbox';
//$lang['outbox'] = 'Outbox';
$lang['archive'] = 'Archive';

$lang['moveconfirm'] = 'Archive message, are you sure?';

$lang['movemsg'] = 'Archive Message';
$lang['pmquota'] = 'PM Quota:';
$lang['archivequota'] = 'Archive Quota:';
$lang['curquota'] = 'Inbox Amount:';
$lang['sendpm'] = 'Send PM';
$lang['subject'] = 'Subject';
$lang['sender'] = 'Sender';
$lang['delpm'] = 'Delete Message';
$lang['date'] = 'Date';
$lang['banneduser'] = 'Banned User';
$lang['PostPM'] = 'Post New Message';
$lang['replypm'] ='Reply to Message';
$lang['send'] = 'Send to';
$lang['pmmsg'] = 'Message';
$lang['btnreply'] = 'Reply'; //@TODO do we EVEN need this? or is this already defined somewhere else?
$lang['pm404'] = 'PM Message doesn\'t exist.';
$lang['pmsubject'] = 'New PM in your Inbox';
$lang['pmaccessdenied'] = 'You do not have access to read this Message.';
$lang['overquota'] = 'The user who your trying to send an PM to currently is over their quota space.';
$lang['folderfull'] = 'This folder is full.';
$lang['blocked'] = 'This user has blocked you from sending them any form of PMs.';
$lang['confirmdelete'] = 'Are you sure you wish to delete this message?';

$lang['sentpmsuccessfully'] = 'PM Sent Successfully';
$lang['delpmsuccessfully'] = 'PM Deleted Successfully';
$lang['archpmsuccessfully'] = 'PM Archived Successfully';
$lang['usrprofilesuccess'] = 'User Profile Updated successfully!';
$lang['usrsettingssuccess'] = 'Successfully Saved User Settings';
$lang['usrmessengersuccess'] = 'Successfully Saved Messenger Settings';
$lang['usrsigsuccess'] = 'Successfully Saved Signature';

//$lang['clearavatarsuccess'] = 'Successfully Cleared Avatar';
//$lang['savedavatarsuccess'] = 'Successfully Saved Avatar';
$lang['removedattachmentsuccess'] = 'Successfully Deleted Attachment';
$lang['removedsubscriptionsuccess'] = 'Successfully Deleted Subscription';
$lang['updatedemailsuccess'] = 'Successfully Updated Email';
$lang['updatedpasswdsuccess'] = 'Successfully Updated Password';
$lang['friendrequestsuccess'] = 'Successfully Requested Friendship';
$lang['blockusersuccess'] = 'Successfully Blocked User';
$lang['friendshipreqblocked'] = 'Friendship request was blocked';
$lang['friendshipreqaccepted'] = 'Friendship request was accepted';
$lang['addfriend'] = 'Add as a Friend';
$lang['blockuser'] = 'Block User';
$lang['emlFriendReqSubject'] = 'Friend Request';

#Login language tags
$login['nouser'] = 'No Username was entered.';
$login['nopass'] = 'No Password was entered.';
$lang['invaliduser'] = 'An Invalid Username was Entered.';
$lang['pass'] = 'Password';
$lang['reg'] = 'Need to register?';
$lang['offlinemsg'] = 'Board is disabled, only Administrators will be allowed to login.';
$lang['remembertxt'] = 'Remember me';
$lang['login'] = 'Login';
$lang['alreadylogged'] = 'you are already logged in.';
$lang['forgot'] = 'Reset Password';
$lang['passwordrecovery'] = 'Password Recovery';
$lang['invalidrecoveryinfo'] = 'Email or username was not found.';
$lang['pwdrecoveremlfail'] = 'Could not email you your password recovery details. Contact your board administrator.';
$lang['incorrectinfo'] = 'Sorry but either the key or the Username are invalid.';
$lang['activationtitle'] = 'User Activation';
$lang['noacctkey'] = 'No activation key was found.';
$lang['correctinfo'] = "Your account has been activated.";
$lang['alreadyactive'] = 'This user has already been activiated.';
#auth.php language tags.
$lang['inactiveuser'] = 'This user has not activated their account. you may either click on the link in the email or contact the admin.';
$lang['invalidlogin'] ='You could not be logged in! the username and/or password are invalid.';
$lang['lockeduser'] = 'You are trying to login to a user\'s account that has been locked out.';
#registration language tags
$lang['email'] = 'Email';
$lang['confirmpass'] = 'Confirm Password';
$lang['pm_notify'] = 'Notify on new Private Messages';
$lang['showemail'] = 'Hide Email from others';
$lang['timezone'] = 'Time Zone';
$lang['timeformat'] = 'Time Format';
$lang['dateformat'] = 'Date Format';
$lang['timeinfo'] = 'select your time format by using the date() syntax from php.';
$lang['style'] = 'Style';
$lang['defaultlang'] = 'Language';
$lang['captcha'] = 'User Verification';
$lang['msn'] = 'MSN Messenger';
$lang['aol'] = 'AOL Messenger';
$lang['yim'] = 'Yahoo Messenger';
$lang['icq'] = 'ICQ Messenger';
$lang['location'] = 'Location';
$lang['sig'] = 'Signature';
$lang['www'] = 'Website';
$lang['tosTitle'] = 'Term of Service Rules';
$lang['agree'] = 'I Agree';
$lang['coppa13'] = 'I am at least 13 years old';
$lang['coppa16'] = 'I am at least 16 years old';
$lang['coppa18'] = 'I am at least 18 years old';
$lang['coppa21'] = 'I am at least 21 years old';
$lang['coppavalidate'] = 'COPPA Verification';
$lang['alertdisablednewuser'] = 'The board administrator has disabled registration.';
$lang['register'] = 'Register';
$lang['nolang'] = 'Select a language.';
$lang['captchanomatch'] = 'CAPTCHA did not match.';
$lang['emailexist'] = 'Your email address has already been used by another member.';
$lang['usernameexist'] = 'This username has already been taken.';
$lang['bannedname'] = 'Sorry, the username you wish to use is banned for security reasons.';
$lang['acctmade'] = "Account Created.";
$lang['acctuser'] = 'Account Pending, please verify your account.';
$lang['acctadmin'] = 'Account Pending, the administrator has to verify your account.';
$lang['alreadyreg'] = "You are already a registered user, if this is not your account logout now.";
$lang['nospecialchar'] = 'Must contain only letters and numbers';
$lang['nonesubject'] = 'New account created at';
$lang['usersubject'] = 'Verify account';
$lang['adminsubject'] = 'Account under review';
#delete topic language tags
$lang['deletemessages'] = 'Deleting Message(s)';
$lang['postcon'] = 'Are you sure you wish to delete this post?';
$lang['topiccon'] = 'Are you sure you wish to delete this topic? Deleting this topic will delete any replies posted to this topic as well.';
$lang['msgdeleted'] = 'Message was deleted successfully. Going back in 3 seconds.';
#edit topic language tags
$lang['edittopic'] = 'Edit Topic';
$lang['editpost'] = 'Edit Post';
#moderator CP language tags
$lang['modcp'] = 'Moderator Panel';
$lang['ipinfo'] = 'IP Information';
$lang['noip'] = 'No IP was located';
$lang['topicip'] = 'IP used on this topic';
$lang['getdns'] = 'Get IP Hostname';
$lang['ipusermatch'] = 'Users who match this IP address';
$lang['totalcount'] = 'Other IP addresses this user has posted from';
$lang['warnuser'] = 'Warn User';
$lang['warntxt'] = 'Here you can raise or lower a user\'s warning level. Based on the administrator\'s preference, the user will be banned automatically depending at what level they are at.';
$lang['warnopt'] = 'Warning Option';
$lang['raisewarn'] = 'Raise User\'s Warning Level';
$lang['lowerwarn'] = 'Lower User\'s Warning Level';
$lang['warnreason'] = 'Reason for Warning Level Adjustment';
$lang['suspensionlength'] = 'Suspend User';
$lang['suspendhint'] = 'Enter amount in hours';
$lang['contactopt'] = 'Contact User Options';
$lang['nocontact'] = 'Don\'t Contact';
$lang['pmcontact'] = 'PM';
$lang['contacttxt'] = 'Contact Message';
$lang['nocontrol'] = 'You cannot alter the warning level of an Administrator.';
$lang['alreadybanned'] = 'This user is already banned.';
$lang['actionraise'] = 'Raised warning level on user';
$lang['actionlowered'] = 'Lowered warning level on user';
$lang['actionbanned'] = 'Warning Reached Banning Status';
$lang['actionsuspend'] = 'User got suspended';
$lang['contactsubject'] = 'A Notice from the staff at ';
$lang['movetopic'] = 'Move Topic';
$lang['sameboard'] = 'The board you selected is where the topic already exist.';
$lang['condel'] = 'Are you sure you wish to delete this?';
#poll box language tags.
$lang['total'] = 'Total Votes';
$lang['castvote'] = 'Cast Vote';
$lang['novote'] = 'No vote was casted.';
$lang['votecasted'] = 'Your vote has been recorded.';
#post form language tags.
$lang['newtopic'] = 'Post New Topic';
$lang['newpoll'] = 'Post New Poll';

$lang['posttopic'] = 'Post Topic';
$lang['postreply'] = 'Post Reply';
$lang['nowrite'] = 'You can not post anything on this board.'; //TODO re-phase this tag.
$lang['postingrules'] = 'Posting Rules';
$lang['img'] = '[img]';
$lang['bbcode'] = 'BBcode';
$lang['smiles'] = 'Smiles';
$lang['moresmiles'] = 'More Smiles';
$lang['smiletxt'] = 'Look to the right of the smile to see the bbcode for that smile.';
$lang['topic'] = 'Topic';
$lang['topicbody'] = 'Topic Body';
$lang['options'] = 'Options';
$lang['type'] = 'Post Type';
$lang['normal'] = 'Normal';
$lang['important'] = 'Important';
$lang['notify'] = 'Notify me of Replies to this topic';
$lang['disablebbcode'] = 'Disable BBCode for this post';
$lang['disablesmiles'] = 'Disable Smiles for this post';
$lang['attachfile'] = 'File to Upload';
$lang['uploadfile'] = ' Attach File';
$lang['clearfile'] = 'Clear List';

$lang['viewfiles'] = 'Manage Uploads';

$lang['addMoreFiles'] = "Add More Files";

$lang['delattach'] = 'Delete File';
$lang['cantdelete'] = 'File could not be deleted, report this issue to the board admin.';
$lang['fdeleteok'] = 'deleted successfully.';
$lang['nopoll'] = 'You cannot create a poll on this board.';
$lang['polltext'] = 'Poll Options (put one per line.)';
$lang['polloptionfield'] = 'Poll Options';
$lang['question'] = 'Question';
$lang['topicreview'] = 'Topic Review';
#post processing language tags.
$lang['flood'] = 'Please wait 30 seconds before posting again.';
$lang['cantpost'] = 'You cannot post on this board.';
$lang['noimportant'] = 'You cannot post an important topic on this board.';
$lang['noattach'] = 'You cannot attach this file on this topic.';
$lang['nofileentry'] = 'You did not enter a file to upload.';
$lang['fileexist'] = 'The file you wish to attach exists already on this server, please rename this file and try again.';
$lang['typelimit'] = 'The file you wish to attach is blacklisted.';
$lang['sizelimit'] = 'The file you wish to upload is too big.';
$lang['zerofile'] = 'The file you are trying to upload is empty.';
$lang['cantupload'] = 'The file you wish to attach did not upload correctly, contact the board administrator about this immediately.';
$lang['cantwriteupload'] = 'The upload folder is not writable. contact the adminsitrator to give the upload folder proper permissions.';
$lang['fileuploaded'] = 'uploaded successfully.';
$lang['attachlimit'] = 'You have reached the upload limit for this topic.';
$lang['longquestion'] = 'Your poll question is too long.';
$lang['noquestion'] = 'You did not enter a question for the poll';
$lang['moreoption'] = 'Please have at least 2 options.';
#topic report language tags
$lang['reporttomod'] = 'Report Post to Moderator';
$lang['topicreporttxt'] = 'Found a post or topic that seems to be off topic or against the rules? Fill out this form and the moderators will be informed about this.';
$lang['Reportedby'] = 'Reported by:';
$lang['reason'] = 'Reason for report';
$lang['spampost'] = 'Spam Post';
$lang['fightpost'] = 'Fight Post';
$lang['advert'] = 'Post is only an advertisement';
$lang['userproblems'] = 'Author of topic harassing others';
$lang['other'] = 'Other(please specify)';
$lang['message'] = 'Message'; //@TODO is an exact duplicate of pmmsg!
$lang['submitreport'] = 'Submit Report';
$lang['reportsubject'] = 'Reported Post Alert';
$lang['reportsent'] = 'Report has been sent to the moderators.';
#User CP language tags.
$lang['usernotexist'] = 'This user does not exist!';
$lang['findposts'] = 'Find all posts by this user';
$lang['findtopics'] = 'Find all topics by this user';
$lang['latesttopics'] = 'Latest Topics';
$lang['latestreplies'] = 'Latest Replies';
$lang['rank'] = 'Rank';
$lang['hideemail'] = 'Hidden';
$lang['postcount'] = 'Number of Posts';
$lang['saveprofile'] = 'Save Profile';
$lang['savesignature'] = 'Save Signature';
$lang['saveavatar'] = 'Save Avatar';
$lang['updateemail'] = 'Update Email';
$lang['changepassword'] = 'Change Password';
$lang['getpassword'] = 'Get Password';
$lang['editprofile'] = 'Edit Profile';
$lang['editprofiletxt'] = 'Below is the optional options, update them to fit your needs, enter your current password to confirm the update.';
$lang['currentpass'] = 'Current Password';
$lang['customtitle'] = 'Custom Title';
$lang['nocustomtitle'] = 'You cannot use a custom title.';
$lang['invalidurl'] = 'The URL entered is invalid.';
$lang['lgavatar'] = 'Your avatar image is too big, must be no bigger than 100x100.';
$lang['cursig'] = 'Current Signature';

$lang['allowed'] = 'Allowed file types';
$lang['currentavatar'] = 'Current Avatar';
$lang['clearavatar'] = 'Remove Current Avatar';
$lang['noavatar'] = 'No Avatar';
$lang['saveavatar'] = 'Save Avatar';
$lang['wrongtype'] = 'INVALID File type!';
$lang['noavatarsel'] = 'No Avatar Selected';
$lang['scription'] = 'Subscribed Topics';
$lang['nosubscription'] = 'No Subscription';
$lang['delsubscription'] = 'Delete Subscription';
$lang['confirmUnsubscribe'] = 'Unsubscribe from this topic?';
$lang['currentemail'] = 'Current Email address';
$lang['newemail'] = 'New Email address';
$lang['confirmemail'] = 'Confirm new Email address';
$lang['noemailmatch'] = 'current email did not match the one on your profile.';
$lang['newpass'] = 'New Password';
$lang['connewpass'] = 'Confirm new Password';
$lang['updatepass'] = 'Update Password';
$lang['newpwdsent'] = 'Your new password has been emailed to you.';
$lang['profilemenu'] = 'Profile Options';
$lang['accountmenu'] = 'Account Menu';
$lang['profilemenu'] = 'Profile Menu';
$lang['notesmenu'] = 'Notes Menu';
$lang['privacymenu'] = 'Privacy Menu';
$lang['editinfo'] = 'Edit Personal Info';
$lang['editsig'] = 'Edit Signature';
$lang['editusrsettings'] = 'Edit User Settings';
$lang['editmsgr'] = 'Manage Messenger IDs';
$lang['changetheme'] = 'Change Theme';
$lang['manageattach'] = 'Manage Attachments';
$lang['emailupdate'] = 'Update Email Address';
$lang['subscriptionsetting'] = 'Manage Subscription'; //todo rename key to manageSubscription
$lang['newnote'] = 'Create a New Note';
$lang['managenotes'] = 'Manage Notes';
$lang['notessettings'] = 'Notes Settings';
$lang['friendslist'] = 'Friend\'s List';
$lang['profilesettings'] = 'Profile Settings';
#attachment language tags.
$lang['attachments'] = 'Attachments';
$lang['filename'] = 'Filename';
$lang['filesize'] = 'File Size';
$lang['filetype'] = 'File Type';
$lang['downloadct'] = 'Number of Downloads';
$lang['nofile'] = 'The file you wish to download is no longer on file. Report this to the board administrator immediately.';
$lang['noattachments'] = 'There is no record of attachments for this Topic.';
#viewboard language tags.
$lang['viewboard'] = 'View Board';
$lang['addnewtopic'] = 'Add a new topic';
$lang['addnewpoll'] = 'Add a new topic with a poll';
$lang['attachment'] = 'Attachment';
$lang['topic'] = 'Topic';
$lang['replies'] = 'Replies';
$lang['repliedmsg'] = 'Replied Message(s)';
$lang['views'] = 'Views';
$lang['lastpost'] = 'Last Posted';
$lang['nopost'] = 'No posts are currently on this board. Click on one of the buttons above to begin a topic.';
$lang['noread'] = 'You can not read any posts in this board.';
$lang['newtopic'] = 'New Topic';
$lang['oldtopic'] = 'Old Topic';
$lang['polltopic'] = 'Poll Topic';
$lang['lockedtopic'] = 'Locked Topic';
$lang['importanttopic'] = 'Important Topic';
$lang['hottopic'] = 'Hot Topic';
$lang['marktopics'] = 'Mark all posts as read';
$lang['doesntexist'] = 'The requested item was not found';
#viewtopic language tags.
$lang['viewtopic'] = 'View Topic';
$lang['ptitle'] = 'Printable Version';
$lang['vieworiginal'] = 'View Original Topic';
$lang['replytopicalt'] = 'Reply to this topic';
$lang['postedon'] ='Posted on';
$lang['warnlevel'] = 'Warning Level';
$lang['iplogged'] = 'IP Logged';
$lang['ipmod'] = 'IP:';
#Administration Panel language tags.
/*
START TEMPORARY LANGUAGE TAGS
*/
$lang['reportbugs'] = 'Report Bugs';
$lang['helpimprove'] = 'Help improve Elite Bulletin Board, report any bugs you encounter.';
/*
END TEMPORARY LANGUAGE TAGS
*/
$lang['admincp'] = 'Administration Panel';
//$lang['sessionlength'] = 'Session Length (In Hours)';
//$lang['nosession'] = 'No session length entered.';
//$lang['invalidsession'] = 'Invalid session length entered.';
//$lang['sessiontoolong'] = 'Session length entered is too long.';
$lang['noboardtype'] = 'No Board Type was identified.';
$lang['noacplog'] = 'No action is listed currently.';
$lang['noaccess'] = 'Sorry but only full administrators can access this section.';
$lang['php_info'] = 'PHP Information';
$lang['server_info'] = 'Server Information';
$lang['php_ver'] = 'PHP Version';
$lang['db_driver'] = 'Database Driver';
$lang['db_version'] = 'Datebase Version';
$lang['acp_auditlog'] = 'Admin Action Log';
$lang['acp_log'] = 'Last Admin Actions';

$lang['acp_performedby'] = 'Performed By';
$lang['acp_actionperformed'] = 'Action Performed';
$lang['acp_performedon'] = 'Performed On';

$lang['acp_lclear'] = 'Clear Audit Log';
$lang['confirmacpclear'] = 'Are you sure you want to clear the Audit Log?';
$lang['boardmenu'] = 'Board Menu';
$lang['generalmenu'] = 'General Menu';
$lang['groupmenu'] = 'Group Menu';
$lang['usermenu'] = 'User Menu';
$lang['stylemenu'] = 'Style Menu';
$lang['settings'] = 'Settings Menu';
$lang['verdetails'] = 'Version Details';
$lang['versiondetails'] = 'View Version Details';
$lang['modlist'] = 'Install an Add-On';

$lang['acpinfo'] = 'Use the menu on the left to administer your board.';
$lang['ebb_version'] = 'Installed version of Elite Bulletin Board';

$lang['update_available'] = 'A newer version of Elite Bulletin Board is available and you should consider upgrading. The newest version is %s, released on %s.';
$lang['update_notavailable'] = 'You are running the current version of Elite Bulletin Board.';
$lang['update_patchurl'] = 'Download Patched Files';
$lang['update_severity'] = 'Severity';
$lang['updateerr'] = 'Unable to Run Update Checker. To find out the latest version of Elite Bulletin Board, visit the official website.';
$lang['manage'] = 'Manage';
$lang['boardsetup'] = 'Board Setup';
$lang['newsletter'] = 'Newsletter';
/*
$lang['createstyle'] = 'Create';
*/

$lang['managestyles'] = 'Manage Styles';
$lang['manageusers'] = 'Manage Users';

$lang['installstyle'] = 'Install Style';
$lang['uninstallstyle'] = 'Uninstall Style';
$lang['uninstallstyleconfirm'] = 'Are you sure you want to uninstall this style?';
$lang['uninstallstylesuccess'] = 'Style Uninstalled Successfully';
$lang['uninstallstylefailure'] = 'Style Uninstall Failed';
$lang['styleinuse'] = 'Style is still used by at least one user.';
$lang['onestyleinstalled'] = 'Only one style is installed.';
$lang['banlist'] = 'Ban List';
$lang['blacklist'] = 'Blacklisted Usernames';
$lang['activateacct'] = 'Activate an account';
$lang['warninglist'] = 'User Warning Log List';
$lang['censor'] = 'Censor';
$lang['groupsetup'] = 'Group Setup';
/*$lang['grouppermission'] = 'Group Permissions';*/
$lang['pendinglist'] = 'Pending List';
$lang['usersettings'] = 'User Settings';
$lang['boardsettings'] = 'Board Settings';
$lang['announcementsettings'] = 'Announcement Settings';
$lang['attachmentsettings'] = 'Attachment Settings';
$lang['userprune'] = 'User-Pruning';
$lang['modinstalltxt'] = 'Below you will see any Add-ons that are available to install.';
#board cp.
$lang['newboard'] = 'Create a Message Board';
$lang['newparentboard'] = 'Create a Parent Board';
$lang['newsubboard'] = 'Create a Child-Board';
$lang['reorder'] = 'Reorder Board';
$lang['reorderText'] = 'Drag panels to reorder the board.';

$lang['reordersuccess'] = 'Reorder Applied.';
$lang['reorderfail'] = 'Fail to Reorder Board.';

$lang['boardname'] = 'Board Name';
$lang['description'] = 'Description';
$lang['boardpermissions'] = 'Board Permissions';
$lang['boardread'] = 'Who can Read this Board?';
$lang['boardwrite'] = 'Who can Post in this Board?';
$lang['boardreply'] = 'Who can Reply to topics?';
$lang['boardvote'] = 'Who can vote on the polls?';
$lang['boardpoll'] = 'Who can create new polls?';
$lang['selaccesstype'] = 'Select Access Type';
$lang['access_private'] = 'Private Access';
$lang['access_admin'] = 'Level 1';
$lang['access_admin_mod'] = 'Level 2';
$lang['access_all'] = 'Everyone';
$lang['access_users'] = 'Registered Users';
$lang['access_none'] = 'No One';
$lang['parentboard'] = 'Parent Board';
$lang['selparent'] = 'Select Parent Board';
$lang['postincrement'] = 'Allow post in board count towards user\'s post count';
$lang['bbcode'] = 'BBCode';
$lang['img'] = '[img] Tag';
$lang['addboard'] = 'Add New Board';

$lang['addboardsuccess'] = 'New Board Added Successfully';
$lang['editboardsuccess'] = 'Board Updated Successfully';

$lang['modifyboard'] = 'Modify Board';
$lang['delboard'] = 'Delete Board';
$lang['catdelwarning'] = 'WARNING: anything associated with this board will get deleted as well!';
$lang['successdeleteboard'] = 'Successfully Deleted Board';
#group cp
$lang['addgroup'] = 'Create a new group';
$lang['manageprofile'] = 'Manage Group Profile';
$lang['grouphidden'] = 'Hidden Group';
$lang['groupaccess'] = 'Group Level';
//
$lang['viewlist'] = 'View Group Memberlist';
//
$lang['groupopts'] = 'Group Options';
$lang['sel_level'] = 'Select Access Level';
$lang['level1'] = 'Level 1 - Full Access';
$lang['level2'] = 'Level 2 - Limited Access';
$lang['level3'] = 'Level 3 - Regular Access';
$lang['groupprofile'] = 'Group Profile';
$lang['groupprofilehnt'] = 'Group Profile is a template of access rules that affect what users in the group can access.';
$lang['addgroupbtn'] = 'Add Group';
$lang['invalidprofilecho'] = 'You cannot use the selected profile for the group your making.';
$lang['modifygroup'] = 'Modify Group';
$lang['nodelgroup'] = 'You cannot delete this group, it is a default group that must stay in tact.';
$lang['userexistgroup'] = 'You cannot delete this group as at least one user is still a member of the group.';
$lang['removefromgroup'] = 'Remove user from this group';
$lang['nouserdelete'] = 'You cannot remove this user from the list, this user is currently the only level 1 user and cannot be removed.';
$lang['selgroup'] = 'Select a Group';
$lang['groupnotexist'] = 'This group does not exist.'; //@TODO see if this exists already.
$lang['grouprights'] = 'Group Rights';
$lang['grantprivateaccess'] = 'Give group access to board';
$lang['ungrantprivateaccess'] = 'Remove group access from board';
$lang['grantaccess'] = 'Add group to board';
$lang['ungrantaccess'] = 'Remove group from board';
$lang['pendinglist'] = 'Pending List';
$lang['nopending'] = 'No user is currently pending for this group.';
$lang['pendingaccept'] = 'Accept User';
$lang['pendingdeny'] = 'Deny User';
$lang['addtogroup'] = 'Add a user to this group';
$lang['addusergroup'] = 'Add User';
$lang['newprofile'] = 'New Profile';
$lang['adminprofile'] = 'Administrative Profile';
$lang['moderatorprofile'] = 'Moderator Profile';
$lang['memberprofile'] = 'Member Profile';
$lang['profilename'] = 'Profile Name';
$lang['accesslevel'] = 'Access Level';
$lang['availableactions'] = 'Available Permission List';
#admin-based actions.
$lang['manage_boards'] = 'User can Add/Edit/Delete Boards';
$lang['prune_boards'] = 'User can Prune boards';
$lang['manage_groups'] = 'User can manage all group operations';
$lang['mass_email'] = 'User can send out a newsletter to all users';
$lang['word_censor'] = 'User can add words to censor list';
$lang['modify_settings'] = 'User can modify board settings';
$lang['manage_styles'] = 'User can Add/Edit/Delete styles';
$lang['view_phpinfo'] = 'User can view PHP Information page';
$lang['check_updates'] = 'User can see if board is up to date';
$lang['see_acp_log'] = 'User can see ACP audit log';
$lang['clear_acp_log'] = 'User can clear ACP audit log';
$lang['manage_banlist'] = 'User can Add/Edit/Delete banlist entries';
$lang['manage_users'] = 'User can alter user profile details';
$lang['prune_users'] = 'User can prune inactive users';
$lang['manage_blacklist'] = 'User can Add/Edit/Delete Blacklist entries';
$lang['manage_warnlog'] = 'User can manage warn log entries';
$lang['activate_users'] = 'User can activate new users';
#moderator-based actions.
$lang['edit_topics'] = 'User can edit topics &amp; posts';
$lang['delete_topics'] = 'User can delete topics &amp; posts';
$lang['lock_topics'] = 'User can lock/unlock topics';
$lang['move_topics'] = 'User can move topics';
$lang['view_ips'] = 'User cabn view IPs';
$lang['warn_users'] = 'User can warn/suspend a user';
#user-based actions.
$lang['attach_files'] = 'User can attach files to topics &amp; posts';
$lang['pm_access'] = 'User can access &amp; use the Private Message System';
$lang['search_board'] = 'User can search the board';
$lang['download_files'] = 'User can download attachments';
$lang['custom_titles'] = 'User can use a custom title';
$lang['view_profile'] = 'User can view others profiles';
$lang['use_avatars'] = 'User can use avatars.';
$lang['use_signatures'] = 'User can use a signature';
$lang['join_groups'] = 'User can join groups';
$lang['create_poll'] = 'User can create a poll topic';
$lang['vote_poll'] = 'User can take part in polls';
$lang['new_topic'] = 'User can create topics';
$lang['reply'] = 'User can reply to topics';
$lang['important_topic'] = 'User can mark a topic as important';
////
$lang['createprofile'] = 'Create New Profile';
$lang['noprofiletype'] = 'Invalid Profile Type was used.';
$lang['modifyprofile'] = 'Modify Group Profile';
$lang['noprofileid'] = 'No profile ID was defined.';
$lang['deleteprofile'] = 'Delete Group Profile';
$lang['inuseprofile'] = 'One or more groups use the profile you wish to delete.';
$lang['reservedprofile'] = 'The profile you wish to delete is reserved and cannot be deleted.';

$lang['spamlist'] = 'Spam List';
$lang['addspamword']= 'Add Spam Word';
$lang['editspamword']= 'Edit Spam Word';
$lang['deletespamword']= 'Delete Spam Word';
$lang['spamword']= 'Spam Word';
$lang['confirmdeletespamword']= 'Are you sure you want to delete this spam word?';
$lang['spamwordaddsuccess'] = 'Spam word added successfully';
$lang['spamwordupdatesuccess'] = 'Spam word updated successfully';
$lang['spamworddeletesuccess'] = 'Spam word deleted successfully';
$lang['spamworddeletefailed'] = 'Failed to delete spam word';
$lang['sendnewsletter'] = 'Send Newsletter';
$lang['failedloadingemaillist'] = 'Failed to get user email list';
$lang['newslettersentsuccess'] = 'Successfully Sent out Newsletter';
$lang['newslettersentfailure'] = 'Failed to Send out Newsletter';

$lang['perpg'] = 'Entries Per Page';
$lang['perpghint'] = 'how many entries to display per page.';

$lang['boardemail'] = 'Board Email';
$lang['announcestat'] = 'Announcement Status';
$lang['announce'] = 'Announcement Message';
$lang['addannouncement'] = 'Create Announcement';
$lang['createannouncementsuccess'] = 'Announcement Added Successfully';
$lang['deleteannouncement'] = 'Delete Announcement';
$lang['confirmdeleteannouncement'] = 'Are you sure you want to delete this announcement?';
$lang['deleteannouncementsuccess'] = 'Announcement Deleted Successfully';
$lang['announcementlist'] = 'Announcement List';

$lang['defaultstyle'] = 'Default Style';
$lang['defaultlangacp'] = 'Default Language'; //@todo rename this later.

$lang['tosstat'] = 'Registration Rules Status';
$lang['tos'] = 'Registration Rules';
$lang['cpcaptcha'] = 'CAPTCHA';
$lang['pminboxquota'] = 'PM Inbox Quota';
$lang['pmarchivequota'] = 'PM Archive Quota';
$lang['activation'] = 'Activation Type';
$lang['activeusers'] = 'User';
$lang['activeadmin'] = 'Admin';
$lang['none'] = 'None';
$lang['autogroupsel'] = 'New user should be ranked as';
$lang['copparule'] = 'COPPA Validation';
$lang['cpcoppa13'] = 'At Least 13';
$lang['cpcoppa16'] = 'At Least 16';
$lang['cpcoppa18'] = 'At Least 18';
$lang['cpcoppa21'] = 'At Least 21';
$lang['defaulttimezone'] = 'Default Time Zone';
$lang['defaultimtformat'] = 'Default Time format';
$lang['ssl'] = 'Must have an SSL certificate to use this.';
$lang['access_options'] = 'User Access Options';
$lang['registerstat'] = 'Allow New Users?';
$lang['warnthreshold'] = 'Warning Threshold';
$lang['warnthresholdhint'] = 'Set the number that will determine an automatic ban from the board.';
$lang['mxcheck'] = 'Validate Email Domain MX record';
$lang['mxcheckhint'] = 'This checks for valid email domains.';
$lang['boardsettingssuccess'] = 'Board Settings Saved Successfully';
$lang['attachmentquota'] = 'Maximum Size user can upload.';
$lang['attachmentquotahint'] = 'Enter this amount in kilobytes.';
$lang['guestdownload'] = 'Can Guest Download the attachments?';
$lang['attachmentsettingssuccess'] = 'Attachment Settings Saved Successfully';

//$lang['attachmentwhitelist'] = 'Attachment Whitelist';
//$lang['attachmentwhitelisthint'] = 'Here you can list what extensions to allow for attachments';
//$lang['extensionhint'] = 'do not include dot(.) (Example: pdf)';
//$lang['addextension'] = 'Add Extension';
//$lang['removeattachwhitelist'] = 'Remove Extension from Whitelist';
//$lang['removeattachwhitelisthint'] = 'Removing an extension from this list will mean banning the removed extension.';
//$lang['removeextension'] = 'Remove Extension';

$lang['savesettings'] = 'Save Settings';
$lang['nocmdid'] = 'No cmd ID was located.';

$lang['seluser'] = 'Select User';
$lang['admintools'] = 'Admin-Only Tools';
$lang['activeuser'] = 'User is active';
$lang['banuser'] = 'Ban this User';
$lang['tickban'] = 'Tick this box to ban this user; this action can be undone later on if needed.';
$lang['deluser'] = 'Delete This User';
$lang['tickdel'] = 'Tick this box to delete this user; this action cannot be undone!';
$lang['notbanned'] = 'User is not banned.';
$lang['warnlogtxt'] = 'Here is where you can see and revoke the warning actions performed.';
$lang['warnperformed'] = 'Authorized By';
$lang['warneffecteduser'] = 'Effected User';
$lang['warnaction'] = 'Action Performed';
$lang['warnreason'] = 'Reason for Action';
$lang['nowarnactions'] = 'No actions has been performed on any users yet.';
$lang['revokeaction'] = 'Revoke Action';
$lang['norid'] = 'No Revoke ID was located.';
$lang['invalidrid'] = 'warning action doesn\'t exist.';
$lang['revoketext'] = 'If you revoke a banned status action on a user who was a member of a group, you will have to re-add them to that group.';
$lang['deletewarnlog'] = 'Clear Warning Action Log';
$lang['deletewarnlogtxt'] = 'Are You sure you want to clear the user warning log? Doing so will not let you revoke any warning actions listed!';
$lang['noinactiveusers'] = 'There are not any inactive users to activate at this time.';
$lang['useractivated'] = 'User is now set as active, an email has been sent to them.';
$lang['userdeny'] = 'User removed from the database, an email has been sent to them.';
$lang['useractivateerror'] = 'User either does not exist or you provided incorrect information.';
$lang['acceptsubject'] = 'Account Approved';
$lang['denysubject'] = 'Account Denied';
$lang['managestyle'] = 'Manage Styles';
$lang['styleinstaller'] = 'Style Installer';
$lang['styleuninstaller'] = 'Uninstall Style';
$lang['confrmuninstall'] = 'Are You Sure You Want to Uninstall this Style?';
$lang['stylenotexist'] = 'Style does not exist.';
$lang['stylename'] = 'Style Name';
$lang['delstylewarning'] = 'One or more users currently use the requested to delete theme.';
$lang['emailban'] = 'Banning E-mail Addresses';
$lang['matchtypetxt'] = 'Select if this email your banning is an exact match(me@mail.com) or a wildcard(*@hotmail.com).';
$lang['exactmatch'] = 'Exact Match';
$lang['wildcardmatch'] = 'Wildcard Match';
$lang['emailunban'] = 'Unbanning E-mail Addresses';
$lang['ipban'] = 'IP banning';
$lang['ipunban'] = 'Unbanning an IP Address';
$lang['blacklistusername'] = 'Blacklisting a Username';
$lang['blackedusername'] = 'Username to blacklist';
$lang['blacklisttype'] = 'Select if this username your blacklisting is an exact match(admin) or a wildcard(*admin).';
$lang['whitelistingusername'] ='Un-Blacklisting a Username';
$lang['addcensor'] = 'Add Censored Word';
$lang['editcensor'] = 'Edit Censored Word';
$lang['deletecensor'] = 'Delete Censored Word';
$lang['confirmdeletecensorword'] = 'Are you sure you want to delete this censored word?';
$lang['censoraddsuccess'] = 'Censored word added successfully';
$lang['censorupdatesuccess'] = 'Censored word updated successfully';
$lang['censordeletesuccess'] = 'Censored word deleted successfully';
$lang['censordeletefailed'] = 'Failed to delete censored word';
$lang['originalword'] = 'Original Word';
$lang['userprunetext'] = 'This will delete any user who has posted nothing. Just click on the link below to process the pruning. It could take a while depending on the number of 0 posters on your userlist.';
$lang['beginuserprune'] = 'Begin User Pruning';
$lang['userprunewarning'] = 'NOTE: This will delete new users who have been here longer than a week and posted nothing, please let them know before doing this!';
