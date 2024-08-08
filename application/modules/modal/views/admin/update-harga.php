<?php
$id = $this->input->get('produkid');
$this->db->where('harga_produkid', $id);
$cekharga = $this->db->get('tb_harga');
if ($cekharga->num_rows() == 0) {
?>
    <center>DATA PRODUK TIDAK VALID</center>
<?php } else { ?>
    <?php $dataharga = $cekharga->row(); ?>
    <?php echo form_open('', array('id' => 'update_harga')); ?>
    <input type="hidden" name="harga_produkid" value="<?php echo $id; ?>">
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