<?php


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



class cPayment 
{
	var $type;
	var $amount;
	var $ordernr;
	var $orderdescription;
	
	function __construct ( $type = false )
	{
		$this->SetType = $type;
	}
	
	/**
	 * Set payment type
	**/
	function SetType ( $type )
	{
		switch ( $type )
		{
			case 'bbs':
				$this->type = $type;
				break;
			default:
				$this->type = false;
				break;
		}
		$this->ResetPaymentVariables ( );
	}
	
	function SetOrderNumber ( $ordernr )
	{
		$this->ordernr = $ordernr;
	}
	
	function SetOrderDescription ( $desc )
	{
		$this->orderdescription = $desc;
	}
	
	/**
	 * Reset variables related to the specific payment type
	**/
	function ResetPaymentVariables ( )
	{
		$this->amount = 0.0;
		$this->ordernr = false;
		$this->orderdescription = '';
		$this->netaxeptTransaction = false;
	}
	
	/**
	 * Get url to redirect to
	**/
	function GetRedirectRoute ( )
	{
		global $Session;
		$c = new dbContent ( );
		if ( $c->load ( GetSettingValue ( 'webshop', 'productpage' . $Session->LanguageCode ) ) )
		{
			return $c->getRoute ( );
		}
		return false;
	}
	
	/**
	 * Paybutton for checkout
	**/
	function PayForm ( )
	{
		if ( $this->ordernr )
		{
			switch ( $this->type )
			{
				case 'bbs':
					return $this->_BBS_RedirectToPayForm ( );
			}
		}
		return '<p><strong>Error:</strong> No order number</p>';
	}
	
	function checkMode ( )
	{
		if ( $_REQUEST[ 'mode' ] == 'completion' )
		{
			return 'complete';
		}
		else if ( $_REQUEST[ 'BBSePay_transaction' ] )
		{
			return 'transaction';
		}
		return 'default';
	}
	
	function restoreFromID ( $id )
	{
		$ns = new dbObject ( 'classNetaxeptPayment' );
		if ( $ns->load ( $id ) )
		{
			$this->ordernr = $ns->WebshopOrderID;
			$this->netaxeptTransaction =& $ns;
			return true;
		}
		return false;
	}
	
	/**
	 * Run capture on transaction
	**/
	function Capture ( )
	{
		$order = new dbObject ( 'classProductOrder' );
		$order->load ( $this->ordernr );
		if ( $this->netaxeptTransaction->UniqueTransactionReference && $order->ID )
		{
			$amount = $this->netaxeptTransaction->NetaxeptAmount;
			$data = Array (
				'merchantid'=>BBS_MERCHANTID,
				'token'=>BBS_TOKEN,
				'transactionid'=>$this->netaxeptTransaction->UniqueTransactionReference,
				'transactionReconRef'=>$this->netaxeptTransaction->NetaxeptID,
				'transactionamount'=>$amount
			);
			$options = '?'; $i = 0;
			foreach ( $data as $k=>$v ) 
			{
				if ( ++$i > 1 ) $options .= '&';
				$options .= "$k=" . urlencode ( $v );
			}
			
			if ( defined ( 'BBS_NO_CURL' ) )
			{
				$result = file_get_contents ( BBS_URL . 'Capture.aspx' . $options );
			}
			else
			{
				$o = curl_init ( BBS_URL . 'Capture.aspx' . $options );
				curl_setopt ( $o, CURLOPT_GET, true );
				curl_setopt ( $o, CURLOPT_RETURNTRANSFER, true );
				$result = curl_exec ( $o );
				curl_close ( $o );
			}
			
			preg_match ( '/.*?\<ResponseCode\>([^<]*)\<\/ResponseCode\>.*?/', $result, $input );
			if ( strtolower ( $input[1] ) == 'ok' )
			{
				$this->netaxeptTransaction->NetaxeptStatus = 'PAID';
				$this->netaxeptTransaction->save ( );
				return true;
			}
			else if ( $input[1] == '99' )
				$this->_errorCode = $input[1];
			else $this->_errorCode = $input[1];
		}
		return false;
	}
	
