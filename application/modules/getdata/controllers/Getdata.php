<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Getdata extends CI_Controller
{


	public function __construct()
	{
		parent::__construct();
		$this->output->set_content_type('application/json');
	}

	public function public_get($filename = 'null', $function_name = 'null')
	{

		try {
			$this->load->model('public_get/' . $filename);
			$result 	= $this->$filename->$function_name();
		} catch (Exception $e) {
			$result 	= [
				'status' 	=> false,
				'message'	=> 'invalid model'
			];
		}

		echo json_encode($result);
	}


	public function user_get($filename = 'null', $function_name = 'null')
	{

		$status 		= true;

		// if( ! $this->ion_auth->logged_in() ){
		// 	$status 	= false;
		// 	$result 	= [
		// 		'status' 	=> false,
		// 		'message'	=> 'Anda harus login !'
		// 	];
		// }

		if (!$this->input->get()) {
			$status 	= false;
			$result 	= [
				'status' 	=> false,
				'message'	=> 'Not allowed method !'
			];
		}

		if ($status) {

			try {
				$this->load->model('user_get/' . $filename);
				$result 	= $this->$filename->$function_name();
			} catch (Exception $e) {
				$result 	= [
					'status' 	=> false,
					'message'	=> 'invalid model'
				];
			}
		}

		echo json_encode($result);
	}


	public function admin_get($filename = 'null', $function_name = 'null')
	{

		$status 		= true;

		if (!$this->ion_auth->is_admin()) {
			$status 	= false;
			$result 	= [
				'status' 	=> false,
				'message'	=> 'you not have permission to access this apis !'
			];
		}

		if (!$this->ion_auth->logged_in()) {
			$status 	= false;
			$result 	= [
				'status' 	=> false,
				'message'	=> 'you not have permission to access this apis !'
			];
		}

		if ($status) {
			try {
				$this->load->model('admin_get/' . $filename);
				$result 	= $this->$filename->$function_name();
			} catch (Exception $e) {
				$result 	= [
					'status' 	=> false,
					'message'	=> 'invalid model'
				];
			}
		}
		echo json_encode($result);
	}
}
