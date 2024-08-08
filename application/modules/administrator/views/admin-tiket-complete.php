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
                    <button class="btn btn-primary" type="submit" id="button-addon2" style="border:2px solid #2949ef"><i class="fa fa-search" aria-hidden="true"></i> CARI DATA</button>
                </div>
            </div>
        </form>
        <div class="table-responsive">

  <table class="table table-hover">
    <tr>
        <th>No.</th>
        <th>Judul</th>
        <th>tipe</th>
        <th>Kategori</th>
        <th>Petugas</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    <?php 
 $limit       = 10;
 $offset      = ($this->input->get('page')) ? $this->input->get('page') : 0;
 $no          = $offset + 1;
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

  $getdata = $this->db->get('tb_tiket', $limit, $offset);
  
  $this->db->where('tiket_status', 'complete');
  $Gettotal = $this->db->get('tb_tiket')->num_rows();
  foreach ($getdata->result() as $show) {
  $this->db->where('tipe_id', $show->tiket_tipe);
  $gettipe = $this->db->get('tb_tipe')->row();
  $this->db->where('kategori_id', $show->tiket_kategori);
  $getkategori = $this->db->get('tb_kategori')->row();

  $this->db->where('id', $show->tiket_petugas);
  $getpetugas = $this->db->get('tb_users')->row();
  $petugas = isset($getpetugas->username) ? $getpetugas->username : '';

  
  
  $this->db->where('id', $show->tiket_userid);
  $getuser = $this->db->get('tb_users')->row();
  ?>
        <tr onclick="window.location.href='<?php echo base_url('admin/admin-tiket-detail?id='.$show->tiket_id); ?>'" style="cursor:pointer;">
        <td><?php echo $no++ ?></td>
        <td><?php echo $show->tiket_judul ?> <br>
        <small>By <?php echo $getuser->username; ?></small>  
      </td>
      <td>
    <span class="badge 
        <?php 
        if ($gettipe->tipe_nama == 'Non-Urgent') { 
            echo 'bg-secondary'; 
        } elseif ($gettipe->tipe_nama == 'Rendah') { 
            echo 'bg-success'; 
        } elseif ($gettipe->tipe_nama == 'Moderat') { 
            echo 'bg-warning';
        } elseif ($gettipe->tipe_nama == 'Tinggi') { 
            echo 'bg-danger'; 
        } elseif ($gettipe->tipe_nama == 'Segera') { 
            echo 'bg-primary';
        } else { 
            echo 'bg-info'; 
        } 
        ?>">
        <?php echo htmlspecialchars($gettipe->tipe_nama); ?>
    </span>
</td>        <td><?php echo $getkategori->kategori_nama ?></td>
        <td><?php echo $petugas ?></td>
        <td><span class="badge <?php if ($show->tiket_status == 'complete') { echo 'bg-success'; } elseif($show->tiket_status == 'pending') { echo 'bg-warning'; }else{ echo 'bg-info';} ?>" ><?php echo $show->tiket_status ?></span></td>
        <td>
          <button onclick="hapustiket('<?php echo $show->tiket_id ?>', event)" class="btn btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i> Hapus Tiket </button>
        </td>
    </tr>
    <?php } ?>
</table>
  </div>
  </div>
</div>

<script>
        function hapustiket(tiket_id, event) {
            event.stopPropagation();
            Swal.fire({
                allowOutsideClick: false,
                title: 'Apakah Anda Yakin?',
                text: "Akan Menghapus Tiket ini? ",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'YA hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.value) {

                    $.ajax({
                            url: '<?php echo site_url('postdata/admin_post/Helpdesk_admin/HapusTiketAdmin') ?>',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                tiket_id: tiket_id,
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
