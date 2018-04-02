

/*******************************************************************************
The contents of this file are subject to the Mozilla Public License
Version 1.1 (the "License"); you may not use this file except in
compliance with the License. You may obtain a copy of the License at
http://www.mozilla.org/MPL/

Software distributed under the License is distributed on an "AS IS"
basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
License for the specific language governing rights and limitations
under the License.

The Original Code is (C) 2004-2010 Blest AS.

The Initial Developer of the Original Code is Blest AS.
Portions created by Blest AS are Copyright (C) 2004-2010
Blest AS. All Rights Reserved.

Contributor(s): Hogne Titlestad, Thomas Wollburg, Inge Jørgensen, Ola Jensen, 
Rune Nilssen
*******************************************************************************/



function check_comment_form ( )
{
	var theform = document.getElementById ( 'comment_form' );

	if ( theform.Subject.value.length <= 0 )
	{
		alert ( "Du må skrive inn et enme." );
		theform.Subject.focus ( );
		return false;
	}
	if ( theform.Message.value.length <= 0 )
	{
		alert ( "Du må skrive inn din beskjed." );
		theform.Message.focus ( );
		return false;
	}
	theform.Controlnumber.value = 'verified_and_comment';
	theform.submit ( );
}
