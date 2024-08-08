<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wallet extends CI_Model
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

	function kirimsaldo()
	{
		$cekpassword 	= $this->ion_auth->hash_password_db(userid(), post('saldo_password'));
		if (!$cekpassword) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= 'Konfirmasi Password yang Anda Masukkan Tidak Sesuai !';
		}
		$wallet_withdrawal              = $this->usermodel->userWallet('withdrawal')->wallet_address;
		$info_walletwd                  = $this->walletmodel->walletAddressBalance($wallet_withdrawal);
		if ($info_walletwd < str_replace('.', '', $this->input->post('saldo_total'))) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= 'Saldo Anda Tidak Cukup!';
		}

		$this->db->where('username', str_replace(' ', '', $this->input->post('saldo_username')));
		$cekusername = $this->db->get('tb_users');
		if ($cekusername->num_rows() == 0) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= 'Username Tujuan Tidak Valid!';
		}

		$this->form_validation->set_rules('saldo_username', 'Username Tujuan', 'required');
		$this->form_validation->set_rules('saldo_total', 'Total Saldo', 'required|numeric|greater_than[0]');
		$this->form_validation->set_rules('saldo_password', 'Password', 'required');
		if ($this->form_validation->run() == FALSE) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {
			$userpengirim = userdata();
			$userpenerima = $cekusername->row();

			// UNTUK PENGIRIM
			$this->db->insert(
				'tb_kirimsaldo',
				[
					'kirimsaldo_userid'		=> $userpengirim->id,
					'kirimsaldo_amount'		=> str_replace('.', '', $this->input->post('saldo_total')),
					'kirimsaldo_desc'		=> 'Kirim SALDO Ke Username : ' . $userpenerima->username,
					'kirimsaldo_date'		=> sekarang(),
					'kirimsaldo_code'		=> strtolower(random_string('alnum', 64)),
				]
			);

			$wallet01     = $this->usermodel->userWallet('withdrawal', $userpengirim->id);
			$this->db->insert(
				'tb_wallet_balance',
				[
					'w_balance_wallet_id'       => $wallet01->wallet_id,
					'w_balance_amount'          => str_replace('.', '', $this->input->post('saldo_total')),
					'w_balance_type'            => 'debit',
					'w_balance_desc'            => 'Kirim SALDO Ke Username : ' . $userpenerima->username,
					'w_balance_date_add'        => sekarang(),
					'w_balance_txid'            => strtolower(random_string('alnum', 64))
				]
			);



			// UNTUK PENERIMA
			$this->db->insert(
				'tb_kirimsaldo',
				[
					'kirimsaldo_userid'		=> $userpenerima->id,
					'kirimsaldo_amount'		=> str_replace('.', '', $this->input->post('saldo_total')),
					'kirimsaldo_desc'		=> 'Terima SALDO Dari Username : ' . $userpengirim->username,
					'kirimsaldo_date'		=> sekarang(),
					'kirimsaldo_code'		=> strtolower(random_string('alnum', 64)),
				]
			);

			$wallet02     = $this->usermodel->userWallet('withdrawal', $userpenerima->id);
			$this->db->insert(
				'tb_wallet_balance',
				[
					'w_balance_wallet_id'       => $wallet02->wallet_id,
					'w_balance_amount'          => str_replace('.', '', $this->input->post('saldo_total')),
					'w_balance_type'            => 'credit',
					'w_balance_desc'            => 'Terima SALDO Dari Username : ' . $userpengirim->username,
					'w_balance_date_add'        => sekarang(),
					'w_balance_txid'            => strtolower(random_string('alnum', 64))
				]
			);


			Self::$data['message']      = 'Saldo Berhasil Dikirim!';
			Self::$data['heading']      = 'Berhasil';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     	= 'Error';
			Self::$data['type']     	= 'error';
		}
		return Self::$data;
	}

	function addbalance()
	{

		$this->db->where('pin_lock', post('pintransaksi'));
		$this->db->where('id', userid());
		$cekpinuser = $this->db->get('tb_users');
		if ($cekpinuser->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Invalid PIN Transation!';
		}

		$this->form_validation->set_rules('amount', 'Amount', 'required');
		$this->form_validation->set_rules('pintransaksi', 'Confirm Transaction Pin', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (!$this->usermodel->is_active(userid())) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Your Investment Status Must Be Active!!';
		}
		if (Self::$data['status']) {

			$kode_unik 		= rand(300, 999);

			$this->db->insert(
				'tb_users_invoice',
				[
					'invoice_user_id'		=> userid(),
					'invoice_amount'		=> post('amount'),
					'invoice_kode_unik'		=> $kode_unik,
					'invoice_date_add'		=> sekarang(),
					'invoice_code'			=> strtolower(random_string('alnum', 64)),
				]
			);

			Self::$data['message']      = 'invoice has been made, please confirm payment!';
			Self::$data['heading']      = 'Success';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     = 'Error';
			Self::$data['type']     = 'error';
		}
		return Self::$data;
	}


	function newwithdrawal()
	{

		// VALID SALDO
		$wallet_withdrawal 	= $this->usermodel->userWallet('withdrawal');
		$balance_wallet_w 	= $this->walletmodel->walletAddressBalance($wallet_withdrawal->wallet_address);
		if ($balance_wallet_w < post('amount')) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= 'Insufficient Wallet Balance!';
		}

		// VALID PIN
		$this->db->where('pin_lock', post('pintransaksi'));
		$this->db->where('id', userid());
		$cekpinuser = $this->db->get('tb_users');
		if ($cekpinuser->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Invalid PIN Transation!';
		}

		$this->form_validation->set_rules('amount', 'Amount', 'required|greater_than[110000]');
		$this->form_validation->set_rules('pintransaksi', 'Confirm Transaction Pin', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}
		$this->db->where('withdrawl_userid', userid());
		$this->db->where('withdrawl_status', 'Pending');
		$cekwddddd = $this->db->get('tb_withdrawl');
		if ($cekwddddd->num_rows() != 0) {
			Self::$data['status']     	= false;
			Self::$data['message']     	= 'Withdrawals previous transactions are still pending!';
		}

		if (date('D') != "Mon") {
			Self::$data['status']     	= false;
			Self::$data['message']     	= 'Withdrawals can only be made on Monday!';
		} else {
			if (date('H:i') > '19:00') {
				Self::$data['status']     	= false;
				Self::$data['message']     	= 'Withdrawals Time Out Please Try Next Week!';
			}
		}

		if (!$this->usermodel->is_active(userid())) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Your Investment Status Must Be Active!!';
		}
		// if (!$this->mainmodel->verification()) {
		// 	Self::$data['status']     = false;
		// 	Self::$data['message']     = 'You must be verified in advance to make this transaction!!';
		// }




		if (Self::$data['status']) {
			$userdata = userdata();

			$this->db->insert(
				'tb_withdrawl',
				[
					'withdrawl_userid'			=> userid(),
					'withdrawl_amount'			=> post('amount'),
					'withdrawl_potongan'		=> '10000',
					'withdrawl_account'			=> $userdata->user_bank_name,
					'withdrawl_bank_name'		=> $userdata->user_bank_account,
					'withdrawl_bank_number'		=> $userdata->user_bank_number,
					'withdrawl_date'			=> sekarang(),
					'withdrawl_trxid'			=> strtolower(random_string('alnum', 64)),
				]
			);

			Self::$data['message']      = 'The withdrawal is in progress!';
			Self::$data['heading']      = 'Success';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     	= 'Error';
			Self::$data['type']     	= 'error';
		}
		return Self::$data;
	}


	function do_wdcapital()
	{

		$this->db->where('invest_userid', userid());
		$this->db->where('invest_wd', '1');
		$this->db->where('invest_code', post('package'));
		$cekinvest = $this->db->get('tb_invest');
		if ($cekinvest->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Invalid Investment Package!';
		}
		// VALID PIN
		$this->db->where('pin_lock', post('pintransaksi'));
		$this->db->where('id', userid());
		$cekpinuser = $this->db->get('tb_users');
		if ($cekpinuser->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Invalid PIN Transation!';
		}

		$this->form_validation->set_rules('package', 'Package Investment', 'required');
		$this->form_validation->set_rules('pintransaksi', 'Confirm Transaction Pin', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (date('D') != "Mon") {
			Self::$data['status']     	= false;
			Self::$data['message']     	= 'Withdrawals can only be made on Monday!';
		} else {
			if (date('H:00') > '22:00') {
				Self::$data['status']     	= false;
				Self::$data['message']     	= 'Withdrawals Time Out Please Try Next Week!';
			}
		}

		if (!$this->usermodel->is_active(userid())) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Your Investment Status Must Be Active!!';
		}
		// if (!$this->mainmodel->verification()) {
		// 	Self::$data['status']     = false;
		// 	Self::$data['message']     = 'You must be verified in advance to make this transaction!!';
		// }

		if (option('withdraw-capital') == 'off') {
			Self::$data['status']     = false;
			Self::$data['message']     = 'admin has not activated this feature!!';
		}

		if (Self::$data['status']) {
			$userdata = userdata();
			$investdata = $cekinvest->row();
			$this->db->insert(
				'tb_withdrawl',
				[
					'withdrawl_userid'			=> userid(),
					'withdrawl_amount'			=> $investdata->invest_amount,
					'withdrawl_potongan'		=> '0',
					'withdrawl_account'			=> $userdata->user_bank_name,
					'withdrawl_bank_name'		=> $userdata->user_bank_account,
					'withdrawl_bank_number'		=> $userdata->user_bank_number,
					'withdrawl_date'			=> sekarang(),
					'withdrawl_trxid'			=> strtolower(random_string('alnum', 64)),
				]
			);

			Self::$data['message']      = 'The withdrawal is in progress!';
			Self::$data['heading']      = 'Success';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     	= 'Error';
			Self::$data['type']     	= 'error';
		}
		return Self::$data;
	}

	function cancelwd()
	{
		$this->db->where('withdrawl_trxid', post('code'));
		// $this->db->where('withdrawl_userid', userid());
		$this->db->where('withdrawl_status', 'Pending');
		$cekwed = $this->db->get('tb_withdrawl');
		if ($cekwed->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Transaction not found or confirmed";
		}

		$this->form_validation->set_rules('code', 'Code Transation', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}
		if (!$this->usermodel->is_active(userid())) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Your Investment Status Must Be Active!!';
		}

		if (Self::$data['status']) {


			$this->db->update(
				'tb_withdrawl',
				[
					'withdrawl_status'	=> 'Canceled'
				],
				[
					'withdrawl_trxid'	=> post('code')
				]
			);

			$user_group     = $this->ion_auth->get_users_groups()->row();
			if ($user_group->id == 1) :
				$datawd = $cekwed->row();
				$userrrdatas = userdata(['id' => $datawd->withdrawl_userid]);
				$email_data             = [
					'username'              => $userrrdatas->username,
				];

				$config['mailtype']         = 'html';
				$config['protocol']         = 'smtp';
				$config['smtp_host']        = 'ssl://mail.globalasiapay.com';
				$config['smtp_user']        = 'notif@globalasiapay.com';
				$config['smtp_pass']        = 'IDprogrammer123';
				$config['smtp_port']        = 465;
				$config['smtp_timeout']     = 5;
				$config['newline']          = "\r\n";


				$this->load->library('email', $config);
				$email_message     = $this->load->view('email-withdrawalreject', $email_data, true);

				$this->email->from('noreply@globalasiapay.com', 'Global Asia Pay');
				$this->email->to($userrrdatas->email);
				$this->email->subject('Withdrawal Information');
				$this->email->message($email_message);
				$this->email->send();
			endif;

			Self::$data['message']      = 'Withdrawal canceled!';
			Self::$data['heading']      = 'Success';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     	= 'Error';
			Self::$data['type']     	= 'error';
		}
		return Self::$data;
	}
	function newtransfer()
	{

		// VALID USERNAME TUJUAN
		$this->db->where('username', post('username'));
		$cekusername = $this->db->get('tb_users');
		if ($cekusername->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Invalid Destination Username!';
		}


		$this->db->where('pin_lock', post('pintransaksi'));
		$this->db->where('id', userid());
		$cekpinuser = $this->db->get('tb_users');
		if ($cekpinuser->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Invalid PIN Transation!';
		}


		$userdata = userdata();
		if (post('username') == $userdata->username) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Not allowed to send balance to your own account, you must have a different username!';
		}

		$this->form_validation->set_rules('amount', 'IDR Amount', 'required');
		$this->form_validation->set_rules('username', 'Destination Username', 'required');
		$this->form_validation->set_rules('pintransaksi', 'Confirm Transaction Pin', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (!$this->usermodel->is_active(userid())) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Your Investment Status Must Be Active!!';
		}

		// if (!$this->mainmodel->verification()) {
		// 	Self::$data['status']     = false;
		// 	Self::$data['message']     = 'You must be verified in advance to make this transaction!!';
		// }
		if (post('amount') < 100000) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Minimum Balance Transfer Rp. 100.000!';
		}

		$wallet_withdrawall      	= $this->usermodel->userWallet('withdrawal');
		$balance_wallet_www       	= $this->walletmodel->walletAddressBalance($wallet_withdrawall->wallet_address);
		if ($balance_wallet_www < post('amount')) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Insufficient Wallet Balance!';
		}


		if (Self::$data['status']) {
			$userdatas 	= $cekusername->row();
			$userdata 	= userdata();

			$walletcredit     	= $this->usermodel->userWallet('withdrawal', $userdatas->id);

			$walletdebit     	= $this->usermodel->userWallet('withdrawal', $userdata->id);

			$this->db->insert(
				'tb_wallet_balance',
				[
					'w_balance_wallet_id'       => $walletdebit->wallet_id,
					'w_balance_amount'          => post('amount'),
					'w_balance_type'            => 'debit',
					'w_balance_desc'            => 'Transfer Saldo to Username : ' . post('username'),
					'w_balance_date_add'        => sekarang(),
					'w_balance_txid'            => strtolower(random_string('alnum', 64))
				]
			);

			$this->db->insert(
				'tb_wallet_balance',
				[
					'w_balance_wallet_id'       => $walletcredit->wallet_id,
					'w_balance_amount'          => post('amount'),
					'w_balance_type'            => 'credit',
					'w_balance_desc'            => 'Recived Saldo from Username : ' . $userdata->username,
					'w_balance_date_add'        => sekarang(),
					'w_balance_txid'            => strtolower(random_string('alnum', 64))
				]
			);


			$this->db->insert(
				'tb_transfer',
				[
					'transfer_userid'			=> userid(),
					'transfer_destiid'			=> $userdatas->id,
					'transfer_usernamedes'		=> post('username'),
					'transfer_description'		=> 'Transfer Saldo to Username : ' . post('username'),
					'transfer_amount'			=> post('amount'),
					'transfer_date'				=> sekarang(),
					'transfer_code'				=> strtolower(random_string('alnum', 64)),
				]
			);

			Self::$data['message']      = 'Transfer Success!';
			Self::$data['heading']      = 'Success';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     	= 'Error';
			Self::$data['type']     	= 'error';
		}
		return Self::$data;
	}


	function confirminvoice()
	{

		$this->db->where('invoice_code', post('code'));
		$this->db->where('invoice_user_id', userid());
		$this->db->where('invoice_status', 'menunggu pembayaran');
		$cekinvoice = $this->db->get('tb_users_invoice');
		if ($cekinvoice->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "invoice is invalid or has been paid";
		} else {
			$config['upload_path']          = './assets/image/';
			$config['allowed_types']        = 'gif|jpg|png|jpeg';
			$config['max_size']             = '9999';
			$config['max_width']            = '9999';
			$config['max_height']           = '9999';
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if (!$this->upload->do_upload('filetransfer')) {
				Self::$data['status']     = false;
				Self::$data['message']     = $this->upload->display_errors();
			}
		}

		$this->form_validation->set_rules('code', 'Code Invoice', 'required');
		$this->form_validation->set_rules('adminbank', 'Bank Admin', 'required');
		$this->form_validation->set_rules('user_bank', 'Bank Account', 'required');
		$this->form_validation->set_rules('user_norek', 'Account number', 'required');
		$this->form_validation->set_rules('user_pemilikrek', 'Account Owner', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {
			$invoicedata 			= $cekinvoice->row();
			$random_string         	= strtolower(random_string('alnum', 64));
			$uploaded               = $this->upload->data();

			$this->db->insert('tb_users_pembayaran', [
				'pembayaran_invoice_id'			=> $invoicedata->invoice_id,
				'pembayaran_ke'             	=> post('adminbank'),
				'pembayaran_bank_jenis'         => post('user_bank'),
				'pembayaran_atas_nama'          => post('user_pemilikrek'),
				'pembayaran_rekening'           => post('user_norek'),
				'pembayaran_nominal'            => $invoicedata->invoice_amount,
				'pembayaran_struk'              => $uploaded['file_name'],
				'pembayaran_date_add'           => sekarang(),
				'pembayaran_code'               => $random_string,
			]);

			$this->db->update(
				'tb_users_invoice',
				[
					'invoice_status'				=> 'diproses'
				],
				[
					'invoice_id'					=> $invoicedata->invoice_id
				]
			);

			Self::$data['message']      = 'the bill has been confirmed';
			Self::$data['heading']      = 'Success';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     	= 'Error';
			Self::$data['type']     	= 'error';
		}

		return Self::$data;
	}

	function transferWallet()
	{

		/**
		
			TODO:
			- validate address destination
			- tidak boleh transfer ke address sendiri
			- validate balance withdrawal cukup
			- validate password
			- validate form
		
		 */
		$userdata 					= userdata();


		$user_wallet_withdrawal		= $this->usermodel->userWallet('withdrawal');
		$get_balance 				= $this->walletmodel->walletAddressBalance($user_wallet_withdrawal->wallet_address);
		if ($get_balance < post('amount')) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= 'Saldo wallet Anda tidak cukup !';
		}

		$this->db->where('wallet_type', 'register');
		$this->db->where('wallet_address', post('wallet_address'));
		$get_wallet_destination 	= $this->db->get('tb_users_wallet');
		if ($get_wallet_destination->num_rows() == 0) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= 'Alamat wallet yang Anda masukkan tidak valid';
		} else {
			$wallet_destination 	= $get_wallet_destination->row();

			if ($wallet_destination->wallet_address == $user_wallet_withdrawal->wallet_address) {
				Self::$data['status'] 	= false;
				Self::$data['message'] 	= 'Tidak bisa melakukan transfer ke alamat sendiri !';
			}
		}

		if ($userdata->gauth_status == 'off') {

			$current_password 	= $this->ion_auth->hash_password_db(userid(), post('current_password'));
			if (!$current_password) {
				Self::$data['status'] 	= false;
				Self::$data['message'] 	= 'Password yang Anda masukkan tidak sesuai !';
			}

			$this->form_validation->set_rules('current_password', 'password Anda', 'required');
		} else {

			$checkResult = $this->googleauthenticator->verifyCode($userdata->gauth_secret, post('oneCodeAuth'), 2);
			if (!$checkResult) {
				Self::$data['status'] 	= false;
				Self::$data['message'] 	= 'OTP Authenticator tidak valid !';
			}

			$this->form_validation->set_rules('oneCodeAuth', 'Kode OTP', 'required');
		}


		$this->form_validation->set_rules('wallet_address', 'alamat wallet', 'required');
		$this->form_validation->set_rules('amount', 'jumlah transfer', 'required|numeric|greater_than[0]');
		if ($this->form_validation->run() == FALSE) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= validation_errors(' ', '<br/>');
		}

		Self::$data['status'] 	= false;
		Self::$data['message'] 	= 'Transfer disabled by Administrator !';

		if (Self::$data['status']) {

			$trx_id 				= hash('sha256', random_string('alnum', 90));

			//mengurangi saldo wallet withdrawal
			$this->db->insert('tb_wallet_balance', [
				'w_balance_wallet_id'  	=> $user_wallet_withdrawal->wallet_id,
				'w_balance_amount'  	=> post('amount'),
				'w_balance_type'  		=> 'debit',
				'w_balance_desc'  		=> 'Transfer saldo ke: ' . $wallet_destination->wallet_address,
				'w_balance_date_add'  	=> sekarang(),
				'w_balance_txid'		=> $trx_id
			]);

			//menambah saldo destination
			$this->db->insert('tb_wallet_balance', [
				'w_balance_wallet_id'  	=> $wallet_destination->wallet_id,
				'w_balance_amount'  	=> post('amount'),
				'w_balance_type'  		=> 'credit',
				'w_balance_desc'  		=> 'Transfer saldo dari: ' . $user_wallet_withdrawal->wallet_address,
				'w_balance_date_add'  	=> sekarang(),
				'w_balance_txid'		=> $trx_id
			]);

			if ($this->ion_auth->is_admin()) {
				$userdatatujuan = userdata(['id' => $wallet_destination->wallet_user_id]);
				//laporan
				$this->db->insert('tb_laporan', [
					'laporan_judul' 		=> 'Laporan coin keluar via Transfer kepada username: ' . $userdatatujuan->username,
					'laporan_jenis' 		=> 'keluar',
					'laporan_jumlah_coin' 	=> post('amount'),
					'laporan_date' 			=> sekarang()
				]);
			}

			//create user logs


			Self::$data['heading']	= 'Berhasil';
			Self::$data['message']	= 'Transfer saldo berhasil';
			Self::$data['type']		= 'success';
		} else {

			Self::$data['heading']	= 'Gagal';
			Self::$data['type']		= 'error';
		}

		return Self::$data;
	}
}

/* End of file Wallet.php */
/* Location: ./application/modules/Postdata/models/user_post/Wallet.php */