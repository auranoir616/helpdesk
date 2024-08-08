<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Package extends CI_Model
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

    function reqest_new()
    {
        if (!$this->ion_auth->hash_password_db(userid(), post('package_password'))) {
            Self::$data['status']       = false;
            Self::$data['message']      = 'Confirm Your Password Is Incorrect!';
        }

        $this->db->where('package_id', (int)1);
        $cekPKT = $this->db->get('tb_packages');
        if ($cekPKT->num_rows() == 0) {
            Self::$data['status']       = false;
            Self::$data['message']      = 'Package Not Found or Unavailable!';
        } else {
            $dataPKT = $cekPKT->row();
            $amount  = preg_replace('/[^0-9.]+/', '', preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $this->input->post('package_amount')));

            if ($amount < $dataPKT->package_minprice || $amount > $dataPKT->package_maxprice) {
                Self::$data['status']       = false;
                Self::$data['message']      = $dataPKT->package_name . '<br>Minimal Rp. ' . number_format($dataPKT->package_minprice, 0, '.', '.') . '<br>Maximum Rp. ' . number_format($dataPKT->package_maxprice, 0, '.', '.');
            }
        }

        $this->form_validation->set_rules('package_code', 'Package', 'required');
        $this->form_validation->set_rules('package_amount', 'Deposit Amount', 'required');
        $this->form_validation->set_rules('package_password', 'Password', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']     = false;
            Self::$data['message']     = validation_errors(' ', '<br/>');
        }

        $this->db->where('invoice_user_id', userid());
        $this->db->where('invoice_status !=', 'success');
        $cekinvoic = $this->db->get('tb_users_invoice');
        if ($cekinvoic->num_rows() != 0) {
            Self::$data['status']     = false;
            Self::$data['message']     = "You have an active transaction, please wait for the previous transaction to complete";
        }

        if (preg_replace('/[^0-9.]+/', '', preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $this->input->post('package_amount'))) < 1) {
            Self::$data['status']     = false;
            Self::$data['message']    = "Enter your Total Deposit";
        }

        if (Self::$data['status']) {
            $dataPKT    = $cekPKT->row();
            $total      = (int)preg_replace('/[^0-9.]+/', '', preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $this->input->post('package_amount')));
            $rannnnnnn  = rand(300, 999);

            $this->db->insert(
                'tb_users_invoice',
                [
                    'invoice_package_id'    => $dataPKT->package_id,
                    'invoice_user_id'       => userid(),
                    'invoice_total'         => $total,
                    'invoice_subtotal'      => (int)$total + $rannnnnnn,
                    'invoice_kodeinv'       => 'INV' . date('Y') . date('m') . date('d') . $rannnnnnn,
                    'invoice_kode_unik'     => $rannnnnnn,
                    'invoice_date_add'      => sekarang(),
                    'invoice_code'          => strtolower(random_string('alnum', 64)),
                ]
            );


            Self::$data['heading']      = 'Success';
            Self::$data['message']      = 'Your Invoice Has Been Created, Please Confirm Payment';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Fail';
            Self::$data['type']         = 'error';
        }

        return Self::$data;
    }


    function confirminv()
    {
        $this->db->where('bankadmin_code', $this->input->post('confirm_bankadmin'));
        $cekkbank = $this->db->get('tb_bankadmin');
        if ($cekkbank->num_rows() == 0) {
            Self::$data['status']     = false;
            Self::$data['message']     = "Please Select Admin Bank";
        } else {
            $config['upload_path']          = './assets/upload/';
            $config['allowed_types']        = 'gif|jpg|png|jpeg';
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
        }

        $this->db->where('invoice_status', 'pending');
        $this->db->where('invoice_user_id', userid());
        $this->db->where('invoice_code', $this->input->post('code'));
        $cekinvoice = $this->db->get('tb_users_invoice');
        if ($cekinvoice->num_rows() == 0) {
            Self::$data['status']     = false;
            Self::$data['message']     = "Bill Not Found Or Confirmed";
        }

        $this->form_validation->set_rules('code', 'Kode Transaksi', 'required');
        $this->form_validation->set_rules('confirm_bankadmin', 'Nama Bank (ADMIN)', 'required');
        $this->form_validation->set_rules('confirm_atasnama', 'Rekening Atas Nama', 'required');
        $this->form_validation->set_rules('confirm_namabank', 'Nama Bank', 'required');
        $this->form_validation->set_rules('confirm_norek', 'No. Rekening', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']     = false;
            Self::$data['message']     = validation_errors(' ', '<br/>');
        }

        if (Self::$data['status']) {
            $datainvoice    = $cekinvoice->row();
            $databank       = $cekkbank->row();
            $uploaded       = $this->upload->data();

            $this->db->insert(
                'tb_users_pembayaran',
                [
                    'pembayaran_invoice_id'     => $datainvoice->invoice_id,
                    'pembayaran_userid'         => userid(),
                    'pembayaran_adbankname'     => $databank->bankadmin_bankname,
                    'pembayaran_adbankaccount'  => $databank->bankadmin_bankaccount,
                    'pembayaran_adbanknumber'   => $databank->bankadmin_banknumber,
                    'pembayaran_bankname'       => $this->input->post('confirm_atasnama'),
                    'pembayaran_bankaccount'    => $this->input->post('confirm_namabank'),
                    'pembayaran_banknumber'     => $this->input->post('confirm_norek'),
                    'pembayaran_struk'          => $uploaded['file_name'],
                    'pembayaran_date_add'       => sekarang(),
                    'pembayaran_nominal'        => $datainvoice->invoice_subtotal,
                    'pembayaran_code'           => strtolower(random_string('alnum', 64)),
                ]
            );


            $this->db->update(
                'tb_users_invoice',
                [
                    'invoice_status'        => 'process',
                ],
                [
                    'invoice_code'          => $this->input->post('code')
                ]
            );


            Self::$data['message']      = 'Invoice Confirmed, Waiting for Admin Confirmation';
            Self::$data['heading']      = 'Success';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Error';
            Self::$data['type']         = 'error';
        }

        return Self::$data;
    }
}
