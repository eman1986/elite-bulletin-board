/*
Filename: acp_vaildate.js
Date Modified: 9/2/08

This File will contain the validate function for the ADMIN CP forms.
*/

function acp_validate(ftype){
	switch(ftype){
		case "acp_cboard":
			//clear values.
			clearmsg("cateerr");
			//Category validation.
			if(document.getElementById('catname').value == ""){
				setStatus(nocat, "cateerr", "err");
				var submit = "false";			  
			}
			var categorylen = document.getElementById('catname').value
			if(categorylen.length > 50){
				setStatus(longcat, "cateerr", "err");
				var submit = "false";			  
			}
		break
		case "acp_board":
			//clear values.
			clearmsg("boarderr");
			clearmsg("descriptionerr");
			clearmsg("catselerr");
			clearmsg("postincrederr");
			clearmsg("bbcodeerr");
			clearmsg("smileerr");
			clearmsg("imgerr");
			//Board/Sub-Board validation.
			if(document.getElementById('boardname').value == ""){
				setStatus(noboard, "boarderr", "err");
				var submit = "false";			  
			}
			var boardlen = document.getElementById('boardname').value
			if(boardlen.length > 50){
				setStatus(longboard, "boarderr", "err");
				var submit = "false";			  
			}
			if(document.getElementById('description').value == ""){
				setStatus(nodescription, "descriptionerr", "err");
				var submit = "false";			  
			}
			if(document.getElementById('catsel').value == ""){
				setStatus(nocatsel, "catselerr", "err");
				var submit = "false";			  
			}
			if(document.getElementById("postincred").checked == false && document.getElementById("nopostincred").checked == false){
				setStatus(nopostincred, "postincrederr", "err");
				var submit = "false";			  
			}
			if(document.getElementById("bbcode").checked == false && document.getElementById("nobbcode").checked == false){
				setStatus(nobbcode, "bbcodeerr", "err");
				var submit = "false";			  
			}
			if(document.getElementById("smile").checked == false && document.getElementById("nosmile").checked == false){
				setStatus(nosmile, "smileerr", "err");
				var submit = "false";			  
			}
			if(document.getElementById("img").checked == false && document.getElementById("noimg").checked == false){
				setStatus(noimg, "imgerr", "err");
				var submit = "false";			  
			}
		break
		case "acp_editboard":
			//clear values.
			clearmsg("boarderr");
			clearmsg("descriptionerr");
			clearmsg("catselerr");
			//Edit Board/Sub-Board validation.
			if(document.getElementById('boardname').value == ""){
				setStatus(noboard, "boarderr", "err");
				var submit = "false";			  
			}
			var boardlen = document.getElementById('boardname').value
			if(boardlen.length > 50){
				setStatus(longboard, "boarderr", "err");
				var submit = "false";			  
			}
			if(document.getElementById('description').value == ""){
				setStatus(nodescription, "descriptionerr", "err");
				var submit = "false";			  
			}
			if(document.getElementById('catsel').value == ""){
				setStatus(nocatsel, "catselerr", "err");
				var submit = "false";			  
			}
		break
		case "acp_bprune":
			//clear values.
			clearmsg("ageerr");
			//Board Prune validation.
			if(document.getElementById('age').value == ""){
				setStatus(noage, "ageerr", "err");
				var submit = "false";			  
			}
		break
		case "acp_newsletter":
			//clear values.
			clearmsg("subjecterr");
			clearmsg("msgerr");
			//Newsletter validation.
			if(document.getElementById('subject').value == ""){
				setStatus(nosubject, "subjecterr", "err");
				var submit = "false";			  
			}
			if(document.getElementById('body').value == ""){
				setStatus(nobody, "msgerr", "err");
				var submit = "false";			  
			}
		break
		case "acp_smiles":
			//clear values.
			clearmsg("codeerr");
			clearmsg("imageerr");
			//Smiles validation.
			var codelen = document.getElementById('code').value
			var imagelen = document.getElementById('image').value
			if(codelen == ""){
				setStatus(nocode, "codeerr", "err");
				var submit = "false";			  
			}
			if(codelen.length > 30){
				setStatus(longcode, "codeerr", "err");
				var submit = "false";			  
			}
			if(imagelen == ""){
				setStatus(noimage, "imageerr", "err");
				var submit = "false";			  
			}
			if(imagelen.length > 80){
				setStatus(longimage, "imageerr", "err");
				var submit = "false";			  
			}
		break
		case "acp_censor":
			//clear values.
			clearmsg("censorerr");
			clearmsg("censoractionerr");
			//censor validation.
			var censorlen = document.getElementById('censor').value
			if(censorlen == ""){
				setStatus(nocensor, "censorerr", "err");
				var submit = "false";			  
			}
			if(censorlen.length > 50){
				setStatus(longcensor, "censorerr", "err");
				var submit = "false";			  
			}
			if(document.getElementById("censorban").checked == false && document.getElementById("censorspam").checked == false){
				setStatus(nocensoraction, "censoractionerr", "err");
				var submit = "false";			  
			}
		break
		case "acp_usettings":
			//clear values.
			clearmsg("termerr");
			clearmsg("pmquoteerr");
			clearmsg("pmreqerr");
			//User Setting validation.
			var pmquotalen = document.getElementById("pmquota").value

			if (document.getElementById("termstat").checked == true && document.getElementById("term").value == ""){
				setStatus(noterm, "termerr", "err");
				var submit = "false";			 
			}
			if(pmquotalen == ""){
				setStatus(nopmquota, "pmquoteerr", "err");
				var submit = "false";		 
			}
			if(isNaN(pmquotalen)){
				setStatus(invalidpmquota, "pmquoteerr", "err");
				var submit = "false";		 
			}
		break
		case "acp_bsettings":
			//clear values.
			clearmsg("boardnameerr");
			clearmsg("boardaddrerr");
			clearmsg("siteaddrerr");
			clearmsg("boardstaterr");
			clearmsg("boardemailerr");
			clearmsg("announcestaterr");
			clearmsg("timeformaterr");
			clearmsg("perpgerr");
			//Board Setting validation.
			var boardname = document.getElementById("boardname").value
			var boardaddr = document.getElementById("boardaddr").value
			var siteaddr = document.getElementById("siteaddr").value
			var perpg = document.getElementById("perpg").value
			var boardemail = document.getElementById("boardemail").value
			var timeform = document.getElementById("timeform").value
			var validurl = /http:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/;
			
			if(boardname == ""){
				setStatus(nobname, "boardnameerr", "err");
				var submit = "false";
			}
			if(boardname.length > 50){
				setStatus(longbname, "boardnameerr", "err");
				var submit = "false";
			}			
			if(!validurl.test(boardaddr)){
				setStatus(invalidbaddr, "boardaddrerr", "err");
				var submit = "false";
			}
			if(boardaddr.length > 255){
				setStatus(longbaddr, "boardaddrerr", "err");
				var submit = "false";
			}
			if(!validurl.test(siteaddr)){
				setStatus(invalidsiteaddr, "siteaddrerr", "err");
				var submit = "false";
			}
			if(siteaddr.length > 255){
				setStatus(longsiteaddr, "siteaddrerr", "err");
				var submit = "false";
			}
			if(perpg == ""){
				setStatus(noperpage, "perpgerr", "err");
				var submit = "false";
			}
			if(isNaN(perpg)){
				setStatus(invalidperpg, "perpgerr", "err");
				var submit = "false";
			}
			if (document.getElementById("boardstat").checked == true && document.getElementById("boardmsg").value == ""){
				setStatus(nobmsg, "boardstaterr", "err");
				var submit = "false";
			}
			var emailvalid = /\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i; 
			if (!emailvalid.test(boardemail)) { 
				setStatus(invalidemail, "boardemailerr", "err");
				var submit = "false"; 
			}
			if(boardemail.length > 255){
				setStatus(longbemail, "boardemailerr", "err");
				var submit = "false";
			}
			if (document.getElementById("announcestat").checked == true && document.getElementById("announce").value == ""){
				setStatus(noamsg, "announcestaterr", "err");
				var submit = "false";
			}
			if(timeform == ""){
				setStatus(notimeform, "timeformaterr", "err");
				var submit = "false";
			}
			if(timeform.length > 14){
				setStatus(longtimeform, "timeformaterr", "err");
				var submit = "false";
			}
		break
		case "acp_msettings":
			//clear values.
			clearmsg("smtphosterr");
			clearmsg("smtpporterr");
			clearmsg("smtpusererr");
			clearmsg("smtppwderr");
			//Mail Setting validation.
			if (document.getElementById("smtpstat").checked == true){
				var smtphost = document.getElementById("smtphost").value
				var smtpport = document.getElementById("smtpport").value
				var smtpuser = document.getElementById("smtpuser").value
				var smtppwd = document.getElementById("smtppwd").value
				if(smtphost == ""){
					setStatus(nosmtphost, "smtphosterr", "err");
					var submit = "false";
				}
				if(smtphost.length > 255){
					setStatus(longsmtphost, "smtphosterr", "err");
					var submit = "false";
				}
				if(smtpport == ""){
					setStatus(nosmtpport, "smtpporterr", "err");
					var submit = "false";
				}
				if(smtpuser == ""){
					setStatus(nosmtpuser, "smtpusererr", "err");
					var submit = "false";
				}
				if(smtpuser.length > 255){
					setStatus(longsmtpuser, "smtpusererr", "err");
					var submit = "false";
				}
				if(smtppwd == ""){
					setStatus(nosmtppwd, "smtppwderr", "err");
					var submit = "false";
				}
				if(smtppwd.length > 255){
					setStatus(longsmtppwd, "smtppwderr", "err");
					var submit = "false";
				}
			}
		break
		case "acp_csettings":
			//clear values.
			clearmsg("cookiedomainerr");
			clearmsg("cookiepatherr");
			//Cookies Setting validation.
			var cookiedomain = document.getElementById("cookiedomain").value
			var cookiepath = document.getElementById("cookiepath").value
			if(cookiedomain == ""){
				setStatus(nocookiedomain, "cookiedomainerr", "err");
				var submit = "false";			 
			}
			if(cookiedomain.length > 255){
				setStatus(longcookiedomain, "cookiedomainerr", "err");
				var submit = "false";			 
			}
			if(cookiepath == ""){
				setStatus(nocookiepath, "cookiepatherr", "err");
				var submit = "false";
			}
			if(cookiepath.length > 255){
				setStatus(longcookiepath, "cookiepatherr", "err");
				var submit = "false";			 
			}
		break
		case "acp_asettings":
			//clear values.
			clearmsg("maxuploaderr");
			//attachment Setting validation.
			var maxupload = document.getElementById("maxupload").value
			if(maxupload == ""){
				setStatus(nomaxupload, "maxuploaderr", "err");
				var submit = "false";			 
			}
			if(maxupload > 102400000){
				setStatus(longmaxupload, "maxuploaderr", "err");
				var submit = "false";			 
			}
			if(isNaN(maxupload)){
				setStatus(invalidmaxupload, "maxuploaderr", "err");
				var submit = "false";		 
			}
		break
		case "acp_awhitelist":
			//clear values.
			clearmsg("extensionerr");
			//attachment whitelist validation.
			var entension = document.getElementById("entension").value
			if(entension == ""){
				setStatus(noextension, "extensionerr", "err");
				var submit = "false";			 
			}
			if(entension.length > 100){
				setStatus(longextension, "extensionerr", "err");
				var submit = "false";			 
			}
		break
		case "acp_groups":
			//clear values.
			clearmsg("groupnameerr");
			clearmsg("groupdescriptionerr");
			clearmsg("groupstatuserr");
			clearmsg("grouplevelerr");
			//group validation.
			var groupname = document.getElementById("groupname").value
			var groupdescription = document.getElementById("groupdescription").value
			var grouplevel = document.getElementById("grouplevel").value
			
			if(groupname == ""){
				setStatus(nogroupname, "groupnameerr", "err");
				var submit = "false";			 
			}
			if(groupdescription == ""){
				setStatus(nogroupdescription, "groupdescriptionerr", "err");
				var submit = "false";			 
			}
			if(groupdescription.length > 255){
				setStatus(longgroupdescription, "groupdescriptionerr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("gclose").checked == false && document.getElementById("gopen").checked == false && document.getElementById("ghidden").checked == false){
				setStatus(nogroupstat, "groupstatuserr", "err");
				var submit = "false";
			}
			if(grouplevel == ""){
				setStatus(nogrouplevel, "grouplevelerr", "err");
				var submit = "false";
			}
		break
		case "acp_editgroup":
			//clear values.
			clearmsg("groupnameerr");
			clearmsg("groupdescriptionerr");
			//modify group validation.
			var groupname = document.getElementById("groupname").value
			var groupdescription = document.getElementById("groupdescription").value
			
			if(groupname == ""){
				setStatus(nogroupname, "groupnameerr", "err");
				var submit = "false";			 
			}
			if(groupdescription == ""){
				setStatus(nogroupdescription, "groupdescriptionerr", "err");
				var submit = "false";			 
			}
			if(groupdescription.length > 255){
				setStatus(longgroupdescription, "groupdescriptionerr", "err");
				var submit = "false";			 
			}
		break
		case "acp_groupmember":
			//clear values.
			clearmsg("addusererr");
			//add user to group validation.
			var adduser = document.getElementById("adduser").value

			if(adduser == ""){
				setStatus(nouser, "addusererr", "err");
				var submit = "false";			 
			}
			if(adduser.length > 25){
				setStatus(longuser, "addusererr", "err");
				var submit = "false";			 
			}
		break
		case "acp_gprofile1":
			//clear values.
			clearmsg("profilenameerr");
			clearmsg("manage_boardserr");
			clearmsg("prune_boardserr");
			clearmsg("manage_groupserr");
			clearmsg("mass_emailerr");
			clearmsg("word_censorerr");
			clearmsg("manage_smileserr");
			clearmsg("modify_settingserr");
			clearmsg("manage_styleserr");
			clearmsg("view_phpinfoerr");
			clearmsg("check_updateserr");
			clearmsg("see_acp_logerr");
			clearmsg("clear_acp_logerr");
			clearmsg("manage_banlisterr");
			clearmsg("manage_userserr");
			clearmsg("prune_userserr");
			clearmsg("manage_blacklisterr");
			clearmsg("manage_rankserr");
			clearmsg("manage_warnlogerr");
			clearmsg("activate_userserr");
			//group profile validation-Admin
			var profilename = document.getElementById("profilename").value
			if(profilename == ""){
				setStatus(noprofilename, "profilenameerr", "err")
			}
			if(profilename.length > 30){
				setStatus(longuser, "profilenameerr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("manage_boards1").checked == false && document.getElementById("manage_boards0").checked == false){
				setStatus(noaction, "manage_boardserr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("prune_boards1").checked == false && document.getElementById("prune_boards0").checked == false){
				setStatus(noaction, "prune_boardserr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("manage_groups1").checked == false && document.getElementById("manage_groups0").checked == false){
				setStatus(noaction, "manage_groupserr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("mass_email1").checked == false && document.getElementById("mass_email0").checked == false){
				setStatus(noaction, "mass_emailerr", "err");
				var submit = "false";
			}
			if(document.getElementById("word_censor1").checked == false && document.getElementById("word_censor0").checked == false){
				setStatus(noaction, "word_censorerr", "err");
				var submit = "false";
			}
			if(document.getElementById("manage_smiles1").checked == false && document.getElementById("manage_smiles0").checked == false){
				setStatus(noaction, "manage_smileserr", "err");
				var submit = "false";
			}
			if(document.getElementById("modify_settings1").checked == false && document.getElementById("modify_settings0").checked == false){
				setStatus(noaction, "modify_settingserr", "err");
				var submit = "false";
			}
			if(document.getElementById("manage_styles1").checked == false && document.getElementById("manage_styles0").checked == false){
				setStatus(noaction, "manage_styleserr", "err");
				var submit = "false";
			}
			if(document.getElementById("view_phpinfo1").checked == false && document.getElementById("view_phpinfo0").checked == false){
				setStatus(noaction, "view_phpinfoerr", "err");
				var submit = "false";
			}
			if(document.getElementById("check_updates1").checked == false && document.getElementById("check_updates0").checked == false){
				setStatus(noaction, "check_updateserr", "err");
				var submit = "false";
			}
			if(document.getElementById("see_acp_log1").checked == false && document.getElementById("see_acp_log0").checked == false){
				setStatus(noaction, "see_acp_logerr", "err");
				var submit = "false";
			}
			if(document.getElementById("clear_acp_log1").checked == false && document.getElementById("clear_acp_log0").checked == false){
				setStatus(noaction, "clear_acp_logerr", "err");
				var submit = "false";
			}
			if(document.getElementById("manage_banlist1").checked == false && document.getElementById("manage_banlist0").checked == false){
				setStatus(noaction, "manage_banlisterr", "err");
				var submit = "false";
			}
			if(document.getElementById("manage_users1").checked == false && document.getElementById("manage_users0").checked == false){
				setStatus(noaction, "manage_userserr", "err");
				var submit = "false";
			}
			if(document.getElementById("prune_users1").checked == false && document.getElementById("prune_users0").checked == false){
				setStatus(noaction, "prune_userserr", "err");
				var submit = "false";
			}
			if(document.getElementById("manage_blacklist1").checked == false && document.getElementById("manage_blacklist0").checked == false){
				setStatus(noaction, "manage_blacklisterr", "err");
				var submit = "false";
			}
			if(document.getElementById("manage_ranks1").checked == false && document.getElementById("manage_ranks0").checked == false){
				setStatus(noaction, "manage_rankserr", "err");
				var submit = "false";
			}
			if(document.getElementById("manage_warnlog1").checked == false && document.getElementById("manage_warnlog0").checked == false){
				setStatus(noaction, "manage_warnlogerr", "err");
				var submit = "false";
			}
			if(document.getElementById("activate_users1").checked == false && document.getElementById("activate_users0").checked == false){
				setStatus(noaction, "activate_userserr", "err");
				var submit = "false";
			}
		break
		case "acp_gprofile2":
			//clear values.
			clearmsg("profilenameerr");
			clearmsg("edit_topicserr");
			clearmsg("delete_topicserr");
			clearmsg("lock_topicserr");
			clearmsg("move_topicserr");
			clearmsg("view_ipserr");
			clearmsg("warn_userserr");
			//group profile validation-Admin
			var profilename = document.getElementById("profilename").value
			if(profilename == ""){
				setStatus(noprofilename, "profilenameerr", "err")
			}
			if(profilename.length > 30){
				setStatus(longuser, "profilenameerr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("edit_topics1").checked == false && document.getElementById("edit_topics0").checked == false){
				setStatus(noaction, "edit_topicserr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("delete_topics1").checked == false && document.getElementById("delete_topics0").checked == false){
				setStatus(noaction, "delete_topicserr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("lock_topics1").checked == false && document.getElementById("lock_topics0").checked == false){
				setStatus(noaction, "lock_topicserr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("move_topics1").checked == false && document.getElementById("move_topics0").checked == false){
				setStatus(noaction, "move_topicserr", "err");
				var submit = "false";
			}
			if(document.getElementById("view_ips1").checked == false && document.getElementById("view_ips0").checked == false){
				setStatus(noaction, "view_ipserr", "err");
				var submit = "false";
			}
			if(document.getElementById("warn_users1").checked == false && document.getElementById("warn_users0").checked == false){
				setStatus(noaction, "warn_userserr", "err");
				var submit = "false";
			}
		break
		case "acp_gprofile3":
			//clear values.
			clearmsg("profilenameerr");
			clearmsg("attach_fileserr");
			clearmsg("pm_accesserr");
			clearmsg("search_boarderr");
			clearmsg("download_fileserr");
			clearmsg("custom_titleserr");
			clearmsg("view_profileerr");
			clearmsg("use_avatarserr");
			clearmsg("use_signatureserr");
			clearmsg("join_groupserr");
			clearmsg("create_pollerr");
			clearmsg("vote_pollerr");
			clearmsg("new_topicerr");
			clearmsg("replyerr");
			clearmsg("important_topicerr");
			//group profile validation-Admin
			var profilename = document.getElementById("profilename").value
			if(profilename == ""){
				setStatus(noprofilename, "profilenameerr", "err")
			}
			if(profilename.length > 30){
				setStatus(longuser, "profilenameerr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("attach_files1").checked == false && document.getElementById("attach_files0").checked == false){
				setStatus(noaction, "attach_fileserr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("pm_access1").checked == false && document.getElementById("pm_access0").checked == false){
				setStatus(noaction, "pm_accesserr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("search_board1").checked == false && document.getElementById("search_board0").checked == false){
				setStatus(noaction, "search_boarderr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("download_files1").checked == false && document.getElementById("download_files0").checked == false){
				setStatus(noaction, "download_fileserr", "err");
				var submit = "false";
			}
			if(document.getElementById("custom_titles1").checked == false && document.getElementById("custom_titles0").checked == false){
				setStatus(noaction, "custom_titleserr", "err");
				var submit = "false";
			}
			if(document.getElementById("view_profile1").checked == false && document.getElementById("view_profile0").checked == false){
				setStatus(noaction, "view_profileerr", "err");
				var submit = "false";
			}
			if(document.getElementById("use_avatars1").checked == false && document.getElementById("use_avatars0").checked == false){
				setStatus(noaction, "use_avatarserr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("use_signatures1").checked == false && document.getElementById("use_signatures0").checked == false){
				setStatus(noaction, "use_signatureserr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("join_groups1").checked == false && document.getElementById("join_groups0").checked == false){
				setStatus(noaction, "join_groupserr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("create_poll1").checked == false && document.getElementById("create_poll0").checked == false){
				setStatus(noaction, "create_pollerr", "err");
				var submit = "false";
			}
			if(document.getElementById("vote_poll1").checked == false && document.getElementById("vote_poll0").checked == false){
				setStatus(noaction, "vote_pollerr", "err");
				var submit = "false";
			}
			if(document.getElementById("new_topic1").checked == false && document.getElementById("new_topic0").checked == false){
				setStatus(noaction, "new_topicerr", "err");
				var submit = "false";
			}
			if(document.getElementById("reply1").checked == false && document.getElementById("reply0").checked == false){
				setStatus(noaction, "replyerr", "err");
				var submit = "false";
			}
			if(document.getElementById("important_topic1").checked == false && document.getElementById("important_topic0").checked == false){
				setStatus(noaction, "important_topicerr", "err");
				var submit = "false";
			}
		break
		case "acp_editgprofile":
			//clear values.
			clearmsg("profilenameerr");
			//group profile validation-Admin
			var profilename = document.getElementById("profilename").value
			if(profilename == ""){
				setStatus(noprofilename, "profilenameerr", "err")
			}
			if(profilename.length > 30){
				setStatus(longuser, "profilenameerr", "err");
				var submit = "false";			 
			}
		break
		case "acp_style":
			//clear values.
			clearmsg("stylenameerr");
			clearmsg("temppatherr");
			//Style validation.
			var stylename = document.getElementById("stylename").value
			var temppath = document.getElementById("temppath").value
			
			if(stylename == ""){
				setStatus(nostylename, "stylenameerr", "err");
				var submit = "false";			 
			}
			if(temppath == ""){
				setStatus(notemppath, "temppatherr", "err");
				var submit = "false";			 
			}
		break
		case "acp_emailban":
			//clear values.
			clearmsg("emailbanerr");
			clearmsg("matchtypeerr");
			//email banlist validation.
			var emailban = document.getElementById("emailban").value

			if(emailban == ""){
				setStatus(noemail, "emailbanerr", "err");
				var submit = "false";			 
			}
			if(emailban.length > 255){
				setStatus(longemail, "emailbanerr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("wildcard").checked == false && document.getElementById("exact").checked == false){
				setStatus(nomtype, "matchtypeerr", "err");
				var submit = "false";			 
			}
		break
		case "acp_ipban":
			//clear values.
			clearmsg("ipbanerr");
			//IP Banlist validation.
			var ipban = document.getElementById("ipban").value

			if(ipban == ""){
				setStatus(noip, "ipbanerr", "err");
				var submit = "false";			 
			}
			if(ipban.length > 15){
				setStatus(longip, "ipbanerr", "err");
				var submit = "false";			 
			}
		break
		case "acp_blacklist":
			//clear values.
			clearmsg("userbanerr");
			clearmsg("matchtypeerr");
			//Username Blacklist validation.
			var userban = document.getElementById("userban").value

			if(userban == ""){
				setStatus(nouser, "userbanerr", "err");
				var submit = "false";			 
			}
			if(userban.length > 25){
				setStatus(longuser, "userbanerr", "err");
				var submit = "false";			 
			}
			if(document.getElementById("wildcard").checked == false && document.getElementById("exact").checked == false){
				setStatus(nomtype, "matchtypeerr", "err");
				var submit = "false";			 
			}
		break
		case "acp_seluser":
			//clear values.
			clearmsg("userselerr");
			//select user validate.
			var usersel = document.getElementById("usersel").value

			if(usersel == ""){
				setStatus(nouser, "userselerr", "err");
				var submit = "false";			 
			}
			if(usersel.length > 25){
				setStatus(longuser, "userselerr", "err");
				var submit = "false";			 
			}
		break
		case "acp_usermanage":
			//clear values.
			clearmsg("emailerr");
			clearmsg("msnerr");
			clearmsg("yimerr");
			clearmsg("aimerr");
			clearmsg("wwwerr");
			clearmsg("icqerr");
			clearmsg("locerr");
			clearmsg("timeformerr");
			clearmsg("sigerr");
			clearmsg("rssfeed1err");
			clearmsg("rssfeed2err");
			//user manager validation.
			var email = document.getElementById("email").value
			var timeform = document.getElementById("timeform").value
			var msn = document.getElementById("msn").value
			var aim = document.getElementById("aim").value
			var icq = document.getElementById("icq").value
			var yim = document.getElementById("yim").value
			var loc = document.getElementById("loc").value
			var url = document.getElementById("url").value
			var sig = document.getElementById("sig").value
			var rss1len = document.getElementById('rss1').value
			var rss2len = document.getElementById('rss2').value
			var emailvalid = /\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i;
			var validurl = /http:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/;

			if(!emailvalid.test(email)){
				setStatus(invalidemail, "emailerr", "err");
				var submit = "false";			 
			}
			if(email.length > 255){
				setStatus(longemail, "emailerr", "err");
				var submit = "false";			 
			}			
			if(msn.length > 255){
				setStatus(longmsn, "msnerr", "err");
				var submit = "false";			  
			}
			if(aim.length > 255){
				setStatus(longaim, "aimerr", "err");
				var submit = "false";			  
			}
			if(icq.length > 15){
				setStatus(longicq, "icqerr", "err");
				var submit = "false";			  
			}
			if(yim.length > 255){
				setStatus(longyim, "yimerr", "err");
				var submit = "false";			  
			}
			if(url.length > 200){
				setStatus(longwww, "wwwerr", "err");
				var submit = "false";			  
			}
			if(!validurl.test(url) && url != ""){
				setStatus(invalidurl, "wwwerr", "err");
				var submit = "false";
			}
			if(timeform == ""){
				setStatus(notimeform, "timeformerr", "err");
				var submit = "false";
			}
			if(timeform.length > 14){
				setStatus(longtimeform, "timeformerr", "err");
				var submit = "false";
			}
			if(loc.length > 70){
				setStatus(longloc, "locerr", "err");
				var submit = "false";			  
			}
			if(sig.length > 255){
				setStatus(longsig, "sigerr", "err");
				var submit = "false";			  
			}
			if(rss1len.length > 200){
				setStatus(longrss, "rssfeed1err", "err");
				var submit = "false";			  
			}
			if(rss2len.length > 200){
				setStatus(longrss, "rssfeed2err", "err");
				var submit = "false";			  
			}
			if(!validurl.test(rss1len) || rss1len == ""){
				setStatus(invalidurl, "rssfeed1err", "err");
				var submit = "false";
			}
			if(!validurl.test(rss2len) || rss2len == ""){
				setStatus(invalidurl, "rssfeed2err", "err");
				var submit = "false";
			}
		break
		case "acp_ranks":
			//clear values.
			clearmsg("ranknameerr");
			clearmsg("rankstarerr");
			clearmsg("rankruleerr");
			//ranks validation.
			var rankname = document.getElementById("rankname").value
			var rankstar = document.getElementById("rankstar").value
			var rankrule = document.getElementById("rankrule").value

			if(rankname == ""){
				setStatus(norankname, "ranknameerr", "err");
				var submit = "false";			 
			}
			if(rankname.length > 50){
				setStatus(longrankname, "ranknameerr", "err");
				var submit = "false";			 
			}
			if(rankstar == ""){
				setStatus(norankstar, "rankstarerr", "err");
				var submit = "false";			 
			}
			if(rankstar.length > 255){
				setStatus(longrankstar, "rankstarerr", "err");
				var submit = "false";			 
			}
			if(rankrule == ""){
				setStatus(norankrule, "rankruleerr", "err");
				var submit = "false";			 
			}
			if(rankrule.length > 4){
				setStatus(longrankrule, "rankruleerr", "err");
				var submit = "false";			 
			}
		break
		default:
			//someone didn't use this function correctly, lets tell them that.
			alert('incorrect use of function!');
			var submit = "false";
	} 
	//see if submit value is true, if so allow form to process.
	if(submit == "false"){
		return false; 
	}else{
		return true; 
	}
	//END OF FUNCTION. 
}
//the function that will display the error message in html-format.
function setStatus(theStatus, theObj, icon){
	obj = document.getElementById(theObj);
	if (obj){
		//see what type of icon to use, if any.
		if(icon == "err"){
			obj.innerHTML = "<div class=\"error\"><img src=\"/images/error.gif\" alt=\"ERROR!\" />" + theStatus + "</div>";
		}else if(icon == "none"){
			obj.innerHTML = "<div>" + theStatus + "</div>";
		}		  
	}
}
//the function that will clear the message(s).
function clearmsg(theObj){
	obj = document.getElementById(theObj);
	if (obj){
	   obj.innerHTML = "<div></div>";
	} 
}
