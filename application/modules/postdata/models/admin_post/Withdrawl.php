<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Withdrawl extends CI_Model
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

	public function approvewd()
	{
		$this->db->where('withdrawl_trxid', post('code'));
		$this->db->where('withdrawl_status', 'Pending');
		$this->db->join('tb_users', 'withdrawl_userid = id');
		$cekwed = $this->db->get('tb_withdrawl');
		if ($cekwed->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Transaksi Tidak Ditemukan atau Dikonfirmasi";
		} else {
			$userdata = $cekwed->row();

			$wallet_withdrawal              = $this->usermodel->userWallet('withdrawal', $userdata->withdrawl_userid)->wallet_address;
			$info_walletwd                  = $this->walletmodel->walletAddressBalance($wallet_withdrawal);
			if ($info_walletwd < $userdata->withdrawl_amount) {
				Self::$data['status'] 	= false;
				Self::$data['message'] 	= 'Saldo Dompet Member Tidak Cukup!';
			}
		}

		$this->form_validation->set_rules('code', 'Kode Transaksi', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {
			$userdata = $cekwed->row();

			$walletnya     = $this->usermodel->userWallet('withdrawal', $userdata->withdrawl_userid);
			$this->db->insert(
				'tb_wallet_balance',
				[
					'w_balance_wallet_id'       => $walletnya->wallet_id,
					'w_balance_amount'          => $userdata->withdrawl_amount,
					'w_balance_type'            => 'debit',
					'w_balance_desc'            => 'Withdrawal Rp. ' . number_format($userdata->withdrawl_amount, 0, '.', '.'),
					'w_balance_date_add'        => sekarang(),
					'w_balance_txid'            => strtolower(random_string('alnum', 64)),
					'w_balance_ket'				=> 'withdrawal',
				]
			);

			$wallet_withdrawal              = $this->usermodel->userWallet('withdrawal', $userdata->withdrawl_userid)->wallet_address;
			$info_walletwd                  = $this->walletmodel->walletAddressBalance($wallet_withdrawal);

			$this->db->update(
				'tb_withdrawl',
				[
					'withdrawl_status'	=> 'Success'
				],
				[
					'withdrawl_trxid'	=> post('code')
				]
			);

			$this->db->insert(
				'tb_notif',
				[
					'notif_useridto' =>  $walletnya->wallet_id, 
					'notif_useridfrom' => userid(),
					'notif_desc' => 'withdrawal Bonus anda sebesar Rp.'.$userdata->withdrawl_amount. ' sudah dikonfirmasi dan ditransfer ke rekening anda',
					'notif_tipe' => 'withdrawal',
					'notif_date' => sekarang(),
					'notif_code' => post('code'),
				]
			);


			// $nowa     = $userdata->user_phone;
			// $pesan    = "Yth. " . $userdata->user_fullname . " Selamat Widtdrawal Sebesar Rp. " . number_format($userdata->withdrawl_amount, 0, '.', '.') . " Sukses!! \r\n\r\nSisa Saldo Rp " . number_format($info_walletwd, 0, '.', '.') . " https://sispenju.com/login";

			// $this->notifWA($nowa, $pesan);

			Self::$data['message']      = 'Withdrawal Sukses!';
			Self::$data['heading']      = 'Success';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     	= 'Error';
			Self::$data['type']     	= 'error';
		}
		return Self::$data;
	}

	// function notifWA($phone = NULL, $message = NULL)
	// {
	// 	$return = array();
	// 	$userkey = 'b3f16549743b';
	// 	$passkey = '59391aaa7eee4bb7f17cf4c1';
	// 	$telepon = $phone;
	// 	$message = str_replace('%20', ' ', $message);
	// 	$url = 'https://console.zenziva.net/wareguler/api/sendWA/';
	// 	$curlHandle = curl_init();
	// 	curl_setopt($curlHandle, CURLOPT_URL, $url);
	// 	curl_setopt($curlHandle, CURLOPT_HEADER, 0);
	// 	curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
	// 	curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
	// 	curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
	// 	curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
	// 	curl_setopt($curlHandle, CURLOPT_POST, 1);
	// 	curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
	// 		'userkey' => $userkey,
	// 		'passkey' => $passkey,
	// 		'to' => $telepon,
	// 		'message' => $message
	// 	));
	// 	json_decode(curl_exec($curlHandle), true);
	// 	curl_close($curlHandle);

	// 	return $return;
	// }


	public function decline_withdrawal()
	{
		Self::$data['heading'] 		= 'Success';
		Self::$data['type']	 		= 'success';
		Self::$data['message'] 		= 'Withdrawal Decline successfully !';

		$this->db->delete('tb_withdrawl', array('withdrawl_trxid' => post('withdrawl_id')));

		$this->db->delete('tb_wallet_balance', [
			'w_balance_txid' 	=> post('withdrawl_id')
		]);

		return Self::$data;
	}

	function rejectwd()
	{
		$this->db->where('withdrawl_trxid', post('code'));
		$this->db->where('withdrawl_status', 'Pending');
		$cekwed = $this->db->get('tb_withdrawl');
		if ($cekwed->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Transaksi tidak ditemukan atau dikonfirmasi";
		}

		$this->form_validation->set_rules('code', 'Kode Transaksi', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {


			$this->db->update(
				'tb_withdrawl',
				[
					'withdrawl_status'	=> 'Rejected'
				],
				[
					'withdrawl_trxid'	=> post('code')
				]
			);

			Self::$data['message']      = 'Withdrawal Di Tolak!';
			Self::$data['heading']      = 'Success';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     	= 'Error';
			Self::$data['type']     	= 'error';
		}
		return Self::$data;
	}

	function tarikTabungan()
	{
		if (!$this->ion_auth->hash_password_db(userid(), post('user_pass'))) {
			Self::$data['status']       = false;
			Self::$data['message']      = 'Konfirmasi Password Tidak Sama';
		}

		$this->form_validation->set_rules('username_tujuan', 'Username Tujuan', 'required');
		$this->form_validation->set_rules('tot_penarikan', 'Total Penarikan', 'required');
		$this->form_validation->set_rules('user_pass', 'Konfirmasi Password Salah', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}


		$this->db->where('user_code', post('show_code'));
		$this->db->join('tb_invnabung', 'tb_invnabung.invnabung_userid = tb_users.id');
		$this->db->join('tb_walletnabung', 'tb_walletnabung.walletnabung_userid = tb_users.id');
		$cekUsers = $this->db->get('tb_users');
		if ($cekUsers->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Users Tidak DI Temukan";
		} else {
			$wallet_withdrawal              = $this->usermodel->userWalletNabung($cekUsers->row()->id);

			if ($wallet_withdrawal < preg_replace('/[^0-9.]+/', '', preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $this->input->post('tot_penarikan')))) {
				Self::$data['status'] 	= false;
				Self::$data['message'] 	= 'Saldo Tabungan Member Tidak Cukup!';
			}
		}

		if (Self::$data['status']) {
			$userdatas = $cekUsers->row();
			$random_string = strtolower(random_string('alnum', 60));
			$total = $wallet_withdrawal - preg_replace('/[^0-9.]+/', '', preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $this->input->post('tot_penarikan')));

			$this->db->insert(
				'tb_walletnabung',
				[
					'walletnabung_userid'		=> $userdatas->walletnabung_userid,
					'walletnabung_amount'	    => $this->input->post('tot_penarikan'),
					'walletnabung_type'			=> 'debit',
					'walletnabung_date'			=> sekarang(),
					'walletnabung_code'			=> $random_string,
				]
			);

			$this->db->insert(
				'tb_historitabungan',
				[
					'historitabungan_userid'	=> $userdatas->walletnabung_userid,
					'historitabungan_desc'		=> 'Debit Dana Tabungan Sebesar Rp. ' . number_format($this->input->post('tot_penarikan'), 0, ',', '.'),
					'historitabungan_total'		=> $total,
					'historitabungan_date'		=> sekarang(),
					'historitabungan_code'	    => $random_string
				]
			);

			// $nowa     = $userdatas->user_phone;
			// $pesan    = "Yth. " . $userdatas->user_fullname . " Penarikan Tabungan (Baitullah) Sebesar Rp. " . number_format($this->input->post('tot_penarikan'), 0, '.', '.') . " Telah Sukses!! \r\n\r\nSisa Saldo Rp " . number_format($total, 0, '.', '.') . " https://sispenju.com/login";

			// $this->notifWA($nowa, $pesan);


			Self::$data['message']      = 'Withdrawl Tabungan Suksess';
			Self::$data['heading']      = 'Success';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     	= 'Error';
			Self::$data['type']     	= 'error';
		}
		return Self::$data;
	}
}

/* End of file Withdrawl.php */
/* Location: ./application/models/Withdrawl.php */