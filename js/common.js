/**
 * common.js
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 04/08/2013
*/

/**
 * Used to redirect a user to a location within the program's directory.
 * @param {string} addr the url to direct user to.
 * @returns {object}
*/
function gotoUrl(addr){
	window.location = addr;
}

/**
 * Used to display a confirmation dialog to the user.
 * @param {string} msg the message displayed to the user.
 * @param {string} addr the url to direct user to.
 * @returns {object}
*/
function confirmDlg(msg, addr) {
	bootbox.confirm(msg, lang.No, lang.Yes, function(result) {
		if (result) {
			gotoUrl(addr);
		}
	});
}

/**
 * runs a check to see if the server has the correct extensions to use the uploader.
 * @returns {object}
 */
function validateUploadAPI() {

	//call .ajax to call server.	
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
 * @param {string} ele the iframe element to capture.
 * @param {string} src the URL to execute.
 * @returns {object}
*/
function loadIframe(ele, src){
	$('#'+ ele).attr('src', src);
}

/**
 * Unselect a selected record.
 * @param {string} ele element id to look for.
 * @returns {object}
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
 * Builds a dialog box
 * @param {string} ele the element ID to assign to the dialog.
 * @param {string} title the title of the dialog.
 * @param {string} route the path to the partial view to load up.
 * @param {integer} width the width of the modal.
 * @returns {buildModal}
 */
function buildModal(ele, title, route, width) {

	if (width === undefined || isNaN(width)) {
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
 * @param {string} formID The ID of the form. 
 * @param {object} onSuccess callback when successful.
 * @param {object} onError callback when an error occurs.
 * @returns {object}
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
 * @param {string} url The ID of the form. 
 * @param {object} onSuccess callback when successful.
 * @param {object} onError callback when an error occurs.
 * @returns {object}
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