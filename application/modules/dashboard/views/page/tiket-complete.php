<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="card-title">Tiket Selesai</h5>
    </div>
    <div class="card-body">
        <form action="<?php echo site_url('tiket') ?>" method="GET">
            <div class="form-group">
                <label for="">Pecarian</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Subjek Tiket" aria-label="Subjek Tiket" aria-describedby="button-addon2" autocomplete="off" name="search" style="border:2px solid #2949ef">
                    <button class="btn btn-primary" type="submit" id="button-addon2" style="border:2px solid #2949ef">CARI DATA</button>
                </div>
            </div>
        </form>
        <div class="table-responsive">

        <table class="table table-hover">
            <tr>
                <th>No.</th>
                <th>Judul</th>
                <th>tipe</th>
                <th>kategori</th>
                <th>Deskripsi</th>
                <th>Status</th>
            </tr>
            <?php
            $limit       = 10;
            $offset      = ($this->input->get('page')) ? $this->input->get('page') : 0;
            $no          = $offset + 1;
            $usertype = userdata()->user_type;

            $search = $this->input->get('search');
            if ($search) {
                $this->db->like('tiket_judul', $search);
                $tiket_ids = $this->db->select('tiket_id')->from('tb_tiket')->get()->result_array();
                $tiket_ids = array_column($tiket_ids, 'tiket_id');
                if (!empty($tiket_ids)) {
                    $this->db->where_in('tiket_id', $tiket_ids);
                } else {
                    $this->db->where('tiket_id', 0); // No matching users
                }
            }
            $this->db->order_by('tiket_date', 'DESC');
            $this->db->where('tiket_status', 'complete');
            if($usertype == 'user'){
                $this->db->where('tiket_userid', userid());
            }else{
                $this->db->where('tiket_petugas', userid());
            }
            $getdata = $this->db->get('tb_tiket', $limit, $offset);
            $this->db->where('tiket_status', 'complete');
            if($usertype == 'user'){
                $this->db->where('tiket_userid', userid());
            }else{
                $this->db->where('tiket_petugas', userid());
            }
            $Gettotal = $this->db->get('tb_tiket')->num_rows();
            foreach ($getdata->result() as $show) {
                $this->db->where('tipe_id', $show->tiket_tipe);
                $gettipe = $this->db->get('tb_tipe')->row();
                $this->db->where('kategori_id', $show->tiket_kategori);
                $getkategori = $this->db->get('tb_kategori')->row();
            ?>
                <tr onclick="window.location.href='<?php echo base_url('detail-tiket?id=' . $show->tiket_id); ?>'" style="cursor:pointer;">
                    <td><?php echo $no++ ?></td>
                    <td><?php echo $show->tiket_judul ?></td>
                    <td>
                        <span class="badge 
        <?php
                if ($gettipe->tipe_nama == 'Non-Urgent') {
                    echo 'bg-secondary'; // Warna untuk Non-Urgent
                } elseif ($gettipe->tipe_nama == 'Rendah') {
                    echo 'bg-success'; // Warna untuk Rendah
                } elseif ($gettipe->tipe_nama == 'Moderat') {
                    echo 'bg-warning'; // Warna untuk Moderat
                } elseif ($gettipe->tipe_nama == 'Tinggi') {
                    echo 'bg-danger'; // Warna untuk Tinggi
                } elseif ($gettipe->tipe_nama == 'Segera') {
                    echo 'bg-primary'; // Warna untuk Segera
                } else {
                    echo 'bg-info'; // Warna default
                }
        ?>">
                            <?php echo htmlspecialchars($gettipe->tipe_nama); ?>
                        </span>
                    </td>
                    <td><?php echo $getkategori->kategori_nama ?></td>
                    <td><?php echo substr(strip_tags($show->tiket_desc), 0, 50); ?></td>
                    <td><span class="badge <?php if ($show->tiket_status == 'complete') {
                                                echo 'bg-success';
                                            } elseif ($show->tiket_status == 'pending') {
                                                echo 'bg-warning';
                                            } else {
                                                echo 'bg-info';
                                            } ?>"><?php echo $show->tiket_status ?></span></td>
                    <!-- <td>

                        <button onclick="selesaikantiket('<?php echo $show->tiket_id ?>', event)" class="btn btn-sm btn-success">selesaikan Tiket</button>
                    </td> -->
                </tr>
            <?php } ?>
        </table>
    </div>
    </div>
</div>

<!-- <script>
    // function hapustiket(idtiket, event) {
    //     event.stopPropagation(); 
    //     Swal.fire({
    //         allowOutsideClick: false,
    //         title: 'Apakah Anda Yakin?',
    //         text: "Akan Menghapus Tiket ini? ",
    //         type: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'YA hapus',
    //         cancelButtonText: 'Batal',
    //     }).then((result) => {
    //         if (result.value) {

    //             $.ajax({
    //                     url: '<?php echo site_url('postdata/user_post/Helpdesk_tiket/HapusTiket') ?>',
    //                     type: 'post',
    //                     dataType: 'json',
    //                     data: {
    //                         idtiket: idtiket,
    //                         <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
    //                     }
    //                 })

    //                 .done(function(data) {

    //                     updateCSRF(data.csrf_data);
    //                     Swal.fire(
    //                         data.heading,
    //                         data.message,
    //                         data.type
    //                     ).then(function() {
    //                         if (data.status) {
    //                             location.reload();
    //                         }
    //                     });

    //                 })
    //         }
    //     });
    // }
    function selesaikantiket(idtiket, event) {
        event.stopPropagation();
        Swal.fire({
            allowOutsideClick: false,
            title: 'Apakah Anda Yakin?',
            text: "Akan menyelesaikan Tiket ini? ",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'YA hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.value) {

                $.ajax({
                        url: '<?php echo site_url('postdata/user_post/Helpdesk_tiket/selesaikanTiket') ?>',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            idtiket: idtiket,
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
</script> -->