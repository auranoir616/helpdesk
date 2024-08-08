<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['login']                         = 'authentication/login';
$route['signup']                        = 'authentication/register';
$route['logout']                        = 'authentication/logout';
$route['signup/(:any)/(:any)']          = 'authentication/register/$1/$2';

$route['dashboard']                     = 'dashboard/view_page/dashboard';
$route['bonusroi']                      = 'cronjob/bonusRoi';
$route['generation/(:any)']             = 'dashboard/view_gen/$1';
$route['mypackage/(:any)']              = 'dashboard/report_package/$1';
$route['admin/(:any)']                  = 'administrator/index/$1';
$route['detail-penj/(:any)']            = 'dashboard/detailPenj/$1';
$route['cetak-inv/(:any)']              = 'dashboard/printPenj/$1';
$route['(:any)']                        = 'dashboard/view_page/$1';
$route['inv-pdf/(:any)']               = 'dashboard/downloadinvpdf/$1';


$route['default_controller']            = 'authentication/login';
$route['404_override']                  = '';
$route['translate_uri_dashes']          = FALSE;
