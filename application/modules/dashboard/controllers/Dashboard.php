<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MX_Controller
{

	public function __construct()
	{
		parent::__construct();

		if (!$this->ion_auth->logged_in()) {

			$this->session->set_flashdata(
				'auth_flash',
				alerts('Anda harus login terlebih dahulu untuk mengakses halaman ini !', 'danger')
			);

			redirect('login', 'refresh');
		}
		if (!userdata()) {
			redirect('logout', 'refresh');
		}
	}

	function view_page($filename = 'dashboard')
	{

		$data = array();
		if (!file_exists(APPPATH . 'modules/dashboard/views/page/' . $filename . '.php')) {
			show_404();
			exit;
		}
		$data['data_group']     = $this->ion_auth->get_users_groups()->row();
		$data['userdata']     	= userdata();

		$this->template->content->view('page/' . $filename, $data);
		$this->template->publish();
	}

	function printPenj($param = null)
	{
		$this->db->where('inv_code', $param);
		$getpenj = $this->db->get('tb_invoice');

		if ($getpenj->num_rows() == 0) {
			show_404();
			exit;
		} else {
			$data = array(
				'penjualan' => $getpenj->row()
			);
			$this->load->view('penjualan', $data);
		}
	}

	function detailPenj($param = null)
	{
		$filename = 'detail-inv';

		$data = array();

		$this->db->where('inv_code', $param);
		// $this->db->where('userpaket_userid', userid());
		$cekkkkkkk = $this->db->get('tb_invoice');

		if (!file_exists(APPPATH . 'modules/dashboard/views/page/' . $filename . '.php')) {
			show_404();
			exit;
		} elseif ($cekkkkkkk->num_rows() != 0) {
			$data['penj'] = $cekkkkkkk->row();
		}
		$data['data_group']     = $this->ion_auth->get_users_groups()->row();
		$data['userdata']     	= userdata();

		$this->template->content->view('page/' . $filename, $data);
		$this->template->publish();
	}

	function report_package($param = null)
	{
		$filename = 'report-package';

		$data = array();

		$this->db->where('userpaket_code', $param);
		$this->db->where('userpaket_userid', userid());
		$cekkkkkkk = $this->db->get('tb_userpaket');

		if (!file_exists(APPPATH . 'modules/dashboard/views/page/' . $filename . '.php')) {
			show_404();
			exit;
		} elseif ($cekkkkkkk->num_rows() != 0) {
			$data['mypackage'] = $cekkkkkkk->row();
		}
		$data['data_group']     = $this->ion_auth->get_users_groups()->row();
		$data['userdata']     	= userdata();

		$this->template->content->view('page/' . $filename, $data);
		$this->template->publish();
	}

	function view_gen($param = 1)
	{
		if ($param < 1 || $param > 10) {
			show_404();
			exit;
		}

		$filename = 'mygeneration';
		if (!file_exists(APPPATH . 'modules/dashboard/views/page/' . $filename . '.php')) {
			show_404();
			exit;
		}

		$this->db->where('titiklevel_level', $param);
		$this->db->where('titiklevel_userid', userid());
		$cektitik = $this->db->get('tb_titiklevel');
		if ($cektitik->num_rows() == 0) {
			show_404();
			exit;
		}

		$data['genke'] = $param;
		$data['titiklevel'] = $cektitik->row();
		$data['userdata']     	= userdata();

		$this->template->content->view('page/' . $filename, $data);
		$this->template->publish();
	}

	function downloadinvpdf($param)
	{
		$this->db->where('inv_code', $param);
		$tblinvoice = $this->db->get('tb_invoice');
		if ($tblinvoice->num_rows() == 0) {
			show_404();
			exit;
		} else {
			$this->db->where('id', $tblinvoice->row()->inv_userid_from);
			$this->db->join('tb_invoice', 'inv_userid_from = id');
			$tblusers = $this->db->get('tb_users');
		}

		$this->load->library('Pdfgenerator');
		$paper 				= 'A4';
		$orientation 		= "portrait";
		$file_pdf 			= $param;
		$data = [
			'logo'		=>	$_SERVER["DOCUMENT_ROOT"] . '/assets/logo.png',
			'invoice'	=>  $tblinvoice->row(),
			'customer'	=>	$tblusers->row(),
			'title'     => '',
		];
		$html = $this->load->view('invoicepdf', $data, true);
		$this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
	}

// 	function downloadinvpdf($param)
// 	{
// 		// Load library
// 		$this->load->library('Pdfgenerator');

// 		// Dapatkan data dari model atau buat HTML secara langsung
// 		$data['title'] = 'Hello, World!';

// 		// Load view dan konversi ke string
// 		$html = $this->load->view('invoicepdf', $data, true);

// 		// Generate PDF
// 		$this->pdfgenerator->generate($html, "generated_pdf");
// 	}
}

/* End of file Dashboard.php */
/* Location: ./application/modules/dashboard/controllers/Dashboard.php */