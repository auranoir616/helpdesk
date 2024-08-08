<?php defined('BASEPATH') or exit('No direct script access allowed');

class Helpdesk_user extends CI_Model
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

    function updateprofile()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');

		if (!$this->ion_auth->hash_password_db(userid(), $this->input->post('user_pass'))) {
			self::$data['status']  = false;
			self::$data['message'] = 'Konfirmasi Password Tidak Sesuai!';
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


		$this->form_validation->set_rules('user_fullname', 'Nama Lengkap', 'required');
		$this->form_validation->set_rules('user_phone', 'No WhatsApp', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required');
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('user_pass', 'Konfirmasi Password', 'required');
		if (!$this->form_validation->run()) {
			self::$data['status']  = false;
			self::$data['message'] = validation_errors(' ', '<br/>');
		}
        if(Self::$data['status']) {
            
            $update_data = [
                'user_fullname'    => $this->input->post('user_fullname'),
                'user_phone'       => $this->input->post('user_phone'),
                'email'  => $this->input->post('email'),
                'username'  => $this->input->post('username'),
                
            ];
            if ($foto_profile && !empty($foto_profile['file_name'])) {
                $update_data['user_picture'] = $foto_profile['file_name'];
            }
            
            $this->ion_auth->update(userid(), $update_data);
            
            // Resize gambar user_ktp
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
            
            self::$data['status']  = true;
            self::$data['heading'] = 'Berhasil';
            self::$data['message'] = 'Data Profile Anda Telah Diperbarui!';
            self::$data['type']    = 'success';
        }else{
            self::$data['status']  = false;
            self::$data['heading'] = 'Gagal';
            self::$data['type']    = 'error';
        }
            
            return self::$data;
	}

    function change_user_password()
	{
        if (!$this->ion_auth->hash_password_db(userid(), $this->input->post('current_password'))) {
			self::$data['status']  = false;
			self::$data['message'] = 'password lama tidak sesuai!';
		}

        if(post('new_password') != post('confirm_password')) {
            self::$data['status']  = false;
            self::$data['message'] = 'password baru tidak sesuai!';
        }
        if(Self::$data['status']) {

		$this->ion_auth->update(post('id'), array('password' => post('new_password')));

		Self::$data['message'] 	= 'Password berhasil di update !';
		Self::$data['heading'] 	= 'Berhasil';
		Self::$data['type'] 	= 'success';
        }else{
            self::$data['status']  = false;
            self::$data['heading'] = 'Gagal';
            self::$data['type']    = 'error';
        }

		return Self::$data;
	}


}