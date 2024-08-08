<div class="alert alert-warning" role="alert">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
    </svg>
    Disaat user Diupgrade Tidak Bisa di Kembalikan Semula
</div>
<?php echo form_open('', array('id' => 'upgrade-member')); ?>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-6">
        <div class="form-group">
            <label for="">Upgrade Member</label>
            <select name="user" id="user" class="form-control" onchange="listtype()">
                <option disabled selected>Pilih Member</option>
                <?php
                $this->db->where_in('user_type', ['agen', 'reseller']);
                $getuser = $this->db->get('tb_users');
                foreach ($getuser->result() as $user) {
                ?>
                    <option value="<?php echo $user->user_code ?>"><?php echo $user->username . ' || ' . $user->user_fullname ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-6">
        <div class="form-group">
            <label for="">Upgrade Menjadi</label>
            <select name="type" id="type" class="form-control">
                <option disabled selected>Pilih Status</option>
            </select>
        </div>
    </div>
</div>
<div class="form-group">
    <button class="btn btn-block btn-primary">Upgrade Member</button>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
    function listtype() {
        let user = $('#user').val();
        // let type = $('#type').val();

        $.ajax({
            url: '<?php echo site_url('getdata/public_get/getdatas/getuser') ?>',
            type: 'get',
            dataType: 'json',
            data: {
                code: user,
            }
        }).done(function(data) {
            $('#type').empty();
            $('#type').append('<option disabled selected>Pilih Status</option>');
            if (data.result.user_type == 'agen') {
                $('#type').append('<option value="distributor">Distributor</option>');
            } else if (data.result.user_type == 'reseller') {
                $('#type').append('<option value="distributor">Distributor</option>');
                $('#type').append('<option value="agen">Agen</option>');
            }
            // $.each(data.result, function(index, val) {
            //     $('#type').append('<option value="' + val.barang_code + '">' + val.barang_nama + '</option>');
            // })

        });
    }

    $(document).ready(function() {


        $('#upgrade-member').submit(function(event) {
            event.preventDefault();

            $.ajax({
                    url: '<?php echo site_url('postdata/admin_post/userlist/upgradeMember') ?>',
                    type: 'post',
                    dataType: 'json',
                    data: $('#upgrade-member').serialize(),
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