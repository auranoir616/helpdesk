<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class BonusReferral extends CI_Model
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

    function approvebns()
    {
        $this->db->where('breferensi_code', post('code'));
        $this->db->where('breferensi_status', 'pending');
        $cekbonus = $this->db->get('tb_breferensi');
        if ($cekbonus->num_rows() == 0) {
            Self::$data['status']     = false;
            Self::$data['message']     = "Bonus Tidak Valid";
        }

        $this->form_validation->set_rules('code', 'Code', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']     = false;
            Self::$data['message']     = validation_errors(' ', '<br/>');
        }

        if (Self::$data['status']) {
            $bonus = $cekbonus->row();

            $this->db->update(
                'tb_breferensi',
                [
                    'breferensi_status'              => 'success',
                ],
                [
                    'breferensi_code'                =>  $bonus->breferensi_code,
                ]
            );


            Self::$data['heading']           = 'Berhasil';
            Self::$data['message']           = 'Bonus Berhasil Disetujui';
            Self::$data['type']              = 'success';
        } else {

            Self::$data['heading']           = 'Error';
            Self::$data['type']              = 'error';
        }

        return Self::$data;
    }

    function cancelbns()
    {
        $this->db->where('breferensi_code', post('code'));
        $this->db->where('breferensi_status', 'pending');
        $cekbonus = $this->db->get('tb_breferensi');
        if ($cekbonus->num_rows() == 0) {
            Self::$data['status']     = false;
            Self::$data['message']     = "Bonus Tidak Valid";
        }

        $this->form_validation->set_rules('code', 'Code', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']     = false;
            Self::$data['message']     = validation_errors(' ', '<br/>');
        }

        if (Self::$data['status']) {
            $bonus = $cekbonus->row();

            $this->db->where('breferensi_code', $bonus->breferensi_code);
            $this->db->delete('tb_breferensi');


            Self::$data['heading']           = 'Berhasil';
            Self::$data['message']           = 'Bonus Berhasil Direject';
            Self::$data['type']              = 'success';
        } else {

            Self::$data['heading']           = 'Error';
            Self::$data['type']              = 'error';
        }

        return Self::$data;
    }
}
