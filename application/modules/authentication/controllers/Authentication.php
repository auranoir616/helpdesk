<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Authentication extends MX_Controller
{
	function __construct()
	{
		parent::__construct();
		// $this->load->library(array('form_validation', 'Recaptcha'));
	}

	function login()
	{

		$data = array();
		if ($this->ion_auth->logged_in()) {
			redirect('dashboard', 'refresh');
		}

		// $data['captcha'] 		= $this->recaptcha->getWidget();
		// $data['script_captcha'] = $this->recaptcha->getScriptTag();
		$this->template->content->view('login', $data);
		$this->template->publish();
	}

	function register($uplineCode = null, $position = 'right')
	{

		$data 					= array();
		$message 				= null;
		$data['showupline']		= false;
		$data['upline'] 		= 'Position Automatic';
		$data['position'] 		= $position;
		$data['message']		= null;


		$data['username_referral'] 		= $this->session->userdata('referralID');
		$status 						= true;

		if ($uplineCode != null) {
			$validate_upline 	= userdata(array('user_code' => $uplineCode));
			if (!$validate_upline) {
				$status 				= false;
				$data['message']  		= alerts('Data upline Anda tidak valid', 'danger');
			}

			if ($this->session->userdata('referralMessage')) {
				$status 		        = false;
				$data['message'] 		= $this->session->userdata('referralMessage');
			}

			if ($status) {
				$data['upline'] 		= $validate_upline->username;
				$data['uplinecode']		= $validate_upline->user_referral_code;
				$data['showupline']		= true;
			}
		}

		$this->template->content->view('register', $data);
		$this->template->publish();
	}

	function formregister($param = null)
	{
		$data = array();

		$this->db->where('pin_code', $param);
		$this->db->where('pin_status', 'available');
		$this->db->join('tb_users', 'id = pin_userid');
		$cekpin = $this->db->get('tb_users_pin');
		if ($cekpin->num_rows() == 0) {
			show_404();
			exit;
		}


		$data 					= array();
		$message 				= null;
		$data['message']		= null;

		if ($this->session->userdata('referralID')) {
			$data['username_referral'] 		= $this->session->userdata('referralID');
		}

		$data['userdata']	= $cekpin->row();
		$this->template->content->view('form-register', $data);
		$this->template->publish('template-register');
	}



	function forgotPassword($forgotten_code = null)
	{

		$content_view 		= 'reset-password';
		$data 				= [];

		if ($forgotten_code != null) {

			$this->db->where('forgotten_password_code', $forgotten_code);
			$get_forgotten_code 	= $this->db->get('tb_users');
			if ($get_forgotten_code->num_rows() != 0) {

				$data['forgotten_data'] 	= $get_forgotten_code->row();
				$content_view 				= 'reset-new-password';
			}
		}

		// $data['captcha'] 		= $this->recaptcha->getWidget();
		// $data['script_captcha'] = $this->recaptcha->getScriptTag();
		$this->template->content->view($content_view, $data);
		$this->template->publish('template');
	}
	public function doResetPassword()
	{

		/**
		
			TODO:
			- ngecek username yang dimasukkan ada apa tidak
			- kalau username nemu data, kita update dengan random code di masukkan pada field "tb_users.forgotten_password_code"
			- kirim email kode tadi dengan format link:

				site_url( 'forgot-password/' . $kode_random_tadi );
		
		 */

		$data['status']         = true;
		$data['message']        = null;
		$data['csrf_data']      = $this->security->get_csrf_hash();

		$this->load->model('emailmodel');
		$userdatas = $this->db->get_where('tb_users', ['username' => post('forgot_username')]);
		if ($userdatas->num_rows() == 0) {
			$data['status']         = false;
			$data['message']        = 'Username yang Anda masukkan tidak valid';
		}

		if ($data['status']) {
			$userdata               = $userdatas->row();
			$new_forgot_code        = random_string('alnum', 40);


			$update_fotgot_data             = array(
				'forgotten_password_time'   => now(),
				'forgotten_password_expired' => strtotime('+1 days', now()),
				'forgotten_password_code'   => $new_forgot_code
			);
			$this->ion_auth->update($userdata->id, $update_fotgot_data);

			$email_data['forgot_code']      = $new_forgot_code;
			$email_data['username']         = $userdata->username;
			$email_data['email']            = $userdata->email;

			$email_message 	= $this->load->view('dashboard/email/email-reset-password', $email_data, true);
			$this->emailmodel->send($userdata->email, 'EcobizGlobal Reset Password', $email_message);

			$data['message']           = 'Link permintaan reset password telah kami kirim ke email Anda.';
			$data['heading']		   = 'Berhasil';
			$data['type']			   = 'success';
		} else {
			$data['heading']		   = 'Gagal';
			$data['type']			   = 'error';
		}


		$this->output->set_content_type('application/json');
		echo json_encode($data);
	}

	public function submitNewPassword()
	{

		/**
		
			TODO:
			- validate kode nya lagi
			- validate form new_password -> required | min_leght[6]
			- validate form confirm_password -> required | matches[new_password]

			- jika true => update user passwordnya & forgot_code => null
		
		 */
		$data['status']         = true;
		$data['message']        = null;
		$data['csrf_data']      = $this->security->get_csrf_hash();
		$userdatas = $this->db->get_where('tb_users', ['forgotten_password_code' => post('forgotten_password_code')]);
		if ($userdatas->num_rows() == 0) {
			$data['status']         = false;
			$data['message']        = 'Code Lupa Password Tidak Valid atau Kadaluarsa';
		}


		$this->form_validation->set_rules('new_password', 'Password Baru', 'required|min_length[6]');
		$this->form_validation->set_rules('confirm_password', 'Konfirmasi Password Baru', 'required|matches[new_password]');
		if ($this->form_validation->run() == false) {
			$data['status']         = false;
			$data['message']        = validation_errors('  ', '<br>');
		}


		if ($data['status']) {

			$userdata               = $userdatas->row();
			$update_users_data 		= array(
				'password' => post('new_password'),
				'forgotten_password_code' => null
			);
			$this->ion_auth->update($userdata->id,  $update_users_data);
			$data['message']           = 'Password berhasil diperbarui, Anda dapat Login dengan password yang baru.';
			$data['heading']		   = 'Berhasil';
			$data['type']			   = 'success';
		} else {

			$data['heading']		   = 'Gagal';
			$data['type']			   = 'error';
		}


		$this->output->set_content_type('application/json');
		echo json_encode($data);
	}

	public function logout()
	{
		$this->ion_auth->logout();
		redirect('login', 'refresh');
	}
}

/* End of file Authentication.php */
/* Location: ./application/modules/authentication/controllers/Authentication.php */