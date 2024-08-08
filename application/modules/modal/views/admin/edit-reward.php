<?php
$code = $this->input->get('code');

$this->db->where('reward_code', $code);
$cekreward = $this->db->get('tb_reward');
if ($cekreward->num_rows() == 0) {
?>
    <center>DATA REWARD TIDAK VALID</center>
<?php } else { ?>
    <?php $rewarddata = $cekreward->row(); ?>
    <?php echo form_open_multipart('', array('id' => 'update_reward')); ?>
    <style>
        .form-check {
            position: relative;
            display: block;
            padding-left: 0;
        }

        .form-switch {
            padding-left: 3.5em;
        }
    </style>
    <input type="hidden" name="code" value="<?php echo $code; ?>">
    <div class="form-group">
        <label for="exampleInputEmail1">Nama Reward</label>
        <input type="text" name="reward_nama" class="form-control" value="<?php echo $rewarddata->reward_nama; ?>" autocomplete="off">
    </div>
    <div class="row">
        <div class="form-group">
            <label for="exampleInputEmail1">Point Reward</label>
            <input type="text" name="reward_poin" class="form-control" value="<?php echo  $rewarddata->reward_poin; ?>" autocomplete="off">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Status Reward</label>
            <select class="form-control" name="reward_status" id="reward_status">
                <option value="Aktif" <?php if ($rewarddata->reward_status == "Aktif") {
                                            echo "selected";
                                        } ?>>Aktif</option>
                <option value="Tidak Aktif" <?php if ($rewarddata->reward_status == "Tidak Aktif") {
                                                echo "selected";
                                            } ?>>Tidak Aktif</option>
            </select>
           
        </div>


        <div class="form-check form-switch" style="padding-left: rem;">
            <input name="switch" value="1" class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" <?php echo ($rewarddata->reward_forall == 'yes') ? 'checked' : false ?>>
            <label class="form-check-label" for="flexSwitchCheckChecked">Tidak Untuk Reseller</label>
        </div>

        <div class="form-group">
            <label>Upload Gambar</label>
            <div class="custom-file">
                <input name="reward_picture" type="file" class="custom-file-input form-control" id="imgcover" onchange="getimggg(this)">
            </div>
            <small class="text-danger">Pilih Gambar Untuk Melihat Preview</small><br>
            <div style="display: flex; justify-content: center; align-items: center;margin-top: 10px;">
                <img id="gambarcover" src="<?php echo base_url('assets/reward/' . $rewarddata->reward_picture); ?>" style="max-width:150px; max-height:150px;margin-top: 10px;border: 1px solid #ddd">
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
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-block btn-primary">Update Data Reward</button>
    </div>
    <?php echo form_close(); ?>
    <script>
        $(document).ready(function() {
            $('#update_reward').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                        url: '<?php echo site_url('postdata/admin_post/Reward/UpdateReward') ?>',
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
<?php } ?>