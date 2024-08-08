<?php
$id = $this->input->get('id');

$this->db->where('id', $id);
$cekuser = $this->db->get('tb_users');
if ($cekuser->num_rows() == 0) {
?>
    <center>DATA USER TIDAK VALID</center>
<?php } else { ?>
    <?php $userdata = $cekuser->row(); ?>
    <?php echo form_open_multipart('', array('id' => 'change_userdata')); ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <div class="row">
    <div class="col-sm-6">

    <div class="form-group">
        <label for="exampleInputEmail1">Nama Lengkap</label>
        <input disabled  type="text" name="user_fullname" class="form-control" placeholder="Nama Lengkap" value="<?php echo $userdata->user_fullname; ?>" autocomplete="off">
    </div>
    </div>
    <div class="col-sm-6">
            <div class="form-group">
                <label for="exampleInputEmail1">Username</label>
                <input disabled  type="text" name="username" class="form-control" placeholder="username" value="<?php echo $userdata->username; ?>" autocomplete="off">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="exampleInputEmail1">Email</label>
                <input disabled  type="text" name="email" class="form-control" placeholder="Alamat Email" value="<?php echo $userdata->email; ?>" autocomplete="off">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="exampleInputEmail1">Nomor WhatsApp</label>
                <input disabled type="text" name="user_phone" class="form-control" placeholder="Nomor WhatsApp" value="<?php echo $userdata->user_phone; ?>" autocomplete="off">
            </div>
        </div>
    </div>
    <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  col-xl-12">
                        <div class="form-group">
                            <label for="">Foto Profile</label>
                            <!-- <input type="file" class="form-control" name="user_picture" id="imgcoverktp" onchange="getimgprofile(this)">
                            <small class="text-danger">Pilih Gambar Untuk Melihat Preview</small><br> -->
                            <div style="display: flex; justify-content: center; align-items: center;margin-top: 10px;">
                                <img id="gambarprofile" src=<?php echo '/assets/upload/' . $userdata->user_picture; ?> style="width:200px; height:150px;margin-top: 10px;border: 1px solid #ddd">
                            </div>
                            <script type="text/javascript">
                                function getimgprofile(input) {
                                    if (input.files && input.files[0]) {
                                        var reader = new FileReader();
                                        reader.onload = function(e) {
                                            $('#gambarprofile')
                                                .attr('src', e.target.result);
                                        };

                                        reader.readAsDataURL(input.files[0]);
                                    }
                                }
                            </script>
                        </div>
                    </div>

                </div>
    <!-- <div class="form-group">
        <button class="btn btn-block btn-primary">Update Data Member</button>
    </div> -->
    <?php echo form_close(); ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#change_userdata').submit(function(event) {
                event.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                        url: '<?php echo site_url('postdata/admin_post/Helpdesk_admin/updatedatamember') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: formData,
                        processData: false,
                        contentType: false
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
    <hr>
    <!-- <h4>Update Password</h4>
    <hr style="border-bottom:1px solid #ccc">
    <?php echo form_open('', array('id' => 'change_password')); ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <div class="form-group">
        <label for="exampleInputEmail1">New Password</label>
        <input type="text" name="password" class="form-control" placeholder="Password" autocomplete="off">
    </div>
    <div class="form-group">
        <button class="btn btn-block btn-primary">Update Password</button>
    </div>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#change_password').submit(function(event) {
                event.preventDefault();

                $.ajax({
                        url: '<?php echo site_url('postdata/admin_post/Helpdesk_admin/updatepasswordmember') ?>',
                        type: 'post',
                        dataType: 'json',
                        data: $('#change_password').serialize(),
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
    </script> -->
<?php } ?>