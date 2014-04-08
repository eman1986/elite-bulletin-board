<?php
//security check
if (!defined('IN_EBB')){
	die("<b>!!ACCESS DENIED HACKER!!</b>");
}
#default help value, used if no help toic is needed.
$help['nohelptitle'] = 'Help Topics';
$help['nohelpbody'] = 'Sorry, no help topic was found for this page.';
#help topic regarding index.php
$help['indextitle'] = 'Welcome to '.$title;
$help['indexbody'] = 'Below you will find what boards you can post on as well get a feel of who is online right now.<br /><br />
If you haven\'t already, please register yourself so that you can join in on the discussions.<br /><br />
A quick hint: to access these help topics quicker, just press F1 and the help topic pane will open, press F1 again to close them.';
#help toipic regarding Gallery.php
$help['gallerytitle'] = 'Avatar Gallery';
$help['gallerybody'] = 'This is where you can select from what the board administrator has provided themselves.<br /><br />
Just click on the radio button that represent the avatar you want and then click on Save Avatar.';
#help topic regarding groups.php
$help['grouplisttopic'] = 'Grouplist';
$help['grouplistbody'] = 'This page will allow to look-up any public group in the community.<br /><br />
Click on the group name to find out more about them<br /><br />
To join a group, go to Profile.php';
#help topic regarding login.php
$help['lostpwdtitle'] = 'Lost Password';
$help['lostpwdbody'] = 'If you forgot your password, fill out the form below and a new password will be emailed to you.<br /><br />
Your account will be deactivated during this time until you reactivate it.';
$help['logintitle'] = 'Logging onto the Board';
$help['loginbody'] = 'Enter in your username & password to log on.<br /><br />
If your on a private computer and want to stay logged on, tick the checkbox that reads: "Tick this to always be logged on". This will require that cookies are enabled.';
#help topic regarding manage.php
$help['ipinfotitle'] = 'IP Information';
$help['ipinfobody'] = 'This has many good uses, from doing a look-up to ban the user to seeing if the user is masking their IP, its a way to keep spammers from hiding.';
$help['dnslookuptitle'] = 'Get IP Hostname';
$help['dnslookupbody'] = 'This tool allows you to reverse the IP and get the full scope on the IP Address.';
$help['warnusertitle'] = 'User Warning Center';
$help['warnuserbody'] = 'This tool allows you to warn, ban, and suspend a user. The board administrator can set the warning value that will ultimately ban the user automatically.<br /><br />
You may also contact the user if desired to provide a reason for the action.<br /><br />
To control abuse, all actions done here are logged and the Administrator can reverse the actions performed.';
#help topic regarding PM.php
$help['pmtitle'] = 'Private Message Center';
$help['pmbody'] = 'This is where you can engage in private conversations.<br /><br />
To Start a new Conversation, Click on "New Topic".<br /><br />
To see who is on your banlist, click "View Banlist".<br /><br />
All saved PM messages will go in the Archive directory.';
$help['pmreadtitle'] = 'PM Messages';
$help['pmreadbody'] = 'From here you can reply, delete or save this message or ban the user who sent the message.<br /><br />
To reply to this message, click on "Reply".<br /><br />
To Delete the message, click on "Delete Message", it\'ll ask to confirm the action.<br /><br />
To archive this  message, click on "Archive Message", it\'ll ask to confirm the action.<br /><br />
To ban the author of this message click on "Ban User", found next to the author\'s name.';
$help['pmcreatetitle'] = 'Writing a new PM message/Replying to a PM Message';
$help['pmcreatebody'] = 'When posting a PM message be aware that if the user your sending the message to has met their quota, your message will NOT go through.<br /><br />
Once that user has cleaned out their inbox is when you can re-send the message.';
$help['pmbantitle'] = 'Blacklisting a User';
$help['pmbanbody'] = 'Blacklisting a user will prevent them from sending you any private messages, however, this DOES NOT prevent them from replying to your topics.';
#help topic regarding Post.php
$help['attachtitle'] = 'Adding an Attachment to a Post';
$help['attachbody'] = 'To add an attachment to your topic, click on "Manage Attachments" and a dialog will display. Once uploaded, it\'ll automatically appear after you finish the rest of the new topic.<br /><br />
As of now, you are only allowed one(1) attachment per topic, this restriction will be lifted in v3.0.0';
$help['polltitle'] = 'Making a Poll Topic';
$help['pollbody'] = 'When making a poll topic, you MUST use at least two(2) options in the poll and each poll option MUST go in order(1,2,3,4,...)<br /><br />
<b>Adding an Attachment to a Post</b><br /><br />
To add an attachment to your topic, click on "Manage Attachments" and a dialog will display. Once uploaded, it\'ll automatically appear after you finish the rest of the new topic.<br /><br />
As of now, you are only allowed one(1) attachment per topic, this restriction will be lifted in v3.0.0';
#help topic regarding Profile.php
$help['editprofiletitle'] = 'Setting up your User Portal Page';
$help['editprofilebody'] = 'When setting up your portal page, you have the option of listing any contact information and also listing two RSS/XML feeds.<br /><br />
When listing feeds on your portal page, please select an appropriate feed as this will be viewable to all users and some content could be deemed as violating board policy.';
$help['sigtitle'] = 'Signature Settings';
$help['sigbody'] = 'When setting up a signature, be aware the limit is 255 characters. You may use BBCode in your signature, but not html.';
$help['avatartitle'] = 'Using an Avatar from a Remote Location';
$help['avatarbody'] = 'When using an avatar from a remote location, there are a few guidelines that MUST be followed:<br /><br />
The formats allowed are: gif, jpeg, jpg, and png.<br /><br />
The avatar cannot be bigger than 100x100.<br /><br />
These rules do not apply to avatars found in the Avatar Gallery.';
$help['groupmanagertitle'] = 'Group Membership Manager';
$help['groupmanagerbody'] = 'From here, you can ether join or unjoin a group.<br /><br />
When joining a group, you have to be approved by the board administrator before you gain any privileges that group provides.';
$help['attachmentmanagertitle'] = 'Attachment Manager';
$help['attachmentmanagerbody'] = 'This is where you can delete any attachments you no longer want related to a topic.<br /><br />
When deleting an attachment, it will automatically be removed from the topic and will also be deleted from the web space it was uploaded onto.';
$help['digesttitle'] = 'Topic Subscription Manager';
$help['digestbody'] = 'When deleting a subscription, you will no longer get an email when a new reply is posted to that topic, you may resubscribe at any time.';
#help topic regarding register.php
$help['registertitle'] = 'Registering an Account';
$help['registerbody'] = 'When registering an account, you will only be able to provide basic information, however, once logged in you may further customize your account.<br /><br />
If a CAPTCHA Image is presented, you MUST correctly match the code.<br /><br />
If any terms are set, you must agree to the terms.<br /><br />
If COPPA is enabled, you MUST meet the set age rule to continue.';
#help topic regarding report.php
$help['reporttitle'] = 'Reporting a Topic';
$help['reportbody'] = 'When reporting a topic, please provide as much details as possible. When you submit the form, any group that is in control of the board will get notified and settle the issue.';
#help topic regarding Search.php
$help['searchtitle'] = 'Advanced Searching';
$help['searchbody'] = 'The Advanced Search allows you to set a more specific term to search for.<br /><br />
You can choose which board to look in, what type of topic, and what user. If you just need a quick search, the Quick Search dialog will be better suited.';
#help topic regarding acp_login.php
$help['acplogintitle'] = 'Logging into Administration Panel';
$help['acploginbody'] = 'Before accessing the Administration Panel, you need to verify that you have the privileges to access the Administration Panel<br /><br />
When logged in, you will have an hour before your session will expire.';
#help topic regarding acp/index.php
$help['acptitle'] = 'Administration Panel';
$help['acpbody'] = 'Here is where you control the entire board.<br /><br />
To manage the board hover over a category and more options will appear.<br /><br />
Below, you see a version checker, your PHP/MySQL setup, and the last logged actions on the Administration Panel.<br /><br />
To get more details regarding a new update, click on "View Version Details", which will display what\'s new with the update.<br /><br />
You also can install Add-Ons from here by click on "Install an Add-On".<br /><br />
Clicking on "PHP Information" will allow you to see what your server is setup, which will better help configure your board.<br /><br />
Finally, all actions performed here are fully logged to ensure if something happens, you know when and who did it and it even logs attempts to login under false pretenses.';
$help['phpinfotitle'] = 'PHP Information';
$help['phpinfotitle'] = 'This will allow you to see how PHP is configured, this will allow you to better configure your board.';
#help topic regarding boardcp.php
$help['boardmanagetitle'] = 'Board Manager';
$help['boardmanagebody'] = 'Here is where you can manage all boards made, you can re-organize them, modify, delete, and create boards.<br /><br />
Click on a category to reach the board, click on a board to reach a sub-board.';
$help['addboardtitle'] = 'Creating a New Category/Board/Sub-Board';
$help['addboardbody'] = 'When creating a new board, you have various permissions and below is each value means.<br /><br />
<i>Private Access</i>: This allows you to make a board private, when set to private you can assign which group can access it in the Group Control Panel.<br /><br />
<i>Level 1</i>: This allows you to make private boards that are for Administrators-Only.<br /><br />
<i>Level 2</i>: This allows you to allow Moderator level and Administrator level users access the board.<br /><br />
<i>Registered Users</i>: This allows any registered user to access the board.<br /><br />
<i>Everyone</i>: This allows everyone access the board.<br /><br />
<i>No One</i>: This is most restrictive, allowing no one at all to access the board; it should be used for archive-purposes only.';
$help['pruneboardtitle'] = 'Pruning a  Board';
$help['pruneboardbody'] = 'When pruning a board enter the age you wish to remove(enter 10 will remove anything 10 days old)<br /><br />
Then Select which board to prune, you can only prune one board at a time.';
#help topic regarding generalcp.php
$help['newslettertitle'] = 'Newsletter Center';
$help['newsletterbody'] = 'Here is where you can email all of your users regarding important information.<br /><br />
When using this tool, be aware that it will send text-only emails, currently does not support HTML content.';
$help['censortitle'] = 'Censorlist/Anti-Spam Filter';
$help['censorbody'] = 'When adding to the censorlist, you have two choices; Ban Use of Word and Mark as Spam.<br /><br />
<i>Ban Use of Word</i>: This just censors the word entered.<br /><br />
<i>Mark as Spam</i>: This will kill the spammer in their tracks as if they post anything that contains that word, it\'ll not go through.<br /><br />';
#help topic regarding acpsettings.php
$help['usercpbody'] = 'This will control anything the User interacts in.';
$help['boardcpbody'] = 'This will control any board-wide actions.';
$help['mailcpbody'] = 'This will setup the way emails are handled.';
$help['cookiecpbody'] = 'This will setup the cookies are made.';
$help['attachcpbody'] = 'This will control how attachments are handled.';
#help topic regarding groupcp.php
$help['newgrouptitle'] = 'Creating a new Group';
$help['newgroupbody'] = 'When creating a new group, you can make the group a public group or a private group, you also can set what group profile that group will abide to.<br /><br />
Group Profiles are a more specific set of rules, allowing you to block portions of the board to a group, you can open up the entire board or even close it all down to the most restrictive condition(this would be best for spammer accounts).';
$help['grouplisttitle'] = 'Grouplist';
$help['grouplistbody'] = 'This lists all members of a group, you may also remove a user from the group if desired.';
$help['grouprightstitle'] = 'Group Rights';
$help['grouprightsbody'] = 'Here is where you set the groups actual rights to a board.<br /><br />
"Give group access to board" will apply any rules marked as "Private"<br /><br />
"Add group to board" will give the group moderator status.';
$help['pendinglisttitle'] = 'Group Pending Approval';
$help['pendinglistbody'] = 'Here is where you either Grant or Deny a user\'s request to be a member of a group.<br /><br />
You also add a user to the group yourself by using the form below the pendinglist.<br /><br />
Deny or accepting, the user will get an email stating your decision.';
$help['groupprofiletitle'] = 'Group Profile';
$help['groupprofilebody'] = 'Group Profile is a new and advanced way of group management.<br /><br />
With group profiles, you are able to completely control what a user can access and what they can\'t.<br /><br />
When creating a new profile, you can create either an administrator profile, moderator profile, or a member profile.<br /><br />
Each type has their own set of rules to apply and each can be modified or deleted later.';
#help topic regarding stylecp.php
$help['stylemanagetitle'] = 'Style Manager';
$help['stylemanagebody'] = 'Here is where you can Add/Modify/Delete a style. You may also install styles by an installer that will do all the hard work for you.';
$help['addstyletitle'] = 'Creating/Modifying a Theme';
$help['addstylebody'] = 'This form will not create a theme but more of just add it to the theme picker in the User Control Panel.<br /><br />
All you do is list a name of the Theme, then provide a path to the template file. By v3.0.0 this will be completely replaced by the installer system.';
#help topic regarding usercp.php
$help['usermanagetitle'] = 'User Account Manager';
$help['usermanagebody'] = 'This is the control center of the user. Here is where you can modify the entire portion of a user.<br /><br />
You also have a few extra features to better manage a user from banning them to deleting their account.';
$help['warnlogtitle'] = 'User Warning Center';
$help['warnlogbody'] = 'Here is where all warnings made to a user are listed.<br /><br />
From here, you can either revoke an action, clear an action or just review actions performed.<br /><br />
WARNING: When clearing the warning log, you will lose any revoking ability and all actions will be final!';
$help['actusertitle'] = 'Activating a User\'s Account';
$help['actuserbody'] = 'All inactive users will be listed here. From this point you can either activate them or deny the inactive user.<br /><br />
When using this to unlock a locked account(when a user made more than five(5) failed attempts) deny will only tell the user you denied the request, it will not delete them.';
$help['bantitle'] = 'Banning Emails/IP Addresses';
$help['banbody'] = 'From here, you can ban email addresses and ban IP Addresses.<br /><br />
Email banning is the only thing that can use wildcards, wildcard IP banning will be made possible in v3.0.0';
$help['blacklisttitle'] = 'Blacklisting a Username';
$help['blacklistbody'] = 'Like the Banlist, Blacklisting is another way of protection.<br /><br />
However, Username Blacklisting is only good for preventing a user from making an account with a name you don\'t approve of.(like admin or siteowner)<br /><br />
You may also use a wildcard blacklist to further protect against loopholes you may not have thought of.';
$help['userprunetitle'] = 'User Pruning';
$help['userprunebody'] = 'Often, inactive users are a strain on a community and having them listed on a member roaster is just hiding any active users.<br /><br />
From here, you can remove all 0 posters, and delete users who have not shown up in a set amount of time.';
?>
