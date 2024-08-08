<?php 


if ( ! function_exists('strong_password') ) {
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	function strong_password( $string = null )
	{

		$status 	= true;

		if (!preg_match('/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $string) ){
			$status = false;

		}

		return $status;

	}

}