	/**
	 * Run annulate on transaction
	**/
	function Annulate ( )
	{
		$order = new dbObject ( 'classProductOrder' );
		$order->load ( $this->ordernr );
		if ( $this->netaxeptTransaction->UniqueTransactionReference && $order->ID )
		{
			$data = Array (
				'merchantid'=>BBS_MERCHANTID,
				'token'=>BBS_TOKEN,
				'transactionid'=>$this->netaxeptTransaction->UniqueTransactionReference,
				'transactionReconRef'=>$this->netaxeptTransaction->NetaxeptID
			);
			$options = '?'; $i = 0;
			foreach ( $data as $k=>$v ) 
			{
				if ( ++$i > 1 ) $options .= '&';
				$options .= "$k=" . urlencode ( $v );
			}
			
			if ( defined ( 'BBS_NO_CURL' ) )
			{
				$result = file_get_contents ( BBS_URL . 'Annul.aspx' . $options );
			}
			else
			{
				$o = curl_init ( BBS_URL . 'Annul.aspx' . $options );
				curl_setopt ( $o, CURLOPT_GET, true );
				curl_setopt ( $o, CURLOPT_RETURNTRANSFER, true );
				$result = curl_exec ( $o );
				curl_close ( $o );
			}
			
			preg_match ( '/.*?\<ResponseCode\>([^<]*)\<\/ResponseCode\>.*?/', $result, $input );
			if ( strtolower ( $input[1] ) == 'ok' )
			{
				$this->netaxeptTransaction->NetaxeptStatus = 'ANNULATED';
				$this->netaxeptTransaction->save ( );
				return true;
			}
			else if ( $input[1] == '99' )
				$this->_errorCode = $input[1];
			else $this->_errorCode = $input[1];
		}
		return false;
	}
	
	/**
	 * Check transaction status
	**/
	function checkTransaction ( )
	{
		if ( $this->type == 'bbs' )
		{
			if ( $this->loadTransaction ( ) )
			{
				$data = Array (
					'merchantid'=>BBS_MERCHANTID,
					'token'=>BBS_TOKEN,
					'transactionid'=>$this->netaxeptTransaction->UniqueTransactionReference,
					'transactionstring'=>$_REQUEST[ 'BBSePay_transaction' ]
				);
				$options = '?'; $i = 0;
				foreach ( $data as $k=>$v ) 
				{
					if ( ++$i > 1 ) $options .= '&';
					$options .= "$k=" . urlencode ( $v );
				}
	
				if ( defined ( 'BBS_NO_CURL' ) )
				{
					$result = file_get_contents ( BBS_URL . 'ProcessSetup.aspx' . $options );
				}
				else
				{
					$o = curl_init ( BBS_URL . 'ProcessSetup.aspx' . $options );
					curl_setopt ( $o, CURLOPT_GET, true );
					curl_setopt ( $o, CURLOPT_RETURNTRANSFER, true );
					$result = curl_exec ( $o );
					curl_close ( $o );
				}
	
				preg_match ( '/.*?\<ResponseCode\>([^<]*)\<\/ResponseCode\>.*?/', $result, $input );
				if ( strtolower ( $input[1] ) == 'ok' )
				{
					$this->netaxeptTransaction->NetaxeptStatus = 'OK';
					$this->netaxeptTransaction->save ( );
					return true;
				}
				$this->_errorCode = $input[1];
				return false;
			}
		}
		return false;
	}
	
	/**
	 * Authorize payment
	**/
	function Authorize ( )
	{
		if ( $this->netaxeptTransaction->NetaxeptStatus == 'OK' )
		{
			$data = Array (
				'merchantid'=>BBS_MERCHANTID,
				'token'=>BBS_TOKEN,
				'transactionid'=>$this->netaxeptTransaction->UniqueTransactionReference,
				'transactionstring'=>$_REQUEST[ 'BBSePay_transaction' ]
			);
			$options = '?'; $i = 0;
			foreach ( $data as $k=>$v ) 
			{
				if ( ++$i > 1 ) $options .= '&';
				$options .= "$k=" . urlencode ( $v );
			}
			
			if ( defined ( 'BBS_NO_CURL' ) )
			{
				$result = file_get_contents ( BBS_URL . 'Auth.aspx' . $options );
			}
			else
			{
				$o = curl_init ( BBS_URL . 'Auth.aspx' . $options );
				curl_setopt ( $o, CURLOPT_GET, true );
				curl_setopt ( $o, CURLOPT_RETURNTRANSFER, true );
				$result = curl_exec ( $o );
				curl_close ( $o );
			}
			preg_match ( '/.*?\<ResponseCode\>([^<]*)\<\/ResponseCode\>.*?/', $result, $input );
			preg_match ( '/.*?\<AuthorizationId\>([^<]*)\<\/AuthorizationId\>.*?/', $result, $authid );
			if ( strtolower ( $input[1] ) == 'ok' )
			{
				$this->netaxeptTransaction->NetaxeptStatus = 'AUTHORIZED';
				$this->netaxeptTransaction->NetaxeptID = $authid[1];
				$this->netaxeptTransaction->save ( );
				return true;
			}
			else if ( $input[1] == '99' )
				$this->_errorCode = $input[1];
			else die ( $input[1] . ':' . $result );
		}
		return false;
	}
	
