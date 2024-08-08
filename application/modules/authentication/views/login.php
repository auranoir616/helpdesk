<div class="login-img ">
    <div class="page" style="background-color: #75b8fa !important; background-size:cover">
        <div class="">

            <!-- <div class="col col-login mx-auto mt-7">
                disini letak logo awal
            </div> -->

            <div class="container-login100">
                <div class="wrap-login100 p-6">
                    <?php echo form_open('', array('id' => 'login-form', 'class' => 'login100-form validate-form')); ?>
                    <div class="pb-2">
                        <div class="text-center pb-3">
                            <a href="<?php echo site_url('') ?>" title="Gleh Store">
                                <img src="<?php echo base_url('assets/logo.png') ?>" class="header-brand-img" alt="Gleh Store" style="max-width: 180px;">
                            </a>
                        </div>
                        <span class="login100-form-title" style="text-align: left;padding-bottom:0px">
                            Login
                        </span>

                    </div>
                    <hr style="border-top: 1px solid #000;margin:0px">
                    <div class="panel panel-primary">
                        <div class="panel-body tabs-menu-body p-0 pt-5">
                            <div class="tab-content">
                                <div class="tab-pane active">
                                    <label for="">Username</label>
                                    <div class="wrap-input100 validate-input input-group">
                                        <a href="javascript:void(0)" class="input-group-text bg-white text-muted">
                                            <i class="zmdi zmdi-account text-muted" aria-hidden="true"></i>
                                        </a>
                                        <input name="authentication_id" class="input100 border-start-0 form-control ms-0" type="text" placeholder="Username" autocomplete="off">
                                    </div>
                                    <div class="mt-5">
                                        <label for="">Password</label>
                                        <div class="wrap-input100 validate-input input-group" id="Password-toggle">
                                            <a href="javascript:void(0)" class="input-group-text bg-white text-muted">
                                                <i class="zmdi zmdi-eye text-muted" aria-hidden="true"></i>
                                            </a>
                                            <input name="authentication_password" class="input100 border-start-0 form-control ms-0" type="password" placeholder="Password" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="text-end pt-4">
                                        <!-- <p class="mb-0"><a href="forgot-password.html" class="text-primary ms-1">Forgot Password?</a></p> -->
                                        <p class="mb-0"><a href="<?php echo site_url('signup') ?>" class="text-primary ms-1">Register</a></p>
                                    </div>
                                    <div class="container-login100-form-btn">
                                        <button type="submit" class="login100-form-btn" style="background: #5298de;border:1px solid #f9ca24;color:#fcfafa;font-weight: bold;" id="btn01">LOGIN</button>
                                        <button type="submit" class="login100-form-btn" style="background: #5298de;border:1px solid #f9ca24;color:#fcfafa;font-weight: bold;" disabled id="btn02">MEMPROSES</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function($) {
                            $('#btn02').hide();
                            $('#login-form').submit(function(event) {
                                event.preventDefault();
                                $('#btn01').hide();
                                $('#btn02').show();

                                $('#login-form').loading();

                                $.ajax({
                                        url: '<?php echo site_url('postdata/public_post/auth/do_login') ?>',
                                        type: 'post',
                                        dataType: 'json',
                                        data: $('#login-form').serialize(),
                                    })
                                    .done(function(data) {

                                        updateCSRF(data.csrf_data);
                                        // grecaptcha.reset();
                                        if (data.status) {
                                            location.href = '<?php echo site_url('admin/admin-dashboard2') ?>';
                                        } else {
                                            swal({
                                                html: true,
                                                title: data.heading,
                                                text: data.message,
                                                type: data.type
                                            })
                                        }

                                    })

                                    .always(function() {
                                        $('#login-form').loading('stop');
                                    });
                                $('#btn01').show();
                                $('#btn02').hide();


                            });

                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>