<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Emailmodel extends CI_Model {



	public $variable;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('email');
	}

	public function send( $email_to = null, $email_subject = null, $email_body = null )
	{

		 if ( ! option('mailer_server') ) {
		 	return false;
 		}
		
		// $data['email_body']		= $email_body;
		// $email_template 		= $this->load->view( 'email/email_template', $data, TRUE);

 		$this->email->from( CNF_EMAIL_USERNAME, CNF_EMAIL_SENDERNAME );
 		$this->email->to( $email_to );
 		$this->email->subject( $email_subject );
 		$this->email->message( $email_body );
	 	return $this->email->send();
	
		
	}





	

}

/* End of file Emailmodel.php */
/* Location: ./application/models/Emailmodel.php */