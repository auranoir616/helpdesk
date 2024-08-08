<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Custom extends CI_Model
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

	function setPengumuman()
	{
		$this->db->where('option_name', 'pengumuman');
		$cekkkkopt = $this->db->get('tb_options');
		if ($cekkkkopt->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Tidak Valid Hubungi Developer";
		}

		$this->form_validation->set_rules('pengumuman', 'Deskripsi Pengumuman', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {

			$this->db->update(
				'tb_options',
				[
					'option_desc1'		   	   => $this->input->post('pengumuman'),
				],
				[
					'option_name'				=> 'pengumuman',
				]
			);


			Self::$data['heading']           = 'Berhasil';
			Self::$data['message']           = 'Pengumuman Berhasil Diperbarui';
			Self::$data['type']              = 'success';
		} else {

			Self::$data['heading']           = 'Error';
			Self::$data['type']              = 'error';
		}

		return Self::$data;
	}

	function kirmpesann()
	{
		$this->db->where('typebroadcast_code', post('type_filter'));
		$cektype = $this->db->get('tb_typebroadcast');
		if ($cektype->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Type Filter Tidak Di Temukan";
		} else {
			$type = $cektype->row();

			if ($type->typebroadcast_code == 'v1luchxfcefgiv2jn0mppfism9yosbbnzdy1dviwotbhugkoqlkj37psaxn389xv') {
				$this->form_validation->set_rules('filt_pesann', 'Pesan', 'required');
				if (!$this->form_validation->run()) {
					Self::$data['status']     = false;
					Self::$data['message']     = validation_errors(' ', '<br/>');
				}
			}

			if ($type->typebroadcast_code == 'uikb2v7colrmnhi5ze6qzhbghouarfretwj4wlayjsxsnyntddxwamcp3e6j0umn') {

				$this->db->where('tglberangkat_code', post('fil_bokingseet'));
				$this->db->where('tglberangkat_status !=', 'success');
				$cektglberangkat = $this->db->get('tb_tglberangkat');
				if ($cektglberangkat->num_rows() != 0) {

					$tanggal = $cektglberangkat->row();

					// $this->db->join('tb_users', 'id = berangkat_userid');
					$this->db->where('berangkat_tglcode', $tanggal->tglberangkat_code);
					$cekberangkat = $this->db->get('tb_berangkat');
					if ($cekberangkat->num_rows() == 0) {
						Self::$data['status']     = false;
						Self::$data['message']     = "Data Keberangkatan Peserta Tidak Di Temukan";
					}
					// echo "<pre>";
					// print_r($datasss);
					// echo "</pre>";
				}
				// else {

				// }

				$this->form_validation->set_rules('fil_bokingseet', 'Booking Seet', 'required');
				$this->form_validation->set_rules('filt_pesann', 'Pesan', 'required');
				if (!$this->form_validation->run()) {
					Self::$data['status']     = false;
					Self::$data['message']     = validation_errors(' ', '<br/>');
				}
			}

			if ($type->typebroadcast_code == 'tv3jig8t0xf9orvbgej6k0p31luyhb4fy4mcaqxw129wmiewondgonvor7ugvlsk') {

				$this->form_validation->set_rules('order_provinsi', 'Provinsi', 'required');
				$this->form_validation->set_rules('order_kota', 'Kabupaten / Kota', 'required');
				$this->form_validation->set_rules('order_kecamatan', 'Kecamatan', 'required');
				$this->form_validation->set_rules('order_kelurahan', 'Kelurahan', 'required');
				$this->form_validation->set_rules('filt_pesann', 'Pesan', 'required');
				if (!$this->form_validation->run()) {
					Self::$data['status']     = false;
					Self::$data['message']     = validation_errors(' ', '<br/>');
				}

				$this->form_validation->set_rules('order_provinsi', 'Provinsi', 'required');
				$this->form_validation->set_rules('order_kota', 'Kabupaten / Kota', 'required');
				$this->form_validation->set_rules('order_kecamatan', 'Kecamatan', 'required');
				$this->form_validation->set_rules('order_kelurahan', 'Kelurahan', 'required');
				$this->form_validation->set_rules('filt_pesann', 'Pesan', 'required');
				if (!$this->form_validation->run()) {
					Self::$data['status']     = false;
					Self::$data['message']     = validation_errors(' ', '<br/>');
				}
			}

			if ($type->typebroadcast_code == 'orz98zmpx91ov7fhimny46rf8lvwcax2ixdn7j5ttrgmynojuls0wfgfaaluzsmc') {
				$this->db->where('id', post('users_id'));
				$cekusers 	= $this->db->get('tb_users');
				if ($cekusers->num_rows() == 0) {
					Self::$data['status']     = false;
					Self::$data['message']    = "Username Tidak Di Temukan";
				}

				$this->form_validation->set_rules('filt_pesann', 'Pesan', 'required');
				if (!$this->form_validation->run()) {
					Self::$data['status']     = false;
					Self::$data['message']     = validation_errors(' ', '<br/>');
				}
			}
		}

		$this->form_validation->set_rules('type_filter', 'Pilih Type', 'required');
		$this->form_validation->set_rules('filt_pesann', 'Pesan', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {
			$typeee = $cektype->row();

			if ($typeee->typebroadcast_code == 'v1luchxfcefgiv2jn0mppfism9yosbbnzdy1dviwotbhugkoqlkj37psaxn389xv') {
				$this->db->where('id !=', 1);
				$getusers = $this->db->get('tb_users');
				foreach ($getusers->result() as $users) {
					$nowa     = $users->user_phone;
					$pesan    = "Username: " . $users->username . "\r\n\r\nPesan: \r\n\r\n" . $this->input->post('filt_pesann') . "\r\n\r\nPT. Saudi Islamic Tour(SISPENJU)";
					$this->notifWA($nowa, $pesan);
				}
			}

			if ($typeee->typebroadcast_code == 'uikb2v7colrmnhi5ze6qzhbghouarfretwj4wlayjsxsnyntddxwamcp3e6j0umn') {

				$tglBerangkat  = $cektglberangkat->row();
				$userberangkat = $cekberangkat->row();

				$this->db->where('id !=', 1);
				$this->db->where('berangkat_tglcode', $tglBerangkat->tglberangkat_code);
				$this->db->join('tb_users', 'id = berangkat_userid');
				$berangkat = $this->db->get('tb_berangkat');
				foreach ($berangkat->result() as $showsss) {
					$nowa     = $showsss->user_phone;
					$pesan    = "Username: " . $showsss->username . "\r\n\r\nPesan: \r\n\r\n" . $this->input->post('filt_pesann') . "\r\n\r\nPT. Saudi Islamic Tour(SISPENJU)";
					$this->notifWA($nowa, $pesan);
				}
			}

			if ($typeee->typebroadcast_code == 'tv3jig8t0xf9orvbgej6k0p31luyhb4fy4mcaqxw129wmiewondgonvor7ugvlsk') {

				$this->db->where('id !=', 1);
				$this->db->where('user_provinsi', post('order_provinsi'));
				$this->db->where('user_kota', post('order_kota'));
				$this->db->where('user_kecamatan', post('order_kecamatan'));
				$this->db->where('user_kelurahan', post('order_kelurahan'));
				$cekusers = $this->db->get('tb_users');
				foreach ($cekusers->result() as $rows) {

					$getprov = $this->db->query('SELECT * FROM wilayah WHERE CHAR_LENGTH(kode) = 2 AND LEFT(kode,2)="' . $rows->user_provinsi . '"');
					$getkab = $this->db->query('SELECT * FROM wilayah WHERE CHAR_LENGTH(kode) = 5 AND LEFT(kode,5)="' . $rows->user_kota . '"');
					$getkec = $this->db->query('SELECT * FROM wilayah WHERE CHAR_LENGTH(kode) = 8 AND LEFT(kode,8)="' . $rows->user_kecamatan . '"');
					$getkel = $this->db->query('SELECT * FROM wilayah WHERE CHAR_LENGTH(kode) = 13 AND LEFT(kode,13)="' . $rows->user_kelurahan . '"');

					$nowa     = $rows->user_phone;
					$pesan    = "Username: " . $rows->username . "\r\n\r\nAlamat : \r\n\r\n" . $getprov->row()->nama . " - " . $getkab->row()->nama . " - " . $getkec->row()->nama . " - " . $getkel->row()->nama . "\r\n\r\nPesan: \r\n\r\n" . $this->input->post('filt_pesann') . "\r\n\r\nPT. Saudi Islamic Tour(SISPENJU)";
					$this->notifWA($nowa, $pesan);
				}
			}

			if ($typeee->typebroadcast_code == 'orz98zmpx91ov7fhimny46rf8lvwcax2ixdn7j5ttrgmynojuls0wfgfaaluzsmc') {
				$users 		= $cekusers->row();

				$nowa     = $users->user_phone;
				$pesan    = "Username: " . $users->username . "\r\n\r\n Pesan: \r\n\r\n" . $this->input->post('filt_pesann') . "\r\n\r\nPT. Saudi Islamic Tour(SISPENJU)";
				$this->notifWA($nowa, $pesan);
			}

			Self::$data['heading']           = 'Berhasil';
			Self::$data['message']           = 'Pesan Berhasil Di Kirim Kepada Semua Member';
			Self::$data['type']              = 'success';
		} else {

			Self::$data['heading']           = 'Error';
			Self::$data['type']              = 'error';
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


	function updatefungsi()
	{

		$this->db->where('option_name', $this->input->post('fungsi'));
		$cekkkkopt = $this->db->get('tb_options');
		if ($cekkkkopt->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Bagian Update Tidak Valid";
		}

		$this->form_validation->set_rules('fungsi', 'VALUE', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {

			if (option($this->input->post('fungsi'))['option_desc1'] == 'yes') {
				$this->db->update(
					'tb_options',
					[
						'option_desc1'		   	   => 'no',
					],
					[
						'option_name'				=> $this->input->post('fungsi'),
					]
				);
			} else {
				$this->db->update(
					'tb_options',
					[
						'option_desc1'		   	   => 'yes',
					],
					[
						'option_name'				=> $this->input->post('fungsi'),
					]
				);
			}

			Self::$data['heading']           = 'Berhasil';
			Self::$data['message']           = 'Fungsi Sistem Berhasil Diperbarui';
			Self::$data['type']              = 'success';
		} else {

			Self::$data['heading']           = 'Error';
			Self::$data['type']              = 'error';
		}

		return Self::$data;
	}

	function updatepaket()
	{
		$this->db->where('package_code', post('code'));
		$cekpaket = $this->db->get('tb_packages');
		if ($cekpaket->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Data Paket Tidak Valid";
		}

		$this->form_validation->set_rules('code', 'KODE', 'required');
		$this->form_validation->set_rules('package_sponsor', 'Bonus Sponsor', 'required');
		$this->form_validation->set_rules('package_pasangan', 'Bonus Pasangan', 'required');
		$this->form_validation->set_rules('package_flushout', 'Flush Out Bonus Pasangan', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}


		if (Self::$data['status']) {

			$this->db->update(
				'tb_packages',
				[
					'package_sponsor'		   => (int)$this->input->post('package_sponsor'),
					'package_pasangan'		   => (int)$this->input->post('package_pasangan'),
					'package_flushout'			=> (int)$this->input->post('package_flushout'),
				],
				[
					'package_code'				=> post('code'),
				]
			);

			Self::$data['heading']           = 'Berhasil';
			Self::$data['message']           = 'Data Paket Berhasil Diperbarui';
			Self::$data['type']              = 'success';
		} else {

			Self::$data['heading']           = 'Error';
			Self::$data['type']              = 'error';
		}

		return Self::$data;
	}

	function approveakun()
	{
		$this->db->where('verdata_status', 'pending');
		$this->db->where('verdata_code', post('code'));
		$cekuser = $this->db->get('tb_verdata');
		if ($cekuser->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Data Tidak Ditemukan";
		}

		$this->form_validation->set_rules('code', 'Kode Verifikasi', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}


		if (Self::$data['status']) {
			$userdata = $cekuser->row();
			$this->db->update(
				'tb_verdata',
				[
					'verdata_status'		=> 'success',
				],
				[
					'verdata_code'			=> post('code'),
				]
			);

			$this->db->update(
				'tb_users',
				[
					'user_verification'			 => '1',
				],
				[
					'id'						=> $userdata->verdata_userid
				]
			);

			Self::$data['heading']           = 'Berhasil';
			Self::$data['message']           = 'Data Member Berhasil Diverifikasi';
			Self::$data['type']              = 'success';
		} else {

			Self::$data['heading']           = 'Error';
			Self::$data['type']              = 'error';
		}

		return Self::$data;
	}

	function rejectakun()
	{
		$this->db->where('verdata_status', 'pending');
		$this->db->where('verdata_code', post('code'));
		$cekuser = $this->db->get('tb_verdata');
		if ($cekuser->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "Data Tidak Ditemukan";
		}

		$this->form_validation->set_rules('code', 'Kode Verifikasi', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}


		if (Self::$data['status']) {

			$this->db->delete('tb_verdata', array('verdata_code' => post('code')));

			Self::$data['heading']           = 'Berhasil';
			Self::$data['message']           = 'Data Member Berhasil Diverifikasi';
			Self::$data['type']              = 'success';
		} else {

			Self::$data['heading']           = 'Error';
			Self::$data['type']              = 'error';
		}

		return Self::$data;
	}

	function tambahProduk()
	{

		$config['upload_path']          = './assets/produk/';
		$config['allowed_types']        = 'jpg|png|jpeg';
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

		$this->form_validation->set_rules('produk_nama', 'Masukkan Nama Produk', 'required');
		$this->form_validation->set_rules('harga', 'Masukkan Harga Produk', 'required');
		$this->form_validation->set_rules('deskripsi', 'Masukkan Keterangan', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

		if (Self::$data['status']) {
			$uploaded   = $this->upload->data();
			$random_string = strtolower(random_string('alnum', 64));

			$urlartikel     =  url_title(post('produk_nama'), '-', true);
			$this->db->where('produk_name', $urlartikel);
			$Cekkk = $this->db->get('tb_produk');
			if ($Cekkk->num_rows() == 1) {
				$random = random_string('numeric', 2);
				$urlfix =  url_title(post('produk_nama') . $random, '-', true);
			} else {
				$urlfix =  url_title(post('produk_nama'), '-', true);
			}

			$this->db->insert(
				'tb_produk',
				[
					'produk_name'						=> $this->input->post('produk_nama'),
					'produk_gambar'						=> $uploaded['file_name'],
					'produk_harga'						=> $this->input->post('harga'),
					'produk_desc'						=> $this->input->post('deskripsi'),
					'produk_alias'						=> $urlfix,
					'produk_code'						=> $random_string
				]
			);


			$configg['image_library']       = 'gd2';
			$configg['source_image']        = './assets/produk/' . $uploaded['file_name'];
			$configg['create_thumb']        = FALSE;
			$configg['maintain_ratio']      = FALSE;
			$configg['quality']             = '50%';
			$configg['width']               = 'auto';
			$configg['height']              = 'auto';
			$configg['new_image']           = './assets/produk/thumbnail/' . $uploaded['file_name'];
			$this->load->library('image_lib', $configg);
			$this->image_lib->resize();

			Self::$data['message']      = 'Penambahan Produk Berhasil';
			Self::$data['heading']      = 'Berhasil';
			Self::$data['type']         = 'success';
		} else {
			Self::$data['heading']      = 'Error';
			Self::$data['type']         = 'error';
		}

		return Self::$data;
	}

	// contoh
	function updateProduk()
	{

		$this->form_validation->set_rules('produk_nama', 'Masukkan Nama Produk', 'required');
		$this->form_validation->set_rules('harga', 'Masukkan Harga Produk', 'required');
		$this->form_validation->set_rules('deskripsi', 'Masukkan Keterangan', 'required');
		$this->form_validation->set_rules('codes', 'Produk Code', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']    = false;
			Self::$data['message']    = validation_errors('', '<br>');
		}

		$this->db->where('produk_code', post('codes'));
		$cekartikel = $this->db->get('tb_produk');
		if ($cekartikel->num_rows() == 0) {
			Self::$data['status']    = false;
			Self::$data['message']    = "DATA TIDAK DITEMUKAN";
		}

		$config['upload_path']          = './assets/produk/';
		$config['allowed_types']        = 'gif|jpg|png|jpeg';
		$config['max_size']             = '99999999';
		$config['max_width']            = '99999999';
		$config['max_height']           = '99999999';
		$config['remove_spaces']        = TRUE;
		$config['encrypt_name']         = TRUE;
		$this->load->library('upload', $config);
		$this->upload->initialize($config);


		if (Self::$data['status']) {

			$urlartikel     =  url_title(post('produk_nama'), '-', true);
			$this->db->where('produk_name', $urlartikel);
			$Cekkk = $this->db->get('tb_produk');
			if ($Cekkk->num_rows() == 1) {
				$random = random_string('numeric', 2);
				$urlfix =  url_title(post('produk_nama') . $random, '-', true);
			} else {
				$urlfix =  url_title(post('produk_nama'), '-', true);
			}


			if (!$this->upload->do_upload('upd_imgcover')) {
				$this->db->where('produk_code', post('codes'));
				$this->db->update('tb_produk', array(
					'produk_name'    => post('produk_nama'),
					'produk_harga'    => post('harga'),
					'produk_desc'    => post('deskripsi'),
					'produk_alias'    => $urlfix,
				));
			} else {
				$uploaded = $this->upload->data();

				if (!empty($cekartikel->row()->produk_gambar)) {
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/produk/' . $cekartikel->row()->produk_gambar)) {
						unlink($_SERVER['DOCUMENT_ROOT'] . '/assets/produk/' . $cekartikel->row()->produk_gambar);
					}

					if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/produk/thumbnail/' . $cekartikel->row()->produk_gambar)) {
						unlink($_SERVER['DOCUMENT_ROOT'] . '/assets/produk/thumbnail/' . $cekartikel->row()->produk_gambar);
					}
				}


				$this->db->where('produk_code', post('codes'));
				$this->db->update('tb_produk', array(
					'produk_name'    => post('produk_nama'),
					'produk_harga'    => post('harga'),
					'produk_desc'    => post('deskripsi'),
					'produk_gambar'    => $uploaded['file_name'],
					'produk_alias'    => $urlfix
				));

				$configg['image_library']    =   'gd2';
				$configg['source_image']     =   './assets/produk/' . $uploaded['file_name'];
				$configg['create_thumb']     =   FALSE;
				$configg['maintain_ratio']   =   FALSE;
				$configg['quality']          =   '50%';
				$configg['width']            =   1024;
				$configg['height']           =   585;
				$configg['new_image']        =   './assets/produk/thumbnail/' . $uploaded['file_name'];
				$this->load->library('image_lib', $configg);
				$this->image_lib->resize();
			}

			Self::$data['heading']    = "SUCCESS";
			Self::$data['message']    = "BERHASIL";
			Self::$data['type']        = "success";
		} else {
			Self::$data['heading']    = "GAGAL";
			Self::$data['type']        = "error";
		}
		return Self::$data;
	}
	// akhir contoh


	function hapusProduk()
	{
		$this->db->where('produk_code', post('code'));
		$cekdata = $this->db->get('tb_produk');
		if ($cekdata->num_rows() == 0) {
			Self::$data['status']       = false;
			Self::$data['message']      = 'Data Bank Tidak Valid!';
		}

		$this->form_validation->set_rules('code', 'Produk Code', 'required');
		if ($this->form_validation->run() == false) {
			Self::$data['status'] = false;
			Self::$data['message'] = validation_errors(' ', '<br>');
		}

		if (Self::$data['status']) {

			$this->db->delete('tb_produk', array('produk_code' => post('code')));


			Self::$data['heading']      = "Berhasil";
			Self::$data['message']      = "Data Produk Berhasil Dihapus";
			Self::$data['type']         = "success";
		} else {
			Self::$data['heading']      = "Gagal";
			Self::$data['type']         = "error";
		}

		return Self::$data;
	}
}
