<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$this->db->where('tiket_id', $id);
$query = $this->db->get('tb_tiket');
$data = $query->row();

$this->db->where('kategori_id', $data->tiket_kategori);
$getkategori = $this->db->get('tb_kategori')->row();
$kategori = $getkategori->kategori_nama;

$this->db->where('tipe_id', $data->tiket_tipe);
$gettipe = $this->db->get('tb_tipe')->row();
$tipe = $gettipe->tipe_nama;

$this->db->where('id', $data->tiket_petugas);
$getpetugas = $this->db->get('tb_users')->row();
$petugas = isset($getpetugas->username) ? $getpetugas->username : '';


// Misalkan gambar tiket disimpan dalam bentuk array
$tiket_images = json_decode($data->tiket_image); // Mengambil gambar dalam bentuk array
?>
<?php 
$status = '';
if($data->tiket_status == 'pending'){
    $status = 'bg-warning';
}elseif($data->tiket_status == 'process'){
    $status = 'bg-info';
}else{
    $status = 'bg-success';
}
?>


<!-- <div class="container"> -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card border-info">
                <div class="card-header text-center <?php echo $status; ?> d-flex justify-content-between text-white">
                    <h1 class="card-title display-5"><?php echo $data->tiket_judul; ?></h1>
                  
                    <?php if($status !== 'bg-success'): ?>
                    <a data-bs-href="<?php echo site_url("modal/admin/helpdesk_pilihpetugas?id=$id") ?>" data-bs-title="Pilih Petugas" data-bs-remote="false" data-bs-toggle="modal" data-bs-target="#dinamicModal" data-bs-backdrop="static" data-bs-keyboard="false" title="Pilih Petugas" class="btn btn-secondary text-white"><i class="fa fa-user-secret" aria-hidden="true"></i> Pilih Petugas</a>
                        <?php endif; ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Detail Tiket</h5>
                    <p class="card-text"><strong>Kategori:</strong> <?php echo $kategori; ?></p>
                    <p class="card-text"><strong>Tipe:</strong>
    <span class="badge 
        <?php 
        if ($tipe == 'Non-Urgent') { 
            echo 'bg-secondary'; // Warna untuk Non-Urgent
        } elseif ($tipe == 'Rendah') { 
            echo 'bg-success'; // Warna untuk Rendah
        } elseif ($tipe == 'Moderat') { 
            echo 'bg-warning'; // Warna untuk Moderat
        } elseif ($tipe == 'Tinggi') { 
            echo 'bg-danger'; // Warna untuk Tinggi
        } elseif ($tipe == 'Segera') { 
            echo 'bg-primary'; // Warna untuk Segera
        } else { 
            echo 'bg-info'; // Warna default
        } 
        ?>">
        <?php echo htmlspecialchars($tipe); ?>
    </span>
</p>                    <p class="card-text"><strong>penanggung Jawab:</strong> <?php echo $petugas; ?></p>
                    <p class="card-text"><strong>Deskripsi:</strong></p>
                    <p><?php echo $data->tiket_desc; ?></p>
                    <!-- Menampilkan gambar-gambar tiket -->
                    <div class="row">
                        <!-- Menampilkan gambar-gambar tiket dalam list -->
                        <?php foreach ($tiket_images as $image): ?>
                            <div class="col-6 col-md-4 m-1">
                                <img src="<?php echo base_url('assets/upload/' . $image); ?>" class="img-thumbnail" alt="" data-bs-toggle="modal"  data-bs-target="#imageModal" data-image="<?php echo base_url('assets/upload/' . $image); ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-footer text-muted text-center">
                    <small>Diposting pada: <?php echo date('d M Y', strtotime($data->tiket_date)); ?></small>
                </div>
            </div>
        </div>
        
    </div>
<!-- </div> -->

<!-- Modal untuk menampilkan gambar yang lebih besar -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Gambar Tiket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="">
            </div>
        </div>
    </div>
</div>

<script>
    // Menangkap klik pada gambar untuk menampilkan modal
    const imageModal = document.getElementById('imageModal');
    imageModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Tombol yang memicu modal
        const imageUrl = button.getAttribute('data-image'); // Mengambil URL gambar dari atribut data-image
        const modalImage = document.getElementById('modalImage'); // Mengambil elemen gambar modal
        modalImage.src = imageUrl; // Mengatur src gambar modal
    });
</script>
