<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Repatorder extends CI_Model
{

    private static $data = [
        'status'     => true,
        'message'     => null,
    ];

    public function __construct()
    {
        parent::__construct();
        Self::$data['csrf_data']         = $this->security->get_csrf_hash();
    }

    function new_repeatorder()
    {
        if (!$this->ion_auth->hash_password_db(userid(), post('ro_password'))) {
            Self::$data['status']     = false;
            Self::$data['message']     = 'Konfirmasi Password Tidak Cocok!';
        } else {
            $total         = (int)preg_replace('/[^0-9.]+/', '', preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $this->input->post('ro_total')));

            $this->db->where('pin_package_id', (int)2);
            $this->db->where('pin_userid', userid());
            $this->db->join('tb_packages', 'pin_package_id = package_id');
            $get_pin = $this->db->get('tb_users_pin');
            if ($get_pin->num_rows() < $total) {
                Self::$data['status']     = false;
                Self::$data['message']     = 'Stok PIN Repeat Order Kurang!';
            }
        }

        $this->form_validation->set_rules('ro_total', 'Total Repeat Order', 'required');
        $this->form_validation->set_rules('ro_password', 'Password', 'required');
        if ($this->form_validation->run() == FALSE) {
            Self::$data['status']     = false;
            Self::$data['message']     = validation_errors('', '<br/>');
        }

        if (Self::$data['status']) {
            $paket  = $get_pin->row();
            $userdata = userdata();
            $total    = (int)preg_replace('/[^0-9.]+/', '', preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $this->input->post('ro_total')));

            // HAPUS SESUAI TOTAL RO
            $this->db->limit($total);
            $this->db->where('pin_userid', userid());
            $this->db->where('pin_package_id', (int)2);
            $cekpinserial = $this->db->get('tb_users_pin');
            foreach ($cekpinserial->result() as $show) {
                $this->db->delete('tb_users_pin', array('pin_code' => $show->pin_code));
            }

            // REPORT RO
            $this->db->insert(
                'tb_historiro',
                [
                    'historiro_userid'  => userid(),
                    'historiro_amount'  => $total,
                    'historiro_date'    => sekarang(),
                    'historiro_code'    => strtolower(random_string('alnum', 64)),
                ]
            );

            $this->db->insert(
                'tb_omset',
                [
                    'omset_userid'  => userid(),
                    'omset_amount'  => (int)$total * $paket->package_price,
                    'omset_desc'    => 'Transaksi Repeat Order Username : ' . $userdata->username . ' dengan ' . $total . ' PIN Kode',
                    'omset_date'    => sekarang(),
                ]
            );
            $this->bonuslevel($userdata->id, $userdata->id, 1);


            /*============================================
            =	            INSERT POINT                =
            ============================================*/
            $this->db->insert(
                'tb_pointro',
                [
                    'pointro_userid'    => userid(),
                    'pointro_amount'    => $total,
                    'pointro_date'      => sekarang(),
                    'pointro_code'      => random_string('alnum', 64)
                ]
            );















            /*============================================
            =	            BONUS SPONSOR            	=
            ============================================*/
            // $wallet_reff     = $this->usermodel->userWallet('withdrawal', $userdata->referral_id);
            // $this->db->insert(
            //     'tb_wallet_balance',
            //     [
            //         'w_balance_wallet_id'       => $wallet_reff->wallet_id,
            //         'w_balance_amount'          => (int)$total * $paket->package_sponsor,
            //         'w_balance_type'            => 'credit',
            //         'w_balance_desc'            => 'Bonus Sponsor, Repeat Order Username : ' . $userdata->username . ' dengan ' . $total . ' PIN Kode',
            //         'w_balance_date_add'        => sekarang(),
            //         'w_balance_ket'             => 'sponsor',
            //         'w_balance_txid'            => hash('SHA256', random_string('alnum', 16)),
            //     ]
            // );

            // if ($paket->package_bv > 0) {
            //     // JIKA BV LEBIH BESAR DARI 0 ATAU ADA BVNYA

            //     /*============================================
            //     =	            OMSET GLOBAL            	=
            //     ============================================*/
            //     $this->db->insert(
            //         'tb_omset',
            //         [
            //             'omset_amount'  => (int)$total * $paket->package_bv,
            //             'omset_desc'    => 'Transaksi Repeat Order Username : ' . $userdata->username . ' dengan ' . $total . ' PIN Kode',
            //             'omset_date'    => sekarang(),
            //         ]
            //     );

            //     $NEWBV  = $userdata->user_omset + (int)$total * $paket->package_bv;

            //     $this->db->update(
            //         'tb_users',
            //         [
            //             'user_omset'    => $NEWBV,
            //         ],
            //         [
            //             'id'            => userid(),
            //         ]
            //     );

            //     // OMSET GROUP
            //     $this->omsetgroup($userdata->id, $userdata->id, ((int)$total * $paket->package_bv));

            //     /*============================================
            //     =	            BONUS LEVEL            		=
            //     ============================================*/
            //     $nilaiBV = 1000 * ((int)$total * $paket->package_bv);

            //     $this->bonuslevel($userdata->id, $userdata->id, $nilaiBV, 1);
            // }

            Self::$data['heading']     = 'Berhasil';
            Self::$data['message']     = 'Transaksi Repeat Order Berhasil';
            Self::$data['type']        = 'success';
        } else {

            Self::$data['heading']     = 'Gagal';
            Self::$data['type']        = 'error';
        }

        return Self::$data;
    }

    function omsetgroup($user_id = null, $user_id_from = null, $getbv = 0)
    {
        $result         = array();
        $status         = true;

        $datauser         = userdata(['id' => $user_id]);
        $userdata         = userdata(['id' => $user_id_from]);
        if ($userdata->upline_id == 0) {
            $status = false;
        }

        $uplinedata     = userdata(['id' => $userdata->upline_id]);

        if ($status) {

            if ($uplinedata) {

                // JIKA PENERIMA SUDAH MEMPUNYA 100BV MAKA OMSET BERTAMBAH
                if ($this->rank->myBV($uplinedata->id) >= 100) {
                    // OMSET LAMA + NILAI BARU
                    $totalBV = $uplinedata->user_omset + $getbv;

                    $this->db->update(
                        'tb_users',
                        [
                            'user_omset'    => $totalBV,
                        ],
                        [
                            'id'            => $uplinedata->id,
                        ]
                    );
                }


                $this->omsetgroup($datauser->id, $uplinedata->id, $getbv);
            }
        }
        return $result;
    }

    function bonuslevel($user_id = null, $user_id_from = null, $level = 1)
    {
        $result         = array();
        $status         = true;
        // $setTot         = $toooot;

        $datauser       = userdata(['id' => $user_id]);
        $userdata       = userdata(['id' => $user_id_from]);

        // GET PAKET
        $this->db->where('package_id', (int)2);
        $get_packages         = $this->db->get('tb_packages')->row();

        $array_term_level     = json_decode($get_packages->package_level);
        if ($level > count($array_term_level)) {
            $status = false;
        }

        if ($userdata->upline_id == 0) {
            $status = false;
        }

        $uplinedata     = userdata(['id' => $userdata->upline_id]);


        if ($status) {
            if ($uplinedata) {

                // PENERIMA BONUS WAJIB RO ATAU BV SEBESAR 100
                // if ($this->rank->myBV($uplinedata->id) >= 100) {

                $wallet             = $this->usermodel->userWallet('withdrawal', $uplinedata->id);


                $this->db->insert(
                    'tb_wallet_balance',
                    [
                        'w_balance_wallet_id'       => $wallet->wallet_id,
                        // 'w_balance_amount'          => ($array_term_level[$level - 1] / 100) * $setTot,
                        'w_balance_amount'          => (int)2500,
                        'w_balance_type'            => 'credit',
                        'w_balance_desc'            => 'Bonus Unilevel, Level Ke ' . $level . ' dari Username : ' . $datauser->username,
                        'w_balance_date_add'        => sekarang(),
                        'w_balance_txid'            => strtolower(random_string('alnum', 64)),
                        'w_balance_ket'             => 'unilevel',
                    ]
                );
                // }

                $this->bonuslevel($datauser->id, $uplinedata->id, $level + 1);
            }
        }
        return $result;
    }
}
