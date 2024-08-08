<?php 
if(userdata()->user_type == 'user'){
    $this->db->where('tiket_userid', userid());
    $this->db->where('tiket_status', 'pending');
    $pending = $this->db->get('tb_tiket')->num_rows();
    
    $this->db->where('tiket_userid', userid());
    $this->db->where('tiket_status', 'process');
    $process = $this->db->get('tb_tiket')->num_rows();
    
    $this->db->where('tiket_userid', userid());
    $this->db->where('tiket_status', 'complete');
    $complete = $this->db->get('tb_tiket')->num_rows();
    $hidden = 'block';
}else{
    $this->db->where('tiket_petugas', userid());
    $this->db->where('tiket_status', 'pending');
    $pending = $this->db->get('tb_tiket')->num_rows();
    
    $this->db->where('tiket_petugas', userid());
    $this->db->where('tiket_status', 'process');
    $process = $this->db->get('tb_tiket')->num_rows();
    
    $this->db->where('tiket_petugas', userid());
    $this->db->where('tiket_status', 'complete');
    $complete = $this->db->get('tb_tiket')->num_rows();
    $hidden = 'none';

}
    ?>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <a href="<?php echo site_url('/tiket-pending') ?>">
            <div class="card  bg-warning img-card box-success-shadow">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="text-white">
                        <h2 class="mb-4 number-font text-black h6">Tiket Pending</h2>
                        <span class="number-font text-black display-6"><?php echo $pending ?></span>
                        </div>
                        <div class="ms-auto"> <i class="ti-time text-white fs-50 me-2 mt-2"></i> </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <?php

    ?>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <a href="<?php echo site_url('/tiket-process') ?>">

            <div class="card  bg-secondary img-card box-secondary-shadow">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="text-white">
                            <h2 class="mb-4 number-font text-black h6">Tiket Terproses</h2>
                            <span class="number-font text-black display-6"><?php echo $process ?></span>

                        </div>
                        <div class="ms-auto"> <i class="ti-reload text-white fs-50 me-2 mt-2"></i> </div>
                    </div>
                </div>
            </div>
        </a>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <a href="<?php echo site_url('/tiket-complete') ?>">
                <div class="card  bg-success img-card box-success-shadow">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="text-white">
                            <h2 class="mb-4 number-font text-black h6">Tiket Selesai</h2>
                            <span class="number-font text-black display-6"><?php echo $complete ?></span>

                            </div>
                            <div class="ms-auto"> <i class="ti-check-box text-white fs-50 me-2 mt-2"></i> </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

</div>


<div class="card">
<div class="card-header flex-row justify-content-between">
<h3 class="card-title">Notifikasi</h3>
<a style="display: <?php echo $hidden; ?>;" href="<?php echo base_url('new-tiket') ?>" class=" btn btn-primary">
<i class="fa fa-plus-square" aria-hidden="true"></i>
Tambah Tiket</a>

    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?php

            $limit       = 10;
            $offset      = ($this->input->get('page')) ? $this->input->get('page') : 0;
            $no          = $offset + 1;
            ?>
            <table class="table border text-nowrap text-md-nowrap table-striped mb-0">
                <thead>
                    <tr>
                        <td width="5%">#</td>
                        <td>Deskripsi</td>
                        <td>Waktu</td>
                        <td>Aksi</td>
                    </tr>
                </thead>
                <tbody>
                    <?php  
                    $this->db->where('notif_useridto', userid());
                    $this->db->where('notif_status', 'unread');
                    $this->db->order_by('notif_date', 'desc');
                    $getNotif = $this->db->get('tb_notif', $limit, $offset)->result();
                    $this->db->where('notif_useridto', userid());
                    $this->db->where('notif_status', 'unread');
                    $this->db->order_by('notif_date', 'desc');
                    $getrows = $this->db->get('tb_notif')->num_rows();
                    foreach ($getNotif as $show) {
                        ?>
                        <tr onclick="window.location.href='<?php echo base_url('detail-tiket?id=' . $show->notif_tiketid) ?>'" style="cursor:pointer;">
                            <td><?php echo $no++ ?></td>
                            <td><?php echo $show->notif_desc ?></td>
                            <td><?php echo waktuYangLalu($show->notif_date) ?></td>
                            <td><a href="javascript:void(0)" onclick="bacanotif('<?php echo $show->notif_id ?>', event)"><i class="fa fa-trash" aria-hidden="true"></i>Hapus</a></td>
                   <?php } ?>
                </tbody>
            </table>

        </div>
        <?php echo $this->paginationmodel->paginate('dashboard', $getrows, $limit) ?>
    </div>
</div>
<script>
    function bacanotif(notif_id, event) {
        event.stopPropagation(); 
                    $.ajax({
                            url: '<?php echo site_url('postdata/user_post/Helpdesk_notif/BacaNotif') ?>',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                notif_id: notif_id,
                                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                            }
                        })
                        .done(function(data) {
                            updateCSRF(data.csrf_data);
                                if (data.status) {
                                   window.location.reload();
                                }
                        

                        })
                
            }


</script>


