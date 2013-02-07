/**
 * common.js
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 02/06/2013
*/

/**
 * Used to redirect a user to a location within the program's directory.
 * @param addr[str] - the url to direct user to.
*/
function gotoUrl(addr){
	window.location = addr;
}

/**
 * Used to display a confirmation dialog to the user.
 * @param msg[str] - the message displayed to the user.
 * @param addr[str] - the url to direct user to.
*/
function confirmDlg(msg, addr){
	if (confirm(msg)){
		gotoUrl(addr);
	}
}

/**
 * runs a check to see if the server has the correct extensions to use the uploader.
*/
function validateUploadAPI() {

	//call .ajax to call server.	
	//
	//index.php/ajax/PrefCheck/
	$.ajax({
        method: "get", url:boardUrl+"index.php/ajax/PrefCheck/attachment",
		success: function(html){
			//see if user can upload files.
			if(html == "OK"){
				//user can upload file, load upload modal.
    			$('#DLG_upload').dialog('open');
			}else{
				$("#div_ErrMsg").show('blind', {}, 500);
				$("#ErrMsg").html(html);
			}
		},
		error: function(xhr, tStat, err){
			$("#div_ErrMsg").show('blind', {}, 500);
			$("#ErrMsg").html(xhr.status + " - " + xhr.statusText);
		}
	}); //END $.ajax(
}

/**
 * Reload iframe element.
 * @param ele[str] the iframe element to capture
 * @param src[str] the URL to execute.
 * @version 06/01/12
**/
function loadIframe(ele, src){
	$('#'+ ele).attr('src', src);
}

/**
 * Unselect a selected record.
 * @params string ele element id to look for.
 * @version 09/07/12
*/
function jTableClearSelection(ele) {
	$('#'+ele).find(".jtable").find("tr").find("input").attr("checked", false);
	$('#'+ele).find(".jtable").find("tr").removeClass("jtable-row-selected");
}

$(document).ready(function() {
	$('#showAllSmiles').click(function(e) {
		e.preventDefault(); //we don't want to leave this page.
        $('#moreSmiles').dialog('open');
    });

	//small loader
	$("#smloading").ajaxStart(function(){
		$(this).show();
 	});
	$("#smloading").ajaxStop(function(){
    	$(this).hide();
	});

    /* login/user box */
    $('#loginButton').click(function (e) {
        e.preventDefault();
        $('#loginBox').slideToggle();
		
		//clear form values/containers.
		$('#div_ErrMsg_QLogin').hide();
		$("#username").val('');
		$("#password").val('');
		$("#username").focus();
    });
    
    $('#searchBtn').click(function (e) {
        e.preventDefault();
        $('#searchBox').slideToggle();
		$('#qSearch').val('');
		$('#searchResults').text('');
		$('#qSearch').focus();
    });
    
    $('#PMBtn').click(function (e) {
        e.preventDefault();
        $('#PMBox').slideToggle();
    });

	//live search event.
	$('#iSearch').click(function() {
		
		var searchResults = '';
		
		//call .ajax to call server.
		$.getJSON(boardUrl+"index.php/search/LiveSearch/"+$('#qSearch').val(), function(frmData) {
			$('#lsloading').show();
			
			$.each(frmData, function(i, val) {
				searchResults += '<a href="'+boardUrl+'viewtopic/'+val.tid+'">'+val.Topic+'</a><br />';
			  });
			
			$('#lsloading').hide();
			  //display results.
			  $('#searchResults').html(searchResults);
		});
	});//END .click

}); //END (document).ready

/**
 * viewRoster
 * AJAX call to load Group Roster list.
**/
function viewRoster(){
	$('#viewGroupRoster').live('click', function() {
		//call .ajax to call server.
		$.ajax({
        	method: "get", url: "quicktools/viewgrouproster.php", data: "groupid=" + $(this).attr("title"),
			beforeSend: function(xhr){
				$("#smloading").show();
			},
			complete: function(xhr, tStat){
				$("#smloading").hide();
			},
			success: function(html){
				$("#groupRoster").show();
				$("#groupRoster").html(html).removeClass("ui-state-error");
			},
			error: function(xhr, tStat, err){
				var msg = lang.jsError + ": ";
	    		$("#groupRoster").html(msg + xhr.status + " " + xhr.statusText).addClass("ui-state-error");
			}
		}); //END $.ajax(
	}); //END .live
}

/** 
 * Builds a dialog box
 * @params string ele the element ID to assign to the dialog.
 * @params string title the title of the dialog.
 * @params string route the path to the partial view to load up.
 * @version 10/08/12
*/
function buildModal(ele, title, route, width) {

	if (width == undefined || isNaN(width)) {
		width = 520;
	}

	var btnDlg = {};
	btnDlg[lang.dlgCancel] = function() {
		$(this).dialog('close');
	};

	$('#'+ele).dialog({
		width: width,
		resizable: true,
		position: 'top',
		title: title,
		modal: true,
		open: function(event, ui) {
			$(this).load(route, function (response, status, xhr) {
				//see if we encountered an error
				if (status == "error") {
					$(this).html(lang.jsError + ": " + xhr.status + " " + xhr.statusText);
				}
			});
		},
		close: function(event, ui) {
			//clear dialog.
			$('#'+ele).empty();
		},
		buttons: btnDlg
    });

    //Add Icon to dialog button.
    $('.ui-dialog-buttonpane').
    find('button:contains("'+lang.dlgCancel+'")').button({
        icons: {
            primary: 'ui-icon-cancel'
        }
    });    
}

/**
 * Process form via AJAX.
 * @param formID The ID of the form. 
 * @param onSuccess callback when successful.
 * @param onError callback when an error occurs.
*/
function processForm(formID, onSuccess, onError) {
	//call .ajax to call server.
	$.post($('#'+formID).attr('action'), $('#'+formID).serialize(), function(frmData) {

		//render JSON.
		var json = $.parseJSON(frmData);

		//process message.
		FormResults(json.status, json.msg);
		formValidation(json.fields);

		//execute callback function if available.
	    if (json.status == "success" && $.isFunction(onSuccess)) {
	      onSuccess();
	    }
	}).error(function(xhr, tStat, err) {
		var msg = lang.jsError + ": ";
		FormResults("error", msg + xhr.status + " " + xhr.statusText);
		
		//execute callback function if available.
	    if($.isFunction(onError)) {
	      onError();
	    }
	}); //END $.post(
}

/**
 * Process GET request.
 * @param url The URL to execute.
 * @param onSuccess callback when successful.
 * @param onError callback when an error occurs.
*/
function processGetRequest(url, onSuccess, onError) {
	//call .ajax to call server.
	$.get(url, function(res) {
		//render JSON.
		var json = $.parseJSON(res);
		FormResults(json.status, json.msg);

		//execute callback function if available.
	    if (json.status == "success" && $.isFunction(onSuccess)) {
	      onSuccess();
	    }
	}).error(function(res) {
		var msg = lang.jsError + ": ";
		FormResults("error", msg + xhr.status + " " + xhr.statusText);
		
		//execute callback function if available.
	    if($.isFunction(onError)) {
	      onError();
	    }
	}); //END $.get(
}

//900,000 = 15 minutes
/*function updateOnline(){

	$("#online").load("quicktools/online.php", function(response, status, xhr) {
		if (status == "error") {
	    	var msg = lang.jsError + ": ";
	    	$("#online").html(msg + xhr.status + " " + xhr.statusText).addClass("ui-state-error");
	  	}
	}); //END $.load
}*/
//setInterval( "updateOnline()", 300000);
