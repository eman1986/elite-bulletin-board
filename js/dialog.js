/**
 * dialog.js
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 10/24/2012
*/
$(document).ready(function(){
	
	//modcp move topic.
	$('#mod_move').dialog({
		autoOpen: false,
		modal: true,
		closeOnEscape: true,
		height: 150,
		width: 250,
		show: 'fade',
		resizable: false,
		draggable: false,
		open: function(event, ui) {
			//focus on form.
			$("#movetopic").focus();
		},
		close: function(event, ui) {
			//clear form.
			$("#movetopic").val("");
		}
	});

	//smiles dialog.
	$('#moreSmiles').dialog({
		autoOpen: false,
		modal: false,
		closeOnEscape: true,
		height:350,
		width:550,
		resizable: true,
		draggable: true,
		title: lang.dlgSmilesList,
		open: function(event, ui){
			$(this).load(boardUrl+'ajax/smiles');
		}// end open
	});
	
	//upload dialog.
	$('#DLG_upload').dialog({
		autoOpen: false,
		modal: true,
		closeOnEscape: true,
		height:400,
		width:450,
		resizable: true,
		draggable: true,
		title: lang.dlgUploadMgr,
		close: function(event, ui) {
   			$('#uploader').val('');
		}
	});

	//confirmation dialog.
	$("#confirmDlg").dialog({
      autoOpen: false,
      modal: true
    });
});//END funct.
