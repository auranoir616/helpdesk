<!DOCTYPE html>
<html>

<head>
    <title>invoice <?php echo $invoice->inv_orderkode ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .invoice-box {
            width: 100%;
            padding: 5px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 12px;
            line-height: 10px;
            color: #555;
        }

        .header {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: start;
            width: 100%;
            background-color: #555;

        }

        img {
            width: 200px;
        }

        .details,
        .items,
        .totals {
            width: 100%;
            margin-bottom: 10px;
        }

        .details td,
        .items td,
        .totals td {
            padding: 5px;
        }

        .details {
            border-bottom: 1px solid #eee;
        }

        .items thead td {
            border-bottom: 1px solid #eee;
            font-weight: bold;
        }

        .items tbody tr:last-child td {
            border-bottom: 1px solid #eee;
        }

        .totals td {
            border-top: 1px solid #eee;
            font-weight: bold;
        }

        .totals .label {
            text-align: left;
        }

        .totals .value {
            text-align: left;
        }

        .watermark {
            position: absolute;
            top: 35%;
            /* Posisi vertikal */
            left: 50%;
            /* Posisi horizontal */
            transform: translate(-50%, -50%);
            /* Pusatkan teks */
            font-size: 100px;
            /* Ukuran teks */
            color: rgba(0, 0, 0, 0.1);
            /* Warna teks dengan transparansi */
            z-index: 10;
            /* Pastikan di atas konten lain */
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header" style="display: flex; justify-content: space-between; align-items: center; background-color: #555;">
            <div style="text-align: left;">
                <h1>PT Gleh Store Industri  </h1>
                <p>Jl. Contoh Alamat</p>
                <p>Telp: 0123-456789</p>
            </div>
            <div style="text-align: right;">
                <img src="<?php echo base_url('assets/gleh.png') ?>" alt="Gleh Store" title="Logo">
            </div>
        </div>
        <?php
        $this->db->where('id', $invoice->inv_user_id);
        $datadistributor = $this->db->get('tb_users')->row();

        $this->db->where('kode', $customer->user_provinsi);
        $getprov = $this->db->get('wilayah')->row();
        $this->db->where('kode', $customer->user_kota);
        $getkota = $this->db->get('wilayah')->row();
        $this->db->where('kode', $customer->user_kecamatan);
        $getkecamatan = $this->db->get('wilayah')->row();
        ?>

        <table class="details">
            <tr>
                <td width="10%">Kode Invoice</td>
                <td width="2%">:</td>
                <td width="35%"><?php echo $invoice->inv_orderkode; ?></td>
                <td width="10%">Penerima</td>
                <td width="2%">:</td>
                <td width="20%"><?php echo $customer->user_fullname; ?></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td><?php echo date('d-m-Y', strtotime($invoice->inv_date_add)); ?></td>
                <td>Tipe Member</td>
                <td>:</td>
                <td><?php echo $customer->user_type; ?></td>
            </tr>
            <tr>
                <td>Distributor</td>
                <td>:</td>
                <td><?php echo $datadistributor->user_fullname; ?></td>
                <td>Alamat</td>
                <td>:</td>
                <td><?php echo $getkota->nama . ', ' . $getkecamatan->nama . ', ' . $customer->user_alamat; ?></td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <td>No</td>
                    <td>Item</td>
                    <td>Qty</td>
                    <td>Harga / produk</td>
                    <td>subtotal</td>
                </tr>
            </thead>

            <tbody>
                <?php
                print_r($logo);
                $total = 0;
                $i = 1;
                foreach (json_decode($invoice->inv_produkid) as $index => $idprod) {
                    $getproduk = $this->db->where('produk_id', $idprod)->get('tb_produk')->row();
                    $qty = json_decode($invoice->inv_qty)[$index];
                    $harga = $invoice->inv_amount;
                    $hargaperitem = $harga / $qty;
                    $total += $hargaperitem * $qty;
                ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $getproduk->produk_nama; ?></td>
                        <td><?php echo $qty; ?></td>
                        <td>Rp.<?php echo number_format($hargaperitem, 0, ',', '.'); ?></td>
                        <td>Rp.<?php echo number_format($harga, 0, ',', '.'); ?></td>
                    </tr>
                <?php } ?>

            </tbody>
            <tfoot class="totals">
                <tr>
                    <td class="label" colspan="4">Total:</td>
                    <td class="value">Rp.<?php echo number_format($total, 0, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>

        <table class="">

        </table>

        <div class="footer">
            <p>Terima kasih telah Menjadi Member di Gleh Store</p>
        </div>
        <div class="watermark">LUNAS</div>
    </div>
</body>

</html>