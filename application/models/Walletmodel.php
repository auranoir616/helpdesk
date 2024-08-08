<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Walletmodel extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		//Do your magic here 	
	}

	function totalpoint($userid = null)
	{
		$return 		= 0;
		$point_masuk 	= $point_keluar = 0;

		$this->db->select_sum('poinrw_total');
		if (is_array($userid)) {
			$this->db->where_in('poinrw_userid', $userid);
		} else {
			$this->db->where('poinrw_userid', $userid);
		}
		$this->db->where('poinrw_tipe', 'credit');
		$get 			= $this->db->get('tb_poinrw');
		$get_point_masuk 	= $get->row()->poinrw_total;

		if (!empty($get_point_masuk)) {
			$point_masuk 	= $get_point_masuk;
		}

		//get saldo keluar
		$this->db->select_sum('poinrw_total');
		if (is_array($userid)) {
			$this->db->where_in('poinrw_userid', $userid);
		} else {
			$this->db->where('poinrw_userid', $userid);
		}
		$this->db->where('poinrw_tipe', 'debit');
		$get 			= $this->db->get('tb_poinrw');
		$get_point_keluar 	= $get->row()->poinrw_total;

		if (!empty($get_point_keluar)) {
			$point_keluar 	= $get_point_keluar;
		}

		$return 			= $point_masuk - $point_keluar;

		return $return;
	}

	function totalOmset($iduser = null, $datestart = null, $dateend = null)
	{

		$return 		= 0;

		$this->db->select_sum('inv_amount');
		$this->db->where('inv_user_id', $iduser);
		if (($datestart != null) && ($dateend != null)) {
			$this->db->where('inv_date_add BETWEEN "' . $datestart . '" AND "' . $dateend . '"');
		} else {
			$this->db->where('inv_date_add BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '"');
		}
		$this->db->where('inv_status', 'success');
		$get 			= $this->db->get('tb_invoice');
		$get_omset_masuk 	= $get->row()->inv_amount;

		$return 			= $get_omset_masuk;

		return $return;
	}

	function stokBarang($brg_code = null)
	{
		$this->db->where('produk_code', $brg_code);
		$produk_id = $this->db->get('tb_produk')->row()->produk_id;


		$return 		= 0;
		$brg_masuk 	= $brg_keluar = 0;

		$this->db->select_sum('stok_amount');
		$this->db->where('stok_produkid', $produk_id);
		$this->db->where('stok_type', 'credit');
		$get 			= $this->db->get('tb_stok');
		$get_brg_masuk 	= $get->row()->stok_amount;

		if (!empty($get_brg_masuk)) {
			$brg_masuk 	= $get_brg_masuk;
		}

		//get saldo keluar
		$this->db->select_sum('stok_amount');
		$this->db->where('stok_produkid', $produk_id);
		$this->db->where('stok_type', 'debit');
		$get 			= $this->db->get('tb_stok');
		$get_brg_keluar 	= $get->row()->stok_amount;

		if (!empty($get_brg_keluar)) {
			$brg_keluar 	= $get_brg_keluar;
		}

		$return 			= $brg_masuk - $brg_keluar;

		return $return;
	}


	function getbonus($userid = null)
	{

		$return 		= 0;
		$saldo_masuk 	= $saldo_keluar = 0;

		$this->db->select_sum('bonus_amount');
		if ($userid != null) {
			$this->db->where('bonus_userid', $userid);
		} else {
			$this->db->where('bonus_userid', userid());
		}
		$this->db->where('bonus_type', 'credit');
		if (!$this->ion_auth->is_admin()) {
			$this->db->where('bonus_status', 'success');
		}
		$get 			= $this->db->get('tb_bonus');
		$get_saldo_masuk 	= $get->row()->bonus_amount;

		if (!empty($get_saldo_masuk)) {
			$saldo_masuk 	= $get_saldo_masuk;
		}


		//get saldo keluar
		$this->db->select_sum('bonus_amount');
		if ($userid != null) {
			$this->db->where('bonus_userid', $userid);
		} else {
			$this->db->where('bonus_userid', userid());
		}
		$this->db->where('bonus_type', 'debit');
		if (!$this->ion_auth->is_admin()) {
			$this->db->where('bonus_status', 'success');
		}
		$gett 			= $this->db->get('tb_bonus');
		$get_saldo_keluar 	= $gett->row()->bonus_amount;

		if (!empty($get_saldo_keluar)) {
			$saldo_keluar 	= $get_saldo_keluar;
		}

		$return 			= $saldo_masuk - $saldo_keluar;

		return $return;
	}



	function saldoro($userid = null)
	{

		$return 		= 0;
		$saldo_masuk 	= $saldo_keluar = 0;

		$this->db->select_sum('saldoro_amount');
		if ($userid != null) {
			$this->db->where('saldoro_userid', $userid);
		} else {
			$this->db->where('saldoro_userid', userid());
		}
		$this->db->where('saldoro_type', 'credit');
		$get 			= $this->db->get('tb_saldoro');
		$get_saldo_masuk 	= $get->row()->saldoro_amount;

		if (!empty($get_saldo_masuk)) {
			$saldo_masuk 	= $get_saldo_masuk;
		}


		//get saldo keluar
		$this->db->select_sum('saldoro_amount');
		if ($userid != null) {
			$this->db->where('saldoro_userid', $userid);
		} else {
			$this->db->where('saldoro_userid', userid());
		}
		$this->db->where('saldoro_type', 'debit');
		$gett 			= $this->db->get('tb_saldoro');
		$get_saldo_keluar 	= $gett->row()->saldoro_amount;

		if (!empty($get_saldo_keluar)) {
			$saldo_keluar 	= $get_saldo_keluar;
		}

		$return 			= $saldo_masuk - $saldo_keluar;

		return $return;
	}


	function walletAddressBalance($wallet_address = null, $date_start = null, $date_end = null)
	{

		$return 		= 0;
		$saldo_masuk 	= $saldo_keluar = 0;

		$this->db->select_sum('w_balance_amount');
		if (($date_start != null) && ($date_end != null)) {
			$this->db->where('w_balance_date_add BETWEEN "' . $date_start . '" AND "' . $date_end . '"');
		}
		$this->db->join('tb_users_wallet', 'wallet_id = w_balance_wallet_id', 'left');
		$this->db->where('wallet_address', $wallet_address);
		$this->db->where('w_balance_type', 'credit');
		$get 			= $this->db->get('tb_wallet_balance');
		$get_saldo_masuk 	= $get->row()->w_balance_amount;

		if (!empty($get_saldo_masuk)) {
			$saldo_masuk 	= $get_saldo_masuk;
		}
		//get saldo keluar
		$this->db->select_sum('w_balance_amount');
		if (($date_start != null) && ($date_end != null)) {
			$this->db->where('w_balance_date_add BETWEEN "' . $date_start . '" AND "' . $date_end . '"');
		}
		$this->db->join('tb_users_wallet', 'wallet_id = w_balance_wallet_id', 'left');
		$this->db->where('wallet_address', $wallet_address);
		$this->db->where('w_balance_type', 'debit');
		$get 			= $this->db->get('tb_wallet_balance');
		$get_saldo_keluar 	= $get->row()->w_balance_amount;

		if (!empty($get_saldo_keluar)) {
			$saldo_keluar 	= $get_saldo_keluar;
		}
		$return 			= $saldo_masuk - $saldo_keluar;

		return $return;
	}


	function walletTABUNGAN($user_id = null)
	{

		$userid = ($user_id == null) ? userid() : $user_id;

		$return 		= 0;
		$saldo_masuk 	= $saldo_keluar = 0;

		$this->db->select_sum('walletnabung_amount');
		$this->db->where('walletnabung_type', 'credit');
		$this->db->where('walletnabung_userid', $userid);
		$get 			= $this->db->get('tb_walletnabung');
		$get_saldo_masuk 	= $get->row()->walletnabung_amount;

		if (!empty($get_saldo_masuk)) {
			$saldo_masuk 	= $get_saldo_masuk;
		}

		//get saldo keluar
		$this->db->select_sum('walletnabung_amount');
		$this->db->where('walletnabung_type', 'debit');
		$this->db->where('walletnabung_userid', $userid);
		$get 			= $this->db->get('tb_walletnabung');
		$get_saldo_keluar 	= $get->row()->walletnabung_amount;

		if (!empty($get_saldo_keluar)) {
			$saldo_keluar 	= $get_saldo_keluar;
		}

		$return 			= $saldo_masuk - $saldo_keluar;

		return $return;
	}


	function totalcredit($wallet_address = null)
	{

		$return 		= 0;
		$saldo_masuk 	= 0;

		$this->db->select_sum('w_balance_amount');
		$this->db->join('tb_users_wallet', 'wallet_id = w_balance_wallet_id', 'left');
		$this->db->where('wallet_address', $wallet_address);
		$this->db->where('w_balance_type', 'credit');
		$get 			= $this->db->get('tb_wallet_balance');
		$get_saldo_masuk 	= $get->row()->w_balance_amount;

		if (!empty($get_saldo_masuk)) {
			$saldo_masuk 	= $get_saldo_masuk;
		}

		$return 			= $saldo_masuk;

		return $return;
	}


	function profitshare($userid = null)
	{

		$return 		= 0;
		$saldo_masuk 	= $saldo_keluar = 0;

		$this->db->select_sum('profitshare_total');
		if ($userid != null) {
			$this->db->where('profitshare_userid', $userid);
		} else {
			$this->db->where('profitshare_userid', userid());
		}
		$this->db->where('profitshare_type', 'credit');
		$gettttt 			= $this->db->get('tb_profitshare');
		$get_saldo_masuk 	= $gettttt->row()->profitshare_total;

		if (!empty($get_saldo_masuk)) {
			$saldo_masuk 	= $get_saldo_masuk;
		}


		//get saldo keluar
		$this->db->select_sum('profitshare_total');
		if ($userid != null) {
			$this->db->where('profitshare_userid', $userid);
		} else {
			$this->db->where('profitshare_userid', userid());
		}
		$this->db->where('profitshare_type', 'debit');
		$get 			= $this->db->get('tb_profitshare');
		$get_saldo_keluar 	= $get->row()->profitshare_total;

		if (!empty($get_saldo_keluar)) {
			$saldo_keluar 	= $get_saldo_keluar;
		}

		$return 			= $saldo_masuk - $saldo_keluar;

		return $return;
	}


	function poinreward($userid = null, $poin = 0)
	{

		$return 		= 0;
		$saldo_masuk 	= $saldo_keluar = 0;

		$this->db->select_sum('poinreward_amount');
		if ($userid != null) {
			$this->db->where('poinreward_userid', $userid);
		} else {
			$this->db->where('poinreward_userid', userid());
		}
		if ($poin != 0) {
			$this->db->where('poinreward_level', $poin);
		}
		$this->db->where('poinreward_type', 'credit');
		$gettttt 			= $this->db->get('tb_poinreward');
		$get_saldo_masuk 	= $gettttt->row()->poinreward_amount;

		if (!empty($get_saldo_masuk)) {
			$saldo_masuk 	= $get_saldo_masuk;
		}


		//get saldo keluar
		$this->db->select_sum('poinreward_amount');
		if ($userid != null) {
			$this->db->where('poinreward_userid', $userid);
		} else {
			$this->db->where('poinreward_userid', userid());
		}
		if ($poin != 0) {
			$this->db->where('poinreward_level', $poin);
		}
		$this->db->where('poinreward_type', 'debit');
		$get 			= $this->db->get('tb_poinreward');
		$get_saldo_keluar 	= $get->row()->poinreward_amount;

		if (!empty($get_saldo_keluar)) {
			$saldo_keluar 	= $get_saldo_keluar;
		}

		$return 			= $saldo_masuk - $saldo_keluar;

		return $return;
	}

	function mycashback($userid = null)
	{

		$return 		= 0;
		$saldo_masuk 	= 0;

		$this->db->select_sum('reportcashback_amount');
		if ($userid != null) {
			$this->db->where('reportcashback_userid', $userid);
		} else {
			$this->db->where('reportcashback_userid', userid());
		}
		$gettttt 			= $this->db->get('tb_reportcashback');
		$get_saldo_masuk 	= $gettttt->row()->reportcashback_amount;

		if (!empty($get_saldo_masuk)) {
			$saldo_masuk 	= $get_saldo_masuk;
		}

		$return 			= $saldo_masuk;

		return $return;
	}



}

/* End of file Walletmodel.php */
/* Location: ./application/models/Walletmodel.php */