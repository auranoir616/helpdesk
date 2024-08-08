<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shareprofit extends CI_Model
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

    function count_ranking()
    {
        $this->db->where('group_id', (int)2);
        $this->db->join('tb_users_groups', 'tb_users.id = tb_users_groups.user_id');
        $get_USER = $this->db->get('tb_users');

        // LOOOPPPPING SEMUA MEMBER
        foreach ($get_USER->result() as $showw) {

            // LOOOOPING UNTUK CEK SEMUA RANKKKKKK
            foreach ($this->rank->qualifSP($showw->user_id, 2) as $item) {
                $this->db->where('usershare_status', 'ranking');
                $this->db->where('usershare_rankid', $item['idrank']);
                $this->db->where('usershare_userid', $showw->user_id);
                $cekUSERSHARE = $this->db->get('tb_usershare');
                if ($cekUSERSHARE->num_rows() == 0) {
                    $this->db->insert(
                        'tb_usershare',
                        [
                            'usershare_userid'  => $showw->user_id,
                            'usershare_rankid'  => $item['idrank'],
                            'usershare_status'  => 'ranking',
                            'usershare_date'    => sekarang(),
                            'usershare_code'    => strtolower(random_string('alnum', 64)),
                        ]
                    );
                }
            }
        }

        Self::$data['heading']      = 'Berhasil';
        Self::$data['message']      = 'Data Ranking Telah Digenerate';
        Self::$data['type']         = 'success';

        return Self::$data;
    }

    function count_royalty()
    {
        $this->db->where('group_id', (int)2);
        $this->db->join('tb_users_groups', 'tb_users.id = tb_users_groups.user_id');
        $get_USER = $this->db->get('tb_users');

        foreach ($get_USER->result() as $showw) {
            // JIKA ID RANK >= 4 KARENA TIDAK SEMUA RANK DAPAT ROYALTI
            foreach ($this->rank->qualifSP($showw->user_id, 2) as $item) {
                if ($item['idrank'] >= 4) {
                    $this->db->where('usershare_status', 'royalty');
                    $this->db->where('usershare_rankid', $item['idrank']);
                    $this->db->where('usershare_userid', $showw->user_id);
                    $cekUSERSHARE = $this->db->get('tb_usershare');
                    if ($cekUSERSHARE->num_rows() == 0) {
                        $this->db->insert(
                            'tb_usershare',
                            [
                                'usershare_userid'  => $showw->user_id,
                                'usershare_rankid'  => $item['idrank'],
                                'usershare_status'  => 'royalty',
                                'usershare_date'    => sekarang(),
                                'usershare_code'    => strtolower(random_string('alnum', 64)),
                            ]
                        );
                    }
                }
            }
        }

        Self::$data['heading']      = 'Berhasil';
        Self::$data['message']      = 'Data Royalty Telah Digenerate';
        Self::$data['type']         = 'success';

        return Self::$data;
    }
}
