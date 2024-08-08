<?php
$idtiket = isset($_GET['id']) ? intval($_GET['id']) : 0;
$this->db->where('tiket_id', $idtiket);
$getTiket = $this->db->get('tb_tiket');
$data = $getTiket->row();

if(!$data) {
    redirect('tiket-pending');
}elseif(userdata() == 'teknisi' && $data->tiket_userid != userid()) {
    redirect('tiket-pending');
}

$this->db->where('kategori_id', $data->tiket_kategori);
$getkategori = $this->db->get('tb_kategori')->row();
$kategori = $getkategori->kategori_nama;

$this->db->where('tipe_id', $data->tiket_tipe);
$gettipe = $this->db->get('tb_tipe')->row();
$tipe = $gettipe->tipe_nama;

// Misalkan gambar tiket disimpan dalam bentuk array
$tiket_images = json_decode($data->tiket_image); // Mengambil gambar dalam bentuk array
?>
<style>
    /* Gaya untuk kartu */
    .card {
        transition: transform 0.2s;
    }

    .card:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Gaya untuk gambar tiket */
    .img-thumbnail {
        border-radius: 10px;
        transition: transform 0.2s;
    }

    .img-thumbnail:hover {
        transform: scale(1.05);
    }

    /* Gaya untuk modal */
    .modal-content {
        border-radius: 10px;
    }

    #pesan-container {
        position: relative;
        padding: 10px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        display: flex;
        flex-direction: column-reverse;
    }

    .message {
        max-width: 80%;
        /* Maksimal lebar pesan */
        padding: 10px;
        border-radius: 15px;
        margin-bottom: 10px;
        position: relative;
        word-wrap: break-word;
        /* Memastikan kata panjang terputus */
    }

    .message-sender {
        background-color: #d4edda;
        /* Warna hijau untuk pengirim */
        margin-left: auto;
        /* Dorong ke kanan */
    }

    .message-receiver {
        background-color: #f8d7da;
        /* Warna merah untuk penerima */
        margin-right: auto;
        /* Dorong ke kiri */
    }

    /* Optional: Mengatur overflow dan scrollbar */
    .overflow-auto {
        overflow-y: auto;
        /* Scroll hanya untuk sumbu Y */
    }

    /* Styling untuk scrollbar */
    .overflow-auto::-webkit-scrollbar {
        width: 8px;
    }

    .overflow-auto::-webkit-scrollbar-thumb {
        background: #888;
        /* Warna scrollbar */
        border-radius: 10px;
    }

    .overflow-auto::-webkit-scrollbar-thumb:hover {
        background: #555;
        /* Warna scrollbar saat hover */
    }

    .img-thumbnail {
        width: 150px;
        /* atau ukuran yang diinginkan */
        height: 150px;
        /* memastikan tinggi tetap sama */
        object-fit: cover;
        /* menjaga aspek gambar */
    }
</style>

<?php
$status = '';
if ($data->tiket_status == 'pending') {
    $status = 'bg-warning';
} elseif ($data->tiket_status == 'process') {
    $status = 'bg-info';
} else {
    $status = 'bg-success';
}
$this->db->where('id', $data->tiket_userid);
$getuser = $this->db->get('tb_users')->row();

if ($data->tiket_status !== 'pending') {
    $this->db->where('id', $data->tiket_petugas);
    $getpetugas = $this->db->get('tb_users')->row();
    $petugas = isset($getpetugas->username) ? $getpetugas->username : '';

    $penerima_pesan = '';

    if (userdata()->user_type == 'teknisi') {
        $penerima_pesan = $getuser->id;
    } else {
        $penerima_pesan = $getpetugas->id;
    }
    $idpesan = '';

    if ($getuser->id < $getpetugas->id) {
        $idpesan = $getuser->id . $getpetugas->id;
    } else {
        $idpesan = $getpetugas->id . $getuser->id;
    }
}


?>