	/**
	 * Get the last error code..
	**/
	function getErrorCode ( )
	{
		return $this->_errorCode;
	}
	
	/**
	 * Load an old transaction
	**/
	function loadTransaction ( )
	{
		global $Session;
		$o = new dbObject ( 'classNetaxeptPayment' );
		$o->UniqueTransactionReference = $Session->UniqueTransactionReference;
		if ( $o = $o->findSingle ( ) )
		{
			$this->ordernr = $o->WebshopOrderID;
			$this->netaxeptTransaction =& $o;
			return true;
		}
		return false;
	}
	
	/** PRIVATE FUNCTIONS *****************************************************/
	
	function _GenerateTransactionID ( )
	{
		global $Session;
		// Make sure the transaction reference is unique
		do
		{
			$o = new dbObject ( 'classNetaxeptPayment' );
			$o->UniqueTransactionReference = str_replace ( Array ( ' ', '.' ), '', str_pad ( microtime ( ) . str_pad ( rand ( 0, 999999 ), 7, '0', STR_PAD_LEFT ), 32, '0', STR_PAD_LEFT ) );
		} 
		while ( $i = $o->findSingle ( ) );
		
		$o->WebshopOrderID = $this->ordernr;
		$o->DateUpdated = date ( 'Y-m-d H:i:s' );
		if ( !$o->DateCreated ) $o->DateCreated = $o->DateUpdated;
		$o->save ( );
		$this->netaxeptTransaction = &$o;
		$Session->Set ( 'UniqueTransactionReference', $this->netaxeptTransaction->UniqueTransactionReference );
		return $this->netaxeptTransaction->UniqueTransactionReference;
	}
	
	function _BBS_RedirectToPayForm ( )
	{
		if ( defined ( 'BBS_URL' ) )
		{
			$data = Array (
				'merchantid'=>BBS_MERCHANTID,
				'token'=>BBS_TOKEN,
				'transactionid'=>$this->_GenerateTransactionID ( ),
				'ordernumber'=>$this->ordernr,
				'orderdescription'=>$this->orderdescription,
				'amount'=>round ( $this->amount * 100 ),
				'currencycode'=>BBS_CURRENCY,
				'redirecturl'=>BASE_URL . $this->GetRedirectRoute ( ) . 'checkout/?payment=bbs',
			);
			$options = '?'; $i = 0;
			foreach ( $data as $k=>$v ) 
			{
				if ( ++$i > 1 ) $options .= '&';
				$options .= "$k=" . urlencode ( $v );
			}
			
			if ( defined ( 'BBS_NO_CURL' ) )
			{
				$result = file_get_contents ( BBS_URL . 'Setup.aspx' . $options );
			}
			else
			{
				$o = curl_init ( BBS_URL . 'Setup.aspx' . $options );
				curl_setopt ( $o, CURLOPT_GET, true );
				curl_setopt ( $o, CURLOPT_RETURNTRANSFER, true );
				$result = curl_exec ( $o );
				curl_close ( $o );
			}
		
			preg_match ( '/.*?\<SetupString\>([^<]*)\<\/SetupString\>.*?/', $result, $input );
			$input = html_entity_decode ( $input[1] );
			preg_match ( '/.*value\=\"([^"]*)\".*/i', $input, $input );
			
			$this->netaxeptTransaction->VerificationHashLong = $input[1];
			$this->netaxeptTransaction->NetaxeptAmount = $data[ 'amount' ];
			$this->netaxeptTransaction->save ();
			
			ob_clean ( );
			$url = str_replace ( 'REST/', '', BBS_URL );
			header ( 'Location: ' . $url . '/terminal/?BBSePay_transaction=' . $input[ 1 ] );
			die ( );
		}
		return "<p><strong>Error:</strong> Kunne ikke koble til BBS. Er ikke riktig satt opp i config.</p>";
	}
	
	/**
	 * Sets the payment amount
	**/
	function SetAmount ( $amount = 0 )
	{
		if ( $amount <= 0 )
			die ( 'Error: Severe payment class error. The money amount is ' . ( $amount < 0 ? ' negative' : '' ) . '!' );
		$this->amount = $amount;
	}
}

?>
