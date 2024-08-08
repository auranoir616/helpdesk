<?php
$this->template->title->set('Daftar User / Pengguna');
$this->template->label->set('ADMIN');
$this->template->sublabel->set('Daftar User / Pengguna');
?>

<div class="card">
    <div class="card-header">
        <div class="d-flex w-100">
            <h3 class="card-title">Daftar User / Pengguna</h3>
        </div>
    </div>
    <div class="card-body">
        <form action="<?php echo site_url('admin/admin-user-list') ?>" method="GET">
            <div class="form-group">
                <label for="">Pecarian</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="USERNAME MEMBER" aria-label="NAMA MEMBER" aria-describedby="button-addon2" autocomplete="off" name="search" style="border:2px solid #2949ef">
                    <button class="btn btn-primary" type="submit" id="button-addon2" style="border:2px solid #2949ef">CARI DATA</button>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>User ID</th>
                        <th>No. Telepon</th>
                        <th>Email</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $limit       = 15;
                    $offset      = ($this->input->get('page')) ? $this->input->get('page') : 0;
                    $no          = $offset + 1;

                    $this->db->order_by('created_on', 'DESC');
                    if ($this->input->get('search')) {
                        $this->db->like('username', $this->input->get('search'));
                        $this->db->or_like('user_fullname', $this->input->get('search'));
                    }
                    $this->db->join('tb_users_groups', 'tb_users.id = tb_users_groups.user_id');
                    $this->db->where('group_id', (int)2);
                    $this->db->where('user_type', 'user');
                    $getdata = $this->db->get('tb_users', $limit, $offset);

                    if ($this->input->get('search')) {
                        $this->db->like('username', $this->input->get('search'));
                        $this->db->or_like('user_fullname', $this->input->get('search'));
                    }
                    $this->db->join('tb_users_groups', 'tb_users.id = tb_users_groups.user_id');
                    $this->db->where('group_id', (int)2);
                    $this->db->where('user_type', 'user');
                    $Gettotal = $this->db->get('tb_users')->num_rows();
                    foreach ($getdata->result() as $show) {
                        if ($show->id != 1) :
                    ?>
                            <tr>
                                <th><?php echo $no++ ?></th>
                                <td>
                                    <?php echo $show->user_fullname ?>
                                    <br>
                                    <small>@<?php echo $show->username; ?></small>
                                </td>

                                <td>
                                    <?php echo $show->user_phone ?>
                                </td>
                                <td>
                                    <?php echo $show->email ?>
                                </td>

                                <td>
                                    <?php echo date('d-M-Y', $show->created_on) ?>
                                </td>
                                <td>


                                            <a data-bs-href="<?php echo site_url('modal/admin/helpdesk_memberupdate?id=' . $show->id) ?>" data-bs-title="DATA MEMBER" data-bs-remote="false" data-bs-toggle="modal" data-bs-target="#dinamicModal" data-bs-backdrop="static" data-bs-keyboard="false" title="DATA MEMBER" class="btn btn-sm btn-primary text-white mb-1" style="min-width: 75px;">
                                                Lihat Data
                                            </a>

                                </td>
                            </tr>
                    <?php endif;
                    } ?>
                </tbody>
            </table>
            <?php echo $this->paginationmodel->paginate('admin-teknisi-list', $Gettotal, $limit) ?>
        </div>
    </div>
</div>
