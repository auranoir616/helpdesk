<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pinserial extends CI_Model
{

    private static $data = [
        'status'     => true,
        'message'     => null,
    ];

    public function __construct()
    {
        parent::__construct();
        Self::$data['csrf_data']     = $this->security->get_csrf_hash();
    }

    // function usepin()
    // {
    //     $this->db->where('pin_code', $this->input->post('code'));
    //     $this->db->where('pin_status', 'available');
    //     $this->db->join('tb_packages', 'pin_package_id = package_id');
    //     $cekpin = $this->db->get('tb_users_pin');
    //     if ($cekpin->num_rows() == 0) {
    //         Self::$data['status']     = false;
    //         Self::$data['message']     = "PIN & SERIAL TIDAK VALID";
    //     }

    //     if ($this->walletmodel->saldoro() > 0) {
    //         Self::$data['status']     = false;
    //         Self::$data['message']    = "SALDO TERSISA Rp. " . number_format($this->walletmodel->saldoro(), 0, ',', '.') . " PASTIKAN SALDO HABIS";
    //     }

    //     $this->form_validation->set_rules('code', 'CODE', 'required');
    //     if (!$this->form_validation->run()) {
    //         Self::$data['status']     = false;
    //         Self::$data['message']     = validation_errors(' ', '<br/>');
    //     }



    //     if (Self::$data['status']) {

    //         $datapinserial = $cekpin->row();

    //         /*============================================
    //         =            UPDATE STATUS DISKON           =
    //         ============================================*/
    //         $this->db->update(
    //             'tb_users',
    //             [
    //                 'user_discount'         => 35,
    //             ],
    //             [
    //                 'id'                    => $datapinserial->pin_userid,
    //             ]
    //         );

    //         /*============================================
    //         =      UPDATE STATUS PIN KE DIGUNAKAN        =
    //         ============================================*/
    //         $this->db->update(
    //             'tb_users_pin',
    //             [
    //                 'pin_status'        => 'used',
    //             ],
    //             [
    //                 'pin_code'          => $datapinserial->pin_code,
    //             ]
    //         );

    //         /*============================================
    //         =            REPORT PIN KE PEMILIK           =
    //         ============================================*/

    //         $this->db->insert(
    //             'tb_reportpin',
    //             [
    //                 'reportpin_userid'      => $datapinserial->pin_userid,
    //                 'reportpin_desc'        => 'Gunakan PIN & Serial Paket ' . $datapinserial->package_name,
    //                 'reportpin_date'        => sekarang(),
    //                 'reportpin_code'        => strtolower(random_string('alnum', 64)),
    //             ]
    //         );

    //         /*============================================
    //         =            INSERT SALDO RO           		=
    //         ============================================*/
    //         $this->db->insert(
    //             'tb_saldoro',
    //             [
    //                 'saldoro_userid'        => userid(),
    //                 'saldoro_amount'        => $datapinserial->package_price,
    //                 'saldoro_type'          => 'credit',
    //                 'saldoro_date'          => sekarang(),
    //                 'saldoro_code'          => strtolower(random_string('alnum', 64)),
    //             ]
    //         );


    //         Self::$data['heading']      = 'Berhasil';
    //         Self::$data['message']      = 'PIN & Serial Berhasil Digunakan.';
    //         Self::$data['type']         = 'success';
    //     } else {
    //         Self::$data['heading']      = 'Error';
    //         Self::$data['type']         = 'error';
    //     }

    //     return Self::$data;
    // }


    function cekkirimpin()
    {
        if (!$this->ion_auth->hash_password_db(userid(), post('konfirmasi_password'))) {
            Self::$data['status']       = false;
            Self::$data['message']      = 'Konfirmasi Password Tidak Sesuai!';
        }

        $this->db->where('package_code', $this->input->post('paket_pin'));
        $cekkkpaket = $this->db->get('tb_packages');
        if ($cekkkpaket->num_rows() == 0) {
            Self::$data['status']       = false;
            Self::$data['message']      = 'Paket PIN Kode Tidak Valid!';
        } else {
            $datapaket = $cekkkpaket->row();

            $this->db->where('pin_userid', userid());
            $this->db->where('package_id', $datapaket->package_id);
            $this->db->join('tb_packages', 'pin_package_id = package_id');
            $cekpinserial = $this->db->get('tb_users_pin');
            if ($cekpinserial->num_rows() < post('total_pin')) {
                Self::$data['status']     = false;
                Self::$data['message']     = 'PIN Kode Tidak Cukup Untuk Dikirim!';
            }
        }

        $this->db->where('username', post('username_tujuan'));
        $cekuser = $this->db->get('tb_users');
        if ($cekuser->num_rows() == 0) {
            Self::$data['status']     = false;
            Self::$data['message']     = 'Username Tujuan Tidak Ditemukan!';
        }

        $this->form_validation->set_rules('username_tujuan', 'Username Tujuan', 'required');
        $this->form_validation->set_rules('paket_pin', 'Paket PIN', 'required');
        $this->form_validation->set_rules('total_pin', 'Total PIN', 'required');
        $this->form_validation->set_rules('konfirmasi_password', 'Konfirmasi Password', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']     = false;
            Self::$data['message']     = validation_errors(' ', '<br/>');
        }

        $mydatas = userdata();
        if ($mydatas->username == post('username_tujuan')) {
            Self::$data['status']     = false;
            Self::$data['message']     = "Tidak Dizinkan Mengirim PIN Kode ke Akun Sendiri";
        }

        if (Self::$data['status']) {
            $datapaket = $cekkkpaket->row();

            $userdatas  = $cekuser->row();

            Self::$data['message']      = 'Kirim ' . $this->input->post('total_pin') . ' PIN ' . $datapaket->package_name . ' Ke Username ' . strtoupper($userdatas->username);
        } else {
            Self::$data['heading']      = 'Gagal';
            Self::$data['type']         = 'error';
        }

        return Self::$data;
    }

    function kirimpinserial()
    {
        if (!$this->ion_auth->hash_password_db(userid(), post('konfirmasi_password'))) {
            Self::$data['status']       = false;
            Self::$data['message']      = 'Konfirmasi Password Tidak Sesuai!';
        }

        $this->db->where('package_code', $this->input->post('paket_pin'));
        $cekkkpaket = $this->db->get('tb_packages');
        if ($cekkkpaket->num_rows() == 0) {
            Self::$data['status']       = false;
            Self::$data['message']      = 'Paket PIN Kode Tidak Valid!';
        } else {
            $datapaket = $cekkkpaket->row();

            $this->db->where('pin_userid', userid());
            $this->db->where('package_id', $datapaket->package_id);
            $this->db->join('tb_packages', 'pin_package_id = package_id');
            $cekpinserial = $this->db->get('tb_users_pin');
            if ($cekpinserial->num_rows() < post('total_pin')) {
                Self::$data['status']     = false;
                Self::$data['message']     = 'PIN Kode Tidak Cukup Untuk Dikirim!';
            }
        }

        $this->db->where('username', post('username_tujuan'));
        $cekuser = $this->db->get('tb_users');
        if ($cekuser->num_rows() == 0) {
            Self::$data['status']     = false;
            Self::$data['message']     = 'Username Tujuan Tidak Ditemukan!';
        }

        $this->form_validation->set_rules('username_tujuan', 'Username Tujuan', 'required');
        $this->form_validation->set_rules('paket_pin', 'Paket PIN', 'required');
        $this->form_validation->set_rules('total_pin', 'Total PIN', 'required');
        $this->form_validation->set_rules('konfirmasi_password', 'Konfirmasi Password', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']     = false;
            Self::$data['message']     = validation_errors(' ', '<br/>');
        }

        $mydatas = userdata();
        if ($mydatas->username == post('username_tujuan')) {
            Self::$data['status']     = false;
            Self::$data['message']     = "Tidak Dizinkan Mengirim PIN Kode ke Akun Sendiri";
        }

        if (Self::$data['status']) {
            $datapaket = $cekkkpaket->row();
            $userdata   = userdata();
            $userdatas  = $cekuser->row();

            $this->db->limit(post('total_pin'));
            $this->db->where('pin_userid', userid());
            $this->db->where('package_id', $datapaket->package_id);
            $this->db->join('tb_packages', 'pin_package_id = package_id');
            $cekpinserial = $this->db->get('tb_users_pin');
            foreach ($cekpinserial->result() as $show) {
                $this->db->update(
                    'tb_users_pin',
                    [
                        'pin_userid'        => $userdatas->id,
                        'pin_date_add'      => sekarang(),
                    ],
                    [
                        'pin_code'          => $show->pin_code,
                    ]
                );
            }

            // =============================================================== //
            //	LAPORAN PENGIRIM									  		   //
            // =============================================================== //
            $this->db->insert(
                'tb_histori_userpin',
                [
                    'histori_userid'            => $userdata->id,
                    'histori_userpindesc'       => 'Kirim ' . $this->input->post('total_pin') . ' PIN ' . $datapaket->package_name . ' ke Username: ' . $userdatas->username,
                    'histori_userpindate'       => sekarang(),
                    'histori_code '             => strtolower(random_string('alnum', 64)),
                ]
            );

            // =============================================================== //
            //	LAPORAN PENERIMA									  		   //
            // =============================================================== //
            $this->db->insert(
                'tb_histori_userpin',
                [
                    'histori_userid'            => $userdatas->id,
                    'histori_userpindesc'       => 'Terima ' . $this->input->post('total_pin') . ' PIN ' . $datapaket->package_name . ' dari Username: ' . $userdata->username,
                    'histori_userpindate'       => sekarang(),
                    'histori_code'              => strtolower(random_string('alnum', 64)),
                ]
            );

            Self::$data['heading']      = 'Berhasil';
            Self::$data['message']      = 'PIN Kode Berhasil Di Kirim';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Gagal';
            Self::$data['type']         = 'error';
        }

        return Self::$data;
    }
}
