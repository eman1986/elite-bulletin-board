/**
 * notify.js
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 04/11/2013
*/

/**
 * Display notification message to user.
 * @param {string} type type of message (Error, Success, Notice).
 * @param {string} message The Message to display to the user.
 * @returns {object}
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
 * Show fields that failed validation.
 * @param {array} fields an array of fields and any errors associated with them.
 * @returns {object}
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