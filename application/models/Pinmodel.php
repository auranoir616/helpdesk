<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pinmodel extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}
	function validatePIN($pin = null, $serial = null)
	{

		$this->db->where('pin_serials', $serial);
		$this->db->where('pin_number', $pin);

		$get 	= $this->db->get('tb_users_pin');
		if (($get->num_rows() > 0) || ($pin == null) && ($serial == null)) {

			//generate new pin & serial
			$rand_pin 		= rand(1, 999999);
			$new_pin 		= str_pad($rand_pin, 6, "0", STR_PAD_LEFT);
			$new_serial 	= strtoupper(random_string('alnum', 4) . random_string('alnum', 4) . random_string('alnum', 4) . random_string('alnum', 4));

			$result = $this->validatePIN($new_pin, $new_serial);
		} else {
			$code = hash('sha256', now() . rand());

			$result['code'] 	= $code;
			$result['pin'] 		= $pin;
			$result['serial'] 	= $serial;
		}

		return $result;
	}
}

/* End of file Pinmodel.php */
/* Location: ./application/models/Pinmodel.php */