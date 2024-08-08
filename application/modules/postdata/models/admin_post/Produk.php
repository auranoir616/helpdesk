<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Produk extends CI_Model
{

    private static $data = [
        'status'     => true,
        'message'     => null,
    ];

    public function __construct()
    {
        parent::__construct();
        Self::$data['csrf_data']     = $this->security->get_csrf_hash();
    }

    function AddProduk()
    {
        $config['upload_path']          = './assets/upload/';
        $config['allowed_types']        = 'jpg|png|jpeg';
        $config['max_size']             = '99999999';
        $config['max_width']            = '99999999';
        $config['max_height']           = '99999999';
        $config['remove_spaces']        = TRUE;
        $config['encrypt_name']         = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('produk_image')) {
            Self::$data['status']     = false;
            Self::$data['message']     = $this->upload->display_errors();
            return Self::$data;
        }
        $this->form_validation->set_rules('produk_nama', 'Masukkan Nama Produk. ', 'required');
        $this->form_validation->set_rules('produk_stok', 'Masukkan Stok Produk. ', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']     = false;
            Self::$data['message']    = validation_errors(' ', '<br/>');
        }
        if (Self::$data['status']) {
            $uploaded   = $this->upload->data();
            $random_string = strtolower(random_string('alnum', 64));
            $this->db->insert(
                'tb_produk',
                [
                    'produk_nama'                        => $this->input->post('produk_nama'),
                    'produk_stok'                        => $this->input->post('produk_stok'),
                    'produk_image'                       => $uploaded['file_name'],
                    'produk_code'                        => $random_string
                ]
            );
            $this->db->insert('tb_harga', [
                'harga_produkid' =>  $this->db->insert_id(),
            ]);

            $stok =  $this->db->insert('tb_stok', [
                'stok_produkid' =>  $this->db->insert_id(),
                'stok_amount'   =>  $this->input->post('produk_stok'),
                'stok_type'     =>  'credit',
                'stok_desc'     =>  'stok awal',
                'stok_date'     =>  sekarang(),
                'stok_code'     =>  $random_string,
                'stok_penerima_userid'     =>  userid(),
                'stok_pengirim_userid'     =>  userid()
            ]);
            if (!$stok) {
                Self::$data['status']     = false;
                Self::$data['message']    = "gagal insert stok";
            }

            $configg['image_library']       = 'gd2';
            $configg['source_image']        = './assets/upload/' . $uploaded['file_name'];
            $configg['create_thumb']        = FALSE;
            $configg['maintain_ratio']      = FALSE;
            $configg['quality']             = '50%';
            $configg['width']               = 'auto';
            $configg['height']              = 'auto';
            $configg['new_image']           = './assets/upload/thumbnail/' . $uploaded['file_name'];
            $this->load->library('image_lib', $configg);
            $this->image_lib->resize();
            Self::$data['message']      = 'Penambahan Produk Berhasil';
            Self::$data['heading']      = 'Berhasil';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Error';
            Self::$data['type']         = 'error';
        }

        return Self::$data;
    }


    function UpdateProduk(){
        $config['upload_path'] = './assets/upload/';
        $config['allowed_types'] = 'jpg|png|jpeg';
        $config['max_size'] = '99999999';
        $config['max_width'] = '99999999';
        $config['max_height'] = '99999999';
        $config['remove_spaces'] = TRUE;
        $config['encrypt_name'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        $this->db->where('produk_code', $this->input->post('code'));
        $cekkproduk = $this->db->get('tb_produk');
        if ($cekkproduk->num_rows() == 0) {
            Self::$data['status'] = false;
            Self::$data['message'] = 'Produk Tidak Valid';
        }
        $this->form_validation->set_rules('produk_nama', 'Nama Produk', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status'] = false;
            Self::$data['message'] = validation_errors(' ', '<br/>');
        }

        if (Self::$data['status']) {
            $produk = $cekkproduk->row();
            if (!$this->upload->do_upload('produk_image')) {
                $this->db->where('produk_code', $produk->produk_code);
                $this->db->update('tb_produk', [
                    'produk_nama'                        => $this->input->post('produk_nama'),
                ]);
            } else {
                $uploaded = $this->upload->data();
                if (!empty($produk->produk_image)) {
                    if (file_exists('./assets/upload/' . $produk->produk_image)) {
                        unlink('./assets/upload/' . $produk->produk_image);
                    }

                    if (file_exists('./assets/upload/thumbnail/' . $produk->produk_image)) {
                        unlink('./assets/upload/thumbnail/' . $produk->produk_image);
                    }
                }

                $this->db->where('produk_code', $produk->produk_code);
                $this->db->update('tb_produk', [
                    'produk_nama'                        => $this->input->post('produk_nama'),
                    'produk_image'                       => $uploaded['file_name'],
                ]);

                $configg['image_library'] = 'gd2';
                $configg['source_image'] = './assets/upload/' . $uploaded['file_name'];
                $configg['create_thumb'] = FALSE;
                $configg['maintain_ratio'] = FALSE;
                $configg['quality'] = '50%';
                $configg['width'] = 'auto';
                $configg['height'] = 'auto';
                $configg['new_image'] = './assets/upload/thumbnail/' . $uploaded['file_name'];
                $this->load->library('image_lib', $configg);
                $this->image_lib->resize();
            }

            Self::$data['heading']         = 'Berhasil';
            Self::$data['message']         = 'Update Produk Berhasil';
            Self::$data['type']             = 'success';
        } else {

            Self::$data['heading']         = 'Gagal';
            Self::$data['type']             = 'error';
        }

        return Self::$data;
    }

    function UpdateStok(){
        $this->db->where('stok_code', $this->input->post('produk_code'));
        $cekStok = $this->db->get('tb_stok');
        if ($cekStok->num_rows() == 0) {
            Self::$data['status'] = false;
            Self::$data['message'] = 'Produk Tidak Valid';
        }

        if ($this->walletmodel->stokBarang($this->input->post('produk_code')) == post('produk_stok')) {
            Self::$data['status'] = false;
            Self::$data['message'] = 'Tidak Ada Perubahan Stock';
        }

        $this->form_validation->set_rules('produk_stok', 'Produk Stok', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status'] = false;
            Self::$data['message'] = validation_errors(' ', '<br/>');
        }


        if (Self::$data['status']){
            $stokSaatIni = $this->walletmodel->stokBarang($this->input->post('produk_code'));
            $stokInput = $this->input->post('produk_stok');
            $stokProdukId = $cekStok->row()->stok_produkid;
            $stokBaru = 0;
            // Tentukan tipe stok (debit atau credit)
            if ($stokInput > $stokSaatIni) {
                $stokType = 'credit'; // Stok masuk
                $stokBaru = $stokInput - $stokSaatIni;
            } elseif ($stokInput < $stokSaatIni) {
                $stokType = 'debit'; // Stok keluar
                $stokBaru = $stokSaatIni - $stokInput;
            }

            $this->db->insert('tb_stok',  [
                'stok_produkid'     => $stokProdukId,
                'stok_amount'       => $stokBaru,
                'stok_type'         => $stokType,
                'stok_desc'         => 'stok update',
                'stok_date'         => sekarang(),
                'stok_code'         => $this->input->post('produk_code'),
                'stok_penerima_userid'     =>  userid(),
                'stok_pengirim_userid'     =>  userid()

            ]);


            Self::$data['status'] = true;
            Self::$data['message'] = 'Update Produk Berhasil';
            Self::$data['type'] = 'success';
        } else {
            Self::$data['message'] = 'Gagal Update Stok, karena tidak ada perubahan';
            Self::$data['type'] = 'error';
        }

        return Self::$data;
    }

    function DeleteProduk()
    {
        $code = $this->input->post('code');

        if (!$code) {
            Self::$data['status'] = false;
            Self::$data['heading'] = 'Error';
            Self::$data['message'] = 'Kode reward tidak valid.';
            Self::$data['csrf_data'] = $this->security->get_csrf_hash();
            Self::$data['type'] = 'error';
            return;
        }
        $cekproduk = $this->db->get('tb_produk');
        if ($cekproduk->num_rows() == 0) {
            Self::$data['status'] = false;
            Self::$data['message'] = 'Produk Tidak Valid';
            Self::$data['type'] = 'error';
        }
        if (Self::$data['status']) {
            $this->db->where('produk_code', $code);
            $produk = $cekproduk->row();
            $this->db->delete('tb_produk');
            if (file_exists('./assets/produk/' . $produk->produk_image)) {
                unlink('./assets/produk/' . $produk->produk_image);
            }

            if (file_exists('./assets/produk/thumbnail/' . $produk->produk_image)) {
                unlink('./assets/produk/thumbnail/' . $produk->produk_image);
            }

            //delete row harga
            $this->db->where('produk_code', $code);
            $this->db->get('tb_produk');
            $getid = $this->db->get('tb_produk')->row();
            $this->db->where('harga_produkid', $getid->produk_id);
            $this->db->delete('tb_harga');

            Self::$data['status'] = true;
            Self::$data['heading'] = 'Berhasil';
            Self::$data['message'] = 'Produk berhasil dihapus.';
            Self::$data['csrf_data'] = $this->security->get_csrf_hash();
            Self::$data['type'] = 'success';
        } else {
            Self::$data['status'] = false;
            Self::$data['heading'] = 'Error';
            Self::$data['csrf_data'] = $this->security->get_csrf_hash();
            Self::$data['message'] = 'Gagal menghapus Produk.';
            Self::$data['type'] = 'error';
        }
        return Self::$data;
    }

    function updateStokMember(){
            $this->form_validation->set_rules('stok_penerima_userid', 'penerima perubahan stok', 'required');
            $this->form_validation->set_rules('stok_type', 'Tipe Transaksi', 'required');
            $this->form_validation->set_rules('stok_amount', 'Total stok', 'required');
            $this->form_validation->set_rules('stok_produkid', 'produk id', 'required');
            if (!$this->form_validation->run()) {
                Self::$data['status'] = false;
                Self::$data['message'] = validation_errors(' ', '<br/>');
            }
            $this->db->where('produk_id', $this->input->post('stok_produkid'));
            $getTbl = $this->db->get('tb_produk');

            if (Self::$data['status']) {
                if($getTbl->num_rows() > 0){
                $stok_desc = '';
                $getProduk = $getTbl->row();
                $perubahanPoin = floor($this->input->post('stok_amount') / $getProduk->produk_min_pembelian) * $getProduk->produk_poin;
                if ($this->input->post('stok_type') == 'credit') {
                    $stok_desc = 'Penambahan Stok Dari Pusat';
                    $this->db->insert('tb_poinrw', [
                        'poinrw_userid'			=> $this->input->post('stok_penerima_userid'),
                        'poinrw_total'			=> $perubahanPoin,
                        'poinrw_desc'			=> "penambahan poin akibat perubahan stok sebesar " .$perubahanPoin,
                        'poinrw_date'			=> sekarang(),
                        'poinrw_tipe'			=> $this->input->post('stok_type')
                    ]);
                } else if ($this->input->post('stok_type') == 'debit') {
                    $stok_desc = 'Pengurangan Stok Dari Pusat';
                    $this->db->insert('tb_poinrw', [
                        'poinrw_userid'			=> $this->input->post('stok_penerima_userid'),
                        'poinrw_total'			=> $perubahanPoin,
                        'poinrw_desc'			=> "pengurangan poin akibat perubahan stok sebesar " .$perubahanPoin,
                        'poinrw_date'			=> sekarang(),
                        'poinrw_tipe'			=> $this->input->post('stok_type')
                    ]);
                }
            }



                $this->db->insert('tb_stok', [
                    'stok_produkid'			        => $this->input->post('stok_produkid'),
                    'stok_amount'			        => $this->input->post('stok_amount'),
                    'stok_penerima_userid'			=> $this->input->post('stok_penerima_userid'),
                    'stok_pengirim_userid'			=> userid(),
                    'stok_desc'			            => $stok_desc,
                    'stok_date'			            => sekarang(),
                    'stok_type'			            => $this->input->post('stok_type'),
                    'stok_code'			            => strtolower(random_string('alnum', 64))
                ]);


                Self::$data['message'] = 'Update Stok Berhasil';
                Self::$data['heading'] = 'Berhasil';
                Self::$data['type'] = 'success';
            } else {
                Self::$data['status'] = false;
                Self::$data['message'] = 'Gagal Update Stok.';
                Self::$data['heading'] = 'Error';
                Self::$data['type'] = 'error';
            }
    
            return Self::$data;
    
    
    }
    function updateHarga(){
        $this->form_validation->set_rules('harga_aceh1', 'aceh 1', 'required');
        $this->form_validation->set_rules('harga_aceh2', 'aceh 2', 'required');
        $this->form_validation->set_rules('harga_medan', 'medan', 'required');
        $this->form_validation->set_rules('harga_riau1', 'riau 1', 'required');
        $this->form_validation->set_rules('harga_riau2', 'riau 2', 'required');
        $this->form_validation->set_rules('harga_kepriau', 'kep. riau', 'required');
        $this->form_validation->set_rules('harga_sumbar', 'sumbar', 'required');
        $this->form_validation->set_rules('harga_jambi', 'jambi', 'required');
        // $this->form_validation->set_rules('harga_reseller', 'reseller', 'required');
        // $this->form_validation->set_rules('harga_resellermin50', 'reseller', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status'] = false;
            Self::$data['message'] = validation_errors(' ', '<br/>');
        }
        $this->db->where('harga_produkid', $this->input->post('harga_produkid'));
        $getdataharga = $this->db->get('tb_harga');

        if (Self::$data['status']) {
            if($getdataharga->num_rows() > 0){
                $this->db->where('harga_produkid', $this->input->post('harga_produkid'));
           $this->db->update('tb_harga',[
                'harga_aceh1' =>  $this->input->post('harga_aceh1'),
                'harga_aceh2' =>  $this->input->post('harga_aceh2'),
                'harga_medan' =>  $this->input->post('harga_medan'),
                'harga_riau1' =>  $this->input->post('harga_riau1'),
                'harga_riau2' =>  $this->input->post('harga_riau2'),
                'harga_kepriau' =>  $this->input->post('harga_kepriau'),
                'harga_sumbar' =>  $this->input->post('harga_sumbar'),
                'harga_jambi' =>  $this->input->post('harga_jambi'),
                // 'harga_reseller' =>  $this->input->post('harga_reseller'),
                // 'harga_resellermin50' =>  $this->input->post('harga_resellermin50'),
            ]);


        }

            Self::$data['message'] = 'Update Harga Produk Berhasil';
            Self::$data['heading'] = 'Berhasil';
            Self::$data['type'] = 'success';
        } else {
            Self::$data['status'] = false;
            Self::$data['message'] = 'Gagal Update Produk.';
            Self::$data['heading'] = 'Error';
            Self::$data['type'] = 'error';
        }

        return Self::$data;


}


}
