<?php

if( !function_exists( 'mysql_error' ) )
{
	function mysql_error( $l = false )
	{
		return mysqli_error( $l );
	}
}

?>
