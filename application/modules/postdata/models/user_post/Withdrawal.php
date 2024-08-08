<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Withdrawal extends CI_Model
{

	private static $data = [
		'status' 	=> true,
		'message' 	=> null,
	];

	public function __construct()
	{
		parent::__construct();
		Self::$data['csrf_data'] 		= $this->security->get_csrf_hash();
	}

	function requestwithdrawal()
	{
		$totalwd 		= preg_replace('/[^0-9.]+/', '', preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $this->input->post('wd_total')));
		$wallet_withdrawal              = $this->usermodel->userWallet('withdrawal')->wallet_address;
		$info_walletwd                  = $this->walletmodel->walletAddressBalance($wallet_withdrawal);
		if ($info_walletwd < $totalwd) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= 'Saldo Dompet Tidak Cukup!';
		}

		if (!$this->ion_auth->hash_password_db(userid(), post('wd_password'))) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= 'Konfirmasi Password Tidak Cocok!';
		}

		$this->form_validation->set_rules('wd_total', 'Total Withdrawals', 'required');
		// $this->form_validation->set_rules('wd_password', 'Password', 'required');
		if ($this->form_validation->run() == FALSE) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= validation_errors('', '<br/>');
		}

		// Mendapatkan waktu saat ini
		$time = date("H:i");
		if ($time <= "08:00" && $time >= "20:00") {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= 'Transaksi Diluar Jam Operasional';
		}

		$this->db->where('withdrawl_userid', userid());
		$this->db->where('withdrawl_status', 'Pending');
		$cekwdddd = $this->db->get('tb_withdrawl');
		if ($cekwdddd->num_rows() > 0) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= 'Anda Memiliki Transaksi Penarikan Yang Tertunda!';
		}

		if ($totalwd < 50000) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= 'Minimum Transaksi Withdrawal Rp. 50.000 !';
		}

		$userdata = userdata();
		if ($userdata->user_bank_account == null) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= 'Harap Mengatur Data Bank Terlebih Dahulu';
		}

		if (Self::$data['status']) {
			$userdata 	= userdata();
			$potongan  	= 10000;
			$code = hash('SHA256', random_string('alnum', 16));

			$this->db->insert('tb_withdrawl', [
				'withdrawl_userid'  		=> userid(),
				'withdrawl_amount'  		=> $totalwd,
				'withdrawl_account'  		=> $userdata->user_bank_account,
				'withdrawl_bank_name'  		=> $userdata->user_bank_name,
				'withdrawl_bank_number'  	=> $userdata->user_bank_number,
				'withdrawl_will_get'  		=> $totalwd - $potongan,
				'withdrawl_potongan'		=> $potongan,
				'withdrawl_trxid' 			=> $code,
				'withdrawl_date'  			=> sekarang(),
			]);

			$this->db->insert(
				'tb_notif',
				[
					'notif_useridto' => 1, 
					'notif_useridfrom' => userid(),
					'notif_desc' => 'withdrawal Bonus dari reseller ' . userdata(['id' => userid()])->username . ' sebesar Rp.'.$totalwd. ' ke rekening '.$userdata->user_bank_account,
					'notif_tipe' => 'withdrawal',
					'notif_date' => sekarang(),
					'notif_code' => $code,
				]
			);
			
			Self::$data['heading'] 	= 'Berhasil';
			Self::$data['message'] 	= 'Permintaan Penarikan Telah Ditambahkan Dalam Antrian';
			Self::$data['type'] 	= 'success';
		} else {

			Self::$data['heading'] 	= 'Gagal';
			Self::$data['type'] 	= 'error';
		}

		return Self::$data;
	}
}

/* End of file Withdrawal.php */
/* Location: ./application/modules/postdata/models/user_post/Withdrawal.php */