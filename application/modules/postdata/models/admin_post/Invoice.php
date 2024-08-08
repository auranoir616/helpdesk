<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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
		$this->load->model('emailmodel');
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
			/*============================================
	        =            UPDATE STATUS INVOICE            =
	        ============================================*/
			$this->db->update('tb_invoice', array('inv_status' => 'success'), array('inv_code' => $inv->inv_code));

			/*============================================
	        =        	  	INSERT HISTORY               =
	        ============================================*/
			$qty = json_decode($inv->inv_qty);
			$i = 0;
			foreach (json_decode($inv->inv_produkid) as $idprod) {
				$getproduk = $this->db->where('produk_id', $idprod)->get('tb_produk')->row();

				// HISTORY PENGIRIM
				$this->db->insert('tb_history_penjualan', [
					'histpenj_userid'		=> $inv->inv_userid_from,
					'histpenj_invproduk'		=> $idprod,
					'histpenj_invkode'		=> post('code'),
					'histpenj_desc'	        => 'Mengirim ' . $getproduk->produk_nama . ' ' . $qty[$i] . 'x Kepada ' . userdata(['id' => $inv->inv_user_id])->user_fullname,
					'histpenj_date'	        => sekarang(),
					'histpenj_code'	        => random_string('alnum', 64)
				]);


				// HISTORY PENERIMA
				$this->db->insert('tb_history_penjualan', [
					'histpenj_userid'		=> $inv->inv_user_id,
					'histpenj_invproduk'		=> $idprod,
					'histpenj_invkode'		=> post('code'),
					'histpenj_desc'	        => 'Menerima ' . $getproduk->produk_nama . ' ' . $qty[$i] . 'x Dari ' . userdata(['id' => $inv->inv_userid_from])->user_fullname,
					'histpenj_date'	        => sekarang(),
					'histpenj_code'	        => random_string('alnum', 64)
				]);

				/*============================================
				=        	  	DEBIT STOCK              	=
				============================================*/
				$this->db->insert('tb_stok', [
					'stok_produkid'	=> $idprod,
					'stok_amount'	=> $qty[$i],
					'stok_type'		=> 'debit',
					'stok_penerima_userid'		=> $inv->inv_user_id,
					'stok_desc'		=> 'penjualan',
					'stok_date'		=> sekarang(),
					'stok_code'		=> random_string('alnum', 64),
					'stok_pengirim_userid'     =>  $inv->inv_userid_from
	
				]);

				/*============================================
				=  	PROSES INSERT POINT KE TARGET USER       =
				============================================*/

				/*
						TODO : (BELUM SELESAI)
						- RUMUS INI TERAPKAN
						// $qty = 1;// inputan dari depan
						
						// $point = 10; // produk_poin
						// $minpem = 1; // Produk_minpem
						
						// if($point < $minpem){
						// echo floor($qty / $point);
						// }else{
						// echo $qty * $point;

						- JIKA POINT DIDAPAT ADALAH 0 MAKA TIDAK USAH INSERT TB_POINTRW

				*/

				// $nominalpoint = 0;

				// if ($getproduk->produk_poin < $getproduk->produk_min_pembelian) {
				// 	$nominalpoint =  floor($qty[$i] / $getproduk->produk_poin);
				// } else {
				// 	$nominalpoint =  $qty[$i] * $getproduk->produk_poin;
				// }

				// Hitung berapa set poin yang didapat berdasarkan minimum pembelian
				// $nominalpoint = ($qty[$i] >= $getproduk->produk_min_pembelian) ? floor($qty[$i] / $getproduk->produk_min_pembelian) * $getproduk->produk_poin : 0;

				// if ($nominalpoint != 0) {
				// 	$this->db->insert('tb_poinrw', [
				// 		'poinrw_userid'	=> $inv->inv_user_id,
				// 		'poinrw_total'	=> $nominalpoint,
				// 		'poinrw_desc'	=> 'Menerima Produk ' . $getproduk->produk_nama . ' Sebanyak ' . $qty[$i] . 'pcs',
				// 		'poinrw_date'	=> sekarang(),
				// 		'poinrw_tipe'	=> 'credit',
				// 	]);
				// }


				$i++;
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

	function updatePoin()
	{
		// $this->db->where('poinrw_userid', $this->input->post('id'));
		$this->form_validation->set_rules('poinrw_userid', 'Poin Member', 'required');
		$this->form_validation->set_rules('poinrw_tipe', 'Tipe Transaksi', 'required');
		$this->form_validation->set_rules('poinrw_total', 'Total Poin', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status'] = false;
			Self::$data['message'] = validation_errors(' ', '<br/>');
		}
		$poinDesc = '';
		if ($this->input->post('poinrw_tipe') == 'credit') {
			$poinDesc = 'Penambahan Poin Dari Pusat';
		} else if ($this->input->post('poinrw_tipe') == 'debit') {
			$poinDesc = 'Pengurangan Poin Dari Pusat';
		}

		if (Self::$data['status']) {
			$this->db->insert('tb_poinrw', [
				'poinrw_userid'			=> $this->input->post('poinrw_userid'),
				'poinrw_total'			=> $this->input->post('poinrw_total'),
				'poinrw_desc'			=> $poinDesc,
				'poinrw_date'			=> sekarang(),
				'poinrw_tipe'			=> $this->input->post('poinrw_tipe')
			]);
			Self::$data['message'] = 'Update Poin Berhasil';
			Self::$data['heading'] = 'Berhasil';
			Self::$data['type'] = 'success';
		} else {
			Self::$data['status'] = false;
			Self::$data['message'] = 'Gagal Update Poin.';
			Self::$data['heading'] = 'Error';
			Self::$data['type'] = 'error';
		}

		return Self::$data;
	}










	/*---------- DIBAWAH ADALAH FUNCTION YANG MUNGKIN MASIH DISFUNGSI ----------*/

	// function rejectinv()
	// {
	// 	$this->db->where('pembayaran_status', 'pending');
	// 	$this->db->where('pembayaran_code', post('code'));
	// 	$this->db->join('tb_users_invoice', 'invoice_id = pembayaran_invoice_id');
	// 	$this->db->join('tb_users', 'invoice_user_id = id');
	// 	$this->db->join('tb_packages', 'package_id = invoice_package_id');
	// 	$cekdatainvoice = $this->db->get('tb_users_pembayaran');
	// 	if ($cekdatainvoice->num_rows() == 0) {
	// 		Self::$data['status']     = false;
	// 		Self::$data['message']     = "Invoice Data Not Found";
	// 	}

	// 	$this->form_validation->set_rules('code', 'Invoice Code', 'required');
	// 	if (!$this->form_validation->run()) {
	// 		Self::$data['status']     = false;
	// 		Self::$data['message']     = validation_errors(' ', '<br/>');
	// 	}
	// 	if (Self::$data['status']) {
	// 		$datainvoice 	= $cekdatainvoice->row();

	// 		/*============================================
	//         =            UPDATE STATUS INVOICE            =
	//         ============================================*/
	// 		$this->db->update('tb_users_invoice', array('invoice_status' => 'pending'), array('invoice_id' => $datainvoice->invoice_id));

	// 		# *HAPUS FILE UNGGAHAN YANG ADA
	// 		if (file_exists('./assets/upload/' . $datainvoice->pembayaran_struk)) {
	// 			unlink($_SERVER['DOCUMENT_ROOT'] . '/assets/upload/' . $datainvoice->pembayaran_struk);
	// 		}
	// 		$this->db->where('pembayaran_invoice_id', $datainvoice->invoice_id);
	// 		$this->db->delete('tb_users_pembayaran');


	// 		Self::$data['heading'] 		= 'Berhasil';
	// 		Self::$data['message'] 		= 'Transaksi Telah Dibatalkan!';
	// 		Self::$data['type']	 		= 'success';
	// 	} else {

	// 		Self::$data['heading'] 		= 'Gagal';
	// 		Self::$data['type']	 		= 'error';
	// 	}

	// 	return Self::$data;
	// }

	// function approveinv()
	// {
	// 	$this->db->where('pembayaran_status', 'pending');
	// 	$this->db->where('pembayaran_code', post('code'));
	// 	$this->db->join('tb_users_invoice', 'invoice_id = pembayaran_invoice_id');
	// 	$this->db->join('tb_users', 'invoice_user_id = id');
	// 	$this->db->join('tb_packages', 'package_id = invoice_package_id');
	// 	$cekdatainvoice = $this->db->get('tb_users_pembayaran');
	// 	if ($cekdatainvoice->num_rows() == 0) {
	// 		Self::$data['status']     = false;
	// 		Self::$data['message']     = "Invoice Data Not Found";
	// 	}

	// 	$this->form_validation->set_rules('code', 'Invoice Code', 'required');
	// 	if (!$this->form_validation->run()) {
	// 		Self::$data['status']     = false;
	// 		Self::$data['message']     = validation_errors(' ', '<br/>');
	// 	}

	// 	if (Self::$data['status']) {
	// 		$datainvoice 	= $cekdatainvoice->row();
	// 		$trx_id 		= hash('SHA256', random_string('alnum', 16));

	// 		/*============================================
	//         =            UPDATE STATUS INVOICE            =
	//         ============================================*/
	// 		$this->db->update('tb_users_invoice', array('invoice_status' => 'success'), array('invoice_id' => $datainvoice->invoice_id));
	// 		$this->db->update('tb_users_pembayaran', array('pembayaran_status' => 'approve'), array('pembayaran_invoice_id' => $datainvoice->invoice_id));


	// 		/*============================================
	//         =        	  	INSERT USERPAKET             =
	//         ============================================*/
	// 		$this->db->insert(
	// 			'tb_userpaket',
	// 			[
	// 				'userpaket_userid'			=>	$datainvoice->invoice_user_id,
	// 				'userpaket_pktid'			=>	$datainvoice->invoice_package_id,
	// 				'userpaket_amount'			=>	$datainvoice->invoice_total,
	// 				'userpaket_roi'				=>	$datainvoice->package_roi,
	// 				'userpaket_datestart'		=>	sekarang(),
	// 				'userpaket_dateend'			=>	date('Y-m-d H:i:s', strtotime('+' . $datainvoice->package_roiday . ' day', now())),
	// 				'userpaket_lastdate'		=> 	date('Y-m-d', strtotime(sekarang())),
	// 				'userpaket_code'			=>	random_string('alnum', 64)
	// 			]
	// 		);

	// 		/*============================================
	//         =        	   INSERT BONUS LEVEL            =
	//         ============================================*/
	// 		$this->bonuslevel($datainvoice->id, $datainvoice->id, $datainvoice->invoice_total, 1);


	// 		Self::$data['heading'] 		= 'Berhasil';
	// 		Self::$data['message'] 		= 'Transaksi Telah Dikonfirmasi!';
	// 		Self::$data['type']	 		= 'success';
	// 	} else {

	// 		Self::$data['heading'] 		= 'Gagal';
	// 		Self::$data['type']	 		= 'error';
	// 	}

	// 	return Self::$data;
	// }

	// function bonuslevel($user_id = null, $user_id_from = null, $harga = 0, $level = 1)
	// {
	// 	$result 		= array();
	// 	$status 		= true;

	// 	$datauser 		= userdata(['id' => $user_id]);
	// 	$userdata 		= userdata(['id' => $user_id_from]);

	// 	// GET PAKET
	// 	$this->db->where('package_id', (int)1);
	// 	$get_packages 		= $this->db->get('tb_packages')->row();

	// 	$array_term_level 	= json_decode($get_packages->package_level);
	// 	if ($level > count($array_term_level)) {
	// 		$status = false;
	// 	}

	// 	if ($userdata->upline_id == 0) {
	// 		$status = false;
	// 	}

	// 	$uplinedata 	= userdata(['id' => $userdata->upline_id]);

	// 	if ($status) {
	// 		if ($uplinedata) {
	// 			$wallet     		= $this->usermodel->userWallet('withdrawal', $uplinedata->id);

	// 			$this->db->insert(
	// 				'tb_wallet_balance',
	// 				[
	// 					'w_balance_wallet_id'       => $wallet->wallet_id,
	// 					'w_balance_amount'          => ($array_term_level[$level - 1] / 100) * $harga,
	// 					'w_balance_type'            => 'credit',
	// 					'w_balance_desc'            => 'Bonus Level Ke ' . $level . ', Username : ' . $datauser->username,
	// 					'w_balance_date_add'        => sekarang(),
	// 					'w_balance_txid'            => strtolower(random_string('alnum', 64)),
	// 					'w_balance_ket'				=> 'unilevel',
	// 				]
	// 			);

	// 			$this->bonuslevel($datauser->id, $uplinedata->id, $harga, $level + 1);
	// 		}
	// 	}
	// 	return $result;
	// }

	// function invoiceapprove()
	// {
	// 	$this->db->where('pembayaran_status', 'pending');
	// 	$this->db->where('pembayaran_code', post('code'));
	// 	$this->db->join('tb_users_invoice', 'invoice_id = pembayaran_invoice_id');
	// 	$this->db->join('tb_users', 'invoice_user_id = id');
	// 	$this->db->join('tb_packages', 'package_id = invoice_package_id');
	// 	$cekdatainvoice = $this->db->get('tb_users_pembayaran');
	// 	if ($cekdatainvoice->num_rows() == 0) {
	// 		Self::$data['status']     = false;
	// 		Self::$data['message']     = "Invoice Data Not Found";
	// 	}

	// 	$this->form_validation->set_rules('code', 'Invoice Code', 'required');
	// 	if (!$this->form_validation->run()) {
	// 		Self::$data['status']     = false;
	// 		Self::$data['message']     = validation_errors(' ', '<br/>');
	// 	}

	// 	if (Self::$data['status']) {
	// 		$datainvoice 	= $cekdatainvoice->row();
	// 		$trx_id 		= hash('SHA256', random_string('alnum', 16));

	// 		/*============================================
	//         =            UPDATE STATUS INVOICE            =
	//         ============================================*/
	// 		$this->db->update('tb_users_invoice', array('invoice_status' => 'success'), array('invoice_id' => $datainvoice->invoice_id));
	// 		$this->db->update('tb_users_pembayaran', array('pembayaran_status' => 'approve'), array('pembayaran_invoice_id' => $datainvoice->invoice_id));

	// 		/*============================================
	//         =       INPUT LANDING UNTUK USER AKTIF  	=
	//         ============================================*/
	// 		$this->db->insert('tb_lending', [
	// 			'lending_userid'            => $datainvoice->id,
	// 			'lending_package_id'        => $datainvoice->package_id,
	// 			'lending_package'           => $datainvoice->package_name,
	// 			'lending_amount'            => $datainvoice->package_price,
	// 			'lending_source'            => 'direct_transfer',
	// 			'lending_datestart'         => sekarang(),
	// 			'lending_dateend'           => date('Y-m-d 23:59:59', strtotime('+50 month', now())),
	// 		]);

	// 		/*============================================
	//         =		      INPUT POIN REWARD  		   =
	//         ============================================*/
	// 		$this->db->insert(
	// 			'tb_poinrw',
	// 			[
	// 				'poinrw_userid'		=> $datainvoice->referral_id,
	// 				'poinrw_pktid'		=> $datainvoice->package_id,
	// 				'poinrw_total'		=> (int)1,
	// 				'poinrw_tipe'		=> 'credit',
	// 				'poinrw_desc'		=> 'Sponsor Bonus From ' . $datainvoice->username . ' Registration',
	// 				'poinrw_date'		=> sekarang(),
	// 				'poinrw_code'		=> strtolower(random_string('alnum', 64)),
	// 			]
	// 		);

	// 		/*============================================
	//         =		      INPUT BONUS SPONSOR  		   =
	//         ============================================*/
	// 		$bonus_sponsor 	= ($datainvoice->package_sponsor / 100) * $datainvoice->package_price;
	// 		$wallet     		= $this->usermodel->userWallet('withdrawal', $datainvoice->referral_id);

	// 		$this->db->insert(
	// 			'tb_wallet_balance',
	// 			[
	// 				'w_balance_wallet_id'       => $wallet->wallet_id,
	// 				'w_balance_amount'          => $bonus_sponsor,
	// 				'w_balance_type'            => 'credit',
	// 				'w_balance_desc'            => 'Sponsor Bonus From ' . $datainvoice->username . ' Registration',
	// 				'w_balance_date_add'        => sekarang(),
	// 				'w_balance_txid'            => strtolower(random_string('alnum', 64)),
	// 				'w_balance_ket'				=> 'sponsor',
	// 			]
	// 		);

	// 		/*============================================
	//         =		      INPUT ROI AKTIF  		   		=
	//         ============================================*/
	// 		$array_data = array();
	// 		$this->db->where('package_id', (int)$datainvoice->package_id);
	// 		$gettttt = $this->db->get('tb_packages');
	// 		$array_bonus     = json_decode($gettttt->row()->package_roi);
	// 		$setplus = $gettttt->row()->package_day;
	// 		foreach ($array_bonus as $roi) {
	// 			$roi_package	= $gettttt->row()->package_price;
	// 			$roi_total		= $roi;
	// 			array_push($array_data, date('Y-m-d', strtotime('+' . $setplus . ' day', now())));
	// 			$setplus    += 10;
	// 		}

	// 		$arra_roi = [
	// 			'paket'		=> $roi_package,
	// 			'total'		=> $roi_total,
	// 			'tanggal'	=> json_encode($array_data),
	// 		];

	// 		$this->db->insert(
	// 			'tb_pktactive',
	// 			[
	// 				'pktactive_userid'		=> $datainvoice->id,
	// 				'pktactive_datelist'	=> $arra_roi['tanggal'],
	// 				'pktactive_pkgamount'	=> $arra_roi['paket'],
	// 				'pktactive_amount'		=> $arra_roi['total'],
	// 				'pktactive_date'		=> sekarang(),
	// 				'pktactive_status'		=> 'active',
	// 				'pktactive_code'		=> strtolower(random_string('alnum', 64)),
	// 			]
	// 		);


	// 		Self::$data['heading'] 		= 'Berhasil';
	// 		Self::$data['message'] 		= 'Transaksi Telah Dikonfirmasi!';
	// 		Self::$data['type']	 		= 'success';
	// 	} else {

	// 		Self::$data['heading'] 		= 'Gagal';
	// 		Self::$data['type']	 		= 'error';
	// 	}

	// 	return Self::$data;
	// }

	// function bonuslevel_ro($user_id = null, $user_id_from = null, $getpaket = 1, $gettot = 1, $level = 1)
	// {
	// 	$result 		= array();
	// 	$status 		= true;
	// 	$paketid		= $getpaket;
	// 	$totro			= $gettot;

	// 	$datauser 		= userdata(['id' => $user_id]);
	// 	$userdata 		= userdata(['id' => $user_id_from]);

	// 	// GET PAKET
	// 	$this->db->where('package_id', $paketid);
	// 	$get_packages 		= $this->db->get('tb_packages')->row();

	// 	$array_term_level 	= json_decode($get_packages->package_titik);
	// 	if ($level > count($array_term_level)) {
	// 		$status = false;
	// 	}

	// 	if ($userdata->upline_id == 1) {
	// 		$status = false;
	// 	}
	// 	if ($userdata->upline_id == 0) {
	// 		$status = false;
	// 	}

	// 	$uplinedata 	= userdata(['id' => $userdata->id]);


	// 	if ($status) {
	// 		if ($uplinedata) {

	// 			if ($userdata->id == $user_id) {
	// 				$dekripsi = 'Cashback dari Transaksi Repeat Order';
	// 			} else {
	// 				$dekripsi = 'Bonus Level, Level Ke ' . $level . ' dari Repeat Order Username : ' . $datauser->username;
	// 			}

	// 			$wallet     		= $this->usermodel->userWallet('withdrawal', $uplinedata->id);

	// 			$this->db->insert(
	// 				'tb_wallet_balance',
	// 				[
	// 					'w_balance_wallet_id'       => $wallet->wallet_id,
	// 					'w_balance_amount'          => (int)$array_term_level[$level - 1] * (int)$totro,
	// 					'w_balance_type'            => 'credit',
	// 					'w_balance_desc'            => $dekripsi,
	// 					'w_balance_date_add'        => sekarang(),
	// 					'w_balance_txid'            => strtolower(random_string('alnum', 64)),
	// 					'w_balance_ket'				=> 'level',
	// 				]
	// 			);

	// 			$this->bonuslevel_ro($datauser->id, $uplinedata->upline_id, $paketid, $totro, $level + 1);
	// 		}
	// 	}
	// 	return $result;
	// }

	// function approveTabungan()
	// {
	// 	$this->db->where('invnabung_status', 'process');
	// 	$this->db->where('paynabung_code', post('code'));
	// 	$this->db->join('tb_invnabung', 'tb_invnabung.invnabung_id = tb_paynabung.paynabung_invid');
	// 	$this->db->join('tb_users', 'tb_users.id = tb_paynabung.paynabung_userid');
	// 	$cekPaynabungg = $this->db->get('tb_paynabung');
	// 	if ($cekPaynabungg->num_rows() == 0) {
	// 		Self::$data['status']     = false;
	// 		Self::$data['message']     = "Data Paynabung Tidak Ditemukan";
	// 	}

	// 	$this->form_validation->set_rules('code', 'Invoice Code', 'required');
	// 	if (!$this->form_validation->run()) {
	// 		Self::$data['status']     = false;
	// 		Self::$data['message']     = validation_errors(' ', '<br/>');
	// 	}

	// 	if (Self::$data['status']) {
	// 		$paynabung = $cekPaynabungg->row();
	// 		$random_string = strtolower(random_string('alnum', 60));

	// 		/*============================================
	//         =         UPDATE STATUS INVNABUNG            =
	//         ============================================*/
	// 		$this->db->update('tb_invnabung', array('invnabung_status' => 'success'), array('invnabung_id' => $paynabung->invnabung_id));
	// 		$this->db->update('tb_paynabung', array('paynabung_status' => 'Approve'), array('paynabung_id' => $paynabung->paynabung_id));

	// 		$userdatas = userdata(['id' => $paynabung->paynabung_userid]);
	// 		$referral_id = $userdatas->referral_id;

	// 		$walletid     = $this->usermodel->userWallet('withdrawal', $referral_id);

	// 		$persen = (int)2 / 100;
	// 		$totalbonus = $paynabung->invnabung_amount * $persen;

	// 		$this->db->insert(
	// 			'tb_wallet_balance',
	// 			[
	// 				'w_balance_wallet_id'       => $walletid->wallet_id,
	// 				'w_balance_amount'          => $totalbonus,
	// 				'w_balance_type'            => 'credit',
	// 				'w_balance_desc'            => 'Bonus Pelunasan Rp. ' . number_format($totalbonus, 0, '.', '.') . ' Dari Username ' . $userdatas->username,
	// 				'w_balance_date_add'        => sekarang(),
	// 				'w_balance_txid'            => strtolower(random_string('alnum', 64)),
	// 				'w_balance_ket'				=> 'sponsor',
	// 			]
	// 		);

	// 		/*============================================
	//         =          INSERT WALLET TABUNGAN           =
	//         ============================================*/
	// 		$this->db->insert(
	// 			'tb_walletnabung',
	// 			[
	// 				'walletnabung_userid'			=> $paynabung->invnabung_userid,
	// 				'walletnabung_amount'			=> $paynabung->invnabung_amount,
	// 				'walletnabung_type'				=> 'credit',
	// 				'walletnabung_date'				=> sekarang(),
	// 				'walletnabung_code'				=> $random_string,
	// 			]
	// 		);
	// 		/*============================================
	//         =          INSERT HISTORI TABUNGAN           =
	//         ============================================*/
	// 		$this->db->insert(
	// 			'tb_historitabungan',
	// 			[
	// 				'historitabungan_userid'		=> $paynabung->invnabung_userid,
	// 				'historitabungan_desc'			=> 'Credit Dana Pelunasan Sebesar Rp. ' . number_format($paynabung->invnabung_amount, 0, ',', '.'),
	// 				'historitabungan_total'			=> $paynabung->invnabung_amount,
	// 				'historitabungan_date'			=> sekarang(),
	// 				'historitabungan_code'			=> $random_string,
	// 			]
	// 		);

	// 		$totSaldo = $this->usermodel->userWalletNabung($paynabung->invnabung_userid);

	// 		$nilaistoran = 1500000;
	// 		$this->db->where('booking_userid', $paynabung->invnabung_userid);
	// 		$cekstoran = $this->db->get('tb_booking');
	// 		if ($cekstoran->num_rows() != 0) {
	// 			$nilaistoran = 1500000;
	// 		}

	// 		// $nowa     = $paynabung->user_phone;
	// 		// $pesan    = "Yth. " . $paynabung->user_fullname . " Setoran pelunasan sebesar Rp. " . number_format($paynabung->invnabung_amount, 0, '.', '.') . " telah berhasil. Total pembayaran biaya umroh anda menjadi: Rp. "  . number_format($totSaldo + $nilaistoran, 0, '.', '.') . " Terima kasih !! - PT. Sispenju Amanah Wisata (SISPENJU TOUR)";

	// 		// $this->notifWA($nowa, $pesan);

	// 		Self::$data['heading'] 		= 'Berhasil';
	// 		Self::$data['type']	 		= 'success';
	// 		Self::$data['message'] 		= 'Transaksi Pelunasan Telah Dikonfirmasi!';
	// 	} else {
	// 		Self::$data['heading'] 		= 'Gagal';
	// 		Self::$data['type']	 		= 'error';
	// 	}
	// 	return Self::$data;
	// }

	// function rejectTabungan()
	// {
	// 	$this->db->where('invnabung_status', 'process');
	// 	$this->db->where('paynabung_code', post('code'));
	// 	$this->db->join('tb_invnabung', 'tb_invnabung.invnabung_id = tb_paynabung.paynabung_invid');
	// 	$cekkPay = $this->db->get('tb_paynabung');
	// 	if ($cekkPay->num_rows() == 0) {
	// 		Self::$data['status']     = false;
	// 		Self::$data['message']     = "Data Tidak Ditemukan atau Sudah Terkonfirmasi";
	// 	}

	// 	$this->form_validation->set_rules('code', 'Kode Invoice', 'required');
	// 	if (!$this->form_validation->run()) {
	// 		Self::$data['status']     = false;
	// 		Self::$data['message']     = validation_errors(' ', '<br/>');
	// 	}

	// 	if (Self::$data['status']) {
	// 		$pay = $cekkPay->row();

	// 		// UPDATE INVOICE
	// 		$this->db->update('tb_invnabung', array('invnabung_status' => 'pending'), array('invnabung_id' => $pay->invnabung_id));
	// 		// DELETE PEMBAYARAN
	// 		$this->db->delete('tb_paynabung', array('paynabung_invid' => $pay->paynabung_invid));

	// 		Self::$data['heading'] 		= 'Berhasil';
	// 		Self::$data['type']	 		= 'success';
	// 		Self::$data['message'] 		= 'Transaksi Ditolak !';
	// 	} else {
	// 		Self::$data['heading'] 		= 'Gagal';
	// 		Self::$data['type']	 		= 'error';
	// 	}
	// 	return Self::$data;
	// }
}

/* End of file Invoice.php */
/* Location: ./application/models/Invoice.php */