<div class="row justify-content-center flex-row">
    <div class="col-xl-8 col-lg-6 col-md-12">
        <div class="card border-info">
            <div class="card-header text-center justify-content-between <?php echo $status; ?> text-white">
                <div class="text-left justify-content-start d-flex flex-column align-items-start">
                    <h4 class="card-title font-weight-900 display-6 mb-2"><?php echo htmlspecialchars($data->tiket_judul); ?></h4>
                    <span class="badge bg-secondary">Oleh : <?php echo $getuser->username; ?></span>
                </div>

                <?php if ($data->tiket_status == 'process' && userdata()->user_type == 'teknisi') : ?>
                    <button class="btn btn-success" onclick="selesaikantiket('<?php echo $data->tiket_id ?>', event)">Selesai</button>
                <?php elseif ($data->tiket_status == 'complete') : ?>
                    <button class="btn btn-dark disabled">tiket selesai</button>
                <?php elseif($data->tiket_status == 'pending' && userdata()->user_type == 'user') : ?>
                    <div class="dropdown">
                        <a class="btn btn-dark dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Pilihan
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="hapustiket('<?php echo $idtiket ?>')">Hapus</a></li>
                            <li><a class="dropdown-item" href="<?php echo base_url('update-tiket?id=' . $idtiket) ?>">Edit</a></li>
                        </ul>
                    </div>
                    <?php elseif($data->tiket_status == 'pending' && userdata()->user_type == 'teknisi') : ?>
                        <button class="btn btn-primary" onclick="konfirmasiTiket('<?php echo $idtiket ?>')">konfirmasi Tiket</button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <h5 class="card-title">Detail Tiket</h5>
                <p class="card-text"><strong>Penanggung Jawab:</strong> <?php echo isset($petugas) ? $petugas : '-'; ?></p>
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
                </p>
                <p class="card-text"><strong>Deskripsi:</strong></p>
                <div>
                    <?php echo $data->tiket_desc; ?>

                </div>

                <!-- Menampilkan gambar-gambar tiket -->
                <div class="row">
                    <ul class="list-unstyled d-flex flex-wrap">
                        <?php foreach ($tiket_images as $image) : ?>
                            <li class="col-4 col-md-3 mb-3 d-flex justify-content-center">
                                <img src="<?php echo base_url('assets/upload/' . $image); ?>" class="img-thumbnail" alt="" data-bs-toggle="modal" style="width: 150px; height: 150px;" data-bs-target="#imageModal" data-image="<?php echo base_url('assets/upload/' . $image); ?>">
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>
            <div class="card-footer text-muted text-center">
                <small>Diposting pada: <?php echo date('d M Y', strtotime($data->tiket_date)); ?></small>
            </div>
        </div>
    </div>
        <div class="col-xl-4 col-lg-6 col-md-12">
            <div class="card border-info h-80 ">
                <div class="card-header bg-warning">
                    <h4>Diskusi Tiket</h4>
                </div>
                <div class="card-body">
                    <div id="pesan-container" class="overflow-auto" style="min-height: 400px; max-height: 400px">
                    
                    
                    </div>
                </div>
                <?php echo form_open('', 'id="form-chat"') ?>
                <div class="card-footer text-muted text-center">
                    <div class="input-group mb-3">
                        <input name="pesan_penerima" value="<?php echo $penerima_pesan; ?>" type="hidden" class="form-control" aria-describedby="button-addon2">
                        <input name="pesan_tiket" type="hidden" value=" <?php echo $idtiket; ?>" class="form-control" aria-describedby="button-addon2">
                        <input required name="pesan_isi" type="text" class="form-control" placeholder="Kirim Pesan" aria-describedby="button-addon2">
                        <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Kirim</button>
                    </div>
                </div>
                <?php echo form_close() ?>
            </div>
        </div>
    <!-- </div> -->

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
    <?php echo date('Y-m-d H:i:s', strtotime('-1 hour')); ?>

    <script>

