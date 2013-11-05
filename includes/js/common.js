/**
 Filename: common.js
 Last Modified: 11/03/2013

 Term of Use:
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.
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
 * @param {object} callback the function to call when an action is confirmed.
 * @returns {object}
*/
function confirmAction(msg, callback) {
    bootbox.confirm(msg, lang.No, lang.Yes, function(result) {
        if (result) {
            if($.isFunction(callback)) {
                callback();
            }
        }
    });
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

/**
 * Used to determine if a user's password is secure enough
 * @param pwd the password string we're validating
 * @returns {string}
*/
function passwordStrength( pwd ) {
    var strongRegex = new RegExp( "^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\W).*$", "g" );
    var mediumRegex = new RegExp( "^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g" );
    var enoughRegex = new RegExp( "(?=.{6,}).*", "g" );

    if (pwd.length == 0) {
        return 'Type Password';
    } else if(false == enoughRegex.test(pwd)) {
        return 'Short';
    } else if(strongRegex.test(pwd)) {
        return 'Strong';
    } else if(mediumRegex.test(pwd)) {
        return 'Medium';
    } else {
        return 'Weak';
    }
}

$(document).ready(function() {

    // =============================
    // SHOW ALL SMILES EVENT
    // =============================
    $('#showAllSmiles').click(function(e) {
        e.preventDefault(); //we don't want to leave this page.
        $('#moreSmiles').dialog('open');
    });

    // =============================
    // SMALL LOADING EVENT
    // =============================
    $("#smloading").ajaxStart(function(){
        $(this).show();
    });
    $("#smloading").ajaxStop(function(){
        $(this).hide();
    });

    // =============================
    // LIVE  SEARCH EVENTS
    // =============================
    $('#iSearch').click(function() {

        var searchResults = '';

        //call .ajax to call server.
        $.getJSON("ajax/search?action=liveSearch&q=" + $('#qSearch').val(), function(frmData) {
            $('#lsloading').show();

            $.each(frmData, function(i, val) {
                searchResults += '<a href="viewtopic.php?tid='+val.tid+'">'+val.Topic+'</a><br />';
            });

            $('#lsloading').hide();
            //display results.
            $('#searchResults').html(searchResults);
        });
    });//END .click

});

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