<style>
    .form-check {
        position: relative;
        display: block;
        padding-left: 0;
    }

    .form-switch {
        padding-left: 2.5em;
    }
</style>
<?php echo form_open_multipart('', array('id' => 'rewardForm')); ?>
<div class="form-group">
    <label for="reward_nama">Nama Reward</label>
    <input type="text" placeholder="Nama Reward" name="reward_nama" class="form-control">
</div>
<div class="form-group">
    <label for="reward_poin">Poin Reward</label>
    <input type="text" placeholder="Poin Reward" name="reward_poin" class="form-control">
</div>

<!-- <div class="form-group"> -->
<div class="form-check form-switch" style="padding-left: rem;">
    <input name="switch" value="1" class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>
    <label class="form-check-label" for="flexSwitchCheckChecked">Tidak Untuk Reseller</label>
</div>
<!-- </div> -->

<div class="form-group">
    <label>Upload Gambar</label>
    <div class="custom-file">
        <input name="reward_picture" type="file" class="custom-file-input form-control" id="imgcover" onchange="getimggg(this)">
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
        $('#rewardForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                    url: '<?php echo site_url('postdata/admin_post/Reward/AddReward') ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                })
                .done(function(data) {
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