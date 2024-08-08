<?php echo form_open_multipart('', array('id' => 'withdrawal-bonus', 'autocomplete' => "off")); ?>
<div class="form-group">
    <label for="jumlah">Jumlah Withdrawal</label>
    <input placeholder="" type="number" name="wd_total" class="form-control">
    <small> 
        <b class="text-danger">*</b>Withdrawal akan dikenakan biaya Rp. 10.000 
    </small> <br>
    <small>
        <b class="text-danger">*</b>Minimal Withdrawal adalah Rp. 50.000
    </small>

</div>
<center class="mb-3">
    <p class="h6">Rekening Tujuan</p>
</center>
<?php
$this->db->where('id', userid());
$datauser = $this->db->get('tb_users')->row();
?>
<div class="form-group">
    <label for="">Rekening Atasnama</label>
    <input disabled type="text" class="form-control" value="<?php echo $datauser->user_bank_account; ?>" placeholder="Rekening Atasnama" name="confirm_account" autocomplete="off">
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="">Nama Bank</label>
            <input disabled type="text" value="<?php echo $datauser->user_bank_name; ?>" class="form-control" placeholder="Nama Bank" name="confirm_bank" autocomplete="off">
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="">Nomor Rekening</label>
            <input disabled type="text" value="<?php echo $datauser->user_bank_number; ?>" class="form-control" placeholder="Nomor Rekening" name="confirm_number" autocomplete="off">
        </div>
    </div>
</div>
<div class="form-group">
                    <label>Konfirmasi Password</label>
                    <div class="input-group" id="show_pasword">
                        <input type="password" class="form-control" placeholder="Konfirmasi Password" aria-label="Konfirmasi Password" aria-describedby="basic-addon2" autocomplete="off" name="wd_password" style="border:1px solid #6c5ffc!important">
                        <div class="input-group-append">
                            <button style="border-top-left-radius: 0;border-bottom-left-radius: 0;" class="btn btn-outline-primary" type="button" id=""><i class="fa fa-eye-slash" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                <script>
                    $(document).ready(function() {
                        $("#show_pasword button").on('click', function(event) {
                            event.preventDefault();
                            if ($('#show_pasword input').attr("type") == "text") {
                                $('#show_pasword input').attr('type', 'password');
                                $('#show_pasword i').addClass("fa-eye-slash");
                                $('#show_pasword i').removeClass("fa-eye");
                            } else if ($('#show_pasword input').attr("type") == "password") {
                                $('#show_pasword input').attr('type', 'text');
                                $('#show_pasword i').removeClass("fa-eye-slash");
                                $('#show_pasword i').addClass("fa-eye");
                            }
                        });
                    });
                </script>

<div class="form-group">
    <button type="submit" class="btn btn-md btn-primary text-white btn-block m-1">Withdraw</button>
</div>
<?php echo form_close(); ?>
<script>
    $(document).ready(function() {
        $('#withdrawal-bonus').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            console.log(...formData.entries());
            $.ajax({
                    url: '<?php echo site_url('postdata/user_post/Withdrawal/requestwithdrawal') ?>',
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