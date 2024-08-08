<html>

<head>
    <style>
        @media print {

            /* Sembunyikan judul dan URL */
            head,
            title,
            footer {
                display: none !important;
            }

            @page {
                margin: 0;
                padding: 0;
                size: auto;
                /* Atur ukuran halaman ke ukuran default */
            }

            /* Sembunyikan header dan footer */
            @page {
                margin-top: 0;
                margin-bottom: 0;
            }
        }

        h2 {
            padding: 0px;
            margin: 0px;
            font-size: 14pt;
        }

        h4 {
            font-size: 12pt;
        }

        text {
            padding: 0px;
        }

        table {
            border-collapse: collapse;
            font-size: 11pt;
        }

        th,
        td {
            padding: 3px;
        }

        .tab th {
            border: 0.5px solid #000;
        }

        .tab td {
            border: 0.5px solid #000;
            padding: 3px;
        }

        table.tab {
            table-layout: auto;
            border: 0.5px solid #000;
            width: 100%;
        }

        table.no-border {
            table-layout: auto;
            border: 0px solid #000;
            width: 100%;
        }

        .rp {
            float: left;
            text-align: left;
        }

        .left {
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .b {
            font-weight: bold;
        }

        .i {
            font-style: italic;
        }

        /* LOGO ATAS */
        .floating-image {
            position: absolute;
            /* atau fixed, tergantung kebutuhan */
            top: 0;
            left: 0;
            z-index: 1000;
            margin-top: 1rem;
            margin-left: 1rem;
        }

        /* Tabulasi Posisi DIV Kiri Kanan */
        .rentan {
            display: flex;
            justify-content: center;
            /* align-items: start; */
            /* align-items: center; */
            /* height: 100vh; */
            margin: 0;
        }

        .container {
            display: flex;
            align-items: flex-start;
            width: 95%;
            justify-content: space-between;
        }

        .left,
        .right {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 10px;
        }

        .left {
            justify-content: flex-start;
        }

        .right {
            justify-content: flex-end;

        }
    </style>

    <title>Fifie Diah</title>
</head>

<body>
    <?php
    function terbilang($angka)
    {
        $angka = abs($angka);
        $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");

        $terbilang = "";
        if ($angka < 12) {
            $terbilang = $huruf[$angka];
        } elseif ($angka < 20) {
            $terbilang = terbilang($angka - 10) . " Belas";
        } elseif ($angka < 100) {
            $terbilang = terbilang($angka / 10) . " Puluh " . terbilang($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = "Seratus " . terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = terbilang($angka / 100) . " Ratus " . terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = "Seribu " . terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = terbilang($angka / 1000) . " Ribu " . terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = terbilang($angka / 1000000) . " Juta " . terbilang($angka % 1000000);
        } else {
            $terbilang = "Angka terlalu besar untuk di proses";
        }

        return $terbilang;
    }


    $touser = userdata(['id' => $penjualan->inv_user_id]);
    $fromuser = userdata(['id' => $penjualan->inv_userid_from]);

    ?>

    <div class="floating-image">
        <img src="<?php echo base_url('assets/logo.svg') ?>" width="220">
    </div>
    <div style="border:1px #000 solid;">
        <div style="page-break-after:always;margin-top:0%;padding:5px 10px 5px 10px;">

            <div style="margin-bottom:20px;">
                <center>
                    <p style="font-size:16pt;font-weight:bold;margin-top:1%;margin-bottom:1%;">Fifie Diah</p>
                    <p style="font-size:12pt;margin-top:1%;margin-bottom:1%;">Jl. Raya Apel Hijau, Batu. Jawa Timur</p>
                </center>
                <hr style="border-color:black;">
            </div>

            <center>
                <div style="margin-bottom:40px;">
                    <p style="font-size:16pt;font-weight:bold;text-align:center;margin-top:1%;margin-bottom:1%;">KUITANSI PENJUALAN</p>
                    <hr style="width: 20%; border-color:black;">
                    <p style="font-size:12pt;text-align:center;margin-top:1%;margin-bottom:3%;">
                        <em>ID ORDER : <?php echo $penjualan->inv_orderkode ?> (Tanggal :
                            <?php
                            setlocale(LC_TIME, 'id_ID');
                            echo strftime('%A, %e %B %Y %H:%M', strtotime($penjualan->inv_date_add));
                            ?>)</em>
                    </p>
                </div>
            </center>

            <div class="rentan">
                <div class="container">
                    <div class="left">
                        <table>
                            <tr>
                                <td>Nama Penerima</td>
                                <td>: <?php echo $touser->user_fullname . " / " . $touser->username . " - " . $touser->user_type ?></td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>:
                                    <?php
                                    /*=================================
                            =       Mengambil Wilayah         =
                            =================================*/

                                    $setprov = null;
                                    $setkab = null;
                                    $setkec = null;
                                    // $setkelur = null;

                                    $getprov = $this->db->query('SELECT * FROM wilayah WHERE CHAR_LENGTH(kode) = 2 AND LEFT(kode,2)="' . $touser->user_provinsi . '"');
                                    if ($getprov->num_rows() != 0) {
                                        $setprov = ucwords(strtolower($getprov->row()->nama));
                                    }
                                    $getkab = $this->db->query('SELECT * FROM wilayah WHERE CHAR_LENGTH(kode) = 5 AND LEFT(kode,5)="' . $touser->user_kota . '"');
                                    if ($getkab->num_rows() != 0) {
                                        $setkab = ucwords(strtolower($getkab->row()->nama));
                                    }
                                    $getkec = $this->db->query('SELECT * FROM wilayah WHERE CHAR_LENGTH(kode) = 8 AND LEFT(kode,8)="' . $touser->user_kecamatan . '"');
                                    if ($getkec->num_rows() != 0) {
                                        $setkec = ucwords(strtolower($getkec->row()->nama));
                                    }

                                    echo "{$touser->user_alamat}, Kec. {$setkec}, {$setkab}, {$setprov}";
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Kontak</td>
                                <td>: <?php echo $touser->user_phone . " / " . $touser->email ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <hr style="border-color:black;">
            </p>

            <div class="rentan">
                <div class="container">
                    <div class="left">
                        <table>
                            <tr>
                                <td>Pengiriman Produk :</td>
                            </tr>

                            <?php
                            $qty = json_decode($penjualan->inv_qty);
                            $i = 0;
                            foreach (json_decode($penjualan->inv_produkid) as $idprod) :
                                $getproduk = $this->db->where('produk_id', $idprod)->get('tb_produk')->row();
                            ?>

                                <tr>
                                    <td><?php echo $getproduk->produk_nama . " " . $qty[$i] . " Pcs "; ?></td>
                                </tr>


                            <?php $i++;
                            endforeach;  ?>


                        </table>
                    </div>
                    <div class="right">
                        <table>
                            <tr>
                                <td>Rincian</td>
                                <td>:</td>
                            </tr>
                            <?php
                            $peramout = json_decode($penjualan->inv_peramount);

                            for ($i = 0; $i < count($peramout); $i++) { ?>
                                <tr>
                                    <td><?php echo $qty[$i] ?> x</td>
                                    <td>: Rp. <?php echo number_format($peramout[$i], 0, ',', '.') ?></td>
                                </tr>
                            <?php } ?>

                            <tr style="font-weight: bold;">
                                <td>Sub Total</td>
                                <td>: Rp. <?php echo number_format($penjualan->inv_amount, 0, ',', '.') ?></td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>

            <div class="rentan">
                <div class="container">
                    <div class="right">
                        <p><b>Terbilang :</b> <i><?php echo terbilang($penjualan->inv_amount) . "Rupiah" ?></i></p>
                    </div>
                </div>

            </div>

            <br>
            <table class="no-border">
                <tr>
                    <td class="center" width="25%">
                        <p style="margin: 0% !important;">TTD PENGIRIM</p>

                        <p style="font-weight: bold !important; margin: 0%; margin-top: 4rem !important; padding: 0% !important;"><?php echo $fromuser->user_fullname ?></p>
                    </td>

                    <td class="center" width="25%">
                        <p style="margin:0% !important;">TTD PENERIMA</p>

                        <p style="font-weight: bold !important; margin: 0%; margin-top: 4rem !important; padding: 0% !important;"><?php echo $touser->user_fullname ?></p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <script>
        window.print();
    </script>
</body>

</html>