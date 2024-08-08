<?php defined('BASEPATH') or exit('No direct script access allowed');

class Helpdesk_notif extends CI_Model
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

    public function BacaNotif()
    {   
        $this->db->where('notif_id', post('notif_id'));
        $getNotif = $this->db->get('tb_notif');
        if( $getNotif->num_rows() == 0){
            Self::$data['status'] = false;
            Self::$data['heading'] = 'Error';
            Self::$data['message'] = 'Notifikasi Tidak ditemukan';
        }
        if (Self::$data['status']) {
            $this->db->where('notif_id', post('notif_id'));
            $this->db->update('tb_notif', [
                'notif_status' => 'read'
            ]);


            Self::$data['heading'] = 'Berhasil';
            Self::$data['message'] = 'Tiket berhasil dihapus.';
            Self::$data['type'] = 'success';
        } else {
            Self::$data['status'] = false;
            Self::$data['heading'] = 'Error';
            Self::$data['message'] = 'Gagal menghapus Tiket.';
            Self::$data['type'] = 'error';
        }
        return Self::$data;
    }
}