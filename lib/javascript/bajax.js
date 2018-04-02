

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



// A simple ajax wrapper that works in all xmlhttprequest compatible browsers
// Blest Arena JAX =)

var globalBajaxProcesses = 0;
var globalBajaxProgressSpeed = 150;
var globalBajaxProgressElement = false;
var globalBajaxProgressElementContainer = false;
var globalBajaxProgressElementCounter = false;

var bajax = function ( parentObject )
{		
	var parentObject;
	var xhrObject;
	var Url;
	var Method;
	var User;
	var Pass;
	var responseXML;
	var postVars;
	
	this.postVars = new Array ();
	
	if ( parentObject )
		this.parentObject = parentObject;
	this.Url = "";
	this.Method = "";
	this.User = "";
	this.Pass = "";		
	
	
	if ( window.XMLHttpRequest ) this.xhrObject = new XMLHttpRequest();
	else if ( window.ActiveXObject )
	{	
		try { this.xhrObject = new ActiveXObject( "Msxml2.XMLHTTP" ); }
		catch ( e )
		{
			try { this.xhrObject = new ActiveXObject ( "Microsoft.XMLHTTP" ); }
			catch ( e ) { this.xhrObject = false; }
		}
	}
							
	var object = this;
	
	this.xhrObject.onreadystatechange = function ( )
	{
		if ( object.getReadyState ( ) == 4 )
		{
			try
			{
				object.responseXML = object.xhrObject.responseXML;
				if ( object.onload )
				{
					object.onload ( );
				}
				// here I am killing myself
				document._bajaxes[ this._bajaxIndex ] = 0;
				globalBajaxProcesses--;
			}
			catch ( e ){ alert ( e ); }
		}
	}
	
	if ( !document._bajaxes )
	{
		document._bajaxes = new Array ( );
	}
	for ( var a = 0; a < document._bajaxes.length; a++ )
	{
		if ( !document._bajaxes[ a ] || document._bajaxes[ a ] == 0 )
		{
			document._bajaxes[ a ] = this;
			this._bajaxIndex = a;
		}
	}
}

bajax.prototype.addVar = function ( varName, varValue )
{
	this.postVars[ varName ] = varValue;
}

bajax.prototype.addVarsFromForm = function ( element ) 
{
	if ( typeof ( element ) != "object" )
		element = document.getElementById ( element );
	
	if ( !element )
	{
		alert ( "Cannot get form object. Does it exist?" );
		return false;
	}
	
	var values = element.elements;
	for ( var i = 0; i < values.length; i++ )
	{
		if ( values[i].name )
		{
			this.addVar ( values[i].name, values[i].value );
		}
	}
}

bajax.prototype.setUsername = function ( user )
{
	if ( user )
		this.User = user;
}

bajax.prototype.setPassword = function ( pass )
{
	if ( pass )
		this.Pass = pass;
}

bajax.prototype.setMethod = function ( method_ )
{
	if ( method_ )
		this.Method = method_;
}

bajax.prototype.setUrl = function ( url )
{
	if ( url )
		this.Url = url;
}

bajax.prototype.abort = function ( )
{
	return this.xhrObject.abort ( );
}

bajax.prototype.getResponseHeader = function ( label_ )
{
	return this.xhrObject.getResponseHeader ( label_ );
}

// Mode is if the connection should be asyncronous
bajax.prototype.open = function ( mode_ )
{				
	var Mode = false;
	
	if ( mode_ )
	{
		if ( mode_ == "asyncronous" || mode_ == "async" || mode_ == true )
			Mode = true;
	}		
	
	if ( this.Method && this.Url )
	{
		if ( this.User || this.Pass )
			return this.xhrObject.open ( this.Method, this.Url, Mode, this.User, this.Pass );
		else
			return this.xhrObject.open (this.Method, this.Url, Mode );
	}		
	return false;
}

bajax.prototype.openUrl = function ( url_, method_, mode_ )
{		
	var Mode = false;
	
	// Prevent caching (please)
	var rand = 'bajaxrand=' + Math.random ( ) * Date.parse( new Date ( ) );
	// 
	if ( url_.indexOf ( "?" ) >= 0 && url_.indexOf ( "=" ) >= 0 )
		url_ += '&' + rand; 
	else url_ += '?' + rand;
	
	if ( mode_ )
	{
		if ( mode_ == "asyncronous" || mode_ == "async" || mode_ == true )
			Mode = true;
	}		
				
	if ( url_ )
	{
		// Store url
		this.Url = url_;
		
		if ( method_ )
		{				
			if ( this.User || this.Pass )
				return this.xhrObject.open ( method_, url_, Mode, this.User, this.Pass );
			else			
				return this.xhrObject.open ( method_, url_, Mode );
		}
		else
		{
			if ( this.User || this.Pass )
				return this.xhrObject.open ( this.Method, url_, Mode, this.User, this.Pass );
			else
				return this.xhrObject.open ( this.Method, url_, Mode );
		}
	}
	return false;
}		

