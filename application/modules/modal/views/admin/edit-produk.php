<?php
$code = $this->input->get('code');

$this->db->where('produk_code', $code);
$cekProduk = $this->db->get('tb_produk');
if ($cekProduk->num_rows() == 0) {
?>
    <center>DATA PRODUK TIDAK VALID</center>
<?php } else { ?>
    <?php $dataProduk = $cekProduk->row(); ?>
    <?php echo form_open_multipart('', array('id' => 'Edit_Produk', 'autocomplete' => "off")); ?>
    <br>
    <h4 align="center">Perubahan Data produk</h4>
    <br>

    <input type="hidden" name="code" class="form-control" value="<?php echo $dataProduk->produk_code; ?>">
    <div class="form-group">
    <label for="produk_nama">Nama Produk</label>
    <input placeholder="Nama Produk" type="text" value="<?php echo $dataProduk->produk_nama; ?>" name="produk_nama" class="form-control">
</div>
<div class="form-group">
    <label>Upload Gambar</label>
    <div class="custom-file">
        <input name="produk_image" type="file" class="custom-file-input form-control" id="imgcover" onchange="getimggg(this)">
    </div>
    <small class="text-danger">Pilih Gambar Untuk Melihat Preview</small><br>
    <div style="display: flex; justify-content: center; align-items: center;margin-top: 10px;">
        <img id="gambarcover" src="<?php echo base_url('assets/upload/' . $dataProduk->produk_image); ?>" style="max-width:150px; max-height:150px;margin-top: 10px;border: 1px solid #ddd">
    </div>
    <script type="text/javascript">
        function getimggg(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#gambarcover')
                        .attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</div>
    
<div class="form-group">
    <button type="submit" class="btn btn-md btn-primary text-white btn-block m-1">Simpan Perubahan</button>
</div>
<?php echo form_close(); ?>

    <?php echo form_open('', array('id' => 'Edit_Stok', 'autocomplete' => "off")); ?>
    <br>
    <h4 align="center">Perubahan Stok</h4>
    <br>

    <div class="form-group">
        <label for="produk_stok">Update Stok Produk</label>
        <input type="number" name="produk_stok" class="form-control" value="<?php echo $this->walletmodel->stokBarang($dataProduk->produk_code); ?>">
        <input type="hidden" name="produk_code" class="form-control" value="<?php echo $dataProduk->produk_code; ?>">
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-md btn-primary text-white btn-block m-1">Simpan perubahan</button>
    </div>

    <?php echo form_close(); ?>
    <?php
// $id = $this->input->get('produkid');
$this->db->where('harga_produkid', $dataProduk->produk_id);
$cekharga = $this->db->get('tb_harga');
if ($cekharga->num_rows() == 0) {
?>
    <center>DATA PRODUK TIDAK VALID</center>
<?php } else { ?>
    <?php $dataharga = $cekharga->row(); ?>
    <?php echo form_open('', array('id' => 'update_harga')); ?>
    <br>
    <h4 align="center">Perubahan harga</h4>
    <br>
    <input type="hidden" name="harga_produkid" value="<?php echo $dataProduk->produk_id; ?>">
    <div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="exampleInputEmail1">Harga Aceh 1</label>
        <input type="number" name="harga_aceh1" placeholder="aceh utara dan sekitarnya" class="form-control" value="<?php echo $dataharga->harga_aceh1 ?>" autocomplete="off">
    </div>
    </div>
    <div class="col-sm-6">
<div class="form-group">
    <label for="exampleInputEmail1">Harga Aceh 2</label>
<input type="number" name="harga_aceh2" placeholder="aceh Selatan dan sekitarnya" class="form-control" value="<?php echo $dataharga->harga_aceh2 ?>" autocomplete="off">
</div>
    </div>
</div>
<div class="row">
<div class="col-sm-6">
    <div class="form-group">
        <label for="exampleInputEmail1">Harga Medan</label>
    <input type="number" name="harga_medan" placeholder="Medan dan sekitarnya" class="form-control" value="<?php echo $dataharga->harga_medan ?>" autocomplete="off">
</div>
</div>
<div class="col-sm-6">
<div class="form-group">
    <label for="exampleInputEmail1">Harga Riau 1</label>
<input type="number" name="harga_riau1" placeholder="Pekanbaru dan sekitarnya" class="form-control" value="<?php echo $dataharga->harga_riau1 ?>" autocomplete="off">
</div>
</div>
</div>
<div class="row">
<div class="col-sm-6">

    <div class="form-group">
        <label for="exampleInputEmail1">Harga Riau 2</label>
    <input type="number" name="harga_riau2" placeholder="Tembilahan dan sekitarnya" class="form-control" value="<?php echo $dataharga->harga_riau2 ?>" autocomplete="off">
</div>
</div>
<div class="col-sm-6">

<div class="form-group">
    <label for="exampleInputEmail1">Harga Kep. Riau</label>
<input type="number" name="harga_kepriau" placeholder="Kep. Riau dan sekitarnya" class="form-control" value="<?php echo $dataharga->harga_kepriau ?>" autocomplete="off">
</div>
</div>
</div>
<div class="row">
<div class="col-sm-6">

    <div class="form-group">
        <label for="exampleInputEmail1">Harga Sumbar</label>
    <input type="number" name="harga_sumbar" placeholder="Sumatra Barat dan sekitarnya" class="form-control" value="<?php echo $dataharga->harga_sumbar ?>" autocomplete="off">
</div>
</div>
<div class="col-sm-6">

<div class="form-group">
    <label for="exampleInputEmail1">Harga Jambi</label>
<input type="number" name="harga_jambi" placeholder="Jambi dan sekitarnya" class="form-control" value="<?php echo $dataharga->harga_jambi ?>" autocomplete="off">
</div>
</div>
</div>
    <div class="form-group">
        <button id="btnSubmit" type="submit" class="btn btn-block btn-primary">Update Harga</button>
    </div>
    <?php echo form_close(); ?>
    <script>
        $(document).ready(function() {
            $('#update_harga').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                console.log(...formData.entries());
                Swal.fire({
                    allowOutsideClick: false,
                    title: 'Apakah Anda yakin?',
                    text: "Akan Mengupdate Harga Dari Produk ini?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Update',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: '<?php echo site_url('postdata/admin_post/Produk/updateHarga'); ?>',
                            type: 'post',
                            dataType: 'json',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(data) {
                                console.log(data);
                                updateCSRF(data.csrf_data); // Pastikan fungsi ini tersedia untuk memperbarui token CSRF jika diperlukan
                                Swal.fire(
                                    data.heading,
                                    data.message,
                                    data.type
                                ).then(function() {
                                    if (data.status) {
                                        window.location.reload();
                                    }
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                                Swal.fire(
                                    'Error',
                                    'Terjadi kesalahan saat mengupdate stok.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });

    </script>

<?php } ?>
    
    <script>
        $(document).ready(function() {
            $('#Edit_Produk').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                console.log(...formData.entries());
                $.ajax({
                        url: '<?php echo site_url('postdata/admin_post/Produk/UpdateProduk') ?>',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                    })
                    .done(function(data) {
                        updateCSRF(data.csrf_data);
                        console.log(data)
                        Swal.fire(
                            data.heading,
                            data.message,
                            data.type
                        ).then(function() {
                            if (data.status) {
                                location.reload();
                            }
                        });
                    });
            });
        });

        function updateCSRF(newToken) {
            $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(newToken);
        }

        $(document).ready(function() {
            $('#Edit_Stok').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                console.log(...formData.entries());

                Swal.fire({
                    allowOutsideClick: false,
                    title: 'Apakah Anda yakin?',
                    text: "Akan Mengupdate Stok Produk ini?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Update',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: '<?php echo site_url('postdata/admin_post/Produk/UpdateStok'); ?>',
                            type: 'post',
                            dataType: 'json',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(data) {
                                console.log(data);
                                updateCSRF(data.csrf_data); // Pastikan fungsi ini tersedia untuk memperbarui token CSRF jika diperlukan
                                Swal.fire(
                                    data.heading,
                                    data.message,
                                    data.type
                                ).then(function() {
                                    if (data.status) {
                                        window.location.reload();
                                    }
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                                Swal.fire(
                                    'Error',
                                    'Terjadi kesalahan saat mengupdate stok.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
<?php } ?>