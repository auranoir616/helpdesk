<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Model
{

	private static $data = [
		'status' 	=> true,
		'message' 	=> null,
	];

	public function __construct()
	{
		parent::__construct();
		Self::$data['csrf_data'] 	= $this->security->get_csrf_hash();
	}

	function do_login()
	{
		$do_login 					= $this->ion_auth->login(post('authentication_id'), post('authentication_password'), true);
		if (!$do_login) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= $this->ion_auth->errors();
		}

		$this->form_validation->set_rules('authentication_id', 'USERID', 'required');
		$this->form_validation->set_rules('authentication_password', 'PASSWORD', 'required');
		if ($this->form_validation->run() == false) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= validation_errors(' ', '<br/>');
		}

		if (!$this->input->post()) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= 'Method not allowed';
		}

		if (Self::$data['status']) {

			// login success create session if admin
			$user_group 	= $this->ion_auth->get_users_groups()->row();
			if ($user_group->name == 'admin') {
				$array = array(
					'admin_userid' => userid()
				);
				$this->session->set_userdata($array);
			}


			Self::$data['message'] 	= 'Anda telah berhasil login. Klik OK untuk melanjutkan';
			Self::$data['heading'] 	= 'Sukses';
			Self::$data['type'] 	= 'success';
		} else {

			Self::$data['heading'] 	= 'Gagal';
			Self::$data['type'] 	= 'error';
		}

		return Self::$data;
	}
	// function login_back_admin()
	// {

	// 	Self::$data['heading'] 		= 'Login Admin Berhasil';
	// 	Self::$data['type']	 		= 'success';

	// 	if (!$this->session->userdata('admin_userid')) {
	// 		Self::$data['status'] 		= false;
	// 		Self::$data['message'] 		= 'Not allowed';
	// 	}

	// 	if (Self::$data['status']) {

	// 		//update status
	// 		$array = array(
	// 			'user_id' => $this->session->userdata('admin_userid')
	// 		);
	// 		$this->session->set_userdata($array);
	// 		Self::$data['message']	= 'Berhasil login kembali menjadi menjadi Admin';
	// 	} else {

	// 		Self::$data['heading'] 		= 'Failed';
	// 		Self::$data['type']	 		= 'error';
	// 	}

	// 	return Self::$data;
	// }

	public function do_register()
	{
		// Load necessary libraries
		$this->load->library('upload');
		$this->load->library('form_validation');
	
		// Configure file upload settings
		$config['upload_path']   = './assets/upload/';
		$config['allowed_types'] = 'jpg|png|jpeg';
		$config['max_size']      = '2048'; // Limit size to 2MB
		$config['remove_spaces'] = TRUE;
		$config['encrypt_name']  = TRUE;
	
		$this->upload->initialize($config);
	
		// File upload processing
		$user_picture = null;
		if (!$this->upload->do_upload('user_ktp')) {
			$user_picture = null;
			Self::$data['status'] = false;
			Self::$data['message'] = $this->upload->display_errors('', '<br/>'); // Display upload error
		} else {
			$user_picture = $this->upload->data();
		}
	
		// Form validation rules
		$this->form_validation->set_rules('user_username', 'username', 'trim|required|min_length[4]|is_unique[tb_users.username]', array(
			'is_unique'    => 'Username Sudah Terdaftar.'
		));
		$this->form_validation->set_rules('reg_nama', 'Nama Lengkap', 'required');
		$this->form_validation->set_rules('reg_hp', 'No. HP', 'trim|required');
		$this->form_validation->set_rules('reg_tipe', 'Tipe User', 'trim|required');
		$this->form_validation->set_rules('reg_password', 'Password', 'trim|required|min_length[6]');
	
		// Check validation
		if ($this->form_validation->run() == FALSE) {
			Self::$data['status'] = false;
			Self::$data['type'] = 'error';
			Self::$data['message'] = validation_errors(' ', '<br/>');
		} else {
			// Prepare additional data for registration
			$additional_data = array(
				'user_fullname' => $this->input->post('reg_nama'),
				'user_email'    => $this->input->post('user_email'),
				'username'      => $this->input->post('user_username'),
				'user_phone'    => $this->input->post('reg_hp'),
				'user_type'     => $this->input->post('reg_tipe'),
				'password_text' => $this->input->post('reg_password'),
				'user_picture'    => $user_picture ? $user_picture['file_name'] : null, // Ensure the filename is set
			);
	
			// Attempt to register the user
			$register = $this->ion_auth->register(
				$this->input->post('user_username'), 
				$this->input->post('reg_password'), 
				$this->input->post('user_email'), 
				$additional_data, 
				array(2) // Group ID
			);
	
			// Check registration result
			if ($register) {
				Self::$data['status'] = true;
				Self::$data['message'] = 'Pendaftaran Akun Baru Berhasil';
				Self::$data['heading'] = 'Berhasil';
				Self::$data['type'] = 'success';
			} else {
				Self::$data['message'] = 'Gagal mendaftar. Silakan coba lagi.';
				Self::$data['heading'] = 'Gagal';
				Self::$data['type'] = 'error';
			}
		}
	
		return Self::$data;
	}
	
}
