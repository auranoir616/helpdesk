<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Getdatas extends CI_Model
{

    private static $data = [
        'status'     => true,
        'message'     => null,
    ];

    public function __construct()
    {
        parent::__construct();
    }


    // public function getNewMessages() {
    //     $userid = $this->input->get('userid');
    //     $lastTimestamp = $this->input->get('lastTimestamp'); 
    //     if($userid < userid()){
    //         $idpesan = $userid . userid();
    //     }else{
    //         $idpesan = userid() . $userid;
    //     }
    //     $this->db->where('pesan_chat', $idpesan);


    //     if ($lastTimestamp) {
    //         $this->db->where('pesan_date >', $lastTimestamp);
    //     }
    //     $this->db->order_by('pesan_date', 'desc');
    // $getpesan = $this->db->get('tb_pesan');

    // $messages = [];
    // foreach ($getpesan->result() as $show) {
    //     $messages[] = [
    //         'isi' => $show->pesan_isi,
    //         'pengirim' => $show->pesan_pengirim,
    //         'tanggal' => waktuYangLalu($show->pesan_date),
    //         'id' => $idpesan,
    //     ];
    // }
    //     return $messages;
    
    // }
    public function getNewMessages() {
    $idpesan = $this->input->get('idpesan');

    $this->db->where('pesan_chat', $idpesan);
    $this->db->order_by('pesan_date', 'desc');
    $getpesan = $this->db->get('tb_pesan');

    $messages = [];
    foreach ($getpesan->result() as $show) {
        $messages[] = [
            'isi' => $show->pesan_isi,
            'pengirim' => $show->pesan_pengirim,
            'tanggal' => $show->pesan_date,
        ];
    }
    return $messages;
}
        public function getNewMessagesAdmin() {
        $userid = $this->input->get('userid'); 
        $lastTimestamp = $this->input->get('lastTimestamp'); 
        $idpesan = 1 . $userid;
    
        $this->db->where('pesan_chat', $idpesan);
        
        if ($lastTimestamp) {
            $this->db->where('pesan_date >', $lastTimestamp);
        }
        
        $this->db->order_by('pesan_date', 'asc');
        $getpesan = $this->db->get('tb_pesan');
    
        $messages = [];
        foreach ($getpesan->result() as $show) {
            $messages[] = [
                'isi' => $show->pesan_isi,
                'pengirim' => $show->pesan_pengirim,
                'tanggal' => $show->pesan_date,
            ];
        }
        return $messages;
    }
            
}
