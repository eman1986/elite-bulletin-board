/**
 * notify.js
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 09/14/2012
*/

/**
 * Displays results of AJAX calls.
 * @param type str "type of message (Error, Success, Notice)."
 * @param message str "The Message to display to the user."
 * @version 5/31/2011
*/
function FormResults(type, message) {
	$(function(){
		//see how we're rendering this notification.
		if (type == "success") {
			$.jnotify(message, 3000);
		} else {
			$.jnotify(message, type, 3000);
		}
	});
}

/**
 * Highlight fields
 * @params array fields an array of fields and any errors associated with them.
 * @version 09/14/12
*/
function formValidation(fields) {

	//loop through fields.
	$.each(fields, function(id, val) {
		if (val == "") {
			$('#'+id).removeClass('ui-state-error');
		} else {
			$('#'+id).addClass('ui-state-error');
		}
		$('#error_'+id).text(val).addClass('error');
	});
}