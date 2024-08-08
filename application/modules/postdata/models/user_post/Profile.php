<?php defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends CI_Model
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

	function updateprofile()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');

		if (!$this->ion_auth->hash_password_db(userid(), $this->input->post('user_pass'))) {
			self::$data['status']  = false;
			self::$data['message'] = 'Konfirmasi Password Tidak Sesuai!';
			return self::$data; // Tambahkan return statement di sini agar keluar dari fungsi jika password tidak cocok
		}

		$config['upload_path']   = './assets/upload/';
		$config['allowed_types'] = 'jpg|png|jpeg';
		$config['max_size']      = '99999999';
		$config['max_width']     = '99999999';
		$config['max_height']    = '99999999';
		$config['remove_spaces'] = TRUE;
		$config['encrypt_name']  = TRUE;
		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		$foto_ktp = null;
		if (!$this->upload->do_upload('user_ktp')) {
			$foto_ktp = null;
		} else {
			$foto_ktp = $this->upload->data();
		}

		$foto_ahli_waris = null;
		if (!$this->upload->do_upload('user_fotoahliwaris')) {
			$foto_ahli_waris = null;
		} else {
			$foto_ahli_waris = $this->upload->data();
		}

		$this->form_validation->set_rules('user_fullname', 'Nama Lengkap', 'required');
		$this->form_validation->set_rules('user_phone', 'No WhatsApp', 'required');
		$this->form_validation->set_rules('user_ahli_waris', 'Ahli waris', 'required');
		$this->form_validation->set_rules('user_pass', 'Konfirmasi Password', 'required');
		if (!$this->form_validation->run()) {
			self::$data['status']  = false;
			self::$data['message'] = validation_errors(' ', '<br/>');
		}

		$update_data = [
			'user_fullname'    => $this->input->post('user_fullname'),
			'user_phone'       => $this->input->post('user_phone'),
			'user_ahli_waris'  => $this->input->post('user_ahli_waris'),
			'user_NIK'  => $this->input->post('user_nik'),
			'user_provinsi'  => $this->input->post('user_provinsi'),
			'user_kota'  => $this->input->post('user_kota'),
			'user_kecamatan'  => $this->input->post('user_kecamatan'),
			'user_alamat'  => $this->input->post('user_alamat'),
			'user_wilayah'  => $this->usermodel->getHargaWilayah(userid()),

		];
		if ($foto_ktp && !empty($foto_ktp['file_name'])) {
			$update_data['user_ktp'] = $foto_ktp['file_name'];
		}

		if ($foto_ahli_waris && !empty($foto_ahli_waris['file_name'])) {
			$update_data['user_foto_ahli_waris'] = $foto_ahli_waris['file_name'];
		}

		$this->ion_auth->update(userid(), $update_data);


		// Resize gambar user_ktp
		if (!empty($foto_ktp['file_name'])) {
			$configg['image_library']   = 'gd2';
			$configg['source_image']    = './assets/upload/' . $foto_ktp['file_name'];
			$configg['create_thumb']    = FALSE;
			$configg['maintain_ratio']  = FALSE;
			$configg['quality']         = '50%';
			$configg['width']           = 'auto';
			$configg['height']          = 'auto';
			$configg['new_image']       = './assets/upload/thumbnail/' . $foto_ktp['file_name'];
			$this->load->library('image_lib', $configg);
			$this->image_lib->resize();
		}
		if (!empty($foto_ahli_waris['file_name'])) {

			// Resize gambar user_foto_ahli_waris
			$configg['image_library']   = 'gd2';
			$configg['source_image']    = './assets/upload/' . $foto_ahli_waris['file_name'];
			$configg['create_thumb']    = FALSE;
			$configg['maintain_ratio']  = FALSE;
			$configg['quality']         = '50%';
			$configg['width']           = 'auto';
			$configg['height']          = 'auto';
			$configg['new_image']       = './assets/upload/thumbnail/' . $foto_ahli_waris['file_name'];
			$this->image_lib->initialize($configg);
			$this->image_lib->resize();
		}
		$this->ion_auth->update(userid(), [
			'user_wilayah'  => $this->usermodel->getHargaWilayah(userid()),
		]);


		self::$data['status']  = true;
		self::$data['heading'] = 'Berhasil';
		self::$data['message'] = 'Data Profile Anda Telah Diperbarui!';
		self::$data['type']    = 'success';

		return self::$data;
	}



	function updatebank()
	{
		if (!$this->ion_auth->hash_password_db(userid(), post('bank_pass'))) {
			Self::$data['status']       = false;
			Self::$data['message']      = 'Konfirmasi Password Tidak Sesuai!';
		}

		$this->form_validation->set_rules('user_bank_account', 'Rekening Atas Nama', 'required');
		$this->form_validation->set_rules('user_bank_name', 'Nama Bank', 'required');
		$this->form_validation->set_rules('user_bank_number', 'Nomor Rekening', 'required');
		$this->form_validation->set_rules('bank_pass', 'Konfirmasi Password', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status'] 	= false;
			Self::$data['message'] 	= validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {

			$this->db->update(
				'tb_users',
				[
					'user_bank_account'		=> post('user_bank_account'),
					'user_bank_name'		=> post('user_bank_name'),
					'user_bank_number'		=> post('user_bank_number'),

				],
				[
					'id'					=> userid(),
				]
			);

			Self::$data['heading'] 	= 'Berhasil';
			Self::$data['message'] 	= 'Data Bank Anda Telah Diperbarui!';
			Self::$data['type'] 	= 'success';
		} else {
			Self::$data['heading'] 	= 'Gagal';
			Self::$data['type'] 	= 'error';
		}
		return Self::$data;
	}

	function klaimreward()
	{
		// VALIDASI REWARD APAKAH ADA
		$this->db->where('reward_code', post('code'));
		$cekreward = $this->db->get('tb_reward');
		if ($cekreward->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Data Reward Tidak Valid";
		} else {
			$datareward = $cekreward->row();
			// VALIDASI POIN
			if ($this->usermodel->poinreward() < $datareward->reward_point) {
				Self::$data['status']     = false;
				Self::$data['message']     = "Poin Anda Tidak Cukup Untuk Klaim Reward Ini";
			}
		}

		// VALIDASI PASSWORD
		if (!$this->ion_auth->hash_password_db(userid(), post('konfirmasi_password'))) {
			Self::$data['status']       = false;
			Self::$data['message']      = 'Konfirmasi Password Tidak Cocok!';
		}

		// VALIDASI DATA
		$this->form_validation->set_rules('code', 'Kode Reward', 'required');
		$this->form_validation->set_rules('reward_bank_account', 'Rekening Atas Nama', 'required');
		$this->form_validation->set_rules('reward_bank_name', 'Nama Bank', 'required');
		$this->form_validation->set_rules('reward_bank_number', 'Nomor Rekening', 'required');
		$this->form_validation->set_rules('reward_phone', 'No WhatsApp', 'required');
		$this->form_validation->set_rules('konfirmasi_password', 'Konfirmasi Password', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		// CEK APAKAH ADA YANG PENDING
		$this->db->where('userreward_status', 'pending');
		$this->db->where('userreward_userid', userid());
		$cek_pending = $this->db->get('tb_userreward');
		if ($cek_pending->num_rows() != 0) {
			Self::$data['status']       = false;
			Self::$data['message']      = 'Anda Memiliki Transaksi Pending!';
		}


		if (Self::$data['status']) {
			$datareward = $cekreward->row();

			$this->db->insert(
				'tb_userreward',
				[
					'userreward_rewardid'		=> $datareward->reward_id,
					'userreward_userid'			=> userid(),
					'userreward_account'		=> $this->input->post('reward_bank_account'),
					'userreward_bank'			=> $this->input->post('reward_bank_name'),
					'userreward_number'			=> $this->input->post('reward_bank_number'),
					'userreward_contact'		=> $this->input->post('reward_phone'),
					'userreward_date'			=> sekarang(),
					'userreward_code'			=> strtolower(random_string('alnum', 64))
				]
			);

			Self::$data['message']      = 'Berhasil Claim Reward & Tenunggu Konfirmasi Dari Admin';
			Self::$data['heading']      = 'Berhasil';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     	= 'Gagal';
			Self::$data['type']     	= 'error';
		}

		return Self::$data;
	}

	function klaim_reward()
	{
		// VALIDASI REWARD APAKAH ADA
		$this->db->where('reward_code', post('code'));
		$cekreward = $this->db->get('tb_reward');
		if ($cekreward->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Data Reward Tidak Valid atau Tidak Ditemukan";
		} else {
			$this->db->where('referral_id', userid());
			$totsponsor = $this->db->get('tb_users')->num_rows();

			$datareward = $cekreward->row();
			// VALIDASI HANYA KLAIM 1X
			$this->db->where('userreward_rewardid', $datareward->reward_id);
			$this->db->where('userreward_userid', userid());
			$CEKKKKKKKK = $this->db->get('tb_userreward',);
			if ($CEKKKKKKKK->num_rows() != 0) {
				Self::$data['status']     = false;
				Self::$data['message']     = "Anda Telah Mengklaim Reward ini!";
			}

			// VALIDASI KUALIFIKASI
			if ($totsponsor < $datareward->reward_point) {
				Self::$data['status']     = false;
				Self::$data['message']     = "Anda Tidak Memenuhi Kualifikasi Untuk Klaim Reward Ini";
			}
		}

		// VALIDASI DATA
		$this->form_validation->set_rules('code', 'Kode Reward', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {
			$datareward = $cekreward->row();

			$this->db->insert(
				'tb_userreward',
				[
					'userreward_rewardid'		=> $datareward->reward_id,
					'userreward_userid'			=> userid(),
					'userreward_date'			=> sekarang(),
					'userreward_code'			=> strtolower(random_string('alnum', 64))
				]
			);

			$wallet 	= $this->usermodel->userWallet('withdrawal', userid());
			$this->db->insert(
				'tb_wallet_balance',
				[
					'w_balance_wallet_id'       => $wallet->wallet_id,
					'w_balance_amount'          => $datareward->reward_amount,
					'w_balance_type'            => 'credit',
					'w_balance_desc'            => 'Transaksi Klaim Reward',
					'w_balance_date_add'        => sekarang(),
					'w_balance_ket'             => 'reward',
					'w_balance_txid'            => hash('SHA256', random_string('alnum', 16)),
				]
			);

			Self::$data['message']      = 'Reward Berhasil Diklaim, Periksa Wallet Anda!';
			Self::$data['heading']      = 'Berhasil';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']     	= 'Gagal';
			Self::$data['type']     	= 'error';
		}

		return Self::$data;
	}

	function tambahtiket(){
        Self::$data['message']      = 'Upload Foto Testimoni Berhasil';
        Self::$data['heading']      = 'Berhasil';


        return Self::$data;


        $config['upload_path']          = './assets/upload/';
        $config['allowed_types']        = 'jpg|png|jpeg';
        $config['max_size']             = '99999999';
        $config['max_width']            = '99999999';
        $config['max_height']           = '99999999';
        $config['remove_spaces']        = TRUE;
        $config['encrypt_name']         = TRUE;

        // Load library upload
        $this->load->library('upload', $config);

        // Upload multiple files from the input field
        $tiket_image = $_FILES['tiket_image'];
        $tiket_image_data = array();

        $this->form_validation->set_rules('tiket_judul', 'Subjek tiket', 'required');
        $this->form_validation->set_rules('tiket_kategori', 'kategori tiket', 'required');
        $this->form_validation->set_rules('tiket_tipe', 'tipe tiket', 'required');
        $this->form_validation->set_rules('tiket_desc', 'deskripsi tiket', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}


        // Loop through uploaded files
        for ($i = 0; $i < count($tiket_image['name']); $i++) {
            $_FILES['file'] = array(
                'name'     => $tiket_image['name'][$i],
                'type'     => $tiket_image['type'][$i],
                'tmp_name' => $tiket_image['tmp_name'][$i],
                'error'    => $tiket_image['error'][$i],
                'size'     => $tiket_image['size'][$i]
            );
            // Attempt to upload file
            if (!$this->upload->do_upload('file')) {
                Self::$data['status']     = false;
                Self::$data['message']    = $this->upload->display_errors();
                return Self::$data;
            }
            $upload_data = $this->upload->data();
            $tiket_image_data[] = $upload_data['file_name'];
        }

        if (Self::$data['status']) {
            // Update the database with new file data
            $this->db->insert(
                'tb_tiket',
                [
                    'tiket_judul' => post('tiket_judul'),
                    'tiket_kategori' => post('tiket_kategori'),
                    'tiket_tipe' => post('tiket_tipe'),
                    'tiket_desc' => post('tiket_desc'),
                    'tiket_image' => json_encode($tiket_image_data),
                ]
            );

            // Optional: Resize images if needed
            foreach ($tiket_image_data as $file_name) {
                $configg['image_library']   = 'gd2';
                $configg['source_image']    = './assets/upload/' . $file_name;
                $configg['create_thumb']    = FALSE;
                $configg['maintain_ratio']  = FALSE;
                $configg['quality']         = '50%';
                $configg['width']           = 'auto';
                $configg['height']          = 'auto';
                $configg['new_image']       = './assets/upload/thumbnail/' . $file_name;

                $this->load->library('image_lib', $configg);
                $this->image_lib->resize();
            }

            Self::$data['message']      = 'Upload Foto Testimoni Berhasil';
            Self::$data['heading']      = 'Berhasil';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Error';
            Self::$data['type']         = 'error';
        }
        return Self::$data;
    }

}

/* End of file Profile.php */
/* Location: ./application/modules/postdata/models/user_post/Profile.php */