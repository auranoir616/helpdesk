<?php defined('BASEPATH') or exit('No direct script access allowed');

class Helpdesk_notes extends CI_Model
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
    function tambahnotes(){

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
        $notes_image = $_FILES['notes_image'];
        $notes_image_data = array();

        $this->form_validation->set_rules('notes_judul', 'Subjek notes', 'required');
        $this->form_validation->set_rules('notes_desc', 'deskripsi notes', 'required');
		if (!$this->form_validation->run()) {
			Self::$data['status']     = false;
			Self::$data['message']     = validation_errors(' ', '<br/>');
		}


        // Loop through uploaded files
        for ($i = 0; $i < count($notes_image['name']); $i++) {
            $_FILES['file'] = array(
                'name'     => $notes_image['name'][$i],
                'type'     => $notes_image['type'][$i],
                'tmp_name' => $notes_image['tmp_name'][$i],
                'error'    => $notes_image['error'][$i],
                'size'     => $notes_image['size'][$i]
            );
            // Attempt to upload file
            if (!$this->upload->do_upload('file')) {
                Self::$data['status']     = false;
                Self::$data['message']    = $this->upload->display_errors();
                return Self::$data;
            }
            $upload_data = $this->upload->data();
            $notes_image_data[] = $upload_data['file_name'];
        }

        if (Self::$data['status']) {
            // Update the database with new file data
            $this->db->insert(
                'tb_notes',
                [
                    'notes_userid' => userid(),
                    'notes_judul' => post('notes_judul'),
                    'notes_desc' => post('notes_desc'),
                    'notes_date' => sekarang(),
                    'notes_image' => json_encode($notes_image_data),
                ]
            );

            // Optional: Resize images if needed
            foreach ($notes_image_data as $file_name) {
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

            Self::$data['message']      = 'Tambah notes Berhasil';
            Self::$data['heading']      = 'Berhasil';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Error';
            Self::$data['type']         = 'error';
        }
        return Self::$data;
    }


}
