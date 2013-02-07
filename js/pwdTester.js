/**
Filename: pwdTester.js
Last Modified: 11/13/2010

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

function passwordStrength( pwd ) {
	var strongRegex = new RegExp( "^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\W).*$", "g" );
	var mediumRegex = new RegExp( "^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g" );
	var enoughRegex = new RegExp( "(?=.{6,}).*", "g" );

	if (pwd.length == 0) {
		return 'Type Password';
	} else if( false == enoughRegex.test( pwd ) ) {
		return 'Short';
	} else if( strongRegex.test( pwd ) ) {
		return 'Strong';
	} else if( mediumRegex.test( pwd ) ) {
		return 'Medium';
	} else {
		return 'Weak';
	}
}
