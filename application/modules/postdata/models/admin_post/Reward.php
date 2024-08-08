<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reward extends CI_Model
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

	function approve()
	{
		$this->db->where('userreward_code', $this->input->post('code'));
		$this->db->where('userreward_status', 'pending');
		$this->db->join('tb_reward', 'userreward_rewardid = reward_id');
		$cekreward = $this->db->get('tb_userreward');
		if ($cekreward->num_rows() == 0) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= "Tidak Valid atau Reward Terkonfirmasi";
		} else {
			$datareward = $cekreward->row();

			if ($this->usermodel->poinreward($datareward->userreward_userid) < $datareward->reward_point) {
				Self::$data['status']     = false;
				Self::$data['message']     = "Poin Member Tidak Cukup Untuk Klaim Reward Ini";
			}
		}

		$this->form_validation->set_rules('code', 'Code Transaksi', 'required');
		if ($this->form_validation->run() == FALSE) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= validation_errors('', '<br/>');
		}

		if (Self::$data['status']) {
			$datareward = $cekreward->row();
			$userdata	= userdata(['id' => $datareward->userreward_userid]);

			$fee = (10 / 100) * $datareward->reward_amount;
			$total = $datareward->reward_amount - $fee;

			// UPDATE STATUS
			$this->db->update(
				'tb_userreward',
				[
					'userreward_status'		=> 'success',
				],
				[
					'userreward_code'		=> $this->input->post('code'),
				]
			);

			$this->db->insert(
				'tb_poinrw',
				[
					'poinrw_userid'             => $datareward->userreward_userid,
					'poinrw_paketid'    		=> $datareward->reward_id,
					'poinrw_total'              => $datareward->reward_point,
					'poinrw_tipe'               => 'debit',
					'poinrw_desc'               => "Klaim Reward " . $total,
					'poinrw_date'               => sekarang(),
					'poinrw_code'               => strtolower(random_string('alnum', 64)),
				]
			);

			$wallet             = $this->usermodel->userWallet('withdrawal', $datareward->userreward_userid);

			$this->db->insert(
				'tb_wallet_balance',
				[
					'w_balance_wallet_id'       => $wallet->wallet_id,
					'w_balance_amount'          => $total,
					'w_balance_type'            => 'credit',
					'w_balance_desc'            => "Klaim Reward " . $total,
					'w_balance_date_add'        => sekarang(),
					'w_balance_txid'            => strtolower(random_string('alnum', 64)),
					'w_balance_ket'             => 'reward',
				]
			);

			$nowa     = $userdata->user_phone;
			$pesan    = "Yth. " . $userdata->user_fullname . " Selamat Pencairan Reward Anda Sebesar Rp. " . number_format($total, 0, '.', '.') . " Sukses!! \r\n\r\nTetap Semangat dan Raih Prestasi Yang Lebih Tinggi Lagi https://sispenju.com";

			$this->notifWA($nowa, $pesan);

			Self::$data['heading'] 	= 'Berhasil';
			Self::$data['message'] 	= 'Transaksi Klaim Reward Dikonfirmasi';
			Self::$data['type'] 	= 'success';
		} else {

			Self::$data['heading'] 	= 'Gagal';
			Self::$data['type'] 	= 'error';
		}

		return Self::$data;
	}

	function notifWA($phone = NULL, $message = NULL)
	{
		$return = array();
		$userkey = 'b3f16549743b';
		$passkey = '59391aaa7eee4bb7f17cf4c1';
		$telepon = $phone;
		$message = str_replace('%20', ' ', $message);
		$url = 'https://console.zenziva.net/wareguler/api/sendWA/';
		$curlHandle = curl_init();
		curl_setopt($curlHandle, CURLOPT_URL, $url);
		curl_setopt($curlHandle, CURLOPT_HEADER, 0);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
		curl_setopt($curlHandle, CURLOPT_POST, 1);
		curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
			'userkey' => $userkey,
			'passkey' => $passkey,
			'to' => $telepon,
			'message' => $message
		));
		json_decode(curl_exec($curlHandle), true);
		curl_close($curlHandle);

		return $return;
	}

	function reject()
	{
		$this->db->where('userreward_code', $this->input->post('code'));
		$this->db->where('userreward_status', 'pending');
		$this->db->join('tb_reward', 'userreward_rewardid = reward_id');
		$cekreward = $this->db->get('tb_userreward');
		if ($cekreward->num_rows() == 0) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= "Tidak Valid atau Reward Terkonfirmasi";
		} else {
			$datareward = $cekreward->row();

			if ($datareward->reward_point < $this->usermodel->poinreward($datareward->userreward_userid)) {
				Self::$data['status']     = false;
				Self::$data['message']     = "Poin Member Tidak Cukup Untuk Klaim Reward Ini";
			}
		}

		$this->form_validation->set_rules('code', 'Code Transaksi', 'required');
		if ($this->form_validation->run() == FALSE) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= validation_errors('', '<br/>');
		}

		if (Self::$data['status']) {

			// UPDATE STATUS
			$this->db->update(
				'tb_userreward',
				[
					'userreward_status'		=> 'reject',
				],
				[
					'userreward_code'		=> $this->input->post('code'),
				]
			);


			Self::$data['heading'] 	= 'Berhasil';
			Self::$data['message'] 	= 'Transaksi Klaim Reward Ditolak';
			Self::$data['type'] 	= 'success';
		} else {

			Self::$data['heading'] 	= 'Gagal';
			Self::$data['type'] 	= 'error';
		}

		return Self::$data;
	}

	public function AddReward()
	{
		// 
		$config['upload_path']          = './assets/reward/';
		$config['allowed_types']        = 'jpg|png|jpeg';
		$config['max_size']             = '99999999';
		$config['max_width']            = '99999999';
		$config['max_height']           = '99999999';
		$config['remove_spaces']        = TRUE;
		$config['encrypt_name']         = TRUE;
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if (!$this->upload->do_upload('reward_picture')) {
			Self::$data['status']     = false;
			Self::$data['message']     = $this->upload->display_errors();
		}
		$this->form_validation->set_rules('reward_nama', 'Masukkan Nama Reward', 'required');
		$this->form_validation->set_rules('reward_poin', 'Masukkan Point Reward', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}
		if (Self::$data['status']) {
			$uploaded   = $this->upload->data();
			$random_string = strtolower(random_string('alnum', 64));

			$this->db->insert(
				'tb_reward',
				[
					'reward_nama'						=> $this->input->post('reward_nama'),
					'reward_poin'						=> $this->input->post('reward_poin'),
					'reward_picture'					=> $uploaded['file_name'],
					'reward_code'						=> $random_string,
					'reward_status' 					=> 'Tidak Aktif',
					'reward_forall'						=> ($this->input->post('switch') == 1) ? 'yes' : 'no'
				]
			);

			$configg['image_library']       = 'gd2';
			$configg['source_image']        = './assets/reward/' . $uploaded['file_name'];
			$configg['create_thumb']        = FALSE;
			$configg['maintain_ratio']      = FALSE;
			$configg['quality']             = '50%';
			$configg['width']               = 'auto';
			$configg['height']              = 'auto';
			$configg['new_image']           = './assets/reward/thumbnail/' . $uploaded['file_name'];
			$this->load->library('image_lib', $configg);
			$this->image_lib->resize();

			Self::$data['message']      = 'Penambahan Reward Berhasil';
			Self::$data['heading']      = 'Berhasil';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']      = 'Error';
			Self::$data['type']         = 'error';
		}

		return Self::$data;
	}

	public function UpdateReward()
	{
		$data = ['status' => true, 'type' => 'success', 'message' => 'ok', 'csrf_data' => $this->security->get_csrf_hash()];

		$config['upload_path'] = './assets/reward/';
		$config['allowed_types'] = 'jpg|png|jpeg';
		$config['max_size'] = '99999999';
		$config['max_width'] = '99999999';
		$config['max_height'] = '99999999';
		$config['remove_spaces'] = TRUE;
		$config['encrypt_name'] = TRUE;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		$uploaded = null;
		if (!empty($_FILES['reward_picture']['name'])) {
			if (!$this->upload->do_upload('reward_picture')) {
				$data['status'] = false;
				$data['message'] = $this->upload->display_errors();
			} else {
				$uploaded = $this->upload->data();
			}
		}

		$this->form_validation->set_rules('reward_nama', 'Nama Reward', 'required');
		$this->form_validation->set_rules('reward_poin', 'Point Reward', 'required');
		$this->form_validation->set_rules('reward_status', 'Point Status', 'required');

		if (!$this->form_validation->run()) {
			$data['status'] = false;
			$data['message'] = validation_errors(' ', '<br/>');
		}

		if ($data['status']) {
			$data_update = [
				'reward_nama' => $this->input->post('reward_nama'),
				'reward_poin' => $this->input->post('reward_poin'),
				'reward_status' => $this->input->post('reward_status'),
				'reward_forall'						=> ($this->input->post('switch') == 1) ? 'yes' : 'no'

			];

			if ($uploaded && !empty($uploaded['file_name'])) {
				$data_update['reward_picture'] = $uploaded['file_name'];
			}

			$code = $this->input->post('code');

			$this->db->where('reward_code', $code);
			if ($this->db->update('tb_reward', $data_update)) {
				if ($uploaded && !empty($uploaded['file_name'])) {
					$configg['image_library'] = 'gd2';
					$configg['source_image'] = './assets/reward/' . $uploaded['file_name'];
					$configg['create_thumb'] = FALSE;
					$configg['maintain_ratio'] = FALSE;
					$configg['quality'] = '50%';
					$configg['width'] = 'auto';
					$configg['height'] = 'auto';
					$configg['new_image'] = './assets/reward/thumbnail/' . $uploaded['file_name'];

					$this->load->library('image_lib', $configg);
					if (!$this->image_lib->resize()) {
						Self::$data['status'] = false;
						Self::$data['message'] = $this->image_lib->display_errors();
					} else {
						Self::$data['message'] = 'Update Reward Berhasil';
						Self::$data['heading'] = 'Berhasil';
						Self::$data['type'] = 'success';
					}
				} else {
					Self::$data['message'] = 'Update Reward Berhasil';
					Self::$data['heading'] = 'Berhasil';
					Self::$data['type'] = 'success';
				}
			} else {
				Self::$data['status'] = false;
				Self::$data['message'] = 'Gagal memperbarui data reward.';
				Self::$data['heading'] = 'Error';
				Self::$data['type'] = 'error';
			}
		}

		return Self::$data;
	}

	function DeleteReward()
	{
		$reward_code = $this->input->post('reward_code');
		if (!$reward_code) {
			Self::$data['status'] = false;
			Self::$data['heading'] = 'Error';
			Self::$data['message'] = 'Kode reward tidak valid.';
			Self::$data['csrf_data'] = $this->security->get_csrf_hash(); // Tambahkan csrf_data
			Self::$data['type'] = 'error';
		}
		$cekreward = $this->db->get('tb_reward');
		if ($cekreward->num_rows() == 0) {
			Self::$data['status'] = false;
			Self::$data['message'] = 'Produk Tidak Valid';
			Self::$data['type'] = 'error';
		}

		$this->db->where('reward_code', $reward_code);
		$reward = $cekreward->row();
		if (Self::$data['status']) {
			$this->db->delete('tb_reward');
			if (file_exists('./assets/produk/' . $reward->reward_picture)) {
				unlink('./assets/produk/' . $reward->reward_picture);
			}

			if (file_exists('./assets/produk/thumbnail/' . $reward->reward_picture)) {
				unlink('./assets/produk/thumbnail/' . $reward->reward_picture);
			}
			// Jika berhasil dihapus
			Self::$data['status'] = true;
			Self::$data['heading'] = 'Berhasil';
			Self::$data['message'] = 'Reward berhasil dihapus.';
			Self::$data['csrf_data'] = $this->security->get_csrf_hash(); // Tambahkan csrf_data
			Self::$data['type'] = 'success';
		} else {
			// Jika gagal dihapus
			Self::$data['status'] = false;
			Self::$data['heading'] = 'Error';
			Self::$data['csrf_data'] = $this->security->get_csrf_hash(); // Tambahkan csrf_data
			Self::$data['message'] = 'Gagal menghapus reward.';
			Self::$data['type'] = 'error';
		}
		return Self::$data;
	}

	function AktifkanReward()
	{
		$reward_status = $this->input->post('status');

		if (Self::$data['status']) {
			$cekreward = $this->db->get('tb_reward');
			if ($cekreward->num_rows() == 0) {
				Self::$data['status'] = false;
				Self::$data['message'] = 'Reward Kosong';
				Self::$data['type'] = 'error';
			}

			$this->db->set('reward_status', $reward_status);
			$this->db->update('tb_reward');

			Self::$data['status'] = true;
			Self::$data['message'] = 'Perubahan Status Reward Berhasil';
			Self::$data['heading'] = 'Berhasil';
			Self::$data['type'] = 'success';
		} else {
			Self::$data['status'] = false;
			Self::$data['heading'] = 'Error';
			Self::$data['message'] = 'Gagal Update Status Reward.';
			Self::$data['type'] = 'error';
		}

		return Self::$data;
	}
}



/* End of file Withdrawl.php */
/* Location: ./application/models/Withdrawl.php */