/**
 * jquery.jUploader.js
 * @version 2.0.1 (01/22/2013)
 * @Requirements: jQuery 1.7 or newer & jQuery UI 1.8 or newer
 * @author Ed <https://github.com/eman1986/jquery.jUploader> 
 * @license http://opensource.org/licenses/mit-license.php MIT License (MIT)
 * CREDIT
 * Concept based off of: http://pixelcone.com/fileuploader/
**/
(function($) {

	/**
	 * jQuery/jQuery UI version detection.
	**/
	var juiVersion = $.ui ? $.ui.version || parseFloat("1.5.2") : null;
	var jqVersion = jQuery.fn.jquery;
	$.jUploader = {version: '2.0.1'};
	$.fn.jUploader = function(config){
		
		config = $.extend({}, {
			lngOk: "Ok", //Ok button on dialog.
			lngYes: "Yes", //Yes button on dialog.
			lngNo: "No", //No button on dialog.
			lngError: "Error" , //General Error Title on dialog.
			duplicateTitle: "Duplicate Entry", //duplicate error Title on dialog.
			duplicateMsg: "already in pending list.", //duplicate file message.
			LoadingCss: "ui-state-highlight", //loading style.
			LoadingGfx: "ajax-loader.gif", //loading animation graphic.
        	LoadingMsg: "Uploading File&hellip;", //loading text verbiage.
        	PendingCss: "uploadData", //pending style.
        	PendingMsg: "Pending&hellip;", //pending text verbiage.
        	FailureCss: "ui-state-error", //error color.
        	FailureMsg: "Upload Failed.", //error text verbiage.
			buttonUpload: "#fileUpload", //upload button.
			buttonClear: "#ClearList", //clear list button.
			buttonAddMore: "#addMoreFiles", // add more files button.
			uploadLimit: "5", //limit of downloads. (0=unlimited)
			uploadLimitTitle: "Limit Exceeded", //limit error title for dialog.
			uploadLimitMsg: "You have reached your upload limit.", //limit text verbiage.
			confirmDeleteTitle: "Delete File?", //title to delete confirmation dialog.
			deleteUrl: "delete.php", //url to delete file.
			confirmDeleteMsg: "Are you sure you want to delete this file?", //delete confirmation verbiage.
			fileMgrUrl: "filemgr.php" //path to view attachment action.
		}, config);
		
		//see if user is using recommended version of jQuery.
		if (jqVersion < "1.7" || juiVersion < parseFloat("1.8")) {
			alert('jUploader requires jquery 1.7 or newer & jQuery UI 1.8 or newer. Please upgrade your copy of jQuery & jQuery UI.');
		}

		//setup some global variables.
		var counter = 0;
		var inputName = "attachment";
		var pendingArr = new Array();
		
  		/**
  		 * Adds our files to the download list.
		**/
		$.jUploader.AddtoQueue = function(e){

			//make sure user hasn't reached their limit yet.
			if (counter < config.uploadLimit || config.uploadLimit == 0) {
            	var loading = '';
            
				//make our buttons clickable.
				$('#frmButtons input').removeAttr("disabled");
				$(config.buttonClear).attr("disabled", false);

				//see if theres a loading graphic defined.
				if ($.trim(config.LoadingGfx) != ''){
					loading = '<img src="'+ config.LoadingGfx +'" alt="'+ config.LoadingMsg +'" title="'+ config.LoadingMsg +'" />';
				} else {
					loading = config.LoadingMsg;
				}
				
				//see if user is re-adding a file already marked as pending.
				if ($.inArray($(e).val(), pendingArr) == -1) {
					//setup our file panel.
					var display = '<div class="'+ config.PendingCss +'" id="jupload_'+ counter +'_msg" title="' + $(e).val() +'">' +
						'<div class="close">&nbsp;</div>' +
						'<span class="fname">'+ $(e).val() +'</span>' +
						'<span class="loader" style="display:none">'+ loading +'</span>' +
						'<div class="status">'+ config.PendingMsg +'</div></div>';
					
					//increment the counter.
					counter++;
					
					if (counter == 1) {
						$(config.buttonUpload).attr("disabled", false);
					}
			
					//add to our div.
					$("#filePendings").append(display);
				
					//add to array.
					pendingArr.push($(e).val());

					//create a form for this file.
					jQUploader.appendForm();
					$(e).hide();
				} else {
					jQUploader.showMsg(config.duplicateTitle,config.duplicateMsg);
					
					//clear form.
					$(e).val('');
				}
			} else {
				jQUploader.showMsg(config.uploadLimitTitle,config.uploadLimitMsg);
				
				//clear form.
				$(e).val('');
			}
		}
		
		/**
		 * Performs our file upload AJAX style.
		**/
		$(document).on("click", config.buttonUpload, function(){
			var results;
			
			if (counter > 0) {
				$('#frmButtons input').attr("disabled", true);
				$("#frmUpload form").each(function(){

					//make sure we have a file present to upload.
					e = $(this);
					var id = "#" + $(e).attr("id");
					var inputID = id + "_input";
					var inputVal = $(inputID).val();
					
					if (inputVal != ""){
						$(id + "_msg .status").html(config.LoadingMsg);
						$(id + "_msg").addClass(config.LoadingCss);
						$(id + "_msg .loader").show();
						$(id + "_msg .close").hide();

						//submit our form ajax-style.
						$(id).submit();
						$(id + "_iframe").load(function() {
							$(id + "_msg .loader").hide();
							var json = $.parseJSON($(this).contents().text());
  							if (json.status == "success") {
								$(id + "_msg").remove();
																
								//reload file manager grid.
								jQUploader.loadFileManager();
  							} else {
           						$(id + "_msg").addClass(config.FailureCss);
								results = config.FailureMsg;
  							}
  							results += '<br />' + json.msg;
							$(id + "_msg .status").html(results);
							$(e).remove();
							
							//see if user can upload anything.
							if (counter < config.uploadLimit || config.uploadLimit == 0){
								$(config.buttonAddMore).show();
								$(config.buttonAddMore).removeAttr("disabled");
							}
							
							$(config.buttonClear).removeAttr("disabled");
						});
					}
				});
			}
		});
		
		/**
		 * Removes a file form the queue.
		**/
		$(document).on("click", '.close', function(){
			var fileNme = $(this).parent().attr("title");
			var id = "#" + $(this).parent().attr("id");
			counter = counter-1; //remove from counter.
			$(id + "_iframe").remove();
			$(id).remove();
			pendingArr.splice($.inArray(fileNme, pendingArr), 1);
			$(id + "_msg").fadeOut("slow",function(){
				$(this).remove();
			});
			
			if (counter == 0) {
				$(config.buttonUpload).attr("disabled", true);
			}
			
			return false;
		});

        /**
		 * Removes a file form the database.
		**/
		$(document).on("click", '.delete', function(e){
			e.preventDefault(); //we don't want to leave this page.
			jQUploader.deleteFile(this);
		});

		/**
		 * Clears our file queue.
		**/
		$(document).on("click", config.buttonClear, function(){
			$("#filePendings").fadeOut("slow",function(){
				$("#filePendings").html("");
				$("#frmUpload").html("");
				$("#filelist").hide();
				counter = 0;
				pendingArr.length = 0; //clear our array.
				jQUploader.appendForm();
				$('#frmButtons input').attr("disabled", true);
				$(config.buttonFileMgr).removeAttr("disabled");
				$(config.buttonAddMore).hide();
				$(this).show();
			});
		});
		
		/**
		 * allow user to continue uploading files.
		**/
		$(document).on("click", config.buttonAddMore, function(){
			$("#filePendings").fadeOut("slow",function(){
				$("#filePendings").html("");
				$("#frmUpload").html("");
				jQUploader.appendForm();
				$('#frmButtons input').attr("disabled", true);
				$(config.buttonFileMgr).removeAttr("disabled");
				$(config.buttonAddMore).hide();
				$(this).show();
			});
		});
		
  		/**
		 * Create our plugin methods.
		**/
		var jQUploader = {
			init: function(e){
				var form = $(e).parents('form');
				jQUploader.formAction = $(form).attr('action');

				$(form).before(' \
    	            <div id="frmUpload"></div> \
					<div id="filePendings"></div> \
					<div id="frmButtons"></div> \
					<div id="jupload_juiDlg"></div> \
				');
				

				//add our two buttons to our div.
				$(config.buttonUpload+','+config.buttonClear+','+config.buttonAddMore).appendTo('#frmButtons');
				$(config.buttonClear).attr("disabled", true);
				$(config.buttonUpload).attr("disabled", true);
				$(config.buttonAddMore).hide();

				//see if our file input field has a name.
				if ( $(e).attr('name') != '' ){
					inputName = $(e).attr('name');
				}

				$(form).hide();
				$("#frmUpload").html(""); //prevents double upload controls.
				this.appendForm();
			},
			appendForm: function(){
				//setup some values for our hidden IFRAME.
				var frmID = "jupload_" + counter;
				var iframeID = "jupload_" + counter + "_iframe";
				var inputID = "jupload_" + counter + "_input";
				var jQIframe = '<form method="post" id="'+ frmID +'" action="'+ jQUploader.formAction +'" enctype="multipart/form-data" target="'+ iframeID +'">' +
				'<input type="file" name="'+ inputName +'" id="'+ inputID +'" class="jupload" onchange="$.jUploader.AddtoQueue(this);" />' +
				'</form>' + 
				'<iframe id="'+ iframeID +'" name="'+ iframeID +'" src="about:blank" style="display:none"></iframe>';
				
				//add our hidden iframe to our div.
				$("#frmUpload").append(jQIframe);
			},
			showMsg: function(title, msg) {
				var btnOk = {};
				 btnOk[config.lngOk] = function() {
					 $(this).dialog("close");
				 };
			
				$("#jupload_juiDlg").text(msg);
				$("#jupload_juiDlg").dialog({
					modal: true,
					width: '500',
					height: '125',
					title:  title,
					closeOnEscape: true,
					buttons: btnOk
				});
			},
			loadFileManager: function() {
				//call .ajax to call server.
				$.ajax({
					method: "get", url: config.fileMgrUrl,
					success: function(html){
						$("#filelist").show();
						$("#filelist").html(html);
					},
					error: function(xhr, tStat, err){
						jQUploader.showMsg(config.lngError,xhr.status + " " + xhr.statusText);
					}
				}); //END $.ajax(
			},
			deleteFile: function(e) {
				var btnDelete = {};
				 btnDelete[config.lngYes] = function() {
				 	$(this).dialog('close');
					$(e).load(config.deleteUrl, { filename: $(e).attr('id') }, function(res, status, xhr) {
						if (status == "error") {
							jQUploader.showMsg(config.lngError,xhr.status + " " + xhr.statusText);
						} else {
							counter = counter-1; //remove from counter.
							pendingArr.splice($.inArray($(e).attr('id'), pendingArr), 1);
						
							//reload file manager grid.
							jQUploader.loadFileManager();
							
							//see if user can upload anything.
							if (counter < config.uploadLimit || config.uploadLimit == 0){
								$(config.buttonAddMore).show();
								$(config.buttonAddMore).removeAttr("disabled");
							}

							//see if all files are gone.
							if (counter == 0) {
								$("#filelist").hide();
								counter = 0; //reset counter
								pendingArr.length = 0; //clear our array.
								jQUploader.appendForm(); //show form.
								$(config.buttonClear).attr("disabled", true);
								$(config.buttonUpload).attr("disabled", true);
								$(config.buttonAddMore).hide();
							}
						}
					});
				 };
				 btnDelete[config.lngNo] = function() {
					 $(this).dialog("close");
				 };

				$("#jupload_juiDlg").text(config.confirmDeleteMsg);
				$("#jupload_juiDlg").dialog({
					modal: true,
					width: '300',
					height: '125',
					title:  config.confirmDeleteTitle,
					closeOnEscape: true,
					buttons: btnDelete
				});
			}
		}
	
		jQUploader.init(this);
		
		return this;
	}
})(jQuery);