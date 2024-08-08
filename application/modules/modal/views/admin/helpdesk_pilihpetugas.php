<?php 
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$this->db->where('user_type', 'teknisi');
$getteknisi = $this->db->get('tb_users');
$teknisi = $getteknisi->result();

?>

<?php echo form_open('', array('id' => 'pilih-petugas-form')); ?>

<div class="form-group">
    <label for="pilih_petugas">Pilih Petugas</label>
    <select class="form-control" name="pilih_petugas" id="pilih_petugas">
        <option value="" selected disabled>---</option>
        <?php foreach($teknisi as $teknisi): ?>
            <option value="<?= $teknisi->id ?>"><?= $teknisi->username ?>( <?= $teknisi->user_fullname ?> )</option>
        <?php endforeach; ?>
    </select>
</div>
<input type="text" name="idtiket" value="<?php echo $id ?>" hidden id="id ">
<div id="teknisi-info" class="mt-3">
    <!-- Informasi teknisi akan ditampilkan di sini -->
</div>

<button type="submit" class="btn btn-primary mt-3 btn-block">Pilih</button>

<?php echo form_close(); ?>


<script>
$(document).ready(function() {
    $('#pilih_petugas').change(function() {
        var userId = $(this).val();

        if (userId) {
            // Ambil data pengguna berdasarkan ID
            $.ajax({
                url: '<?php echo base_url("getdata/admin_get/Getother/getdatapetugas"); ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    id: userId
                },
                
            }).done(function(data) {
                    if (data) {
                        // Tampilkan informasi pengguna di div
                        $('#teknisi-info').html(`
                            <h5>Detail Petugas</h5>
                            <p><strong>Username:</strong> ${data.username}</p>
                            <p><strong>Nama Lengkap:</strong> ${data.user_fullname}</p>
                            <p><strong>Email:</strong> ${data.email}</p>
                            <p><strong>No. Telepon:</strong> ${data.user_phone}</p>
                        `);
                    } else {
                        $('#teknisi-info').html('<p>Tidak ada data pengguna yang ditemukan.</p>');
                    }
                });
        } else {
            $('#teknisi-info').empty(); // Hapus informasi jika tidak ada yang dipilih
        }
    });
});
$(document).ready(function() {
        $('#pilih-petugas-form').submit(function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: '<?php echo site_url('postdata/admin_post/Helpdesk_admin/pilihPetugas') ?>',
                type: 'post',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function(data) {
                
                if (data.status) {
                    Swal.fire(data.heading, data.message, data.type).then(function() {
                        window.location.reload();

                    })

                } else {
                    Swal.fire(data.heading, data.message, data.type).then(function() {
                        window.location.reload();
                    });
                }
            });
        });
    });


</script>
