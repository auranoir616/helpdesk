<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paginationmodel extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function paginate( $uri_string = null, $num_rows = 0, $limit_per_page = 20 )
	{

		$this->load->library('pagination');
		
		$config['base_url'] 		= $uri_string;
		$config['total_rows'] 		= $num_rows;
		$config['per_page'] 		= $limit_per_page;
		$this->pagination->initialize($config);
		
		return $this->pagination->create_links();

	}

}

/* End of file Paginationmodel.php */
/* Location: ./application/models/Paginationmodel.php */