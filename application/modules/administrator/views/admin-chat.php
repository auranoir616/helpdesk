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
$iduser = isset($_GET['id_user']) ? $_GET['id_user'] : null;

$visible = '';
if($iduser == null){
    $visible = 'd-none';
}
 ?>
<div class="row justify-content-center flex-row">
    <div class="col-xl-4 col-lg-6 col-md-12">
        <div class="card border-info">
            <div class="card-header bg-success text-center justify-content-between text-white">
                <div class="text-left justify-content-start d-flex flex-column align-items-start">
                    <h6 class="card-title font-weight-900 display-6">MEMBER</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="list-group">
                <?php 
                    $this->db->where('user_type', 'teknisi'); 
                    $this->db->or_where('user_type', 'user'); 
                    $this->db->order_by('user_type', 'DESC');
                    $getUser = $this->db->get('tb_users');
                    foreach ($getUser->result() as $user):
                        if($iduser == $user->id){
                            $active = 'active';
                        }else{
                            $active = '';
                        };
                ?>
                    <button type="button" class="list-group-item <?php echo $active; ?> list-group-item-action" aria-current="true" onclick="startChat('<?php echo $user->id; ?>')">
                        <?php echo $user->username . ' (' . $user->user_fullname . ')'; ?>
                    </button>
                <?php endforeach; ?>
                </div>
            </div>
            <div class="card-footer text-muted text-center"></div>
        </div>
    </div>
    <div class="col-xl-8 col-lg-6 col-md-12">
        <div class="card border-info <?php echo $visible; ?> h-80">
            <div class="card-header bg-warning">
                <h5 class="card-title font-weight-900" ><?php echo isset(userdata(['id' => $iduser])->username) ? userdata(['id' => $iduser])->username : ''; ?></h5>
            </div>
            <div class="card-body">
                <div id="pesan-container" class="overflow-auto" style="min-height: 400px; max-height: 400px">
                   
                 
                </div>
            </div>
            <?php echo form_open('', 'id="form-chat"') ?>
            <div class="card-footer text-muted  text-center ">

                <div class="input-group mb-3">
                    <input name="pesan_penerima" value="<?php echo $iduser; ?>" type="hidden" class="form-control" aria-describedby="button-addon2">
                    <input required name="pesan_isi" type="text" class="form-control" placeholder="Kirim Pesan" aria-describedby="button-addon2">
                    <button class="btn btn-outline-secondary" type="submit" id="button-addon2"><i class="fa fa-paper-plane" aria-hidden="true"></i> Kirim</button>
                </div>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>

<script>
function startChat(userId) {
    window.location.href = '<?php echo current_url(); ?>?id_user=' + userId;
}

$(document).ready(function() {
    let lastTimestamp = '<?php echo date('Y-m-d H:i:s', strtotime('-1 hour')); ?>'; // Inisialisasi timestamp terakhir
    function fetchNewMessages() {
        $.ajax({
            url: '<?php echo site_url('getdata/public_get/Getdatas/getNewMessagesAdmin') ?>',
            type: 'GET',
            data: { 
                lastTimestamp: lastTimestamp,
                userid: <?php echo isset($iduser) ? $iduser : '1'; ?>,
            },
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    data.forEach(function(message) {
                        var isSender = message.pengirim == <?php echo userid(); ?>; // Cek jika pesan pengirim adalah pengguna saat ini
                        var messageClass = isSender ? 'message-sender' : 'message-receiver';
                        $('#pesan-container').prepend(`
                            <div class="message ${messageClass}">
                                <span>${message.isi}</span><br>
                                <small class="text-muted">${message.tanggal}</small>
                            </div>
                        `);
                    });
                    lastTimestamp = data[0].tanggal; 
                }
                setTimeout(fetchNewMessages, 2000);
            },
            error: function() {
                console.error('Error fetching new messages');
                setTimeout(fetchNewMessages, 5000); 
            }
        });
    }
    fetchNewMessages();
});


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
</script>


