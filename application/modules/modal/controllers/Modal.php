<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Modal extends MX_Controller
{

	public function __construct()
	{

		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('login', 'refresh');
		}
	}

	public function admin($filename = 'null')
	{

		if (!$this->ion_auth->is_admin()) {
			$this->output->set_status_header(403);
			exit;
		}


		if (!file_exists(APPPATH . '/modules/modal/views/admin/' . $filename . '.php')) {
			show_404();
			exit;
		}

		echo $this->load->view('admin/' . $filename, [], true);
	}

	public function adminpanel($filename = 'null')
	{

		$user_group 	= $this->ion_auth->get_users_groups()->row();
		if ($user_group->id != 6) {
			$this->output->set_status_header(403);
			exit;
		}


		if (!file_exists(APPPATH . '/modules/modal/views/adminpanel/' . $filename . '.php')) {
			show_404();
			exit;
		}

		echo $this->load->view('adminpanel/' . $filename, [], true);
	}


	public function csupport($filename = 'null')
	{

		$user_group 	= $this->ion_auth->get_users_groups()->row();
		if ($user_group->id != 5) {
			$this->output->set_status_header(403);
			exit;
		}


		if (!file_exists(APPPATH . '/modules/Modal/views/csupport/' . $filename . '.php')) {
			show_404();
			exit;
		}

		echo $this->load->view('csupport/' . $filename, [], true);
	}


	public function member($filename = 'null')
	{

		// $user_group 	= $this->ion_auth->get_users_groups()->row();
		// if ($user_group->id != 2 || $user_group->id != 1) {
		// 	$this->output->set_status_header(403);
		// 	exit;
		// }

		if (!file_exists(APPPATH . '/modules/modal/views/member/' . $filename . '.php')) {
			show_404();
			exit;
		}

		echo $this->load->view('member/' . $filename, [], true);
	}
}

/* End of file Modal.php */
/* Location: ./application/modules/modal/controllers/Modal.php */