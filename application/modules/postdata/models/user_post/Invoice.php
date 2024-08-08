<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends CI_Model
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

	function cancelinv()
	{
		$this->db->where('inv_code', post('code'));
		$cekinvoice = $this->db->get('tb_invoice');
		if ($cekinvoice->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Invoice Tidak Ditemukan';
		}

		$this->form_validation->set_rules('code', 'Invoice Code', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {
			$inv = $cekinvoice->row();

			$this->db->insert(
				'tb_notif',
				[
					'notif_useridto' => $inv->inv_userid_from,
					'notif_useridfrom' => userid(),
					'notif_desc' => 'Transaksi DITOLAK oleh distributor ' . userdata(['id' => $inv->inv_user_id])->username . ' Silahkan Hubungi Distributor atau Pusat',
					'notif_tipe' => 'transaksi',
					'notif_date' => sekarang(),
					'notif_code' => $this->input->post('code'),
				]
			);

			$this->db->where('inv_code', $inv->inv_code);
			$this->db->delete('tb_invoice');

			Self::$data['heading'] 		= 'Berhasil';
			Self::$data['message'] 		= 'Transaksi Telah Dibatalkan!';
			Self::$data['type']	 		= 'success';
		} else {
			Self::$data['heading'] 		= 'Gagal';
			Self::$data['type']	 		= 'error';
		}

		return Self::$data;
	}

	function approveinv()
	{
		$this->db->where('inv_code', post('code'));
		$cekinvoice = $this->db->get('tb_invoice');
		if ($cekinvoice->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = 'Invoice Tidak Ditemukan';
		}

		$this->form_validation->set_rules('code', 'Invoice Code', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {
			$inv = $cekinvoice->row();
			/*===========================================
	        =            UPDATE STATUS INVOICE           =
	        ============================================*/
			$this->db->update('tb_invoice', array('inv_status' => 'process'), array('inv_code' => $inv->inv_code));
			$namaproduk = "";
			$qty = json_decode($inv->inv_qty);
			$i = 0;
			$isiqty = count($qty) - 1;

			foreach (json_decode($inv->inv_produkid) as $idprod) {
				$getproduk = $this->db->where('produk_id', $idprod)->get('tb_produk')->row();
				$tandabaca = ($isiqty == $i) ? "." : ", ";
				$namaproduk = $getproduk->produk_nama . ' ' . $qty[$i] . ' Pcs' . $tandabaca;
				/*============================================
				=       	DEBIT STOCK DARI PENGIRIM      	 =
				============================================*/
				$this->db->insert('tb_stok', [
					'stok_produkid'				=> $idprod,
					'stok_penerima_userid'		=> $inv->inv_userid_from,
					'stok_pengirim_userid'    	=>  $inv->inv_user_id,
					'stok_amount'				=> $qty[$i],
					'stok_type'		=> 'debit',
					'stok_desc'		=> 'Penjualan',
					'stok_date'		=> sekarang(),
					'stok_code'		=> random_string('alnum', 64),
				]);
				$i++;
			}


			$this->db->insert('tb_history_penjualan', [
				'histpenj_userid'		=> $inv->inv_userid_from,
				'histpenj_invkode'		=> $inv->inv_orderkode,
				'histpenj_invproduk' 	=> str_replace([',', '.'], ['<br>', ''], $namaproduk),
				'histpenj_desc'			=> 'Mengirim ' . $namaproduk . ' Kepada ' . userdata(['id' => $inv->inv_user_id])->user_fullname . ', ' . '' . userdata(['id' => $inv->inv_user_id])->username,
				'histpenj_date'			=> sekarang(),
				'histpenj_code'			=> random_string('alnum', 64)
			]);

			/*============================================
				=       	Bonus Omset Reseller	      	 =
				============================================*/

			$totalqty = array_sum($qty);
			$this->bonus_omset1($inv->inv_userid_from, $totalqty);

			$this->bonus_omsetdistributor($inv->inv_userid_from, $totalqty);


			/*============================================
				=       	insert tb transaksi		      	 =
				============================================*/

			$this->db->where('id', $inv->inv_userid_from);
			$getuser = $this->db->get('tb_users')->row();
			$upline = $getuser->referral_id;

			$this->db->insert('tb_transaksi', [
				'transaksi_userid'				=> $inv->inv_userid_from,
				'transaksi_user_referralid'		=> $upline,
				'transaksi_inv'					=> $inv->inv_orderkode,
				'transaksi_qty'					=> $totalqty,
				'transaksi_date'				=> sekarang(),
				'transaksi_code'				=> random_string('alnum', 64),
				'transaksi_desc'				=> 'pembelian reseller',
			]);
			//update notif
			$this->db->where('notif_code', $this->input->post('code'));
			$this->db->update('tb_notif', [
				'notif_status'					=> 'read',
			]);
			//set notif untuk menerima
			$this->db->insert(
				'tb_notif',
				[
					'notif_useridto' => $inv->inv_userid_from,
					'notif_useridfrom' => userid(),
					'notif_desc' => 'Transaksi sudah diproses oleh distributor ' . userdata(['id' => userid()])->username . ' harap Menunggu Barang datang ke Alamat Tujuan Anda',
					'notif_tipe' => 'transaksi',
					'notif_date' => sekarang(),
					'notif_code' => $this->input->post('code'),
				]
			);




			// verified member
			$typeuser = userdata(['id' => $inv->inv_userid_from])->user_type;
			$statususer = userdata(['id' => $inv->inv_userid_from])->user_type_status;
			$this->db->select(' transaksi_userid, SUM(transaksi_qty) AS total_qty ');
			$this->db->where('transaksi_userid', $inv->inv_userid_from);
			$this->db->group_by('transaksi_userid');
			$gettransaksi = $this->db->get('tb_transaksi')->row();
			if ($statususer == 'unverified' && $typeuser == 'reseller' && $gettransaksi->total_qty >= 50) {
				$this->db->where('id', $inv->inv_userid_from);
				$this->db->update('tb_users', ['user_type_status' => 'verified']);
			} elseif ($typeuser !== 'reseller') {
				$this->db->where('id', $inv->inv_userid_from);
				$this->db->update('tb_users', ['user_type_status' => 'verified']);
			}


			Self::$data['heading'] 		= 'Berhasil';
			Self::$data['message'] 		= 'Transaksi Telah Dikonfirmasi!';
			Self::$data['type']	 		= 'success';
		} else {

			Self::$data['heading'] 		= 'Gagal';
			Self::$data['type']	 		= 'error';
		}

		return Self::$data;
	}


	function konfirmasiReward()
	{
		$this->db->where('userreward_code', $this->input->post('code'));
		$cekReward = $this->db->get('tb_userreward');
		if ($cekReward->num_rows() == 0) {
			Self::$data['status']  = false;
			Self::$data['message'] = "Klaim Reward Tidak Valid";
		} else {
			$rewardid = $cekReward->row()->userreward_rewardid;
			$this->db->where('reward_id', $rewardid);
			$detecreward = $this->db->get('tb_reward');
			if ($detecreward->num_rows() == 0) {
				Self::$data['status']  = false;
				Self::$data['message'] = "Reward Tidak Valid";
			}
		}

		$this->form_validation->set_rules('code', 'Kode Reward', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}


		if (Self::$data['status']) {
			$reward = $cekReward->row();
			$perreward = $detecreward->row();

			$this->db->insert('tb_history_klaim', [
				'histklaim_userid'     => $reward->userreward_userid,
				'histklaim_rewardid'   => $reward->userreward_rewardid,
				'histklaim_desc'       => 'klaim reward ' . $perreward->reward_nama,
				'histklaim_date'       => sekarang(),
				'histklaim_code'       => random_string('alnum', 64),
			]);

			$this->db->where('userreward_code', $reward->userreward_code);
			$this->db->update('tb_userreward', [
				'userreward_status'    => 'success',
			]);

			$this->db->insert('tb_poinrw', [
				'poinrw_userid'			=> $reward->userreward_userid,
				'poinrw_total'			=> $perreward->reward_poin,
				'poinrw_desc'			=> "klaim reward " . $perreward->reward_nama,
				'poinrw_date'			=> sekarang(),
				'poinrw_tipe'			=> 'debit'
			]);


			Self::$data['message']      = 'Konfirmasi Reward Berhasil';
			Self::$data['heading']      = 'Berhasil';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading'] = 'Error';
			Self::$data['type']    = 'error';
		}

		return Self::$data;
	}

	function rejectklaim()
	{
		$this->db->where('userreward_code', $this->input->post('code'));
		$cekReward = $this->db->get('tb_userreward');
		if ($cekReward->num_rows() == 0) {
			Self::$data['status']  = false;
			Self::$data['message'] = "Klaim Reward Tidak Valid";
		} else {
			$rewardid = $cekReward->row()->userreward_rewardid;
			$this->db->where('reward_id', $rewardid);
			$detecreward = $this->db->get('tb_reward');
			if ($detecreward->num_rows() == 0) {
				Self::$data['status']  = false;
				Self::$data['message'] = "Reward Tidak Valid";
			}
		}

		$this->form_validation->set_rules('code', 'Kode Reward', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {
			$reward = $cekReward->row();

			$this->db->where('userreward_code', $reward->userreward_code);
			$this->db->update('tb_userreward', [
				'userreward_status'    => 'reject',
			]);

			Self::$data['message']      = 'Reject Reward Berhasil';
			Self::$data['heading']      = 'Berhasil';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading'] = 'Error';
			Self::$data['type']    = 'error';
		}

		return Self::$data;
	}

	function klaimrw()
	{
		$this->db->where('reward_code', $this->input->post('code'));
		$getreward = $this->db->get('tb_reward');
		if ($getreward->num_rows() == 0) {
			Self::$data['status']       = false;
			Self::$data['message']      = 'Paket Repeat Order Tidak Valid!';
		} else {
			$reward = $getreward->row();
			if ($this->usermodel->poinreward(userid()) < $reward->reward_poin) {
				Self::$data['status']       = false;
				Self::$data['message']      = 'Point Anda Tidak Cukup!';
			}
		}


		$this->form_validation->set_rules('code', 'Kode Reward', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {
			$reward  = $getreward->row();
			$userdata = userdata();

			// $this->db->order_by('titiklevel_id', 'asc');
			// $this->db->where('titiklevel_downlineid', userid());
			// $this->db->where('titiklevel_level', 1);
			// $cektitik  = $this->db->get('tb_titiklevel')->row();	

			// $atasnya = userdata(['id' => $cektitik->titiklevel_userid])->id;

			/*=======================================
			=			INSERT USER REWARD			=
			========================================*/
			$this->db->insert('tb_userreward', [
				'userreward_rewardid'	=> $reward->reward_id,
				'userreward_userid'		=> userid(), //Cari ID Atasnya
				'userreward_account'	=> $userdata->user_bank_account,
				'userreward_bank'		=> $userdata->user_bank_name,
				'userreward_number'		=> $userdata->user_bank_number,
				'userreward_date'		=> sekarang(),
				'userreward_code'		=> random_string('alnum', 64)
			]);

			Self::$data['message']      = 'Klaim Reward Berhasil';
			Self::$data['heading']      = 'Berhasil';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     	= 'Error';
			Self::$data['type']     	= 'error';
		}

		return Self::$data;
	}

	function repeat_order()
	{
		if (!$this->ion_auth->hash_password_db(userid(), post('ro_password'))) {
			Self::$data['status']       = false;
			Self::$data['message']      = 'Konfirmasi Password Anda Tidak Sesuai!';
		}

		$this->db->where('package_code', $this->input->post('ro_paket'));
		$gettttt = $this->db->get('tb_packages');
		if ($gettttt->num_rows() == 0) {
			Self::$data['status']       = false;
			Self::$data['message']      = 'Paket Repeat Order Tidak Valid!';
		}

		$this->form_validation->set_rules('ro_paket', 'Paket RO', 'required');
		$this->form_validation->set_rules('ro_total', 'Total RO', 'required');
		$this->form_validation->set_rules('ro_password', 'Password', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		$this->db->where('invoice_user_id', userid());
		$this->db->where('invoice_type', 'ro');
		$this->db->where('invoice_status !=', 'success');
		$cekinvoic = $this->db->get('tb_users_invoice');
		if ($cekinvoic->num_rows() != 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Anda Memiliki Transaksi Aktif, Mohon Menunggu Selesai Transaksi Sebelumnya";
		}

		if (preg_replace('/[^0-9.]+/', '', preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $this->input->post('ro_total'))) < 1) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Minimal Transaksi Repeat Order 1";
		}

		if (Self::$data['status']) {
			$datapaket = $gettttt->row();
			$total 		= (int)preg_replace('/[^0-9.]+/', '', preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $this->input->post('ro_total')));
			$rannnnnnn 	= rand(300, 999);

			$this->db->insert(
				'tb_users_invoice',
				[
					'invoice_package_id'		=> $datapaket->package_id,
					'invoice_type'				=> 'ro',
					'invoice_user_id'			=> userid(),
					'invoice_total'				=> $total,
					'invoice_amount'			=> ((int)$datapaket->package_price * $total),
					'invoice_subamount'			=> ((int)$datapaket->package_price * $total) + $rannnnnnn,
					'invoice_kodeinv'			=> date('Y') . date('m') . date('d') . $rannnnnnn,
					'invoice_kode_unik'			=> $rannnnnnn,
					'invoice_date_add'			=> sekarang(),
					'invoice_code'				=> strtolower(random_string('alnum', 64)),
				]
			);


			Self::$data['message']      = 'Invoice Repeat Order Anda Telah Dibuat, Harap Segara Konfirmasi';
			Self::$data['heading']      = 'Berhasil';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     	= 'Error';
			Self::$data['type']     	= 'error';
		}

		return Self::$data;
	}


	function konfirmasipembayaran()
	{
		$this->db->where('bankadmin_code', $this->input->post('confirm_pembayaran'));
		$cekkbank = $this->db->get('tb_bankadmin');
		if ($cekkbank->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Invalid Payment Method";
		} else {
			$config['upload_path']          = './assets/upload/';
			$config['allowed_types']        = 'gif|jpg|png|jpeg';
			$config['max_size']             = '99999999';
			$config['max_width']            = '99999999';
			$config['max_height']           = '99999999';
			$config['remove_spaces']        = TRUE;
			$config['encrypt_name']         = TRUE;
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if (!$this->upload->do_upload('confirm_fileimg')) {
				Self::$data['status']     = false;
				Self::$data['message']     = $this->upload->display_errors();
			}
		}

		$this->db->where('invoice_status', 'pending');
		$this->db->where('invoice_user_id', userid());
		$this->db->where('invoice_code', $this->input->post('code'));
		$cekinvoice = $this->db->get('tb_users_invoice');
		if ($cekinvoice->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "You Have No Transaction To Confirm";
		}

		$this->form_validation->set_rules('code', 'Transaction Code', 'required');
		$this->form_validation->set_rules('confirm_pembayaran', 'Payment Method', 'required');
		$this->form_validation->set_rules('confirm_account', 'Account in the Name', 'required');
		$this->form_validation->set_rules('confirm_bank', 'Bank Name', 'required');
		$this->form_validation->set_rules('confirm_number', 'Account Number', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {
			$datainvoice 	= $cekinvoice->row();
			$databank 		= $cekkbank->row();
			$uploaded		= $this->upload->data();

			$this->db->insert('tb_users_pembayaran', [
				'pembayaran_invoice_id'				=> $datainvoice->invoice_id,
				'pembayaran_userid'					=> userid(),
				'pembayaran_adbankname'				=> $databank->bankadmin_bankname,
				'pembayaran_adbankaccount'			=> $databank->bankadmin_bankaccount,
				'pembayaran_adbanknumber'			=> $databank->bankadmin_banknumber,
				'pembayaran_bankname'				=> $this->input->post('confirm_bank'),
				'pembayaran_bankaccount'			=> $this->input->post('confirm_account'),
				'pembayaran_banknumber'				=> $this->input->post('confirm_number'),
				'pembayaran_struk'					=> $uploaded['file_name'],
				'pembayaran_date_add'				=> sekarang(),
				'pembayaran_nominal'				=> $datainvoice->invoice_amount,
				'pembayaran_code'					=> strtolower(random_string('alnum', 64)),
			]);


			$this->db->update(
				'tb_users_invoice',
				[
					'invoice_status'				=> 'process',
				],
				[
					'invoice_code'					=> $this->input->post('code')
				]
			);

			/*============================================
            =	            set notifikasi         		=
            ============================================*/





			Self::$data['message']      = 'Invoice Confirmed, Waiting for Admin Confirmation';
			Self::$data['heading']      = 'Success';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     	= 'Error';
			Self::$data['type']     	= 'error';
		}

		return Self::$data;
	}
	///////////////////////////////////////////////////////////////////////////

	function konfirmterima()
	{
		$config['upload_path']          = './assets/upload/';
		$config['allowed_types']        = 'gif|jpg|png|jpeg';
		$config['max_size']             = '99999999';
		$config['max_width']            = '99999999';
		$config['max_height']           = '99999999';
		$config['remove_spaces']        = TRUE;
		$config['encrypt_name']         = TRUE;
		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if (!$this->upload->do_upload('inv_bukti')) {
			Self::$data['status']     = false;
			Self::$data['message']     = $this->upload->display_errors();
		}
		$this->db->where('inv_code', $this->input->post('code'));
		$cekinvoice = $this->db->get('tb_invoice');
		if ($cekinvoice->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Transaksi Tidak Ditemukan";
		}

		$this->form_validation->set_rules('code', 'Invoice Code', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {
			$uploaded		= $this->upload->data();
			$inv 	= $cekinvoice->row();
			$this->db->where('inv_code', $inv->inv_code);
			$this->db->update('tb_invoice', [
				'inv_status'				=> 'success',
				'inv_bukti_pengiriman'		=> $uploaded['file_name'],
			]);

			$namaproduk = "";

			$qty = json_decode($inv->inv_qty);
			$i = 0;
			$isiqty = count($qty) - 1;
			foreach (json_decode($inv->inv_produkid) as $idprod) {
				$getproduk = $this->db->where('produk_id', $idprod)->get('tb_produk')->row();
				$tandabaca = ($isiqty == $i) ? "." : ", ";
				$namaproduk = $getproduk->produk_nama . ' ' . $qty[$i] . ' Pcs' . $tandabaca;

				/*============================================
				=       	CREDIT STOCK KE TARGET      	 =
				============================================*/
				$this->db->insert('tb_stok', [
					'stok_penerima_userid'		=> $inv->inv_userid_from,
					'stok_pengirim_userid'     =>  $inv->inv_user_id,
					'stok_produkid'	=> $idprod,
					'stok_amount'	=> $qty[$i],
					'stok_type'		=> 'credit',
					'stok_desc'		=> 'Pembelian',
					'stok_date'		=> sekarang(),
					'stok_code'		=> random_string('alnum', 64),

				]);

				$i++;
			}

			$this->db->insert('tb_history_penjualan', [
				'histpenj_userid'		=> $inv->inv_user_id,
				'histpenj_invkode'		=> $inv->inv_orderkode,
				'histpenj_invproduk' 	=> str_replace([',', '.'], ['<br>', ''], $namaproduk),
				'histpenj_desc'			=> 'Menerima ' . $namaproduk . ' Kepada ' . userdata(['id' => $inv->inv_userid_from])->user_fullname . ', ' . '' . userdata(['id' => $inv->inv_userid_from])->username,
				'histpenj_date'			=> sekarang(),
				'histpenj_code'			=> random_string('alnum', 64)
			]);

			//update notif
			$this->db->where('notif_code', $this->input->post('code'));
			$this->db->update('tb_notif', [
				'notif_status'					=> 'read',
			]);

			$this->db->insert(
				'tb_notif',
				[
					'notif_useridto' => $inv->inv_userid_from,
					'notif_useridfrom' => userid(),
					'notif_desc' => 'Transaksi sudah diproses oleh distributor ' . userdata(['id' => userid()])->username . ' harap Menunggu Barang datang ke Alamat Tujuan Anda',
					'notif_tipe' => 'transaksi',
					'notif_date' => sekarang(),
					'notif_code' => $this->input->post('code'),
				]
			);

			Self::$data['heading']    = 'Success';
			Self::$data['message']    = 'Unggah Bukti Pengiriman Berhasil';
			Self::$data['type']        = 'success';
		} else {
			Self::$data['heading']    = 'GAGAL';
			Self::$data['type']        = 'error';
		}

		return Self::$data;
	}

	function bonus_omset1($userid, $qty)
	{

		$status         = true;
		$result         = array();
		$this->db->where('id', $userid);
		$getuser = $this->db->get('tb_users')->row();
		$upline = $getuser->referral_id;
		$datauser         = userdata(['id' => $userid]);

		if ($datauser->user_type !== 'reseller') {
			$status = false;
		}
		if ($upline == 1) {
			$status = false;
		}


		if ($status) {
			if ($upline) {
				$wallet             = $this->usermodel->userWallet('withdrawal', $upline);
				$this->db->insert(
					'tb_wallet_balance',
					[
						'w_balance_wallet_id'       => $wallet->wallet_id,
						'w_balance_amount'          => $qty * 500,
						'w_balance_type'            => 'credit',
						'w_balance_desc'            => 'Bonus Omset Pembelian reseller ' . $datauser->username,
						'w_balance_date_add'        => sekarang(),
						'w_balance_txid'            => strtolower(random_string('alnum', 64)),
						'w_balance_ket'             => 'bonus omset reseller',
					]
				);
			}
		}
		return $result;
	}

	function bonus_omsetdistributor($userid, $qty)
	{
		$status         = true;
		$result         = array();
		$this->db->where('id', $userid);
		$getuser = $this->db->get('tb_users')->row();
		$upline = $getuser->referral_id;

		if (userdata(['id' => $userid])->user_type != 'distributor') {
			$status = false;
		}

		if (userdata(['id' => $upline])->user_type != 'distributor') {
			$status = false;
		}

		$datauser         = userdata(['id' => $userid]);
		if ($status) {
			if ($upline) {
				$wallet             = $this->usermodel->userWallet('withdrawal', $upline);
				$this->db->insert(
					'tb_wallet_balance',
					[
						'w_balance_wallet_id'       => $wallet->wallet_id,
						'w_balance_amount'          => $qty * 200,
						'w_balance_type'            => 'credit',
						'w_balance_desc'            => 'Bonus Omset Pembelian Distributor ' . $datauser->username,
						'w_balance_date_add'        => sekarang(),
						'w_balance_txid'            => strtolower(random_string('alnum', 64)),
						'w_balance_ket'             => 'bonus omset Distributor',
					]
				);
			}
		}
		return $result;
	}
}

	/* End of file Invoice.php */
/* Location: ./application/modules/Postdata/models/user_post/Invoice.php */