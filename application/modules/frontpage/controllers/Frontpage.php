<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Frontpage extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    function index($page = 'home')
    {
        $data = array();

        if (!file_exists(APPPATH . 'modules/frontpage/views/' . $page . '.php')) {
            show_404();
            exit;
        }
        $this->template->content->view($page, $data);
        $this->template->publish();
    }
}
