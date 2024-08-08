<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">FAQ</h5>
                <a href="<?php echo site_url('admin/admin-add-faq') ?>" 
                   class="btn btn-sm btn-primary text-white mb-1" 
                   style="min-width: 75px;">
                   <i class="fa fa-plus-square" aria-hidden="true"></i>
                    Tambahkan FAQ
                </a>
            </div>
            <div class="card-body">
                <div class="accordion" id="accordionExample">
                    <?php
                    $this->db->where('faq_status', 'aktif');
                    $getFaq = $this->db->get('tb_faq');  
                    foreach ($getFaq->result() as $index => $faq) {
                    ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                <button class="accordion-button" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse<?php echo $index; ?>" 
                                        aria-expanded="false" 
                                        aria-controls="collapse<?php echo $index; ?>">
                                    <?php echo $faq->faq_question; ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $index; ?>" 
                                 class="accordion-collapse collapse" 
                                 aria-labelledby="heading<?php echo $index; ?>" 
                                 data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <?php echo $faq->faq_answer; ?>
                                    <hr>
                                    <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <button type="button" onclick="hapusfaq('<?php echo $faq->faq_id ?>', event)" class="btn btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i> Hapus</button>
                                    <a href="<?php echo site_url('admin/admin-update-faq?idfaq=' . $faq->faq_id) ?>" class="btn btn-sm btn-success"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
        function hapusfaq(faq_id, event) {
            event.stopPropagation();
            Swal.fire({
                allowOutsideClick: false,
                title: 'Apakah Anda Yakin?',
                text: "Akan Menghapus FAQ ini? ",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'YA hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.value) {

                    $.ajax({
                            url: '<?php echo site_url('postdata/admin_post/Helpdesk_admin/hapusfaq') ?>',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                faq_id: faq_id,
                                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                            }
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
                }
            });
        }
    </script>


