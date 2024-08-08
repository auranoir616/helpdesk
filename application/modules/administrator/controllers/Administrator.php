<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Administrator extends CI_Controller
{


	public function __construct()
	{
		parent::__construct();

		if (!$this->ion_auth->is_admin()) :
			redirect('login', 'refresh');
		endif;
	}

	function index($filename = 'dashboard/dashboard')
	{

		$data = array();
		if (!file_exists(APPPATH . 'modules/administrator/views/' . $filename . '.php')) {
			show_404();
			exit;
		}
		$data['data_group']     = $this->ion_auth->get_users_groups()->row();
		$data['userdata']     	= userdata();

		$this->template->content->view('administrator/' . $filename, $data);
		$this->template->publish('dashboard/template');
	}
}

/* End of file Administrator.php */
/* Location: ./application/controllers/Administrator.php */