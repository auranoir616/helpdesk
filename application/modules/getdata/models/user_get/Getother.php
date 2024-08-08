<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Getother extends CI_Model
{

    private static $data = [
        'status'     => true,
        'message'     => null,
    ];

    public function __construct()
    {
        parent::__construct();
    }

    function getdashKabKota()
    {
        $userdata = userdata();
        $datasssss = array();
        $getkabupatenn =  $this->db->query("SELECT * FROM wilayah WHERE CHAR_LENGTH(kode) = 5 AND LEFT(kode, 2) = '" . $this->input->get('provinsi_id') . "'");
        foreach ($getkabupatenn->result() as $show) {

            $this->db->where('id !=', 1);
            $this->db->where('user_kota', $show->kode);
            $cekpinnn = $this->db->get('tb_users');
            if ($cekpinnn->num_rows() != 0) {
                $datasssss[] = array(
                    'kode'            => $show->kode,
                    'nama'          => $show->nama,
                );
            }
        }
        Self::$data['result'] = $datasssss;
        return Self::$data;
    }

    function getdashKec()
    {
        $userdata = userdata();
        $datasssss = array();
        $getkabupatenn =  $this->db->query("SELECT * FROM wilayah WHERE CHAR_LENGTH(kode) = 8 AND LEFT(kode, 5) = '" . $this->input->get('kabkota_id') . "'");
        foreach ($getkabupatenn->result() as $show) {

            $this->db->where('id !=', 1);
            $this->db->where('user_kecamatan', $show->kode);
            $cekspinnn = $this->db->get('tb_users');
            if ($cekspinnn->num_rows() != 0) {
                $datasssss[] = array(
                    'kode'            => $show->kode,
                    'nama'          => $show->nama,
                );
            }
        }
        Self::$data['result'] = $datasssss;
        return Self::$data;
    }

    function getdashKel()
    {
        $userdata = userdata();
        $datasssss = array();
        $getKell = $this->db->query("SELECT * FROM wilayah WHERE CHAR_LENGTH(kode) = 13 AND LEFT(kode, 8) = '" . $this->input->get('kecamatan_id') . "'");
        foreach ($getKell->result() as $show) {

            $this->db->where('id !=', 1);
            $this->db->where('user_kelurahan', $show->kode);
            $cekpinnn = $this->db->get('tb_users');
            if ($cekpinnn->num_rows() != 0) {
                $datasssss[] = array(
                    'kode'            => $show->kode,
                    'nama'          => $show->nama,
                );
            }
        }
        Self::$data['result'] = $datasssss;
        return Self::$data;
    }

    function getTYPEs()
    {
        Self::$data['paket'] = NULL;
        $this->db->where('typebroadcast_code', $this->input->get('typecode'));
        $getTYPPPE = $this->db->get('tb_typebroadcast');
        if ($getTYPPPE->num_rows() != 0) {
            $TYPEpaket = $getTYPPPE->row();

            if ($TYPEpaket->pintype_kode != 'RO') {
                $this->db->where('package_kode', $TYPEpaket->pintype_kode);
                $cekPKET = $this->db->get('tb_packages');

                if ($cekPKET->num_rows() != 0) {
                    Self::$data['paket'] = $cekPKET->result();
                }
            } else {
                $this->db->where('package_kode', $TYPEpaket->pintype_kode);
                $cekPKET = $this->db->get('tb_packages');

                if ($cekPKET->num_rows() != 0) {
                    Self::$data['paket'] = $cekPKET->result();
                }
            }
        }
        Self::$data['result'] = $getTYPPPE->row();
        return Self::$data;
    }


    function cekusername()
    {
        $data['status']     = FALSE;
        $data['pesan']      = "Username Tidak Dikenal";
        $data['userdata']   = NULL;

        $this->db->where('username', $this->input->get('uname'));
        $cek_user = $this->db->get('tb_users');
        if ($cek_user->num_rows() != 0) {
            $data['status']     = TRUE;
            $data['pesan']      = NULL;
            $data['userdata']   = $cek_user->row();
        }
        return $data;
    }

    function cekusernametabungan()
    {
        $data['status']     = FALSE;
        $data['pesan']      = "Username Tidak Dikenal";
        $data['userdata']   = NULL;

        $this->db->where('username', $this->input->get('uname'));
        $this->db->where('invnabung_status', 'success');
        $this->db->join('tb_walletnabung', 'tb_walletnabung.walletnabung_userid = tb_users.id');
        $this->db->join('tb_invnabung', 'tb_invnabung.invnabung_userid = tb_users.id');
        $cek_user = $this->db->get('tb_users');
        if ($cek_user->num_rows() != 0) {
            $data['status']     = TRUE;
            $data['pesan']      = NULL;
            $data['userdata']   = $cek_user->row();
        }

        return $data;
    }


    function cekusername_berangkat()
    {
        $data['status']     = FALSE;
        $data['pesan']      = "Username Tidak Dikenal";
        $data['userdata']   = NULL;

        $this->db->where('username', $this->input->get('uname'));
        $cek_user = $this->db->get('tb_users');
        if ($cek_user->num_rows() != 0) {

            $this->db->where('berangkat_userid', $cek_user->row()->id);
            $this->db->join('tb_tglberangkat', 'tglberangkat_code = berangkat_tglcode');
            $this->db->join('tb_users', 'id = berangkat_userid');
            $get_tanggalll = $this->db->get('tb_berangkat');

            $data['status']     = TRUE;
            $data['pesan']      = NULL;
            $data['userdata']   = $get_tanggalll->row();
        }
        return $data;
    }

    function getdetailpaket()
    {
        $this->db->where('package_code', $this->input->get('paket_id'));
        $getbank = $this->db->get('tb_packages');
        Self::$data['result'] = $getbank->row();
        return Self::$data;
    }

    function hitungharga()
    {
        $totalll = 0;
        $jmlpin     = preg_replace('/[^0-9.]+/', '', preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $this->input->get('ro_total')));
        $harga_paket  = preg_replace('/[^0-9.]+/', '', preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $this->input->get('harga_paket')));

        $totalll    = (int)$harga_paket * (int)$jmlpin;

        $hitung['harga']    = $totalll;
        return $hitung;
    }

    function getbankadmin()
    {
        $this->db->where('bankadmin_code', $this->input->get('jenisbank'));
        $getbank = $this->db->get('tb_bankadmin');
        Self::$data['result'] = $getbank->row();
        return Self::$data;
    }

    function getkabkota()
    {
        $this->db->where('province_id', $this->input->get('provinsi_id'));
        $getkabupatenn = $this->db->get('tb_kabupaten');
        Self::$data['result'] = $getkabupatenn->result();
        return Self::$data;
    }

    function getkecamatan()
    {
        $this->db->where('regency_id', $this->input->get('kabkota_id'));
        $getkecamatan = $this->db->get('tb_kecamatan');
        Self::$data['result'] = $getkecamatan->result();
        return Self::$data;
    }

    function getkelurahan()
    {
        $this->db->where('district_id', $this->input->get('kecamatan_id'));
        $getkelurahan = $this->db->get('villages');
        Self::$data['result'] = $getkelurahan->result();
        return Self::$data;
    }

    function getwilayahKabKota()
    {
        $getkabupatenn = $this->db->query("SELECT * FROM wilayah WHERE CHAR_LENGTH(kode) = 5 AND LEFT(kode, 2) = '" . $this->input->get('provinsi_id') . "'");
        Self::$data['result'] = $getkabupatenn->result();
        return Self::$data;
    }

    function getwilayahKec()
    {
        $getkecamatan = $this->db->query("SELECT * FROM wilayah WHERE CHAR_LENGTH(kode) = 8 AND LEFT(kode, 5) = '" . $this->input->get('kabkota_id') . "'");
        Self::$data['result'] = $getkecamatan->result();
        return Self::$data;
    }

    function getwilayahKel()
    {
        $getkelurahan = $this->db->query("SELECT * FROM wilayah WHERE CHAR_LENGTH(kode) = 13 AND LEFT(kode, 8) = '" . $this->input->get('kecamatan_id') . "'");
        Self::$data['result'] = $getkelurahan->result();
        return Self::$data;
    }
}
