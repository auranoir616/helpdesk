<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Helpdesk_admin extends CI_Model
{
    private static $data = [
        'status'    => true,
        'message'   => null,
    ];

    public function __construct()
    {
        parent::__construct();
        Self::$data['csrf_data']    = $this->security->get_csrf_hash();
    }


    function tambahTiketUntukUser(){

        $config['upload_path']          = './assets/upload/';
        $config['allowed_types']        = 'gif|jpg|png|jpeg|bmp|svg|webp';
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
        $this->form_validation->set_rules('tiket_userid', 'User ', 'required');
        $this->form_validation->set_rules('tiket_petugas', 'petugas', 'required');
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
                    'tiket_userid' => post('tiket_userid'),
                    'tiket_petugas' => post('tiket_petugas'),
                    'tiket_judul' => post('tiket_judul'),
                    'tiket_kategori' => post('tiket_kategori'),
                    'tiket_tipe' => post('tiket_tipe'),
                    'tiket_desc' => post('tiket_desc'),
                    'tiket_date' => sekarang(),
                    'tiket_image' => json_encode($tiket_image_data),
                    'tiket_status' => 'process',
                ]
            );
        $tikeid = $this->db->insert_id();
                    //notif untuk user
        $this->db->insert('tb_notif',[
            'notif_useridfrom' => userid(),
            'notif_useridto' => post('tiket_userid'),
            'notif_desc' => 'Tiket '.post('tiket_judul').' Sudah Ditambahkan oleh Admin Untuk Anda',
            'notif_date' => sekarang(),
            'notif_tiketid' => $tikeid ,
        ]);
        //nmotif untuk petugas
        $this->db->insert('tb_notif',[
            'notif_useridfrom' => userid(),
            'notif_useridto' => post('tiket_petugas'),
            'notif_desc' =>  'Tiket '.post('tiket_judul').' Sudah Ditambahkan oleh Admin Untuk Anda selesaikan',
            'notif_date' => sekarang(),
            'notif_tiketid' => $tikeid,
        ]);

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

            Self::$data['message']      = 'Tambah Tiket Berhasil';
            Self::$data['heading']      = 'Berhasil';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Error';
            Self::$data['type']         = 'error';
        }
        return Self::$data;
    }

    

