<?php
$code = $this->input->get('code');
$this->db->where('inv_code', $code);
$this->db->where('inv_status', 'process');
$this->db->where('inv_userid_from', userid());
$cekinv = $this->db->get('tb_invoice');
if ($cekinv->num_rows() == 0) {
?>
    <center>
        <h3>Pembelian Anda Masih Belum Diproses Distributor</h3>
    </center>
<?php } else {
?>

    <style>
        .file-input {
            position: relative;
            overflow: hidden;
            margin: 10px;
            width: 250px;
            /* Sesuaikan lebar sesuai kebutuhan */
            height: 100px;
            /* Sesuaikan tinggi sesuai kebutuhan */
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
            line-height: 50px;
            /* Sesuaikan dengan tinggi input untuk vertikal centering */
            cursor: pointer;
        }

        .file-input input[type="file"] {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
    </style>
    <h6>
        <b>Upload Bukti Terima barang</b>
    </h6>

    <?php echo form_open_multipart('', array('id' => 'konfirmterima', 'autocomplete' => "off")); ?>
    <div class="form-group">
        <div class="file-upload d-flex justify-content-center align-items-center mb-3">
            <div class="file-select file-input">
                <div class="file-select-button" id="fileName">Upload Tanda Terima</div>
                <div class="file-select-name" id="noFile"> <i class="ti-upload text-black fs-30 me-2 mt-2"></i>
             </div>
                <input type="file" name="inv_bukti" id="chooseFile" onchange="readURL(this)">
                <input type="hidden" name="code" id="code" value="<?php echo $code ?>">
            </div>
        </div>
        <center>

            <img id="imgggggg" style="max-width:150px; max-height:150px;margin-top: 10px;border: 1px solid #ddd">
        </center>
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
            $('#konfirmterima').on('submit', function(e) {
                e.preventDefault(); // Memastikan form tidak ter-submit secara default
                var formData = new FormData(this);

                $('#btn01confirmB').hide();
                $('#btn02confirmB').show();

                $.ajax({
                        url: '<?php echo site_url('postdata/user_post/invoice/konfirmterima') ?>',
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