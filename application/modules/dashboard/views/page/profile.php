<?php
$this->template->title->set('Settings');
?>

<div class="row">
<div class="col-sm-12 col-md-12 col-lg-6">
        <div class="card">
            <div class="card-header flex-row justify-content-between bg-primary text-white">
                <h3 class="card-title">Update Profile</h3>
            </div>
            <div class="card-body">
                <?php echo form_open_multipart('', 'id="updateprofile"');
                ?>
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" class="form-control" value="<?php echo $userdata->user_fullname ?>" name="user_fullname" autocomplete="off" placeholder="Nama Lengkap">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" value="<?php echo $userdata->username ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" name="email" value="<?php echo $userdata->email ?>">
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>No WhatsApp</label>
                            <input type="text" class="form-control" value="<?php echo $userdata->user_phone
                                                                            ?>" placeholder="No WhatsApp" name="user_phone">
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  col-xl-12">
                        <div class="form-group">
                            <label for="">Foto Profile</label>
                            <input type="file" class="form-control" name="user_picture" id="imgcoverktp" onchange="getimgprofile(this)">
                            <small class="text-danger">Pilih Gambar Untuk Melihat Preview</small><br>
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

                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <div class="input-group" id="show_hide_passwordddd">
                        <input type="password" class="form-control" placeholder="Konfirmasi Password" aria-label="Konfirmasi Password" aria-describedby="basic-addon2" autocomplete="off" name="user_pass" style="border:1px solid #6c5ffc!important">
                        <div class="input-group-append">
                            <button style="border-top-left-radius: 0;border-bottom-left-radius: 0;" class="btn btn-outline-primary" type="button" id=""><i class="fa fa-eye-slash" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                <script>
                    $(document).ready(function() {
                        $("#show_hide_passwordddd button").on('click', function(event) {
                            event.preventDefault();
                            if ($('#show_hide_passwordddd input').attr("type") == "text") {
                                $('#show_hide_passwordddd input').attr('type', 'password');
                                $('#show_hide_passwordddd i').addClass("fa-eye-slash");
                                $('#show_hide_passwordddd i').removeClass("fa-eye");
                            } else if ($('#show_hide_passwordddd input').attr("type") == "password") {
                                $('#show_hide_passwordddd input').attr('type', 'text');
                                $('#show_hide_passwordddd i').removeClass("fa-eye-slash");
                                $('#show_hide_passwordddd i').addClass("fa-eye");
                            }
                        });
                    });
                </script>
                <div class="form-group">
                    <button id='btn010' style="font-weight:bold;color:#fff" type="submit" class="btn btn-primary btn-block">UPDATE PROFILE</button>
                    <button id='btn020' style="font-weight:bold;color:#fff" type="button" class="btn btn-primary btn-block" disabled>PROSES UPDATE</button>
                </div>
                <?php echo form_close(); ?>
                <script>
                    $('#btn020').hide();
                    $('#updateprofile').submit(function(event) {
                        event.preventDefault();
                        $('#btn010').hide();
                        $('#btn020').show();
                        var formData = new FormData(this);
                        console.log(...formData.entries());
                        $.ajax({
                                url: '<?php echo site_url('postdata/user_post/Helpdesk_user/updateprofile') ?>',
                                type: 'POST',
                                dataType: 'json',
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
                                $('#btn010').show();
                                $('#btn020').hide();
                            })
                    });
                </script>
            </div>
        </div>
    </div>


    <div class="col-sm-12 col-md-6 col-lg-6">
        <div class="card">
            <div class="card-header flex-row justify-content-between bg-primary text-white">
                <h3 class="card-title">Update Password</h3>
            </div>
            <div class="card-body">
                <?php echo form_open('', 'id="updatepass"'); ?>
                <div class="form-group">
                    <label>Password Lama</label>
                    <div class="input-group" id="old_password">
                        <input type="password" class="form-control" placeholder="Password Lama" aria-label="Password Lama" aria-describedby="basic-addon2" autocomplete="off" name="current_password" style="border:1px solid #6c5ffc!important">
                        <div class="input-group-append">
                            <button style="border-top-left-radius: 0;border-bottom-left-radius: 0;" class="btn btn-outline-primary" type="button" id=""><i class="fa fa-eye-slash" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password Baru</label>
                    <div class="input-group" id="new_password">
                        <input type="password" class="form-control" placeholder="Password Baru" aria-label="Password Baru" aria-describedby="basic-addon2" autocomplete="off" name="new_password" style="border:1px solid #6c5ffc!important">
                        <div class="input-group-append">
                            <button style="border-top-left-radius: 0;border-bottom-left-radius: 0;" class="btn btn-outline-primary" type="button" id=""><i class="fa fa-eye-slash" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Ulangi Password Baru</label>
                    <div class="input-group" id="repeatnew_password">
                        <input type="password" class="form-control" placeholder="Ulangi Password Baru" aria-label="Ulangi Password Baru" aria-describedby="basic-addon2" autocomplete="off" name="confirm_password" style="border:1px solid #6c5ffc!important">
                        <div class="input-group-append">
                            <button style="border-top-left-radius: 0;border-bottom-left-radius: 0;" class="btn btn-outline-primary" type="button" id=""><i class="fa fa-eye-slash" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                <script>
                    $(document).ready(function() {
                        $("#old_password button").on('click', function(event) {
                            event.preventDefault();
                            if ($('#old_password input').attr("type") == "text") {
                                $('#old_password input').attr('type', 'password');
                                $('#old_password i').addClass("fa-eye-slash");
                                $('#old_password i').removeClass("fa-eye");
                            } else if ($('#old_password input').attr("type") == "password") {
                                $('#old_password input').attr('type', 'text');
                                $('#old_password i').removeClass("fa-eye-slash");
                                $('#old_password i').addClass("fa-eye");
                            }
                        });

                        $("#new_password button").on('click', function(event) {
                            event.preventDefault();
                            if ($('#new_password input').attr("type") == "text") {
                                $('#new_password input').attr('type', 'password');
                                $('#new_password i').addClass("fa-eye-slash");
                                $('#new_password i').removeClass("fa-eye");
                            } else if ($('#new_password input').attr("type") == "password") {
                                $('#new_password input').attr('type', 'text');
                                $('#new_password i').removeClass("fa-eye-slash");
                                $('#new_password i').addClass("fa-eye");
                            }
                        });

                        $("#repeatnew_password button").on('click', function(event) {
                            event.preventDefault();
                            if ($('#repeatnew_password input').attr("type") == "text") {
                                $('#repeatnew_password input').attr('type', 'password');
                                $('#repeatnew_password i').addClass("fa-eye-slash");
                                $('#repeatnew_password i').removeClass("fa-eye");
                            } else if ($('#repeatnew_password input').attr("type") == "password") {
                                $('#repeatnew_password input').attr('type', 'text');
                                $('#repeatnew_password i').removeClass("fa-eye-slash");
                                $('#repeatnew_password i').addClass("fa-eye");
                            }
                        });
                    });
                </script>
                <div class="form-group">
                    <button id='btn01' type="submit" style="font-weight:bold;color:#fff" class="btn btn-primary btn-block">UPDATE PASSWORD</button>
                    <button id='btn02' type="button" style="font-weight:bold;color:#fff" class="btn btn-primary btn-block" disabled>PROSES UPDATE</button>
                </div>
                <?php echo form_close(); ?>
            </div>
            <script>
                $('#btn02').hide();
                $('#updatepass').submit(function(event) {
                    event.preventDefault();
                    $('#btn01').hide();
                    $('#btn02').show();
                    $.ajax({
                            url: '<?php echo site_url('postdata/user_post/Helpdesk_user/change_user_password') ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: $('#updatepass').serialize(),
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
                            $('#btn01').show();
                            $('#btn02').hide();
                        })
                });
            </script>
        </div>
    </div>



</div>