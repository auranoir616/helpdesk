<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifikasi extends CI_Model {

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	function getusername()
	{
		$this->db->select('username');
		$this->db->like('username', 	$this->input->get('notif_username'));
		$getusername = $this->db->get('tb_users');
		
		return $getusername->result();

	}
}

/* End of file Notifikasi.php */
/* Location: ./application/modules/getdata/models/admin_get/Notifikasi.php */