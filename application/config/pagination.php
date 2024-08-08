<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Pagination Config
 * 
 * Just applying codeigniter's standard pagination config with twitter 
 * bootstrap stylings
 * 
 * @license		http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author		Mike Funk
 * @link		http://codeigniter.com/user_guide/libraries/pagination.html
 * @email		mike@mikefunk.com
 * 
 * @file		pagination.php
 * @version		1.3.1
 * @date		03/12/2012
 * 
 * Copyright (c) 2011
 */
 
// --------------------------------------------------------------------------
// $config['base_url'] = '';
$config['per_page'] 		= 2;
$config['uri_segment'] 		= 3;
$config['num_links'] 		= 9;
$config['page_query_string'] = TRUE;
// $config['use_page_numbers'] = TRUE;
$config['query_string_segment'] = 'page';
$config['reuse_query_string'] 	= true;

$config['full_tag_open'] 	= '<nav aria-label="Page navigation example"><ul class="pagination">';
$config['full_tag_close'] 	= '</ul></nav><!--pagination-->';
$config['first_link'] 		= '&laquo; Pertama';
$config['first_tag_open'] 	= '<li class="page-item prev page">';
$config['first_tag_close'] 	= '</li>';
$config['last_link'] 		= 'Terakhir &raquo;';
$config['last_tag_open'] 	= '<li class="page-item next page">';
$config['last_tag_close'] 	= '</li>';
$config['next_link'] 		= 'Selanjutnya &rarr;';
$config['next_tag_open'] 	= '<li class="page-item next page">';
$config['next_tag_close'] 	= '</li>';
$config['prev_link'] 		= '&larr; Sebelumnya';
$config['prev_tag_open'] 	= '<li class="page-item prev page">';
$config['prev_tag_close'] 	= '</li>';
$config['cur_tag_open'] 	= '<li class="page-item active"><a class="page-link" href="">';
$config['cur_tag_close'] 	= '</a></li>';
$config['num_tag_open']		= '<li class="page-item page">';
$config['num_tag_close'] 	= '</li>';
// $config['display_pages'] = FALSE;
// 
$config['anchor_class'] 		= 'follow_link';
$config['attributes'] = array('class' => 'page-link');
// --------------------------------------------------------------------------
/* End of file pagination.php */
/* Location: ./bookymark/application/config/pagination.php */