<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Getother extends CI_Model
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

    function cariusers()
    {
        $username   = $this->input->get('username');

        $this->db->where('username', $username);
        $cekUserInternal  = $this->db->get('tb_users');
        if ($cekUserInternal->num_rows() != 0) {
            $userdata = $cekUserInternal->row();
            Self::$data['result'] = $userdata;
        } else {
            Self::$data['status']   = false;
            Self::$data['message']  = "Username Tujuan Tidak Ditemukan";
            Self::$data['type']     = 'error';
            Self::$data['result']   = array();
        }
        return Self::$data;
    }

    function getdatapetugas(){
        $id = $this->input->get('id');
        $this->db->where('id', $id);
        $getusers = $this->db->get('tb_users');
        $data = $getusers->row();
        return $data;

    }
}
