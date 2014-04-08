/*
Filename: vaildate.js
Last Modified: 2/9/12

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This File will contain the validate function for the MAIN BOARD forms.
*/

function validate(ftype){
	//based on the form type, the function will validate the requested form.
	switch (ftype){
		case "upload":
			//file upload validate.
			if (document.getElementById('attach').value == ""){
				setStatus(nosel, "attacherr", "err");
				var submit = "false";
			}else{
			 	//clear the error message.
				clearmsg("attacherr");
				clearmsg("loading");
				//setup the loading message.
				setStatus(loadingtxt, "loading", "none");
				var submit = "true";
			}
		break
		case "post_topic":
			//clear values.
			clearmsg("topicerr");
			clearmsg("bodyerr");
		    //new topic validate.
			var topiclen = document.getElementById('topic').value
			if(topiclen.length > 50){
				setStatus(longsubject, "topicerr", "err");
				var submit = "false";			  
			}
			if (document.getElementById('topic').value == ""){
				setStatus(nosubject, "topicerr", "err");
				var submit = "false";
			}
			if (document.getElementById('body').value == ""){
				setStatus(nobody, "bodyerr", "err");
				var submit = "false";			 
			}
		break
		case "post_poll":
			//clear values.
			clearmsg("topicerr");
			clearmsg("bodyerr");
			clearmsg("questionerr");
			clearmsg("pollerr");
			clearmsg("opt1err");
			clearmsg("opt2err");
			clearmsg("opt3err");
			clearmsg("opt4err");
			clearmsg("opt5err");
			clearmsg("opt6err");
			clearmsg("opt7err");
			clearmsg("opt8err");
			clearmsg("opt9err");
			clearmsg("opt10err");
		    //new poll validate.
		    var topiclen = document.getElementById('topic').value
			if(topiclen.length > 50){
				setStatus(longsubject, "topicerr", "err");
				var submit = "false";			  
			}
			var questionlen = document.getElementById('question').value
			if(questionlen.length > 50){
				setStatus(longquestion, "questionerr", "err");
				var submit = "false";			  
			}
			if (document.getElementById('topic').value == ""){
				setStatus(nosubject, "topicerr", "err");
				var submit = "false";
			}
			if (document.getElementById('body').value == ""){
				setStatus(nobody, "bodyerr", "err");
				var submit = "false";			 
			}
			if (document.getElementById('question').value == ""){
				setStatus(noquestion, "questionerr", "err");
				var submit = "false";			 			 
			}
			if (document.getElementById('opt1').value == "" || document.getElementById('opt2').value == ""){
				setStatus(nopoll, "pollerr", "err");
				var submit = "false";			 			 
			}
			var opt1len = document.getElementById('opt1').value
			if(opt1len.length > 50){
				setStatus(longpoll, "opt1err", "err");
				var submit = "false";			  
			}
			var opt2len = document.getElementById('opt2').value
			if(opt2len.length > 50){
				setStatus(longpoll, "opt2err", "err");
				var submit = "false";			  
			}
			var opt3len = document.getElementById('opt3').value
			if(opt3len.length > 50){
				setStatus(longpoll, "opt3err", "err");
				var submit = "false";			  
			}
			var opt4len = document.getElementById('opt4').value
			if(opt4len.length > 50){
				setStatus(longpoll, "opt4err", "err");
				var submit = "false";			  
			}
			var opt5len = document.getElementById('opt5').value
			if(opt5len.length > 50){
				setStatus(longpoll, "opt5err", "err");
				var submit = "false";			  
			}
			var opt6len = document.getElementById('opt6').value
			if(opt6len.length > 50){
				setStatus(longpoll, "opt6err", "err");
				var submit = "false";			  
			}
			var opt7len = document.getElementById('opt7').value
			if(opt7len.length > 50){
				setStatus(longpoll, "opt7err", "err");
				var submit = "false";			  
			}
			var opt8len = document.getElementById('opt8').value
			if(opt8len.length > 50){
				setStatus(longpoll, "opt8err", "err");
				var submit = "false";			  
			}
			var opt9len = document.getElementById('opt9').value
			if(opt9len.length > 50){
				setStatus(longpoll, "opt9err", "err");
				var submit = "false";			  
			}
			var opt10len = document.getElementById('opt10').value
			if(opt10len.length > 50){
				setStatus(longpoll, "opt10err", "err");
				var submit = "false";			  
			}
		break
		case "post_reply":
			//clear values.
			clearmsg("bodyerr");
			//new reply validate.
			if (document.getElementById('body').value == ""){
				setStatus(nobody, "bodyerr", "err");
				var submit = "false";			 
			}
		break
		case "report":
			//clear values.
			clearmsg("msgerr");
			//new reply validate.
			if (document.getElementById('msg').value == ""){
				setStatus(nomsg, "msgerr", "err");
				var submit = "false";			 
			}
		break
		case "login":
			//clear values.
			clearmsg("usererr");
			clearmsg("passerr");
			//login validate.
			if (document.getElementById('user').value == ""){
				setStatus(nouser, "usererr", "err");
				var submit = "false";			 
			}
			if (document.getElementById('pass').value == ""){
				setStatus(nopass, "passerr", "err");
				var submit = "false";			 
			}
		break
		case "login_forgotpass":
			//clear values.
			clearmsg("usererr");
			//forgot password validate.
			if (document.getElementById('user').value == ""){
				setStatus(nouser, "usererr", "err");
				var submit = "false";			 
			}
		break
		case "register":
			//clear values.
			clearmsg("usererr");
			clearmsg("emailerr");
			clearmsg("passerr");
			clearmsg("vpasserr");
			clearmsg("timeformerr");
			clearmsg("newpmerr");
			clearmsg("hideemailerr");
			clearmsg("toserr");
			clearmsg("captchaerr");
			clearmsg("coppaerr");
			//register validate.
			var userlen = document.getElementById('user').value
			if(userlen.length > 25){
				setStatus(longuser, "usererr", "err");
				var submit = "false";			  
			}
			if(userlen.length < 4){
				setStatus(shortuser, "usererr", "err");
				var submit = "false";			  
			}		
			var uchar=/^[a-zA-Z0-9-_]+$/;
			if (!uchar.test(document.getElementById('user').value)) { 
				setStatus(invaliduser, "usererr", "err");
				var submit = "false"; 
			}
			var emaillen = document.getElementById('email').value
			if(emaillen.length > 255){
				setStatus(longemail, "emailerr", "err");
				var submit = "false";			  
			}
			var emailvalid = /\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i; 
			if (!emailvalid.test(document.getElementById('email').value)) { 
				setStatus(invalidemail, "emailerr", "err");
				var submit = "false"; 
			}
			var passlen = document.getElementById('pass').value
			var vpasslen = document.getElementById('vpass').value
			if (document.getElementById('pass').value == "" && document.getElementById('vpass').value == ""){
				setStatus(nopass, "passerr", "err");
				setStatus(novpass, "vpasserr", "err");
				var submit = "false";			 
			}
			if (passlen != vpasslen){
				setStatus(nopwdmatch, "vpasserr", "err");
				var submit = "false";
			}
			if (document.getElementById('timeform').value == ""){
				setStatus(notime, "timeformerr", "err");
				var submit = "false";			 
			}
			if (document.getElementById("newpm1").checked == false && document.getElementById("newpm2").checked == false){
				setStatus(nopm, "newpmerr", "err");
				var submit = "false";			 
			}
			if (document.getElementById('yeshide').checked == false && document.getElementById('nohide').checked == false){
				setStatus(nohideemail, "hideemailerr", "err");
				var submit = "false";			 
			}
			if(tos_stat == 1){
				if (document.getElementById('tos').checked == false){
					setStatus(invalidtos, "toserr", "err");
					var submit = "false";			 
				}	
			}
			if(captcha_stat == 1){		
				if (document.getElementById('captcha').value == ""){
					setStatus(nocaptcha, "captchaerr", "err");
					var submit = "false";			 
				}	
			}
			if(coppa_stat == 1){
				if (document.getElementById('coppa').checked == false){
					setStatus(invalidcoppa, "coppaerr", "err");
					var submit = "false";			 
				}			
			}	
		break
		case "pm_new":
			//clear values.
			clearmsg("sendtoerr");
			clearmsg("subjecterr");
			clearmsg("bodyerr");
			//new pm message validate.
			if(document.getElementById('send').value == ""){
				setStatus(nosender, "sendtoerr", "err");
				var submit = "false";			 
			}
			if(document.getElementById('subject').value == ""){
				setStatus(nosubject, "subjecterr", "err");
				var submit = "false";
			}
			if(document.getElementById('body').value == ""){
				setStatus(nobody, "bodyerr", "err");
				var submit = "false";			 
			}
			var subjectlen = document.getElementById('subject').value
			if(subjectlen.length > 25){
				setStatus(longsubject, "subjecterr", "err");
				var submit = "false";			  
			}
			var subjectlen = document.getElementById('send').value
			if(subjectlen.length > 25){
				setStatus(longsender, "sendtoerr", "err");
				var submit = "false";			  
			}
		break
		case "pm_reply":
			//clear values.
			clearmsg("sendtoerr");
			clearmsg("bodyerr");
			//pm reply message validate.
			if(document.getElementById('send').value == ""){
				setStatus(nosender, "sendtoerr", "err");
				var submit = "false";			 
			}
			if(document.getElementById('body').value == ""){
				setStatus(nobody, "bodyerr", "err");
				var submit = "false";			 
			}
			var subjectlen = document.getElementById('send').value
			if(subjectlen.length > 25){
				setStatus(longsender, "sendtoerr", "err");
				var submit = "false";			  
			}		
		break
		case "pm_banlist":
			//clear values.
			clearmsg("banusererr");
			//pm banlist validate.
			if(document.getElementById('banuser').value == ""){
				setStatus(nouser, "banusererr", "err");
				var submit = "false";			 
			}
			var subjectlen = document.getElementById('banuser').value
			if(subjectlen.length > 25){
				setStatus(longuser, "banusererr", "err");
				var submit = "false";			  
			}			
		break
		case "search":
			//clear values.
			clearmsg("keyworderr");
			clearmsg("authorerr");
			clearmsg("typeerr");
			//search validate.
			var keywordlen = document.getElementById('keyword').value
			var authorlen = document.getElementById('author').value
           	if(keywordlen == ""){
			setStatus(nokeyword, "keyworderr", "err");
			var submit = "false";            	 
           	}
           	if(authorlen == "" && keywordlen == ""){
			setStatus(nokeyword, "authorerr", "err");
			var submit = "false";            	 
           	}
			if(keywordlen.length > 50){
				setStatus(longkeyword, "keyworderr", "err");
				var submit = "false";			  
			}
			if(authorlen.length > 25){
				setStatus(longuser, "authorerr", "err");
				var submit = "false";			  
			}
			if (document.getElementById("topic").checked == false && document.getElementById("post").checked == false){
				setStatus(notype, "typeerr", "err");
				var submit = "false";			 
			}			
		break
		case "edit_profile":
			//clear values.
			clearmsg("pwderr");
			clearmsg("msnerr");
			clearmsg("yimerr");
			clearmsg("aimerr");
			clearmsg("wwwerr");
			clearmsg("ctitleerr");
			clearmsg("icqerr");
			clearmsg("locerr");
			clearmsg("timeformerr");
			clearmsg("rssfeed1err");
			clearmsg("rssfeed2err");
			//edit profile validate.
			var pwdlen = document.getElementById('pwd').value
			if(pwdlen.length > 25){
				setStatus(longpwd, "pwderr", "err");
				var submit = "false";			  
			}
			if(pwdlen == ""){
				setStatus(nopwd, "pwderr", "err");
				var submit = "false";			  
			}
			var ctitlelen = document.getElementById('ctitle').value
			if(ctitlelen.length > 20){
				setStatus(longctitle, "ctitleerr", "err");
				var submit = "false";			  
			}
			var msnlen = document.getElementById('msn').value
			if(msnlen.length > 255){
				setStatus(longmsn, "msnerr", "err");
				var submit = "false";			  
			}
			var aimlen = document.getElementById('aim').value
			if(aimlen.length > 255){
				setStatus(longaim, "aimerr", "err");
				var submit = "false";			  
			}
			var icqlen = document.getElementById('icq').value
			if(icqlen.length > 15){
				setStatus(longicq, "icqerr", "err");
				var submit = "false";			  
			}
			var yimlen = document.getElementById('yim').value
			if(yimlen.length > 255){
				setStatus(longyim, "yimerr", "err");
				var submit = "false";			  
			}
			var wwwlen = document.getElementById('www').value
			var validurl = /http:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/;
			if(wwwlen.length > 200){
				setStatus(longwww, "wwwerr", "err");
				var submit = "false";			  
			}
			if(!validurl.test(wwwlen) && wwwlen != ""){
				setStatus(invalidurl, "wwwerr", "err");
				var submit = "false";
			}
			var timeformat = document.getElementById('timeform').value
			if(timeformat == ""){
				setStatus(notimeform, "timeformerr", "err");
				var submit = "false";			  
			}
			var loclen = document.getElementById('loc').value
			if(loclen.length > 70){
				setStatus(longloc, "locerr", "err");
				var submit = "false";			  
			}
			var rss1len = document.getElementById('rss1').value
			var rss2len = document.getElementById('rss2').value
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
		case "edit_sig":
			//clear values.
			clearmsg("sigerr");		
			//edit sig validate.
			var siglen = document.getElementById('sig').value
			if(siglen.length > 255){
				setStatus(longsig, "sigerr", "err");
				var submit = "false";			  
			}
		break
		case "edit_avatar":
			//clear values.
			clearmsg("avatarerr");		
			//edit avatar validate.
			var avatarlen = document.getElementById('avatar').value
			var validurl = /http:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/;
			if(avatarlen.length > 255){
				setStatus(longavatar, "avatarerr", "err");
				var submit = "false";			  
			}			
			if(!validurl.test(avatarlen) && avatarlen != ""){
				setStatus(invalidurl, "avatarerr", "err");
				var submit = "false";
			}
		break
		case "edit_email":
			//clear values.
			clearmsg("curemailerr");		
			clearmsg("newemailerr");
			clearmsg("vemailerr");
			//edit email validate.
			var cemaillen = document.getElementById('curemail').value
			var nemaillen = document.getElementById('newemail').value
			var vemaillen = document.getElementById('vemail').value
			var emailvalid = /\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i; 
			if(cemaillen.length > 255){
				setStatus(longemail, "curemailerr", "err");
				var submit = "false";			  
			}
			if (!emailvalid.test(cemaillen)) { 
				setStatus(invalidemail, "curemailerr", "err");
				var submit = "false"; 
			}			
			if(nemaillen.length > 255){
				setStatus(longemail, "newemailerr", "err");
				var submit = "false";			  
			}
			if(!emailvalid.test(nemaillen)){
				setStatus(invalidemail, "newemailerr", "err");
				var submit = "false";			  
			}			
			if(vemaillen.length > 255){
				setStatus(longemail, "vemailerr", "err");
				var submit = "false";			  
			}
			if(!emailvalid.test(vemaillen)){
				setStatus(invalidemail, "vemailerr", "err");
				var submit = "false";			  
			}
			if(nemaillen != vemaillen){
				setStatus(nomatch, "vemailerr", "err");
				var submit = "false";			  
			}			
		break
		case "edit_password":
			//clear values.
			clearmsg("curpwderr");		
			clearmsg("newpwderr");
			clearmsg("vpwderr");		
			//edit password validate.
			var cpwdlen = document.getElementById('cpwd').value
			var npwdlen = document.getElementById('npwd').value
			var vpwdlen = document.getElementById('vpwd').value			
			if(cpwdlen == ""){
				setStatus(nocpwd, "curpwderr", "err");
				var submit = "false";
			}
			if(npwdlen == ""){
				setStatus(nonpwd, "newpwderr", "err");
				var submit = "false";
			}
			if(vpwdlen == ""){
				setStatus(novpwd, "vpwderr", "err");
				var submit = "false";
			}
			if(npwdlen != vpwdlen){
				setStatus(nomatch, "vpwderr", "err");
				var submit = "false";			  
			}
		break
		case "mod_move":
			//clear values.
			clearmsg("moveerr");
			//Move Topic validate.
			if(document.getElementById('board').value == ""){
				setStatus(noboard, "moveerr", "err");
				var submit = "false";			  
			}
		break
		case "mod_warn":
			//clear values.
			clearmsg("warnerr");
			clearmsg("reasonerr");
			clearmsg("contacterr");
			// User Warn validate.
			if (document.getElementById("addwarn").checked == false && document.getElementById("removewarn").checked == false){
				setStatus(nowarn, "warnerr", "err");
				var submit = "false";			 
			}
			var reason = document.getElementById('wreason').value
			if(reason == ""){
				setStatus(noreason, "reasonerr", "err");
				var submit = "false";			
			}
			if(reason.length > 255){
				setStatus(longreason, "reasonerr", "err");
				var submit = "false";			
			}
			if(document.getElementById('contactopt').value != "None" && document.getElementById('contactmsg').value == ""){
				setStatus(nocontact, "contacterr", "err");
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
			obj.innerHTML = "<div class=\"error\"><img src=\"images/error.gif\" alt=\"ERROR!\" />" + theStatus + "</div>";
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
