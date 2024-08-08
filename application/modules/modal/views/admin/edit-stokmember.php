<?php
$userid = $this->input->get('userid');
$produkid = $this->input->get('produkid');

// $this->db->where('t', $id);
$cekpoin = $this->db->get('tb_stok');
if ($cekpoin->num_rows() == 0) {
?>
    <center>DATA REWARD TIDAK VALID</center>
<?php } else { ?>
    <?php $poindata = $cekpoin->row(); ?>
    <?php echo form_open_multipart('', array('id' => 'update_stokmember')); ?>
    <div class="form-group">
    <label for="stok_type">Tipe Transaksi</label>
    <select name="stok_type" onchange="ubahWarna()" class="form-control" id="stok_type" required>
        <option value="" selected disabled>pilih tipe</option>
        <option value="credit">Tambah Stok</option>
        <option value="debit">Kurangi Stok</option>
    </select>
    </div>
    <div class="form-group">
    <label for="stok_produkid">Produk Yang akan di Update</label>
    <select name="stok_produkid" class="form-control" id="stok_produkid" required>
        <option value="" selected disabled>pilih Produk</option>
    <?php $produk = $this->db->get('tb_produk'); ?>
    <?php foreach($produk->result() as $pro) { ?>
        <option value="<?php echo $pro->produk_id?>"><?php echo $pro->produk_nama ?></option>
        <?php } ?>
    </select>
    </div>

    <div class="form-group">
        <label for="exampleInputEmail1">Stok</label>
        <input type="number" name="stok_amount" placeholder="Masukkan jumlah stok yang akan ditambahkan / dikurangi" class="form-control" value="" autocomplete="off">
        <input type="hidden" name="stok_penerima_userid" class="form-control" value="<?php echo $userid; ?>" autocomplete="off">
    </div>
    <div class="form-group">
        <button id="btnSubmit" type="submit" class="btn btn-block btn-primary">Update Stok</button>
    </div>
    <?php echo form_close(); ?>
    <script>
        $(document).ready(function() {
            $('#update_stokmember').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                console.log(...formData.entries());
                Swal.fire({
                    allowOutsideClick: false,
                    title: 'Apakah Anda yakin?',
                    text: "Akan Mengupdate Stok Dari Member ini?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Update',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: '<?php echo site_url('postdata/admin_post/Produk/updateStokMember'); ?>',
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
function ubahWarna() {
    const selectipe = document.getElementById('stok_type');
    console.log(selectipe.value);
    const btnSubmit = document.getElementById('btnSubmit');
    if (selectipe.value === 'credit') {
        btnSubmit.classList.remove('btn-danger');
        btnSubmit.classList.remove('btn-primary');
        btnSubmit.classList.add('btn-success');
        btnSubmit.textContent = 'Tambah Stok';
    } else if (selectipe.value === 'debit') {
        btnSubmit.classList.add('btn-danger');
        btnSubmit.classList.remove('btn-success');
        btnSubmit.classList.remove('btn-primary');
        btnSubmit.textContent = 'Kurangi Stok';
    }
}
    </script>
<?php } ?>