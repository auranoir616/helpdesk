<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Networkmodel extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function getTopSponsor()
	{

		$get 	= $this->db->query('SELECT count(referral_id) AS total_reff, referral_id
					FROM tb_users
					JOIN tb_lending ON lending_userid = id
					WHERE referral_id != 0 
					AND referral_id != 1 
					GROUP BY referral_id
					ORDER BY count(referral_id) DESC
					LIMIT 5
				');	

		return $get->result();

	}

	public function getTopSponsor10()
	{

		$get 	= $this->db->query('SELECT count(referral_id) AS total_reff, referral_id
					FROM tb_users
					WHERE referral_id != 0 
					-- AND referral_id != 1 
					GROUP BY referral_id
					ORDER BY count(referral_id) DESC
					LIMIT 5
				');	

		return $get->result();

	}

	public function getTopBonus($limit = 0, $offset = 0)
	{

		$get 	= $this->db->query('SELECT
						SUM(bonus_amount) AS total_bonus,
						bonus_userid,
						tb_users.* 
					FROM
						tb_bonus
					JOIN tb_users ON tb_users.id = tb_bonus.bonus_userid 
					WHERE bonus_userid != 1 
					GROUP BY
						bonus_userid
					ORDER BY
						count(bonus_amount) DESC
					LIMIT 10
				');	

		return $get->result();

	}

}

/* End of file Networkmodel.php */
/* Location: ./application/models/Networkmodel.php */