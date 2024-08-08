<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Registrasi extends CI_Model
{

    private static $data = [
        'status'     => true,
        'message'     => null,
    ];

    public function __construct()
    {
        parent::__construct();
        Self::$data['csrf_data']         = $this->security->get_csrf_hash();
    }

    function register(){
        $this->db->where('user_code', post('distribusi'));
        $cekupline = $this->db->get('tb_users');
        if ($cekupline->num_rows() == 0) {
            Self::$data['status']     = false;
            Self::$data['message']     = 'Distribusi Tidak Valid';
        }

        $this->db->where('user_code', post('refferal'));
        $cekreff = $this->db->get('tb_users');
        if ($cekreff->num_rows() == 0) {
            Self::$data['status']     = false;
            Self::$data['message']     = 'Referensi Tidak Valid';
        }
        /*============================================
		=     VALIDASI INPUT AGAR TIDAK KOSONG       =
		============================================*/

        // $this->form_validation->set_rules('user_username', 'Username', 'trim|required|min_length[3]|is_unique[tb_users.username]', array(
        //     'is_unique'    => 'Username Sudah Digunakan, Gunakan Username Lain.'
        // ));
        $this->form_validation->set_rules('user_name', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('user_email', 'Alamat Email', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('user_phone', 'Nomor WhatsApp', 'trim|required');
        $this->form_validation->set_rules('user_bank_name', 'Jenis Bank', 'trim|required');
        $this->form_validation->set_rules('user_bank_number', 'Nomor Rekening', 'trim|required');
        $this->form_validation->set_rules('user_bank_account', 'Nomor Rekening', 'required');
        $this->form_validation->set_rules('user_alamat', 'Alamat', 'required');
        $this->form_validation->set_rules('user_provinsi', 'Provinsi', 'required');
        $this->form_validation->set_rules('user_kota', 'Kota', 'required');
        $this->form_validation->set_rules('user_kecamatan', 'Kecamatan', 'required');
        $this->form_validation->set_rules('kode_pos', 'Kode Pos', 'required');
        $this->form_validation->set_rules('tipe_anggota', 'Type Anggota', 'required');
        $this->form_validation->set_rules('distribusi', 'Distribusi', 'required');
        $this->form_validation->set_rules('refferal', 'Referensi', 'required');
        $this->form_validation->set_rules('user_password', 'Password', 'trim|required|min_length[6]');
        $this->form_validation->set_rules('user_confpassword', 'Ulangi Password', 'trim|required|matches[user_password]');
        if (!$this->form_validation->run()) {
            Self::$data['status']     = false;
            Self::$data['message']     = validation_errors(' ', '<br/>');
        }

        if (Self::$data['status']) {
            $random_string         = strtolower(random_string('alnum', 64));
            $trx_id             = hash('SHA256', random_string('alnum', 16));
            // $datapaket			= $cekpinnn->row();
            $gusername         = $this->generateUsername(NULL, post('tipe_anggota'));
            $upline = $cekupline->row();
            $referal = $cekreff->row();


            /*============================================
			=            INPUT DATA PENDAFTAR            =
			============================================*/
            $additional_data     = array(
                'upline_id'             => $upline->id,
                'referral_id'           => $referal->id,
                'user_fullname'         => $this->input->post('user_name'),
                'user_phone'            => $this->input->post('user_phone'),
                'user_bank_account'     => $this->input->post('user_bank_account'),
                'user_bank_name'        => $this->input->post('user_bank_name'),
                'user_bank_number'      => $this->input->post('user_bank_number'),
                'user_provinsi'         => $this->input->post('user_provinsi'),
                'user_kota'             => $this->input->post('user_kota'),
                'user_kecamatan'        => $this->input->post('user_kecamatan'),
                'user_alamat'           => $this->input->post('user_alamat'),
                'user_kodepos'          => $this->input->post('kode_pos'),
                'user_referral_code'    => random_string('alnum', 6),
                'user_type'             => post('tipe_anggota'),
                'password_text'         => post('user_password'),
                'user_code'             => $random_string,
            );

            $this->ion_auth->register(str_replace(' ', '', $gusername), $this->input->post('user_password'), str_replace(' ', '', $this->input->post('user_email')), $additional_data, array(2));
            $last_user         = userdata(array('user_code' => $random_string));

            /*============================================
			=              MEMBUAT WALLET               =
			============================================*/
            $this->db->insert(
                'tb_users_wallet',
                [
                    'wallet_user_id'      => $last_user->id,
                    'wallet_address'      => generateWallet(),
                    'wallet_type'         => 'withdrawal',
                    'wallet_date_added'   => sekarang()
                ]
            );

            /*============================================
			=            INSERT BONUS REFRENSI           =
			============================================*/
            $this->db->insert('tb_breferensi', [
                'breferensi_userid' => $referal->id,
                'breferensi_amount' => 10000, //menyusul untuk nominal
                'breferensi_date'   => sekarang(),
                'breferensi_code'   => $random_string,
            ]);


            /*============================================
			=            INSERT TITIK LEVEL              =
			============================================*/
            $this->titiklevel($last_user->id, $last_user->id, 1);



            Self::$data['heading']     = 'Berhasil';
            Self::$data['message']     = 'Registrasi User Berhasil';
            Self::$data['type']        = 'success';
        } else {

            Self::$data['heading']     = 'Gagal';
            Self::$data['type']        = 'error';
        }

        return Self::$data;
    }

    function generateUsername($username_text = null, $tipe = null)
    {
        $alias = null;
        if ($tipe == 'distributor') {
            $alias = 'DS';
        } elseif ($tipe == 'agen') {
            $alias = 'AG';
        } elseif ($tipe == 'reseller') {
            $alias = 'RS';
        }

        $this->db->where('user_type', $tipe);
        $this->db->where('id !=', (int)1);
        $summember = $this->db->get('tb_users')->num_rows();

        $numberoff = $summember + 1;

        $new_username     = ($username_text == null) ? $alias . sprintf('%07d', $numberoff) : $username_text;

        $this->db->where('username', $new_username);
        $cek             = $this->db->get('tb_users');

        if ($cek->num_rows() == 1) {

            $rand_username     = $alias . ($numberoff + 1);
            $this->generateUsername($rand_username);
        } else {
            return $new_username;
        }
    }

    function titiklevel($user_id = null, $user_id_from = null, $level = 1)
    {
        $result         = array();
        $status         = true;

        $datauser         = userdata(['id' => $user_id]);
        $userdata         = userdata(['id' => $user_id_from]);
        if ($userdata->upline_id == 0) {
            $status = false;
        }

        $uplinedata     = userdata(['id' => $userdata->upline_id]);

        if ($status) {

            if ($uplinedata) {
                $this->db->insert('tb_titiklevel', [
                    'titiklevel_userid'             => $uplinedata->id,
                    'titiklevel_downlineid'         => $datauser->id,
                    'titiklevel_level'              => $level,
                    'titiklevel_date'               => sekarang(),
                ]);

                $this->titiklevel($datauser->id, $uplinedata->id, $level + 1);
            }
        }
        return $result;
    }
}