public function pilihPetugas(){
        $this->db->where('id', $this->input->post('pilih_petugas'));
        $cekUser = $this->db->get('tb_users');
        if($cekUser->num_rows() == 0){
            Self::$data['status'] = false;
            Self::$data['message'] = 'Petugas tidak ditemukan';
    }   
        $this->db->where('tiket_id', $this->input->post('idtiket'));
        $cekTiket = $this->db->get('tb_tiket');
        if($cekTiket->num_rows() == 0){
            Self::$data['status'] = false;
            Self::$data['message'] = 'Tiket tidak ditemukan';
    }   

    if(Self::$data['status']){
        $this->db->where('tiket_id', $this->input->post('idtiket'));
        $this->db->update('tb_tiket', [
            'tiket_petugas' => $this->input->post('pilih_petugas'),
        ]);
        //notif untuk user
        $this->db->insert('tb_notif',[
            'notif_useridfrom' => userid(),
            'notif_useridto' => $cekTiket->row()->tiket_userid,
            'notif_desc' => 'Tiket '.$cekTiket->row()->tiket_judul.' Sudah dikonfirmasi oleh Admin',
            'notif_date' => sekarang(),
            'notif_tiketid' => $this->input->post('idtiket'),
        ]);
        //nmotif untuk petugas
        $this->db->insert('tb_notif',[
            'notif_useridfrom' => userid(),
            'notif_useridto' => $this->input->post('pilih_petugas'),
            'notif_desc' => 'Anda Dipilih untuk Menangani Tiket '.$cekTiket->row()->tiket_judul ,
            'notif_date' => sekarang(),
            'notif_tiketid' => $this->input->post('idtiket'),
        ]);

        Self::$data['message']      = 'Pilih Petugas Berhasil, Status Tiket Diperbarui!';
        Self::$data['heading']      = 'Berhasil';
        Self::$data['type']         = 'success';
    }else{
        Self::$data['heading']      = 'Error';
        Self::$data['type']         = 'error';
    }
    return Self::$data;
}
    function HapusTiketAdmin()
    {
        $tiket_id = $this->input->post('tiket_id');

        if (!$tiket_id) {
            Self::$data['status'] = false;
            Self::$data['heading'] = 'Error';
            Self::$data['message'] = 'Terjadi Kesalahan Silahkan Coba Lagi';
            Self::$data['csrf_data'] = $this->security->get_csrf_hash();
            Self::$data['type'] = 'error';
        }
        $this->db->where('tiket_id', $tiket_id);
        $cekTiket = $this->db->get('tb_tiket');
        if ($cekTiket->num_rows() == 0) {
            Self::$data['status'] = false;
            Self::$data['message'] = 'Tiket Tidak Ditemukan';
            Self::$data['type'] = 'error';
        }
        if (Self::$data['status']) {
            $this->db->where('tiket_id', $tiket_id);
            $tiket = $cekTiket->row();
            $this->db->delete('tb_tiket');
            if (file_exists('./assets/upload/' . $tiket->tiket_image)) {
                unlink('./assets/upload/' . $tiket->tiket_image);
            }

            if (file_exists('./assets/upload/thumbnail/' . $tiket->tiket_image)) {
                unlink('./assets/upload/thumbnail/' . $tiket->tiket_image);
            }

            $this->db->insert('tb_notif',[
                'notif_useridfrom' => userid(),
                'notif_useridto' => $cekTiket->row()->tiket_userid,
                'notif_desc' => 'Tiket '.$cekTiket->row()->tiket_judul.' Dihapus oleh Admin. ',
                'notif_date' => sekarang(),
                'notif_tiketid' => $tiket_id,
            ]);

            Self::$data['status'] = true;
            Self::$data['heading'] = 'Berhasil';
            Self::$data['message'] = 'Tiket berhasil dihapus.';
            Self::$data['csrf_data'] = $this->security->get_csrf_hash();
            Self::$data['type'] = 'success';
        } else {
            Self::$data['status'] = false;
            Self::$data['heading'] = 'Error';
            Self::$data['csrf_data'] = $this->security->get_csrf_hash();
            Self::$data['message'] = 'Gagal menghapus Tiket.';
            Self::$data['type'] = 'error';
        }
        return Self::$data;
    }


    function updatedatamember()
	{
		$this->db->where('id', post('id'));
		$cekuser = $this->db->get('tb_users');
		if ($cekuser->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "User Data Not Found";
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

		$foto_profile = null;
		if (!$this->upload->do_upload('user_picture')) {
			$foto_profile = null;
		} else {
			$foto_profile = $this->upload->data();
		}


		$this->form_validation->set_rules('id', 'User id', 'required');
		$this->form_validation->set_rules('user_fullname', 'Full Name', 'required');
		$this->form_validation->set_rules('email', 'Email Address', 'required');
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('user_phone', 'Phone Number', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}


		if (Self::$data['status']) {

            $update_data = [
                'user_fullname'    => $this->input->post('user_fullname'),
                'user_phone'       => $this->input->post('user_phone'),
                'email'  => $this->input->post('email'),
                'username'  => $this->input->post('username'),
                
            ];
            if ($foto_profile && !empty($foto_profile['file_name'])) {
                $update_data['user_picture'] = $foto_profile['file_name'];
            }
            $this->db->where('id', post('id'));
			$this->db->update('tb_users',$update_data);

            if (!empty($foto_profile['file_name'])) {
                $configg['image_library']   = 'gd2';
                $configg['source_image']    = './assets/upload/' . $foto_profile['file_name'];
                $configg['create_thumb']    = FALSE;
                $configg['maintain_ratio']  = FALSE;
                $configg['quality']         = '50%';
                $configg['width']           = 'auto';
                $configg['height']          = 'auto';
                $configg['new_image']       = './assets/upload/thumbnail/' . $foto_profile['file_name'];
                $this->load->library('image_lib', $configg);
                $this->image_lib->resize();
            }
            

			Self::$data['heading']           = 'Success';
			Self::$data['message']           = 'Data Member Berhasil Diperbarui';
			Self::$data['type']              = 'success';
		} else {

			Self::$data['heading']           = 'Error';
			Self::$data['type']              = 'error';
		}

		return Self::$data;
	}

    function updatepasswordmember()
	{
		$this->db->where('id', post('id'));
		$cekuser = $this->db->get('tb_users');
		if ($cekuser->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "User Data Not Found";
		}

		$this->form_validation->set_rules('id', 'User id', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}


		if (Self::$data['status']) {
			$userdata = $cekuser->row();

			$this->ion_auth->update($userdata->id, [
				'password'	=> post('password')
			]);

			Self::$data['heading']           = 'Success';
			Self::$data['message']           = 'Password Member Berhasil Diperbarui';
			Self::$data['type']              = 'success';
		} else {

			Self::$data['heading']           = 'Error';
			Self::$data['type']              = 'error';
		}

		return Self::$data;
	}

    function tambahfaq(){
        $this->form_validation->set_rules('faq_question', 'Pertanyaan ', 'required');
        $this->form_validation->set_rules('faq_answer', 'Jawaban', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

        if (Self::$data['status']) {
            $this->db->insert(
                'tb_faq',
                [
                    'faq_question' => post('faq_question'),
                    'faq_answer' => post('faq_answer'),
                    'faq_status' => 'aktif',
                    'faq_date' => sekarang(),
                    'faq_userid' => userid(),
                ]
            );

            Self::$data['message']      = 'Tambah FAQ Berhasil';
            Self::$data['heading']      = 'Berhasil';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Error';
            Self::$data['type']         = 'error';
        }
        return Self::$data;
    }

    function hapusfaq(){
        $this->db->where('faq_id', post('faq_id'));
		$cekuser = $this->db->get('tb_faq');
		if ($cekuser->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "FAQ tidak ditemukan";
		}

        if (Self::$data['status']) {
            $this->db->where('faq_id', post('faq_id'));
            $this->db->delete('tb_faq');

            Self::$data['message']      = 'FAQ Berhasil Dihapus';
            Self::$data['heading']      = 'Berhasil';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Error';
            Self::$data['type']         = 'error';
        }
        return Self::$data;
    }

    function updatefaq(){

        $this->db->where('faq_id', post('faq_id'));
		$cekuser = $this->db->get('tb_faq');
		if ($cekuser->num_rows() == 0) {
			Self::$data['status']     = false;
			Self::$data['message']     = "FAQ tidak ditemukan";
		}

        $this->form_validation->set_rules('faq_question', 'Pertanyaan ', 'required');
        $this->form_validation->set_rules('faq_answer', 'Jawaban', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}

        if (Self::$data['status']) {
            $this->db->where('faq_id', post('faq_id'));
            $this->db->update(
                'tb_faq',
                [
                    'faq_question' => post('faq_question'),
                    'faq_answer' => post('faq_answer'),
                    'faq_status' => 'aktif',
                    'faq_date' => sekarang(),
                    'faq_userid' => userid(),
                ]
            );

            Self::$data['message']      = 'Update FAQ Berhasil';
            Self::$data['heading']      = 'Berhasil';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Error';
            Self::$data['type']         = 'error';
        }
        return Self::$data;
    }








}
