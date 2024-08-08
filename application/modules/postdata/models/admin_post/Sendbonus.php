<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sendbonus extends CI_Model
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

    function sendsingle()
    {

        $this->db->where('user_verification', '1');
        $this->db->where('user_code', post('bonus_code'));
        $cekuser = $this->db->get('tb_users');
        if ($cekuser->num_rows() == 0) {
            Self::$data['status']     = false;
            Self::$data['message']     = "User Tidak ditemukan atau tidak aktif";
        }

        $this->form_validation->set_rules('bonus_code', 'Code', 'required');
        $this->form_validation->set_rules('bonus_amount', 'Total Bonus', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']     = false;
            Self::$data['message']     = validation_errors(' ', '<br/>');
        }

        if (Self::$data['status']) {
            $userdattta     = $cekuser->row();
            $bonus          = $this->input->post('bonus_amount');

            $this->db->insert(
                'tb_logbonus',
                [
                    'logbonus_userid'       => $userdattta->id,
                    'logbonus_amount'       => $bonus,
                    'logbonus_date'         => sekarang(),
                    'logbonus_code'         => strtolower(random_string('alnum', 64)),
                ]
            );


            $this->startbonus(1, $user_id = $userdattta->user_code, $bonus);

            Self::$data['heading']    = 'Berhasil';
            Self::$data['message']    = 'Bonus Dikirim';
            Self::$data['type']        = 'success';
        } else {
            Self::$data['heading']     = 'Error';
            Self::$data['type']     = 'error';
        }

        return Self::$data;
    }


    function sendmulti()
    {
        $this->load->library('Excel');

        $config['upload_path']          = './assets/excel/';
        $config['allowed_types']        = 'xls|xlsx|csv';
        $config['max_size']             = '99999';
        $config['encrypt_name']         = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('file_excel')) {
            Self::$data['status']     = false;
            Self::$data['message']     = $this->upload->display_errors();
        }

        if (Self::$data['status']) {
            $uploaded             = $this->upload->data();
            $EXCELFile          = './assets/excel/' . $uploaded['file_name'];
            $inputFileType      = PHPExcel_IOFactory::identify($EXCELFile);
            $objReader          = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel        = $objReader->load($EXCELFile);

            $sheet              = $objPHPExcel->getSheet(0);
            $highestRow         = $sheet->getHighestRow();
            $highestColumn      = $sheet->getHighestColumn();

            for ($row = 1; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

                $this->db->where('user_verification', '1');
                $this->db->where('username', $rowData[0][0]);
                $cekuser = $this->db->get('tb_users');
                if ($cekuser->num_rows() == 1) {
                    $userdata = $cekuser->row();
                    $this->startbonus(1, $user_id = $userdata->user_code, $rowData[0][1]);

                    $this->db->insert(
                        'tb_logbonus',
                        [
                            'logbonus_userid'       => $userdata->id,
                            'logbonus_amount'       => $rowData[0][1],
                            'logbonus_date'         => sekarang(),
                            'logbonus_code'         => strtolower(random_string('alnum', 64)),
                        ]
                    );
                } else {
                    $this->db->insert(
                        'tb_errorbonus',
                        [
                            'errorbonus_desc'       => "Error, Username : " . $rowData[0][0] . " Tidak Ditemukan",
                            'errorbonus_date'         => sekarang(),
                        ]
                    );
                }
            }

            Self::$data['heading']      = 'Berhasil';
            Self::$data['message']      = 'Bonus Dikirim';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Error';
            Self::$data['type']         = 'error';
        }

        return Self::$data;
    }

    function startbonus($level = 1, $usercode = null, $bonus)
    {
        $result         = array();
        $userdata       = userdata(['user_code' => $usercode]);
        $arraybonus     = ['5', '4', '3', '2', '1', '1', '1'];

        $paramcode      = $usercode;
        $parambonus     = $bonus;


        $this->db->where('titiklevel_downlineid', $userdata->id);
        $this->db->where('titiklevel_level', $level);
        $cektitiklevel = $this->db->get('tb_titiklevel');

        if ($cektitiklevel->num_rows() != 0 && $level <= 7) {

            $amount_bonus        = $arraybonus[$level - 1];
            $sendbonus           = ($bonus / 100) * $amount_bonus;

            $wallllllet     = $this->usermodel->userWallet('withdrawal', $cektitiklevel->row()->titiklevel_userid);

            $this->db->insert(
                'tb_wallet_balance',
                [
                    'w_balance_wallet_id'       => $wallllllet->wallet_id,
                    'w_balance_amount'          => $sendbonus,
                    'w_balance_type'            => 'credit',
                    'w_balance_desc'            => 'Bonus Generation, To ' . $level . ' From ' . $userdata->username,
                    'w_balance_date_add'        => sekarang(),
                    'w_balance_txid'            => strtolower(random_string('alnum', 64)),
                ]
            );

            $this->startbonus($level + 1, $paramcode, $parambonus);
        }

        return $result;
    }
}
