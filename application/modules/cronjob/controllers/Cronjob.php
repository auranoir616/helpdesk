<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cronjob extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

	function bonus_omset2()
	{

		$status         = true;
		$result         = array();
		$omsetdistributor = 0;
		$this->db->where('user_type', 'distributor');
		$datuser = $this->db->get('tb_users')->result();

		foreach ($datuser as $value) {
			$this->db->select('stok_pengirim_userid as id, SUM(stok_amount) as qty');
			$this->db->where('stok_pengirim_userid', $value->id);
            $start_date = date('Y-m-01'); // Tanggal awal bulan
            $end_date = date('Y-m-t'); // Tanggal akhir bulan

            // Menambahkan filter tanggal untuk rentang bulan ini
            $this->db->where('stok_date >=', $start_date);
            $this->db->where('stok_date <=', $end_date);
			$this->db->where('stok_type', 'debit');
			$this->db->where('stok_desc', 'penjualan');
			$getdata = $this->db->get('tb_stok')->row();
			$omsetdistributor += $getdata->qty;
		}
		// jumlah reseller yang memenuhi syarat
        $start_date = date('Y-m-01'); // Tanggal awal bulan
        $end_date = date('Y-m-t'); // Tanggal akhir bulan
        $querysql = "
           SELECT transaksi_user_referralid
FROM (
    SELECT transaksi_user_referralid, transaksi_userid, SUM(transaksi_qty) AS total_qty
    FROM tb_transaksi
    WHERE transaksi_date >= ' $start_date' AND transaksi_date <= '$end_date' AND transaksi_user_referralid <> 1
    GROUP BY transaksi_user_referralid, transaksi_userid
    HAVING total_qty > 50
) AS subquery
GROUP BY transaksi_user_referralid
HAVING COUNT(transaksi_userid) > 2;
        ";

		$getuser = $this->db->query($querysql);
		$totalrsmemenuhi = $getuser->num_rows();
        $reseller = $getuser->result();

		if ($status) {
            $totalbonusraw         = $omsetdistributor * 500 / $totalrsmemenuhi;
			$totalbonusbagi = $totalbonusraw / 100;
			$totalbonusasli = floor($totalbonusbagi) * 100;
            foreach ($reseller as $value) {
                $wallet             = $this->usermodel->userWallet('withdrawal', $value->transaksi_user_referralid);
                $this->db->insert(
                    'tb_wallet_balance',
				[
					'w_balance_wallet_id'       => $wallet->wallet_id,
					'w_balance_amount'          => $totalbonusasli,
					'w_balance_type'            => 'credit',
					'w_balance_desc'            => 'Bonus Omset kedua dari penjualan reseller karena memenuhi syarat',
					'w_balance_date_add'        => sekarang(),
					'w_balance_txid'            => strtolower(random_string('alnum', 64)),
					'w_balance_ket'             => 'bonus omset reseller kedua',
                    ]
                );
            }
        }


		return $result;
	}
}




