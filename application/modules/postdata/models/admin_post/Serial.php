<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Serial extends CI_Model
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

	function newpinserial()
	{
		$this->db->where('package_code', $this->input->post('pin_package'));
		$cekpaket = $this->db->get('tb_packages');
		if ($cekpaket->num_rows() == 0) {
			Self::$data['status']       = false;
			Self::$data['message']      = 'Paket PIN Tidak Valid!';
		}

		if (!$this->ion_auth->hash_password_db(userid(), post('pin_password'))) {
			Self::$data['status']       = false;
			Self::$data['message']      = 'Konfirmasi Password Tidak Sesuai!';
		}

		$this->form_validation->set_rules('pin_package', 'Paket PIN', 'required');
		$this->form_validation->set_rules('pin_total', 'Total PIN', 'required');
		$this->form_validation->set_rules('pin_password', 'Konfirmasi Password', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= validation_errors(' ', '<br/>');
		}

		if (str_replace('.', '', $this->input->post('pin_total')) > 300) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= "Maksimal Generate 300 PIN";
		}


		if (Self::$data['status']) {

			$datapaket = $cekpaket->row();

			for ($jumlah = 0; $jumlah < str_replace('.', '', $this->input->post('pin_total')); $jumlah++) {
				$kodeeee         = 'WDI' . strtoupper(random_string('numeric', 3) . random_string('alnum', 4) . random_string('alnum', 4) . random_string('alnum', 4) . random_string('alnum', 4) . $datapaket->package_kode);

				$this->db->insert(
					'tb_users_pin',
					[
						'pin_package_id'        => $datapaket->package_id,
						'pin_kode'              => $kodeeee,
						'pin_date_add'          => sekarang(),
						'pin_userid'            => userid(),
						'pin_code'              => strtolower(random_string('alnum', 64)),
					]
				);
			}

			Self::$data['heading']	= 'Berhasil';
			Self::$data['message']	= 'PIN Kode Berhasil Dibuat';
			Self::$data['type']		= 'success';
		} else {
			Self::$data['heading'] 	= 'Error';
			Self::$data['type'] 	= 'error';
		}

		return Self::$data;
	}


	function newpackage()
	{
		$this->db->where('pin_lock', post('pintransaksi'));
		$this->db->where('id', userid());
		$cekpin = $this->db->get('tb_users');
		if ($cekpin->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'TRANSACTION PIN DOES NOT COMPLETE !';
		}

		$this->form_validation->set_rules('package_nominal', 'Nominal Package', 'required');
		// $this->form_validation->set_rules('package_ticket', 'Nominal Ticket', 'required');
		// $this->form_validation->set_rules('package_sponsor', 'Bonus Sponsor', 'required');
		// $this->form_validation->set_rules('package_daily', 'Daily Profit', 'required');
		$this->form_validation->set_rules('package_hphi', 'HPHI', 'required');
		$this->form_validation->set_rules('pintransaksi', 'Transaction Pin', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {


			$this->db->insert(
				'tb_packages',
				[
					'package_type'				=> 'investment',
					'package_range_start'		=> post('package_nominal'),
					'package_hphi'				=> post('package_hphi'),
					'package_daily'				=> '3334',
					'package_code'				=> hash('sha256', random_string('numeric', 20))
				]
			);

			Self::$data['heading']	= 'Success';
			Self::$data['message']	= 'Data Successfully Saved';
			Self::$data['type']		= 'success';
		} else {
			Self::$data['heading'] 	= 'Failed';
			Self::$data['type'] 	= 'error';
		}
		return Self::$data;
	}

	function generateNewSerials()
	{

		$this->db->where('pin_lock', post('pintransaksi'));
		$this->db->where('id', userid());
		$cekpin = $this->db->get('tb_users');
		if ($cekpin->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'TRANSACTION PIN DOES NOT COMPLETE !';
		}

		$this->form_validation->set_rules('rm_desc', 'Desc Bonus', 'required');
		$this->form_validation->set_rules('rm_leftpoin', 'Point Left', 'required');
		$this->form_validation->set_rules('rm_rightpoin', 'Point Right', 'required');
		$this->form_validation->set_rules('pintransaksi', 'Transaction Pin', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= validation_errors(' ', '<br/>');
		}


		Self::$data['heading'] 		= 'Success';
		Self::$data['type']	 		= 'success';
		Self::$data['message'] 		= post('total_serial') . ' New Serial has added !';

		for ($i = 1; $i <= post('total_serial'); $i++) {

			$random_string = random_string('alnum', 4) . '-';
			$random_string .= random_string('alnum', 4) . '-';
			$random_string .= random_string('alnum', 4) . '-';
			$random_string .= random_string('alnum', 4);

			$this->db->insert('tb_serials', [
				'serial_number'   		=> strtoupper($random_string),
				'serial_package_id'   	=> post('package_id'),
				'serial_date_add'   	=> sekarang(),
				'serial_used'   		=> 'false',
				'serial_used_by'   		=> null,
			]);
		}

		return Self::$data;
	}

	function cekkirimpin()
	{
		if (!$this->ion_auth->hash_password_db(userid(), post('konfirmasi_password'))) {
			Self::$data['status']       = false;
			Self::$data['message']      = 'Konfirmasi Password Tidak Sesuai!';
		}

		$this->db->where('package_code', $this->input->post('paket_pin'));
		$cekkkpaket = $this->db->get('tb_packages');
		if ($cekkkpaket->num_rows() == 0) {
			Self::$data['status']       = false;
			Self::$data['message']      = 'Paket PIN Serial Tidak Valid!';
		}

		$this->db->where('username', post('username_tujuan'));
		$cekuser = $this->db->get('tb_users');
		if ($cekuser->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Username Tujuan Tidak Ditemukan!';
		}

		$this->form_validation->set_rules('username_tujuan', 'Username Tujuan', 'required');
		$this->form_validation->set_rules('paket_pin', 'Paket PIN', 'required');
		$this->form_validation->set_rules('total_pin', 'Total PIN', 'required');
		$this->form_validation->set_rules('konfirmasi_password', 'Konfirmasi Password', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		$mydatas = userdata();
		if ($mydatas->username == post('username_tujuan')) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Tidak Dizinkan Mengirim PIN Serial ke Akun Sendiri";
		}

		if (Self::$data['status']) {
			$datapaket = $cekkkpaket->row();

			$userdatas  = $cekuser->row();

			Self::$data['message']      = 'Kirim ' . $this->input->post('total_pin') . ' PIN ' . $datapaket->package_name . ' Ke Username ' . strtoupper($userdatas->username);
		} else {
			Self::$data['heading']      = 'Gagal';
			Self::$data['type']         = 'error';
		}

		return Self::$data;
	}

	function kirimpinserial()
	{
		if (!$this->ion_auth->hash_password_db(userid(), post('konfirmasi_password'))) {
			Self::$data['status']       = false;
			Self::$data['message']      = 'Konfirmasi Password Tidak Sesuai!';
		}

		$this->db->where('package_code', $this->input->post('paket_pin'));
		$cekkkpaket = $this->db->get('tb_packages');
		if ($cekkkpaket->num_rows() == 0) {
			Self::$data['status']       = false;
			Self::$data['message']      = 'Paket PIN Tidak Valid!';
		}

		$this->db->where('username', post('username_tujuan'));
		$cekuser = $this->db->get('tb_users');
		if ($cekuser->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Username Tujuan Tidak Ditemukan!';
		}

		$this->form_validation->set_rules('username_tujuan', 'Username Tujuan', 'required');
		$this->form_validation->set_rules('paket_pin', 'Paket PIN', 'required');
		$this->form_validation->set_rules('total_pin', 'Total PIN', 'required');
		$this->form_validation->set_rules('konfirmasi_password', 'Konfirmasi Password', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		$mydatas = userdata();
		if ($mydatas->username == post('username_tujuan')) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Tidak Dizinkan Mengirim PIN Serial ke Akun Sendiri";
		}

		if (Self::$data['status']) {
			$datapaket = $cekkkpaket->row();
			$userdata   = userdata();
			$userdatas  = $cekuser->row();

			// =============================================================== //
			//	PROSES MEMBUAT PIN SERIAL									  //
			// =============================================================== //
			for ($jumlah = 0; $jumlah < str_replace('.', '', $this->input->post('total_pin')); $jumlah++) {
				$kodeeee         = 'WDI' . strtoupper(random_string('numeric', 3) . random_string('alnum', 4) . random_string('alnum', 4) . random_string('alnum', 4) . random_string('alnum', 4) . $datapaket->package_kode);

				$this->db->insert(
					'tb_users_pin',
					[
						'pin_package_id'        => $datapaket->package_id,
						'pin_kode'              => $kodeeee,
						'pin_date_add'          => sekarang(),
						'pin_userid'			=> $userdatas->id,
						'pin_code'              => strtolower(random_string('alnum', 64)),
					]
				);
			}

			// =============================================================== //
			//	LAPORAN PENGIRIM									  		   //
			// =============================================================== //
			$this->db->insert(
				'tb_histori_userpin',
				[
					'histori_userid'          	=> $userdata->id,
					'histori_userpindesc'     	=> 'Kirim ' . $this->input->post('total_pin') . ' PIN ' . $datapaket->package_name . ' ke Username: ' . $userdatas->username,
					'histori_userpindate'		=> sekarang(),
					'histori_code '            => strtolower(random_string('alnum', 64)),
				]
			);

			// =============================================================== //
			//	LAPORAN PENERIMA									  		   //
			// =============================================================== //
			$this->db->insert(
				'tb_histori_userpin',
				[
					'histori_userid'			=> $userdatas->id,
					'histori_userpindesc'		=> 'Terima ' . $this->input->post('total_pin') . ' PIN ' . $datapaket->package_name . ' dari Username: ' . $userdata->username,
					'histori_userpindate'		=> sekarang(),
					'histori_code'				=> strtolower(random_string('alnum', 64)),
				]
			);

			Self::$data['heading']      = 'Berhasil';
			Self::$data['message']      = 'PIN Kode Berhasil di Kirim';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']      = 'Gagal';
			Self::$data['type']         = 'error';
		}

		return Self::$data;
	}
}
