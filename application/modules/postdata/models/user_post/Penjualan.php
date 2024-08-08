<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penjualan extends CI_Model
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

    /*===========================================================================
    =       ADD DAN UPDT BELUM SESUAI BAGIAN PERPCS RESELLER JAWA DAN LUAR      =
    ============================================================================*/
    function addcart()
    {
        // $this->load->library('cart');
        // $this->db->where('produk_code', $this->input->post('code'));
        // $cekproduk = $this->db->get('tb_produk');
        // if ($cekproduk->num_rows() == 0) {
        //     Self::$data['status']     = false;
        //     Self::$data['message']     = 'Data Produk Tidak Valid';
        // }

        // $this->form_validation->set_rules('code', 'ProdukID', 'required');
        // if (!$this->form_validation->run()) {
        //     Self::$data['status']     = false;
        //     Self::$data['message']     = validation_errors(' ', '<br/>');
        // }

        // if (Self::$data['status']) {
        //     $produk = $cekproduk->row();

        //     $harga = 0;
        //     $userid = 0;

        //     $datass = array(
        //         'id'      => $produk->produk_code,
        //         'userid'  => $userid,
        //         'qty'     => 1,
        //         'price'   => $harga,
        //         'name'    => $produk->produk_nama,
        //     );
        //     $this->cart->insert($datass);

        //     Self::$data['heading']      = 'Berhasil';
        //     Self::$data['message']      = 'Produk Ditambah Ke Keranjang!';
        //     Self::$data['type']         = 'success';
        // } else {
        //     Self::$data['heading']      = 'Error';
        //     Self::$data['type']         = 'error';
        // }
        // return Self::$data;
        $this->load->library('cart');
        $this->db->where('produk_code', $this->input->post('code'));
        $cekproduk = $this->db->get('tb_produk');
        if ($cekproduk->num_rows() == 0) {
            Self::$data['status']     = false;
            Self::$data['message']     = 'Data Produk Tidak Valid';
        }
        $this->form_validation->set_rules('code', 'ProdukID', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']     = false;
            Self::$data['message']     = validation_errors(' ', '<br/>');
        }
        if (Self::$data['status']) {
            $produk = $cekproduk->row();

            $harga = 0;
            $user = userdata();
            $this->db->where('cart_user_id', $user->id);
            $this->db->where('cart_produk', $produk->produk_nama);
            $cekcart = $this->db->get('tb_cart');
            if ($cekcart->num_rows() > 0) {
                $this->db->where('cart_user_id', $user->id);
                $this->db->where('cart_produk', $produk->produk_nama);
                $this->db->update('tb_cart', ['cart_qty' => $cekcart->row()->cart_qty + 1]);
            } else {


                $dataCart = array(
                    'cart_produk'           => $produk->produk_nama,
                    'cart_user_id'          => $user->id,
                    'cart_status'           => 'pending',
                    'cart_qty'              => 1,
                    'cart_code'             => random_string('alnum', 64),
                    'cart_produk_code'             => $this->input->post('code'),

                );
                $this->db->insert('tb_cart', $dataCart);
            }
            Self::$data['heading']      = 'Berhasil';
            Self::$data['message']      = 'Produk Ditambah Ke Keranjang!';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Error';
            Self::$data['type']         = 'error';
        }
        return Self::$data;
    }


    function pilihtujuan()
    {
        $this->db->where('user_code', $this->input->post('code'));
        $getusertipe = $this->db->get('tb_users');

        $this->form_validation->set_rules('code', 'CODE', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']    = false;
            Self::$data['message']    = validation_errors('', '</br>');
        }

        if (Self::$data['status']) {
            $user = $getusertipe->row();
            // PULAU JAWA 31 - 35

            /*
                update harga dan user yang ditargetkan tapi buatlah harga dan id user secara default 0
            */


            foreach ($this->cart->contents() as $items) {

                $this->db->where('produk_code', $items['id']);
                $produk = $this->db->get('tb_produk')->row();

                $hargapcs = 0;
                if ($user->user_provinsi > 30 && $user->user_provinsi <= 36) {
                    // USER ADALAH JAWA
                    if ($user->user_type == 'distributor') {
                        $hargapcs = $produk->produk_harga_jw_distributor;
                    } elseif ($user->user_type == 'agen') {
                        $hargapcs = $produk->produk_harga_jw_agen;
                    } elseif ($user->user_type == 'reseller') {
                        // $hargapcs = $produk->produk_harga_jw_reseller_10;
                        // KONDISI MINIMAL PCS
                        $qty = $items['qty'];
                        if ($produk->produk_id == 1) {
                            // HARGA PRODUK SATU
                            if ($qty < 24) {
                                $hargapcs = $produk->produk_harga_jw_reseller_10;
                            } elseif ($qty > 24 && $qty < 49) {
                                $hargapcs = $produk->produk_harga_jw_reseller_25;
                            } else {
                                $hargapcs = $produk->produk_harga_jw_reseller_50;
                            }
                        } elseif ($produk->produk_id == 2) {
                            // HARGA PRODUK DUA
                            if ($qty < 10) {
                                $hargapcs = $produk->produk_harga_jw_reseller_5;
                            } elseif ($qty > 9 && $qty < 24) {
                                $hargapcs = $produk->produk_harga_jw_reseller_10;
                            } elseif ($qty > 24 && $qty < 49) {
                                $hargapcs = $produk->produk_harga_jw_reseller_25;
                            } else {
                                $hargapcs = $produk->produk_harga_jw_reseller_50;
                            }
                        }
                    }
                } else {
                    // USER DILUAR JAWA
                    if ($user->user_type == 'distributor') {
                        $hargapcs = $produk->produk_harga_lj_distributor;
                    } elseif ($user->user_type == 'agen') {
                        $hargapcs = $produk->produk_harga_lj_agen;
                    } elseif ($user->user_type == 'reseller') {
                        // $hargapcs = $produk->produk_harga_lj_reseller_5;
                        // KONDISI MINIMAL PCS
                        $qty = $items['qty'];
                        if ($produk->produk_id == 1) {
                            // HARGA PRODUK SATU
                            if ($qty < 24) {
                                $hargapcs = $produk->produk_harga_lj_reseller_10;
                            } elseif ($qty > 24 && $qty < 49) {
                                $hargapcs = $produk->produk_harga_lj_reseller_25;
                            } else {
                                $hargapcs = $produk->produk_harga_lj_reseller_50;
                            }
                        } elseif ($produk->produk_id == 2) {
                            // HARGA PRODUK DUA
                            if ($qty < 10) {
                                $hargapcs = $produk->produk_harga_lj_reseller_5;
                            } elseif ($qty > 9 && $qty < 24) {
                                $hargapcs = $produk->produk_harga_lj_reseller_10;
                            } elseif ($qty > 24 && $qty < 49) {
                                $hargapcs = $produk->produk_harga_lj_reseller_25;
                            } else {
                                $hargapcs = $produk->produk_harga_lj_reseller_50;
                            }
                        }
                    }
                }


                $data = array(
                    'rowid'     => $items['rowid'],
                    'userid'    => $user->id,
                    'price'     => $items['qty'] * $hargapcs
                );

                $this->cart->update($data);
            }

            $totalll = 0;
            foreach ($this->cart->contents() as $carttotal) {
                $totalll += $carttotal['qty'] * $carttotal['price'];
            }

            Self::$data['heading']    = 'Success';
            Self::$data['message']    = 'User Tujuan Berhasil Dipilih';
            Self::$data['type']        = 'success';
        } else {
            Self::$data['heading']    = 'GAGAL';
            Self::$data['type']        = 'error';
        }

        return Self::$data;
    }

    function updtecart()
    {
        $this->form_validation->set_rules('codes', 'CODE', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']    = false;
            Self::$data['message']    = validation_errors('', '</br>');
        }
        $a = 1;
        foreach ($this->cart->contents() as $carts) {

            $this->db->where('produk_code', $carts['id']);
            $getproduk = $this->db->get('tb_produk');
            if ($getproduk->num_rows() == 0) {
                Self::$data['status']    = false;
                Self::$data['message']    = "Produk Tidak Diketahui!";
            } else {
                $product = $getproduk->row();

                $userdata = userdata(['id' => $carts['userid']]);
                $hargapcs = 0;

                if ($userdata->user_provinsi > 30 && $userdata->user_provinsi < 36) {
                    // USER ADALAH JAWA
                    if ($userdata->user_type == 'distributor') {
                        $hargapcs = $product->produk_harga_jw_distributor;
                    } elseif ($userdata->user_type == 'agen') {
                        $hargapcs = $product->produk_harga_jw_agen;
                    } elseif ($userdata->user_type == 'reseller') {
                        // KONDISI MINIMAL PCS
                        $qty = $this->input->post($a . '[qty]');
                        if ($product->produk_id == 1) {
                            // HARGA PRODUK SATU
                            if ($qty < 24) {
                                $hargapcs = $product->produk_harga_jw_reseller_10;
                            } elseif ($qty >= 24 && $qty < 49) {
                                $hargapcs = $product->produk_harga_jw_reseller_25;
                            } else {
                                $hargapcs = $product->produk_harga_jw_reseller_50;
                            }
                        } elseif ($product->produk_id == 2) {
                            // HARGA PRODUK DUA
                            if ($qty < 10) {
                                $hargapcs = $product->produk_harga_jw_reseller_5;
                            } elseif ($qty >= 10 && $qty < 24) {
                                $hargapcs = $product->produk_harga_jw_reseller_10;
                            } elseif ($qty >= 24 && $qty < 49) {
                                $hargapcs = $product->produk_harga_jw_reseller_25;
                            } else {
                                $hargapcs = $product->produk_harga_jw_reseller_50;
                            }
                        }
                    }
                } else {
                    // USER ADALAH LUAR JAWA
                    if ($userdata->user_type == 'distributor') {
                        $hargapcs = $product->produk_harga_lj_distributor;
                    } elseif ($userdata->user_type == 'agen') {
                        $hargapcs = $product->produk_harga_lj_agen;
                    } elseif ($userdata->user_type == 'reseller') {
                        // KONDISI MINIMAL PCS
                        $qty = $this->input->post($a . '[qty]');
                        if ($product->produk_id == 1) {
                            // HARGA PRODUK SATU
                            if ($qty < 24) {
                                $hargapcs = $product->produk_harga_lj_reseller_10;
                            } elseif ($qty >= 24 && $qty < 49) {
                                $hargapcs = $product->produk_harga_lj_reseller_25;
                            } else {
                                $hargapcs = $product->produk_harga_lj_reseller_50;
                            }
                        } elseif ($product->produk_id == 2) {
                            // HARGA PRODUK DUA
                            if ($qty < 10) {
                                $hargapcs = $product->produk_harga_lj_reseller_5;
                            } elseif ($qty >= 10 && $qty < 24) {
                                $hargapcs = $product->produk_harga_lj_reseller_10;
                            } elseif ($qty >= 24 && $qty < 49) {
                                $hargapcs = $product->produk_harga_lj_reseller_25;
                            } else {
                                $hargapcs = $product->produk_harga_lj_reseller_50;
                            }
                        }
                    }
                }

                // Update item di keranjang dengan harga yang benar
                $data = array(
                    'rowid' => $carts['rowid'],
                    'qty' => $this->input->post($a . '[qty]'),
                    'price' => $hargapcs,
                    'total' => $this->input->post($a . '[qty]') * $hargapcs
                );

                $this->cart->update($data);
                $a++;
            }
        }

        // Hitung total keseluruhan
        $totalll = 0;
        foreach ($this->cart->contents() as $carttotal) {
            $totalll += $carttotal['qty'] * $carttotal['price'];
        }

        Self::$data['heading'] = 'Success';
        Self::$data['message'] = 'Keranjang Anda Berhasil Diperbarui';
        Self::$data['type'] = 'success';

        return Self::$data;
    }


    function delcart()
    {
        $this->form_validation->set_rules('code', 'CODE', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']    = false;
            Self::$data['message']    = validation_errors('', '</br>');
        }

        if (Self::$data['status']) {

            $data = array(
                'rowid' => post('code'),
                'qty'   => 0
            );

            $this->cart->update($data);

            Self::$data['heading']    = 'SUCCESS';
            Self::$data['message']    = 'BERHASIL DIHAPUS';
            Self::$data['type']        = 'success';
        } else {
            Self::$data['heading']    = 'GAGAL';
            Self::$data['type']        = 'error';
        }

        return Self::$data;
    }

    function clrcart()
    {
        $this->form_validation->set_rules('code', 'CODE', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']    = false;
            Self::$data['message']    = validation_errors('', '</br>');
        }

        if (Self::$data['status']) {

            $this->cart->destroy();

            Self::$data['heading']    = 'SUCCESS';
            Self::$data['message']    = 'BERHASIL DIHAPUS';
            Self::$data['type']        = 'success';
        } else {
            Self::$data['heading']    = 'GAGAL';
            Self::$data['type']        = 'error';
        }

        return Self::$data;
    }

    function checkoutcart()
    {
        if (count($this->cart->contents()) == 0) {
            Self::$data['status']   = false;
            Self::$data['message']  = "Tidak ada produk di keranjang";
        }

        foreach ($this->cart->contents() as $cart) {

            $this->db->where('produk_code', $cart['id']);
            $cekproduk = $this->db->get('tb_produk');
            if ($cekproduk->num_rows() == 0) {
                Self::$data['status']   = false;
                Self::$data['message']  = "Produk Tidak Di Ketahui !";
            }
            if ($this->usermodel->totalStok(userid(), $cekproduk->row()->produk_id) < $cart['qty']) {
                Self::$data['status']   = false;
                Self::$data['message']  = "Stok Produk Kurang!";
            }
        }

        $this->db->where('inv_userid_from', userid());
        $this->db->where('inv_status !=', 'success');
        $cekinvoice_ro = $this->db->get('tb_invoice');
        if ($cekinvoice_ro->num_rows() != 0) {
            Self::$data['status']   = false;
            Self::$data['message']  = "Transaksi Anda Sebelumnya Masih Pending !";
        }

        $this->form_validation->set_rules('code', 'CODE ITEM', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']    = false;
            Self::$data['message']    = validation_errors('', '</br>');
        }

        if (Self::$data['status']) {
            $produk = $cekproduk->row();
            $code = strtolower(random_string('alnum', 64));

            $id_produk      = array();
            $qtyorder       = array();
            $produk         = array();
            $row_id         = array();
            $harga          = array();
            $invoice_total  = 0;

            foreach ($this->cart->contents() as $cart) {
                $subperamount = $cart['price'] * $cart['qty'];

                $this->db->where('produk_code', $cart['id']);
                $getProdak = $this->db->get('tb_produk')->row();

                array_push($id_produk, $getProdak->produk_id); // Masukkan id_produk ke variable array $produk_id
                array_push($qtyorder, $cart['qty']); // Masukkan qty ke variabel array qty_produk
                array_push($produk, $cart['name']); // Masukkan qty ke variabel array Nama
                array_push($row_id, $cart['rowid']);
                array_push($harga, $subperamount);
                $invoice_total += $cart['subtotal'];
                $userid = $cart['userid'];
            }

            $this->db->insert(
                'tb_invoice',
                [
                    'inv_produkid'            => json_encode($id_produk),
                    'inv_userid_from'         => userid(),
                    'inv_user_id'             => $userid,
                    'inv_amount'              => $invoice_total,
                    'inv_peramount'           => json_encode($harga),
                    'inv_qty'                 => json_encode($qtyorder),
                    'inv_date_add'            => sekarang(),
                    'inv_orderkode'           => strtoupper('FFDH' . time()),
                    'inv_code'                => $code
                ]
            );

            foreach ($row_id as $id) {
                $this->cart->remove($id);
            }

            Self::$data['heading']    = 'Success';
            Self::$data['message']    = 'Pengiriman Produk Berhasil, Menunggu Konfirmasi Admin';
            Self::$data['type']        = 'success';
        } else {
            Self::$data['heading']    = 'GAGAL';
            Self::$data['type']        = 'error';
        }

        return Self::$data;
    }
    ////////////////////////////////////////////////////////////////////////////?
    function checkout()
    {
        $config['upload_path']          = './assets/upload/';
        $config['allowed_types']        = 'gif|jpg|png|jpeg';
        $config['max_size']             = '99999999';
        $config['max_width']            = '99999999';
        $config['max_height']           = '99999999';
        $config['remove_spaces']        = TRUE;
        $config['encrypt_name']         = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('inv_image')) {
            Self::$data['status']     = false;
            Self::$data['message']     = $this->upload->display_errors();
        }

        $this->db->where('cart_user_id', userid());
        $this->db->where('cart_status', 'pending');
        $checkCart = $this->db->get('tb_cart');
        if ($checkCart->num_rows() == 0) {
            Self::$data['status']   = false;
            Self::$data['message']  = "Produk Tidak Di Ketahui !";
        }

        $this->db->where('inv_userid_from', userid());
        $this->db->where('inv_status =', 'pending');
        $cekinvoice_ro = $this->db->get('tb_invoice');
        if ($cekinvoice_ro->num_rows() != 0) {
            Self::$data['status']   = false;
            Self::$data['message']  = "Transaksi Anda Sebelumnya Masih Pending !";
        }
        $this->db->select('tb_produk.*, tb_cart.*');
        $this->db->from('tb_cart');
        $this->db->join('tb_produk', 'tb_cart.cart_produk_code = tb_produk.produk_code');
        $this->db->where('tb_cart.cart_user_id', userid());
        $this->db->where('tb_cart.cart_status', 'pending');
        $dataCart = $this->db->get();

        //cek cart
        $datacartuser = $dataCart->result();
        foreach ($datacartuser as $key => $value) {
            if ($value->cart_check == 'not_checked') {
                Self::$data['status']   = false;
                Self::$data['message']  = "harap periksa produk dan tekan tombol Check untuk melanjutkan !";
            }
        }

        if (Self::$data['status']) {
            $produkId = array();
            $hargaProduk = array();
            $qty = array();
            $code = strtolower(random_string('alnum', 64));
            $totalAmount = 0;
            $invcode = '';
            $tambahanharga = 0;
            $uploaded        = $this->upload->data();


            if (userdata(['id' => userid()])->user_type == 'distributor') {
                $invcode = strtoupper('DSOR' . time());
                $tambahanharga = 1000;
            } elseif (userdata(['id' => userid()])->user_type == 'master') {
                $invcode = strtoupper('MSOR' . time());
            } else {
                $invcode = strtoupper('RSOR' . time());
            }
            foreach ($dataCart->result() as $key => $value) {
                array_push($produkId,  (int)$value->produk_id);
                $hargaTotal = $value->cart_total_harga;
                array_push($hargaProduk, $value->cart_qty * ($value->cart_harga_produk + $tambahanharga));
                array_push($qty, (int)$value->cart_qty);
                $totalAmount += $hargaTotal;
            }


            $dataInput = [
                'inv_userid_from' => userid(),
                'inv_user_id' => $this->input->post('distributor'),
                'inv_produkid' => json_encode($produkId),
                'inv_amount' => $totalAmount,
                'inv_peramount' => json_encode($hargaProduk),
                'inv_qty'    => json_encode($qty),
                'inv_date_add' => sekarang(),
                'inv_orderkode'  => $invcode,
                'inv_code'       => $code,
                'inv_image'       => $uploaded['file_name']
            ];
            /*============================================
            =	            set notifikasi         		=
            ============================================*/
            $this->db->insert(
                'tb_notif',
                [
                    'notif_useridto' => $this->input->post('distributor'),
                    'notif_useridfrom' => userid(),
                    'notif_desc' => 'Transaksi Baru dari ' . userdata(['id' => userid()])->username . 'sebesar Rp.' . $totalAmount . ', perlu dikonfirmasi',
                    'notif_tipe' => 'transaksi',
                    'notif_date' => sekarang(),
                    'notif_code' => $code,
                ]
            );


            $this->db->insert('tb_invoice', $dataInput);
            // update cart
            $this->db->where('cart_user_id', userid());
            $this->db->delete('tb_cart');

            Self::$data['heading']    = 'Success';
            Self::$data['message']    = 'Pengiriman Produk Berhasil, Menunggu Konfirmasi';
            Self::$data['type']        = 'success';
        } else {
            Self::$data['heading']    = 'GAGAL';
            Self::$data['type']        = 'error';
        }

        return Self::$data;
    }

    function addToCart()
    {
        $this->load->library('cart');
        $this->db->where('produk_code', $this->input->post('code'));
        $cekproduk = $this->db->get('tb_produk');
        if ($cekproduk->num_rows() == 0) {
            Self::$data['status']     = false;
            Self::$data['message']     = 'Data Produk Tidak Valid';
        }
        $this->form_validation->set_rules('code', 'ProdukID', 'required');
        if (!$this->form_validation->run()) {
            Self::$data['status']     = false;
            Self::$data['message']     = validation_errors(' ', '<br/>');
        }
        $user = userdata();
        $minqty = 0;
        if ($user->user_type == 'reseller' && $user->user_type_status == 'verified') {
            $minqty = 50;
        } elseif ($user->user_type == 'distributor') {
            $minqty = 2000;
        } elseif ($user->user_type == 'master') {
            $minqty = 12000;
        } else {
            $minqty = 10;
        }
        if (Self::$data['status']) {
            $produk = $cekproduk->row();

            $this->db->where('cart_user_id', $user->id);
            $this->db->where('cart_produk', $produk->produk_nama);
            $cekcart = $this->db->get('tb_cart');

            if ($cekcart->num_rows() > 0) {
                $this->db->where('cart_user_id', $user->id);
                $this->db->where('cart_produk', $produk->produk_nama);

                $this->db->update('tb_cart', [
                    'cart_qty' => $cekcart->row()->cart_qty + $minqty,
                    'cart_total_harga' => $cekcart->row()->cart_total_harga + $this->input->post('hargawilayahuser') * $minqty,

                ]);
            } else {

                $dataCart = array(
                    'cart_produk'           => $produk->produk_nama,
                    'cart_user_id'          => $user->id,
                    'cart_total_harga'      => $this->input->post('hargawilayahuser') * $minqty,
                    'cart_status'           => 'pending',
                    'cart_qty'              => $minqty,
                    'cart_code'             => random_string('alnum', 64),
                    'cart_produk_code'      => $this->input->post('code'),
                    'cart_transaksi'      => 'pembelian',
                    'cart_check'      => 'not_checked',

                );
                $this->db->insert('tb_cart', $dataCart);
            }
            Self::$data['heading']      = 'Berhasil';
            Self::$data['message']      = 'Produk Ditambah Ke Keranjang!';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Error';
            Self::$data['type']         = 'error';
        }
        return Self::$data;
    }
    function update()
    {
        $this->db->where('cart_code', $this->input->post('cart_code'));
        $cekproduk = $this->db->get('tb_cart');
        if ($cekproduk->num_rows() == 0) {
            Self::$data['status']     = false;
            Self::$data['message']     = 'Data Produk Tidak Valid';
        }
        //minimal order tiap user
        $minqty = 0;
        $user = userdata();
        if ($user->user_type == 'reseller' && $user->user_type_status == 'verified') {
            $minqty = 50;
        } elseif ($user->user_type == 'distributor') {
            $minqty = 2000;
        } elseif ($user->user_type == 'master') {
            $minqty = 12000;
        } else {
            $minqty = 10;
        }

        if ($this->input->post('jmlorder') < $minqty) {
            Self::$data['status']     = false;
            Self::$data['message']     = 'jumlah pembelian sebagai ' . $user->user_type . ' minimal ' . $minqty . ' pcs';
        }
        //periksa stok di distributor
        $stoktersedia = $this->usermodel->totalStok($this->input->post('wilayahdistributor'), $this->input->post('produk_id'));
        if ($this->input->post('jmlorder') >= $stoktersedia) {
            Self::$data['status']     = false;
            Self::$data['message']     = 'Stok Distributor tidak cukup';
        }
        if ($this->input->post('wilayahdistributor') == '') {
            Self::$data['status']     = false;
            Self::$data['message']     = 'Distributor Wajib Diisi';
        }

        // if ($this->input->post('expedisi') == '') {
        //     Self::$data['status']     = false;
        //     Self::$data['message']     = 'Expedisi Wajib Diisi';
        // }


        if (Self::$data['status']) {
            $this->db->where('cart_code', $this->input->post('cart_code'));
            $this->db->where('cart_user_id', userid());
            $this->db->update('tb_cart', [
                'cart_qty' => $this->input->post('jmlorder'),
                'cart_total_harga' => $this->input->post('hargawilayahuser') * $this->input->post('jmlorder'),
                'cart_check'      => 'checked',
                'cart_harga_produk'      => $this->input->post('hargawilayahuser'),

            ]);

            Self::$data['heading']      = 'Berhasil';
            Self::$data['message']      = 'Data Produk Berhasil diupdate!';
            Self::$data['type']         = 'success';
        } else {
            Self::$data['heading']      = 'Error';
            Self::$data['type']         = 'error';
        }
        return Self::$data;
    }

    function hapus()
    {
        $this->db->where('cart_code', $this->input->post('cart_code'));
        $cekproduk = $this->db->get('tb_cart');
        if ($cekproduk->num_rows() == 0) {
            Self::$data['status']     = false;
            Self::$data['message']     = 'Data Produk Tidak Valid';
        }

        if (Self::$data['status']) {
            $this->db->where('cart_code', $this->input->post('cart_code'));
            $this->db->where('cart_user_id', userid());
            $this->db->delete('tb_cart');


            Self::$data['heading']    = 'SUCCESS';
            Self::$data['message']    = 'BERHASIL DIHAPUS';
            Self::$data['type']        = 'success';
        } else {
            Self::$data['heading']    = 'GAGAL';
            Self::$data['type']        = 'error';
        }

        return Self::$data;
    }

    // function updatePenjualanReseller()
    // {
    //     $this->load->library('cart');
    //     $this->db->where('cart_code', $this->input->post('cart_code'));
    //     $cekproduk = $this->db->get('tb_cart');
    //     if ($cekproduk->num_rows() == 0) {
    //         Self::$data['status']     = false;
    //         Self::$data['message']     = 'Data Produk Tidak Valid';
    //     }

    //     $this->form_validation->set_rules('hargaperproduk', 'harga per produk', 'required');
    //     if ($this->form_validation->run() == false) {
    //         Self::$data['status']     = false;
    //         Self::$data['message']     = validation_errors(' ', '<br/>');
    //     }

    //     //cek maksimal harga jual
    //     $hargainput = $this->input->post('hargaperproduk');
    //     $wilayah = $this->usermodel->getHargaWilayah(userid());
    //     $this->db->where('harga_produkid', $this->input->post('produk_id'));
    //     $this->db->select($wilayah);
    //     $hargadistributor = $this->db->get('tb_harga')->row();
    //     if ($hargainput > $hargadistributor->$wilayah + 10000) {
    //         Self::$data['status']     = false;
    //         Self::$data['message']     = 'maksimal selisih harga jual kekonsumen adalah 10000';
    //     }

    //     //cek minimal penjualan
    //     $minqty = 1;
    //     if ($this->input->post('jmlorder') < $minqty) {
    //         Self::$data['status']     = false;
    //         Self::$data['message']     = 'jumlah penjualan minimal ' . $minqty;
    //     }
    //     //periksa stok 
    //     $stoktersedia = $this->usermodel->totalStok(userid(), $this->input->post('produk_id'));
    //     if ($this->input->post('jmlorder') >= $stoktersedia) {
    //         Self::$data['status']     = false;
    //         Self::$data['message']     = 'Stok Tidak Cukup Silahkan Beli Lagi';
    //     }

    //     if (Self::$data['status']) {
    //         $this->db->where('cart_code', $this->input->post('cart_code'));
    //         $this->db->where('cart_user_id', userid());
    //         $this->db->where('cart_produk_code', $this->input->post('produk_code'));
    //         $this->db->where('cart_transaksi', 'penjualan');
    //         $this->db->update('tb_cart', [
    //             'cart_qty' => $this->input->post('jmlorder'),
    //             'cart_total_harga' => $this->input->post('hargaperproduk') * $this->input->post('jmlorder'),
    //             'cart_harga_produk' => $this->input->post('hargaperproduk'),
    //             'cart_check'      => 'checked',

    //         ]);

    //         Self::$data['heading']      = 'Berhasil';
    //         Self::$data['message']      = 'Produk Berhasil Diupdate!';
    //         Self::$data['type']         = 'success';
    //     } else {
    //         Self::$data['heading']      = 'Error';
    //         Self::$data['type']         = 'error';
    //     }
    //     return Self::$data;
    // }

    // function addToCartPenjualanReseller()
    // {
    //     $this->load->library('cart');
    //     $this->db->where('produk_code', $this->input->post('code'));
    //     $cekproduk = $this->db->get('tb_produk');
    //     if ($cekproduk->num_rows() == 0) {
    //         Self::$data['status']     = false;
    //         Self::$data['message']     = 'Data Produk Tidak Valid';
    //     }
    //     $this->form_validation->set_rules('code', 'ProdukID', 'required');
    //     if (!$this->form_validation->run()) {
    //         Self::$data['status']     = false;
    //         Self::$data['message']     = validation_errors(' ', '<br/>');
    //     }
    //     $user = userdata();
    //     $minqty = 1;
    //     if (Self::$data['status']) {
    //         $produk = $cekproduk->row();

    //         $harga = 0;
    //         $this->db->where('cart_user_id', $user->id);
    //         $this->db->where('cart_produk', $produk->produk_nama);
    //         $this->db->where('cart_transaksi', 'penjualan');
    //         $cekcart = $this->db->get('tb_cart');
    //         if ($cekcart->num_rows() > 0) {
    //             $this->db->where('cart_user_id', $user->id);
    //             $this->db->where('cart_produk', $produk->produk_nama);
    //             $this->db->where('cart_transaksi', 'penjualan');

    //             $this->db->update('tb_cart', [
    //                 'cart_qty' => $cekcart->row()->cart_qty + $minqty,
    //                 'cart_total_harga' => $cekcart->row()->cart_total_harga + $this->input->post('cart_total_code') * $minqty,
    //             ]);
    //         } else {

    //             $dataCart = array(
    //                 'cart_produk'           => $produk->produk_nama,
    //                 'cart_user_id'          => $user->id,
    //                 'cart_total_harga'      => $minqty,
    //                 'cart_status'           => 'pending',
    //                 'cart_qty'              => $minqty,
    //                 'cart_code'             => random_string('alnum', 64),
    //                 'cart_produk_code'      => $this->input->post('code'),
    //                 'cart_transaksi'      => 'penjualan',
    //             );
    //             $this->db->insert('tb_cart', $dataCart);
    //         }
    //         Self::$data['heading']      = 'Berhasil';
    //         Self::$data['message']      = 'Produk Ditambah Ke Keranjang!';
    //         Self::$data['type']         = 'success';
    //     } else {
    //         Self::$data['heading']      = 'Error';
    //         Self::$data['type']         = 'error';
    //     }
    //     return Self::$data;
    // }

    // function checkoutPenjualanReseller()
    // {
    //     $this->db->where('cart_user_id', userid());
    //     $this->db->where('cart_status', 'pending');
    //     $this->db->where('cart_transaksi', 'penjualan');
    //     $checkCart = $this->db->get('tb_cart');
    //     if ($checkCart->num_rows() == 0) {
    //         Self::$data['status']   = false;
    //         Self::$data['message']  = "Produk Tidak Di Ketahui !";
    //     }
    //     $this->form_validation->set_rules('namapenerima', 'nama Penerima', 'required');
    //     $this->form_validation->set_rules('wapenerima', 'Nomer wa Penerima', 'required');
    //     $this->form_validation->set_rules('alamatpenerima', 'Alamat Penerima', 'required');
    //     if ($this->form_validation->run() == false) {
    //         Self::$data['status']     = false;
    //         Self::$data['message']     = validation_errors(' ', '<br/>');
    //     }

    //     $this->db->select('tb_produk.*, tb_cart.*');
    //     $this->db->from('tb_cart');
    //     $this->db->join('tb_produk', 'tb_cart.cart_produk_code = tb_produk.produk_code');
    //     $this->db->where('tb_cart.cart_user_id', userid());
    //     $this->db->where('tb_cart.cart_status', 'pending');
    //     $this->db->where('tb_cart.cart_transaksi', 'penjualan');
    //     $dataCart = $this->db->get();
    //     if (Self::$data['status']) {
    //         $produkId = array();
    //         $hargaProduk = array();
    //         $qty = array();
    //         $code = strtolower(random_string('alnum', 64));
    //         // $totalharga = array();
    //         $totalharga = 0;
    //         $invcode = strtoupper('CSOR' . time());
    //         $datapenerima = array();
    //         $datapenerima = [
    //             $this->input->post('namapenerima'),
    //             $this->input->post('wapenerima'),
    //             $this->input->post('alamatpenerima'),
    //         ];
    //         foreach ($dataCart->result() as $key => $value) {
    //             array_push($produkId,  (int)$value->produk_id);
    //             $totalharga = $totalharga + (int)$value->cart_total_harga;
    //             array_push($hargaProduk, (int)$value->cart_harga_produk);
    //             array_push($qty, (int)$value->cart_qty);
    //         }

    //         $dataInput = [
    //             'inv_userid_from' => userid(),
    //             'inv_user_id' => 0,
    //             'inv_produkid' => json_encode($produkId),
    //             'inv_amount' => $totalharga,
    //             'inv_peramount' => json_encode($hargaProduk),
    //             'inv_qty'    => json_encode($qty),
    //             'inv_date_add' => sekarang(),
    //             'inv_orderkode'  => $invcode,
    //             'inv_code'       => $code,
    //             'inv_status'       => 'success',
    //             'inv_data_cust'     => json_encode($datapenerima)
    //         ];

    //         $this->db->insert('tb_invoice', $dataInput);
    //         // update cart
    //         $this->db->where('cart_user_id', userid());
    //         $this->db->where('cart_status', 'pending');
    //         $this->db->where('cart_transaksi', 'penjualan');
    //         $this->db->delete('tb_cart');

    //         $namaproduk = array();
    //         $totalqty = 0;
    //         foreach ($dataCart->result() as $key => $value) {
    //             array_push($namaproduk, $value->produk_nama);
    //             // insert stok
    //             $this->db->insert('tb_stok', [
    //                 'stok_produkid'    => $value->produk_id,
    //                 'stok_pengirim_userid'     =>  userid(),
    //                 'stok_penerima_userid'        => 0,
    //                 'stok_amount'    => $value->cart_qty,
    //                 'stok_type'        => 'debit',
    //                 'stok_desc'        => 'Penjualan',
    //                 'stok_date'        => sekarang(),
    //                 'stok_code'        => random_string('alnum', 64),
    //             ]);
    //             // insert history
    //             $this->db->insert('tb_history_penjualan', [
    //                 'histpenj_userid'        => userid(),
    //                 'histpenj_invkode'        => $invcode,
    //                 'histpenj_invproduk'       => $value->produk_nama,
    //                 'histpenj_desc'            => 'Menjual barang ' . $value->produk_nama . ' ke pelanggan ',
    //                 'histpenj_date'            => sekarang(),
    //                 'histpenj_code'            => random_string('alnum', 64)
    //             ]);

    //             $totalqty += $value->cart_qty; // Penjumlahan array ke total

    //         }

    //         if ($totalqty >= 50) {
    //             $this->bonus_omset(userid(), userid(), 1, 1, $totalqty);
    //             $this->bonus_omset_kedua();
    //         }


    //         Self::$data['heading']    = 'Success';
    //         Self::$data['message']    = 'Pengiriman Produk Berhasil';
    //         Self::$data['type']        = 'success';
    //     } else {
    //         Self::$data['heading']    = 'GAGAL';
    //         Self::$data['type']        = 'error';
    //     }

    //     return Self::$data;
    // }

    function bonus_omset($user_id = null, $user_id_from = null, $pktid = null, $level = 1, $qty)
    {
        $result         = array();
        $status         = true;
        $paketid        = $pktid;

        $this->db->where('package_id', $paketid);
        $get_packages         = $this->db->get('tb_packages')->row();

        $array_term_level     = json_decode($get_packages->package_level);
        if ($level > count($array_term_level)) {
            $status = false;
        }

        $datauser         = userdata(['id' => $user_id]);
        $userdata         = userdata(['id' => $user_id_from]);
        $cekdistributor = userdata(['id' => $userdata->upline_id])->user_type;
        if ($cekdistributor == 'distributor') {
            $status = false;
        }

        $datauser         = userdata(['id' => $user_id]);
        $userdata         = userdata(['id' => $user_id_from]);
        if ($userdata->referral_id == 0) {
            $status = false;
        }


        $uplinedata     = userdata(['id' => $userdata->upline_id]);

        if ($status) {
            if ($uplinedata) {
                $wallet             = $this->usermodel->userWallet('withdrawal', $uplinedata->id);
                $this->db->insert(
                    'tb_wallet_balance',
                    [
                        'w_balance_wallet_id'       => $wallet->wallet_id,
                        'w_balance_amount'          => $array_term_level[$level - 1] * $qty,
                        'w_balance_type'            => 'credit',
                        'w_balance_desc'            => 'Bonus Omset Reseller, Level ke ' . $level . ' dari Penjualan Produk : ' . $datauser->username,
                        'w_balance_date_add'        => sekarang(),
                        'w_balance_txid'            => strtolower(random_string('alnum', 64)),
                        'w_balance_ket'             => 'bonus omset reseller',
                    ]
                );

                $this->bonus_omset($datauser->id, $uplinedata->id, $paketid, $level + 1, $qty);
            }
        }
        return $result;
    }


    ///////////////!
}
