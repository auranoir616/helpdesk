<?php
$code = $this->input->get('code');
$dist = $this->input->get('distributor');
$this->db->where('cart_code', $code);
$this->db->where('cart_status', 'pending');
$this->db->where('cart_user_id', userid());
$cekcart = $this->db->get('tb_cart');

if ($cekcart->num_rows() == 0) {
?>
    <center>Data not found</center>
<?php } else {
?>
    <?php
    $datacart = $cekcart->result();
    $this->db->where('id', $dist);
    $datadistributor = $this->db->get('tb_users')->row();
    ?>
    <div>
        <center class="mb-3">
            <p class="h6">Pembelian Produk</p>
        </center>
        <?php foreach ($datacart as $item) {
            $this->db->where('produk_code', $item->cart_produk_code);
            $dataproduk = $this->db->get('tb_produk')->row();
        ?>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="">Distributor</label>
                        <input class="form-control" readonly type="text" value="<?php echo $datadistributor->username; ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="">Nama Produk</label>
                        <input class="form-control" readonly type="text" value="<?php echo $dataproduk->produk_nama; ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="">Quantity</label>
                        <div class="input-group mb-3">
                            <input value="<?php echo $item->cart_qty; ?>" type="text" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="">Total Harga</label>
                        <div class="input-group mb-3">
                            <input value="Rp.<?php echo number_format($item->cart_total_harga); ?>" type="text" class="form-control" readonly>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <center class="mb-3">
                    <p class="h6">Rekening Distributor</p>
                </center>
                <input type="hidden" name="code" value="<?php echo $code; ?>">
                <div class="form-group">
                    <label for="">Nama Bank</label>
                    <input class="form-control" readonly type="text" value="<?php echo $datadistributor->user_bank_name ? $datadistributor->user_bank_name : 'Distributor Belum Memiliki Rekening'; ?>">
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Rekening Atas Nama</label>
                            <div class="input-group mb-3">
                                <input value="<?php echo $datadistributor->user_bank_account ? $datadistributor->user_bank_account : 'Distributor Belum Memiliki Rekening'; ?>" type="text" class="form-control" id="bankaccount" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="button" onclick="copy1()" style="border-top-left-radius: 0;border-bottom-left-radius: 0;"><i class="zmdi zmdi-copy"></i></button>
                                </div>
                            </div>
                        </div>
                        <script>
                            function copy1() {
                                var copyText = document.getElementById("bankaccount");
                                copyText.select();
                                copyText.setSelectionRange(0, 99999);
                                document.execCommand("copy");
                                Swal.fire(
                                    "Berhasil",
                                    "Rekening Atas Nama di Copy",
                                    "success"
                                )
                            }
                        </script>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Nomor Rekening</label>
                            <div class="input-group mb-3">
                                <input value="<?php echo $datadistributor->user_bank_number ? $datadistributor->user_bank_number : 'Distributor Belum Memiliki Rekening'; ?>" type="text" class="form-control" id="banknumber" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="button" onclick="copy2()" style="border-top-left-radius: 0;border-bottom-left-radius: 0;"><i class="zmdi zmdi-copy"></i></button>
                                </div>
                            </div>
                        </div>
                        <script>
                            function copy2() {
                                var copyText = document.getElementById("banknumber");
                                copyText.select();
                                copyText.setSelectionRange(0, 99999);
                                document.execCommand("copy");
                                Swal.fire(
                                    "Berhasil",
                                    "Nomor Rekening di Copy",
                                    "success"
                                )
                            }
                        </script>
                    </div>
                </div>
                <hr style="height: 3px;">
                <center class="mb-3">
                    <p class="h6">Konfirmasi Pembayaran</p>
                </center>
                <?php
                $this->db->where('id', userid());
                $datauser = $this->db->get('tb_users')->row();
                ?>
                <div class="form-group">
                    <label for="">Rekening Atasnama</label>
                    <input type="text" class="form-control" value="<?php echo $datauser->user_bank_account; ?>" placeholder="Rekening Atasnama" name="confirm_account" autocomplete="off">
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Nama Bank</label>
                            <input type="text" value="<?php echo $datauser->user_bank_name; ?>" class="form-control" placeholder="Nama Bank" name="confirm_bank" autocomplete="off">
                            <small>Contoh: BNI, BRI, BCA, BTN</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Nomor Rekening</label>
                            <input type="text" value="<?php echo $datauser->user_bank_number; ?>" class="form-control" placeholder="Nomor Rekening" name="confirm_number" autocomplete="off">
                        </div>
                    </div>
                </div>
                <style>
                        .file-upload {
                            display: block;
                            text-align: center;
                            font-family: Helvetica, Arial, sans-serif;
                            font-size: 12px;
                        }

                        .file-upload .file-select {
                            display: block;
                            border: 2px solid #dce4ec;
                            color: #34495e;
                            cursor: pointer;
                            height: 40px;
                            line-height: 40px;
                            text-align: left;
                            background: #ffffff;
                            overflow: hidden;
                            position: relative;
                        }

                        .file-upload .file-select .file-select-button {
                            background: #dce4ec;
                            padding: 0 10px;
                            display: inline-block;
                            height: 40px;
                            line-height: 40px;
                        }

                        .file-upload .file-select .file-select-name {
                            line-height: 40px;
                            display: inline-block;
                            padding: 0 10px;
                        }

                        .file-upload .file-select:hover {
                            border-color: #34495e;
                            transition: all 0.2s ease-in-out;
                            -moz-transition: all 0.2s ease-in-out;
                            -webkit-transition: all 0.2s ease-in-out;
                            -o-transition: all 0.2s ease-in-out;
                        }

                        .file-upload .file-select:hover .file-select-button {
                            background: #34495e;
                            color: #ffffff;
                            transition: all 0.2s ease-in-out;
                            -moz-transition: all 0.2s ease-in-out;
                            -webkit-transition: all 0.2s ease-in-out;
                            -o-transition: all 0.2s ease-in-out;
                        }

                        .file-upload.active .file-select {
                            border-color: #3fa46a;
                            transition: all 0.2s ease-in-out;
                            -moz-transition: all 0.2s ease-in-out;
                            -webkit-transition: all 0.2s ease-in-out;
                            -o-transition: all 0.2s ease-in-out;
                        }

                        .file-upload.active .file-select .file-select-button {
                            background: #3fa46a;
                            color: #ffffff;
                            transition: all 0.2s ease-in-out;
                            -moz-transition: all 0.2s ease-in-out;
                            -webkit-transition: all 0.2s ease-in-out;
                            -o-transition: all 0.2s ease-in-out;
                        }

                        .file-upload .file-select input[type="file"] {
                            z-index: 100;
                            cursor: pointer;
                            position: absolute;
                            height: 100%;
                            width: 100%;
                            top: 0;
                            left: 0;
                            opacity: 0;
                            filter: alpha(opacity=0);
                        }

                        .file-upload .file-select.file-select-disabled {
                            opacity: 0.65;
                        }

                        .file-upload .file-select.file-select-disabled:hover {
                            cursor: default;
                            display: block;
                            border: 2px solid #dce4ec;
                            color: #34495e;
                            cursor: pointer;
                            height: 40px;
                            line-height: 40px;
                            margin-top: 5px;
                            text-align: left;
                            background: #ffffff;
                            overflow: hidden;
                            position: relative;
                        }

                        .file-upload .file-select.file-select-disabled:hover .file-select-button {
                            background: #dce4ec;
                            color: #666666;
                            padding: 0 10px;
                            display: inline-block;
                            height: 40px;
                            line-height: 40px;
                        }

                        .file-upload .file-select.file-select-disabled:hover .file-select-name {
                            line-height: 40px;
                            display: inline-block;
                            padding: 0 10px;
                        }
                    </style>

<?php echo form_open_multipart('', array('id' => 'konfirmpembayaran', 'autocomplete' => "off")); ?>
<input type="hidden" name="distributor" value="<?php echo $dist; ?>">
<div class="form-group">
    <div class="file-upload">
        <div class="file-select">
            <div class="file-select-button" id="fileName">Pilih File</div>
            <div class="file-select-name" id="noFile">Unggah Bukti Transfer</div>
            <input type="file" name="inv_image" id="chooseFile" onchange="readURL(this)">
        </div>
    </div>
    <img id="imgggggg" style="max-width:150px; max-height:150px;margin-top: 10px;border: 1px solid #ddd">
    <script type="text/javascript">
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imgggggg').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</div>
<div class="form-group">
    <button id="btn01confirmB" type="submit" class="btn btn-block btn-success">KONFIRMASI PEMBAYARAN</button>
    <button id="btn02confirmB" type="button" class="btn btn-block btn-success" disabled>MEMPROSES</button>
</div>
<?php echo form_close(); ?>
<script>
    jQuery(document).ready(function($) {
        $('#btn02confirmB').hide();
        $('#konfirmpembayaran').on('submit', function(e) {
            e.preventDefault(); // Memastikan form tidak ter-submit secara default
            var formData = new FormData(this);

            $('#btn01confirmB').hide();
            $('#btn02confirmB').show();

            $.ajax({
                url: '<?php echo site_url('postdata/user_post/penjualan/checkout') ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
            })
            .done(function(data) {
                updateCSRF(data.csrf_data);
                Swal.fire(
                    data.heading,
                    data.message,
                    data.type
                ).then(function() {
                        location.reload();
                    $('#btn01confirmB').show();
                    $('#btn02confirmB').hide();
                });
            })
            .fail(function(xhr, status, error) {
                console.error(xhr.responseText);
                Swal.fire(
                    'Error',
                    'Gagal mengirim data. Silakan coba lagi.',
                    'error'
                );
                $('#btn01confirmB').show();
                $('#btn02confirmB').hide();
            });
        });
    });
</script>
        <?php } ?>
            </div>