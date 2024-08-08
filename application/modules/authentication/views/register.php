<div class="page" style="background-color: #75b8fa !important; background-size:cover">
<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Registrasi User</h3>
                        <hr>
                        <?php echo form_open_multipart('', array('id' => 'register-form')); ?>
                            <div class="mb-3">
                                <label for="user_username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="user_username" name="user_username" autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="reg_nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="reg_nama" name="reg_nama" autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="reg_hp" class="form-label">No. HP</label>
                                <input type="text" class="form-control" id="reg_hp" name="reg_hp"  autocomplete="off">
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="reg_tipe" class="form-label">Tipe Member</label>
                                    <select class="form-control" id="reg_tipe" name="reg_tipe">
                                        <option selected value="">Pilih tipe Member</option>
                                        <option value="teknisi">Teknisi</option>
                                        <option value="user">User</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="user_email" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="user_email" name="user_email" ">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="user_ktp" class="form-label">Foto KTP</label>
                                <input type="file" class="form-control" id="user_ktp" name="user_ktp" onchange="previewImage(this)">
                                <small class="text-danger">Pilih Gambar Untuk Melihat Preview</small><br>
                                <div style="display: flex; justify-content: center; align-items: center; margin-top: 10px;">
                                    <img id="imagePreview" style="width: 200px; height: 150px; margin-top: 10px; border: 1px solid #ddd;" src="<?php echo site_url('/assets/user-black.png') ?>" alt="Preview">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="reg_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="reg_password" name="reg_password" >
                            </div>
                            <p class="text-center">Already have an account? <a href="<?php echo site_url('login') ?>" title="Login Now">Login Now</a></p>
                            <div class="text-center">
                                <button type="submit" class="btn btn-block" id="btnRegister" style="background: #5298de; border: 1px solid #f9ca24; color: #fcfafa; font-weight: bold;">REGISTER</button>
                                <button type="button" class="btn btn-block" id="btnProcessing" disabled style="background: #5298de; border: 1px solid #f9ca24; color: #fcfafa; font-weight: bold; display: none;">MEMPROSES</button>
                            </div>
                        <?php echo form_hidden('user_upline', $upline); ?>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function previewImage(input) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }

    $(document).ready(function() {
        $('#btnProcessing').hide();
        $('#register-form').submit(function(event) {
            event.preventDefault();
            $('#btnRegister').hide();
            $('#btnProcessing').show();
            var formData = new FormData(this);
            $.ajax({
                url: '<?php echo site_url('postdata/public_post/auth/do_register') ?>',
                type: 'post',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function(data) {
                if (data.status) {
                    Swal.fire(data.heading, data.message, data.type).then(function() {
                        window.location.href = '<?php echo site_url('login') ?>';
                    })

                } else {
                    Swal.fire(data.heading, data.message, data.type).then(function() {
                        window.location.reload();
                    });
                    $('#btnProcessing').hide();
                    $('#btnRegister').show();
                }
            });
        });
    });
</script>
