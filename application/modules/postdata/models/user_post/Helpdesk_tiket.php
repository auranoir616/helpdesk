<?php defined('BASEPATH') or exit('No direct script access allowed');

class Helpdesk_tiket extends CI_Model
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

    function tambahtiket(){

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
                    'tiket_userid' => userid(),
                    'tiket_judul' => post('tiket_judul'),
                    'tiket_kategori' => post('tiket_kategori'),
                    'tiket_tipe' => post('tiket_tipe'),
                    'tiket_desc' => post('tiket_desc'),
                    'tiket_date' => sekarang(),
                    'tiket_image' => json_encode($tiket_image_data),
                ]
            );
            $tiket_id = $this->db->insert_id();

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

                        // untuk admin
                        $this->db->insert('tb_notif',[
                            'notif_useridfrom' => userid(),
                            'notif_useridto' => 0,
                            'notif_desc' => 'User '. userdata()->username. ' telah menambah tiket baru',
                            'notif_date' => sekarang(),
                            'notif_tiketid' => $tiket_id,
                        ]);
            

            Self::$data['message']      = 'Tambah Tiket Berhasil';
            Self::$data['heading']      = 'Berhasil';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Error';
            Self::$data['type']         = 'error';
        }
        return Self::$data;
    }

    function HapusTiket()
    {
        $tiket_id = $this->input->post('idtiket');

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
    function selesaikanTiket()
    {
        $tiket_id = $this->input->post('idtiket');

        if (!$tiket_id) {
            Self::$data['status'] = false;
            Self::$data['heading'] = 'Error';
            Self::$data['message'] = 'Terjadi Kesalahan Silahkan Coba Lagi';
            Self::$data['csrf_data'] = $this->security->get_csrf_hash();
            Self::$data['type'] = 'error';
        }

        $cekTiket = $this->db->get('tb_tiket');
        if ($cekTiket->num_rows() == 0) {
            Self::$data['status'] = false;
            Self::$data['message'] = 'Tiket Tidak Ditemukan';
            Self::$data['type'] = 'error';
        }
        if (Self::$data['status']) {
            $this->db->where('tiket_id', $tiket_id);
            $this->db->update('tb_tiket',[
                'tiket_status' => 'complete'
            ]);
            //untuk user
            $this->db->insert('tb_notif',[
                'notif_useridfrom' => userid(),
                'notif_useridto' => $cekTiket->row()->tiket_userid,
                'notif_desc' => 'Tiket '.$cekTiket->row()->tiket_judul.' Sudah diselesaikan Oleh '. userdata()->username,
                'notif_date' => sekarang(),
                'notif_tiketid' => $tiket_id,
            ]);
            // untuk admin
            $this->db->insert('tb_notif',[
                'notif_useridfrom' => userid(),
                'notif_useridto' => 0,
                'notif_desc' => 'Tiket '.$cekTiket->row()->tiket_judul.' Sudah diselesaikan '. userdata()->username,
                'notif_date' => sekarang(),
                'notif_tiketid' => $tiket_id,
            ]);

           
            Self::$data['status'] = true;
            Self::$data['heading'] = 'Berhasil';
            Self::$data['message'] = 'Tiket berhasil Diselesaikan.';
            Self::$data['csrf_data'] = $this->security->get_csrf_hash();
            Self::$data['type'] = 'success';
        } else {
            Self::$data['status'] = false;
            Self::$data['heading'] = 'Error';
            Self::$data['csrf_data'] = $this->security->get_csrf_hash();
            Self::$data['message'] = 'Gagal menyelesaikan Tiket.';
            Self::$data['type'] = 'error';
        }
        return Self::$data;
    }

    function updateTiket(){

        $this->db->where('tiket_id', post('tiket_id'));
        $cekTiket = $this->db->get('tb_tiket');
        if ($cekTiket->num_rows() == 0) {
            Self::$data['status'] = false;
            Self::$data['message'] = 'Tiket Tidak Ditemukan';
            Self::$data['type'] = 'error';
        }


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
            $tiket = $cekTiket->row();
            if (file_exists('./assets/upload/' . $tiket->tiket_image)) {
                unlink('./assets/upload/' . $tiket->tiket_image);
            }

            if (file_exists('./assets/upload/thumbnail/' . $tiket->tiket_image)) {
                unlink('./assets/upload/thumbnail/' . $tiket->tiket_image);
            }

            // Update the database with new file data
            $this->db->where('tiket_id', post('tiket_id'));
            $this->db->update(
                'tb_tiket',
                [
                    'tiket_userid' => userid(),
                    'tiket_judul' => post('tiket_judul'),
                    'tiket_kategori' => post('tiket_kategori'),
                    'tiket_tipe' => post('tiket_tipe'),
                    'tiket_desc' => post('tiket_desc'),
                    'tiket_date' => sekarang(),
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

            Self::$data['message']      = 'Tambah Tiket Berhasil';
            Self::$data['heading']      = 'Berhasil';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Error';
            Self::$data['type']         = 'error';
        }
        return Self::$data;
    }
    public function KonfirmasiPetugas(){
        $this->db->where('tiket_id', $this->input->post('idtiket'));
        $cekTiket = $this->db->get('tb_tiket');
        if($cekTiket->num_rows() == 0){
            Self::$data['status'] = false;
            Self::$data['message'] = 'Tiket tidak ditemukan';
    }   

    if(Self::$data['status']){
        $this->db->where('tiket_id', $this->input->post('idtiket'));
        $this->db->update('tb_tiket', [
            'tiket_status' => 'process',
        ]);
        //notif untuk user
        $this->db->insert('tb_notif',[
            'notif_useridfrom' => userid(),
            'notif_useridto' => $cekTiket->row()->tiket_userid,
            'notif_desc' => 'Tiket '.$cekTiket->row()->tiket_judul.' Sudah Tangani oleh petugas '. userdata()->username,
            'notif_date' => sekarang(),
            'notif_tiketid' => $this->input->post('idtiket'),
        ]);
        //nmotif untuk admin
        $this->db->insert('tb_notif',[
            'notif_useridfrom' => userid(),
            'notif_useridto' => 0,
            'notif_desc' => 'Petugas '.userdata()->username.' Sejutu untuk Menangani Tiket '.$cekTiket->row()->tiket_judul ,
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




    

    }