// function fetchNewMessages() {
//     $.ajax({
//         url: '<?php echo site_url('getdata/public_get/Getdatas/getNewMessages') ?>',
//         type: 'GET',
//         data: {
//             idpesan: <?php echo $idpesan; ?> // Kirim ID chat
//         },
//         dataType: 'json',
//         success: function(data) {
//             if (data.length > 0) {
//                 console.log(messages);
//                 data.reverse().forEach(function(message) {
//                     const isSender = message.pengirim === '<?php echo userid(); ?>';
//                     const messageDiv = `<div class="message ${isSender ? 'message-sender' : 'message-receiver'}">
//                                             <span>${message.isi}</span><br>
//                                             <small class="text-muted">${waktuYangLalu(message.tanggal)}</small>
//                                         </div>`;
//                     $('#pesan-container').append(messageDiv);
//                 });
//             }
//             // Panggil fungsi ini lagi setelah interval waktu tertentu
//             setTimeout(fetchNewMessages, 5000); // Ulangi setiap 2 detik
//         },
//         error: function() {
//             console.error('Error fetching new messages');
//             setTimeout(fetchNewMessages, 5000); // Coba lagi setelah 5 detik jika gagal
//         }
//     });
// }

// // Mulai fungsi saat halaman dimuat
// $(document).ready(function() {
//     fetchNewMessages();
// });


        const imageModal = document.getElementById('imageModal');
        imageModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const imageUrl = button.getAttribute('data-image');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageUrl;
        });

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
                confirmButtonText: 'YA Selesai',
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

        jQuery(document).ready(function($) {
            $('#form-chat').submit(function(event) {
                event.preventDefault();
                $.ajax({
                        url: '<?php echo site_url('postdata/user_post/Helpdesk_chat/KirimPesan') ?>',
                        type: 'post',
                        dataType: 'json',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                    })
                    .done(function(data) {
                        updateCSRF(data.csrf_data);
                //         // Swal.fire(
                //         //     data.heading,
                //         //     data.message,
                //         //     data.type
                //         // ).then(function() {
                //         // Clear the input field after sending the message
                        $('input[name="pesan_isi"]').val('');

                //         // Append the new message to the pesan-container
                //         var newMessage = `
                //     <div class="message message-sender">
                //         <span>${data.pesan_isi}</span> <br>
                //         <small class="text-muted">${data.pesan_date}</small>
                //     </div>
                // `;
                //         $('#pesan-container').prepend(newMessage); // Use prepend to show the latest message at the top
                        // });
                    });
            });
        });


        // jQuery(document).ready(function($) {
        //     $('#form-chat').submit(function(event) {
        //         event.preventDefault();

        //         $.ajax({
        //                 url: '<?php echo site_url('postdata/user_post/Helpdesk_chat/KirimPesan') ?>',
        //                 type: 'post',
        //                 dataType: 'json',
        //                 data: new FormData(this),
        //                 contentType: false,
        //                 cache: false,
        //                 processData: false,
        //             })
        //             .done(function(data) {
        //                 updateCSRF(data.csrf_data);
        //                 // Swal.fire(
        //                 //     data.heading,
        //                 //     data.message,
        //                 //     data.type
        //                 // ).then(function() {
        //                 // Clear the input field after sending the message
        //                 $('input[name="pesan_isi"]').val('');

        //                 // Append the new message to the pesan-container
        //                 var newMessage = `
        //             <div class="message message-sender">
        //                 <span>${data.pesan_isi}</span> <br>
        //                 <small class="text-muted">${data.pesan_date}</small>
        //             </div>
        //         `;
        //                 $('#pesan-container').prepend(newMessage); // Use prepend to show the latest message at the top
        //                 // });
        //             });
        //     });
        // });

        function hapustiket(idtiket) {
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
                            url: '<?php echo site_url('postdata/user_post/Helpdesk_tiket/HapusTiket') ?>',
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
                                    window.location.href = '<?php echo site_url('tiket-pending') ?>';
                                }
                            });

                        })
                }
            });
        }

        function konfirmasiTiket(idtiket) {
            Swal.fire({
                allowOutsideClick: false,
                title: 'Apakah Anda Yakin?',
                text: "Akan Menangani Tiket ini? ",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'YA Tangani',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.value) {

                    $.ajax({
                            url: '<?php echo site_url('postdata/user_post/Helpdesk_tiket/KonfirmasiPetugas') ?>',
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
                                    window.location.href = '<?php echo site_url('tiket-pending') ?>';
                                }
                            });

                        })
                }
            });
        }



    </script>