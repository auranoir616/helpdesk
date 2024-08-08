<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bank extends CI_Model
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

    function savenewbank()
    {

        if (!$this->ion_auth->hash_password_db(userid(), post('confirm_password'))) {
            Self::$data['status']       = false;
            Self::$data['message']      = 'Konfirmasi Password Tidak Sesuai!';
        }

        $this->form_validation->set_rules('bank_account', 'Rekening Atas Nama', 'required');
        $this->form_validation->set_rules('bank_name', 'Nama Bank', 'required');
        $this->form_validation->set_rules('bank_number', 'Nomor Rekening', 'required');
        $this->form_validation->set_rules('confirm_password', 'Konfirm Password', 'required');
        if ($this->form_validation->run() == false) {
            Self::$data['status'] = false;
            Self::$data['message'] = validation_errors(' ', '<br>');
        }

        if (Self::$data['status']) {
            $random_string = strtolower(random_string('alnum', 64));

            $this->db->insert(
                'tb_bankadmin',
                [
                    'bankadmin_bankname'        => post('bank_name'),
                    'bankadmin_bankaccount'     => post('bank_account'),
                    'bankadmin_banknumber'      => post('bank_number'),
                    'bankadmin_code'            => $random_string,
                ]
            );

            Self::$data['heading']      = "Berhasil";
            Self::$data['message']      = "Data Bank Baru Berhasil Disimpan";
            Self::$data['type']         = "success";
        } else {
            Self::$data['heading']      = "Gagal";
            Self::$data['type']         = "error";
        }

        return Self::$data;
    }


    function saveupdatebank()
    {

        if (!$this->ion_auth->hash_password_db(userid(), post('confirm_password'))) {
            Self::$data['status']       = false;
            Self::$data['message']      = 'Konfirmasi Password Tidak Sesuai!';
        }

        $this->form_validation->set_rules('code', 'Bank Code', 'required');
        $this->form_validation->set_rules('bank_account', 'Rekening Atas Nama', 'required');
        $this->form_validation->set_rules('bank_name', 'Nama Bank', 'required');
        $this->form_validation->set_rules('bank_number', 'Nomor Rekening', 'required');
        $this->form_validation->set_rules('confirm_password', 'Konfirm Password', 'required');
        if ($this->form_validation->run() == false) {
            Self::$data['status'] = false;
            Self::$data['message'] = validation_errors(' ', '<br>');
        }

        if (Self::$data['status']) {

            $this->db->update(
                'tb_bankadmin',
                [
                    'bankadmin_bankname'        => post('bank_name'),
                    'bankadmin_bankaccount'     => post('bank_account'),
                    'bankadmin_banknumber'      => post('bank_number'),
                ],
                [
                    'bankadmin_code'            => $this->input->post('code')
                ]
            );

            Self::$data['heading']      = "Berhasil";
            Self::$data['message']      = "Data Bank Berhasil Diperbarui";
            Self::$data['type']         = "success";
        } else {
            Self::$data['heading']      = "Gagal";
            Self::$data['type']         = "error";
        }

        return Self::$data;
    }

    function hapusbank()
    {
        $this->db->where('bankadmin_code', post('code'));
        $cekdatabank = $this->db->get('tb_bankadmin');
        if ($cekdatabank->num_rows() == 0) {
            Self::$data['status']       = false;
            Self::$data['message']      = 'Data Bank Tidak Valid!';
        }

        $this->form_validation->set_rules('code', 'Bank Code', 'required');
        if ($this->form_validation->run() == false) {
            Self::$data['status'] = false;
            Self::$data['message'] = validation_errors(' ', '<br>');
        }

        if (Self::$data['status']) {

            $this->db->delete('tb_bankadmin', array('bankadmin_code' => post('code')));


            Self::$data['heading']      = "Berhasil";
            Self::$data['message']      = "Data Bank Berhasil Dihapus";
            Self::$data['type']         = "success";
        } else {
            Self::$data['heading']      = "Gagal";
            Self::$data['type']         = "error";
        }

        return Self::$data;
    }
}
