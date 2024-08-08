<?php echo form_open_multipart('', array('id' => 'Add_Produk', 'autocomplete' => "off")); ?>
<div class="form-group">
    <label for="produk_nama">Nama Produk</label>
    <input placeholder="Nama Produk" type="text" name="produk_nama" class="form-control">
</div>
<div class="form-group">
    <label for="produk_stok">Stok Produk</label>
    <input placeholder="Stok Produk" type="text" name="produk_stok" class="form-control">
</div>
<div class="form-group">
    <label>Upload Gambar</label>
    <div class="custom-file">
        <input name="produk_image" type="file" class="custom-file-input form-control" id="imgcover" onchange="getimggg(this)">
    </div>
    <small class="text-danger">Pilih Gambar Untuk Melihat Preview</small><br>
    <div style="display: flex; justify-content: center; align-items: center;margin-top: 10px;">
        <img id="gambarcover" style="max-width:150px; max-height:150px;margin-top: 10px;border: 1px solid #ddd">
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
    <button type="submit" class="btn btn-md btn-primary text-white btn-block m-1">Submit</button>
</div>
<?php echo form_close(); ?>
<script>
    $(document).ready(function() {
        $('#Add_Produk').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            console.log(...formData.entries());
            $.ajax({
                    url: '<?php echo site_url('postdata/admin_post/Produk/AddProduk') ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                })
                .done(function(data) {
                    console.log(data)
                    updateCSRF(data.csrf_data);
                    Swal.fire(
                        data.heading,
                        data.message,
                        data.type
                    ).then(function() {
                        if (data.status) {
                            location.reload();
                        }
                    });

                })
        });
    });
</script>