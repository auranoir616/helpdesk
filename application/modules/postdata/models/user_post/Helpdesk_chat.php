<?php defined('BASEPATH') or exit('No direct script access allowed');

class Helpdesk_chat extends CI_Model
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

    public function KirimPesan(){
      $this->db->where('id', $this->input->post('pesan_penerima'));
      $cekPenerima = $this->db->get('tb_users');
      if($cekPenerima->num_rows() == 0){    
        Self::$data['status'] = false;
        Self::$data['message'] = 'petugas tidak ditemukan';
        Self::$data['type'] = 'error';
    }
    $this->form_validation->set_rules('pesan_isi', 'pesan', 'required');
    if (!$this->form_validation->run()) {
        Self::$data['status']     = false;
        Self::$data['message']     = validation_errors(' ', '<br/>');
    }

    $pesan_pengirim = intval(userid()); 
    $pesan_penerima = intval($this->input->post('pesan_penerima'));
        $idchat = $this->generateChatId($pesan_pengirim , $pesan_penerima);
    
    if(Self::$data['status']){
        $this->db->insert('tb_pesan', [
            'pesan_pengirim' => userid(),
            'pesan_penerima' => $this->input->post('pesan_penerima'),
            'pesan_isi' => $this->input->post('pesan_isi'),
            'pesan_date' => sekarang(),
            'pesan_chat' => $idchat ,
            'pesan_tiket' => $this->input->post('pesan_tiket') ,
        ]);
        $pesan_id = $this->db->insert_id();
        $this->db->where('pesan_id', $pesan_id);
        $pesan_baru = $this->db->get('tb_pesan')->row();

        Self::$data['status'] = true;
        Self::$data['message'] = 'Pesan berhasil dikirim';
        Self::$data['type'] = 'success';
        Self::$data['pesan_isi'] = $pesan_baru->pesan_isi;
        Self::$data['pesan_date'] = waktuYangLalu($pesan_baru->pesan_date);
        }else{
        Self::$data['status'] = false;
        Self::$data['message'] = 'Gagal kirim pesan';
    }

return Self::$data;

}

function generateChatId($userA, $userB) {
    $idchat = '';
    if($userA < $userB) {
        $idchat = $userA.''.$userB;
    } else {
        $idchat = $userB.''.$userA;
    }
    return $idchat;
}

}