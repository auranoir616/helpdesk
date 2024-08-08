<div class="card">
    <div class="card-header d-flex justify-content-between">
    <button onclick="window.location.href='<?php echo base_url('new-notes'); ?>'" type="button" class="btn btn-primary btn-block">TAMBAH DATA</button>
    </div>
    <div class="card-body">
        <!-- <form action="<?php echo site_url('tiket') ?>" method="GET">
            <div class="form-group">
                <label for="">Pecarian</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Subjek Tiket" aria-label="Subjek Tiket" aria-describedby="button-addon2" autocomplete="off" name="search" style="border:2px solid #2949ef">
                    <button class="btn btn-primary" type="submit" id="button-addon2" style="border:2px solid #2949ef">CARI DATA</button>
                </div>
            </div>
        </form> -->
        <div class="table-responsive">

        <table class="table table-hover">
            <tr>
                <th>No.</th>
                <th>Judul</th>
                <th>Deskripsi</th>
                <th>Waktu</th>
                <th>Aksi</th>
            </tr>
            <?php
            $limit       = 10;
            $offset      = ($this->input->get('page')) ? $this->input->get('page') : 0;
            $no          = $offset + 1;
            $this->db->order_by('notes_date', 'DESC');
            $this->db->where('notes_userid', userid());
            $getdata = $this->db->get('tb_notes', $limit, $offset);
            $this->db->where('notes_userid', userid());
            $Gettotal = $this->db->get('tb_notes')->num_rows();
            foreach ($getdata->result() as $show) {

            ?>
                <tr onclick="window.location.href='<?php echo base_url('view-notes?id=' . $show->notes_id); ?>'" style="cursor:pointer;">
                    <td><?php echo $no++ ?></td>
                    <td><?php echo $show->notes_judul ?></td>
                    <td><?php echo substr(strip_tags($show->notes_desc), 0, 50); ?></td>
                    <td><?php echo waktuYangLalu($show->notes_date); ?></td>
                    
                    <!-- <td>
                        <button onclick="selesaikantiket('<?php echo $show->notes_id ?>', event)" class="btn btn-sm btn-success">selesaikan Tiket</button>
                    </td> -->
                </tr>
            <?php } ?>
        </table>
    </div>
    </div>
</div>

