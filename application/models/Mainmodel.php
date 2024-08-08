<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mainmodel extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}


	function verification()
	{

		$return 			= true;
		if ($this->ion_auth->logged_in()) {
			$get_userdata 	= userdata();

			if ($get_userdata->user_verification == "0") {
				$return = false;
			}
		}

		return $return;
	}

	function pinlock()
	{

		$return 			= true;
		if ($this->ion_auth->logged_in()) {
			$get_userdata 	= userdata();

			if ($get_userdata->pin_lock == null) {
				$return = false;
			}
		}

		return $return;
	}
}

/* End of file Mainmodel.php */
/* Location: ./application/models/Mainmodel.php */