bajax.prototype.send = function ( data_ )
{			
	var contentType = "application/x-www-form-urlencoded; charset=utf-8";
	var query;
	
	var arcount = 0; for ( var a in this.postVars ) arcount++;
	
	if ( arcount )
	{
		// we are posting a form
		var pairs = new Array ();
		for ( varName in this.postVars )
		{
			pairs.push ( varName + "=" + encodeURIComponent ( this.postVars [ varName ] ) );
		}
		this.xhrObject.setRequestHeader( "Content-Type", contentType );
		
		query = pairs.join ( "&" );
		
		if ( data_ )
		{
			data_ += "&" + query;
		}
		else
			data_ = query;
	}
	
	globalBajaxProcesses++;
	
	if ( data_ )
		return this.xhrObject.send ( data_ );
	return this.xhrObject.send ( this.getNull ( ) );
}

bajax.prototype.getNull = function ( )
{
	try
	{
		if ( null )
		{
			return null;
		}
	}
	catch ( e )
	{
		if ( NULL )
		{
			return NULL;
		}
	}
}

bajax.prototype.setRequestHeader = function ( label_, value_ )
{
	return bajax.prototype.setRequestHeader ( label_, value_ );
}

// Set function to run when we have a response (optional)
bajax.prototype.setResponseFunction = function ( function_ )
{	
	if ( typeof ( function_ ) != "undefined" )
		this.xhrObject.onreadystatechange = function_;				
}		
		
// 
bajax.prototype.getReadyState = function ( )
{
	if ( this.onwaitstate ) this.onwaitstate ( );
	return this.xhrObject.readyState;
}

//
bajax.prototype.getResponseText = function ( )
{
	var response = this.xhrObject.responseText;
	
	if ( response )
	{
		response = response.trim();
	}
	
	return response;
}

// Check if a keyword is in the text or return keyword
bajax.prototype.getResponseKeyword = function ( varKey )
{
	if ( typeof ( varKey ) == "undefined" ) varKey = false;
	
	var response = this.getResponseText ( );
	
	if ( 
		( response.substr ( 0, varKey.length ) == varKey ||
		( response.indexOf ( varKey ) > 0 ) ) && varKey
	)
	{
		return true;
	}
	else
	{
		var answer = response;
		answer = str_replace ( " ", "", answer );
		answer = str_replace ( "\n", "", answer );
		answer = str_replace ( "\r", "", answer );
		answer = str_replace ( "\t", "", answer );
		if ( answer.length > 0 )
			return answer;
	}
	return false;
}

//
bajax.prototype.getResponseXML = function ( )
{	
	return this.xhrObject.responseXML;
}

bajax.prototype.getStatus = function ( )
{
	return this.xhrObject.status;
}

bajax.prototype.getStatusText = function ( )
{
	return this.xhrObject.statusText;
}

// To be used to retrieve values from dom by tagname
function getXMLValue ( varName, obj )
{		
	var Value;
	if ( ( Value = obj.getElementsByTagName ( varName ).item ( 0 ) ) )
	{
		Value = Value.firstChild.data;
		return Value;		
	}
	return false;
}

// bajax the lazy way - replace innerHTML on an element via ajax
if ( navigator.userAgent.indexOf ( "MSIE" ) <= 0 )
{
	HTMLElement.prototype.getBajax = function ( url, data )
	{
		var req = new bajax();
		req.element = this;
		req.onload = function ()
		{
			this.element.innerHTML = this.getResponseText ();
		}
		ajax.openUrl( url, "get", true );
		ajax.send( data );
	}
}

function bajaxProgressMeter ( )
{
	if ( typeof ( globalBajaxProgressElement ) == "object" )
	{
		if ( globalBajaxProcesses > 0 )
			globalBajaxProgressElement.style.width = Math.round ( 100 / globalBajaxProcesses ) + "%";
		else
			globalBajaxProgressElement.style.width = "0px";
	}
	if ( typeof ( globalBajaxProgressElementCounter ) == "object" )
		globalBajaxProgressElementCounter.innerHTML = globalBajaxProcesses;
	if ( typeof ( globalBajaxProgressElementContainer ) == "object" )
	{
		if ( globalBajaxProcesses > 0 )
		{
			globalBajaxProgressElementContainer.style.visibility = 'visible';
			globalBajaxProgressElementContainer.className = 'visible';
		}
		else
		{
			globalBajaxProgressElementContainer.style.visibility = 'hidden';
			globalBajaxProgressElementContainer.className = '';
		}
	}
	if ( typeof ( globalBajaxProgressElement ) == "object" )
	{
		if ( globalBajaxProcesses > 0 )
			globalBajaxProgressElement.style.visibility = 'visible';
		else
			globalBajaxProgressElement.style.visibility = 'hidden';
	}
	setTimeout ( "bajaxProgressMeter ( )", globalBajaxProgressSpeed );
}

/**
 * url is target url
 * varfunc is an optional function to run when the target is loaded
 * divid (optional) is the resulting div to load result into 
**/
function jload ( url, varfunc, divid )
{
	if ( !divid ) divid = false;
	if ( !varfunc ) varfunc = false;
	var j = new bajax ( );
	j.object = document.getElementById ( divid );
	j.openUrl ( url, 'get', true );
	j.func = varfunc;
	j.onload = function ( )
	{
		if ( this.object ) this.object.innerHTML = this.getResponseText ( );
		if ( this.func ) this.func ( );
	}
	j.send ( );
}



