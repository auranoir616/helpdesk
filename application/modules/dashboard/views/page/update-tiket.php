<?php
$this->template->title->set('Update Tiket');
$idtiket = isset($_GET['id']) ? intval($_GET['id']) : 0;

$this->db->where('tiket_id', $idtiket);
$getTiket = $this->db->get('tb_tiket');
$tiket = $getTiket->row();


?>

<div class="card">
    <div class="card-header">
    <div class="card-title display-6 font-weight-bold text-dark">Update tiket</div>
    </div>
    <div class="card-body">
        <?php echo form_open_multipart('', 'id="update-tiket"') ?>
        <input type="hidden" name="tiket_id" value="<?php echo $idtiket; ?>">
        <div class="form-group">
            <label for="">Subjek Tiket</label>
            <input type="text" value="<?php echo $tiket->tiket_judul ?>" class="form-control" name="tiket_judul" placeholder="Judul Tiket" autocomplete="off">
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
            <div class="form-group">
                <label for="">Kategori Tiket</label>
                <select name="tiket_kategori" id="" class="form-control">
                    <option disabled selected>Pilih Kategori</option>
                    <?php
                $GetKAT = $this->db->get('tb_kategori');
                foreach ($GetKAT->result() as $show) {
                    ?>
                    <option <?php echo $tiket->tiket_kategori == $show->kategori_id ? 'selected' : ''; ?> value="<?php echo $show->kategori_id; ?>"><?php echo $show->kategori_nama; ?></option>
                    <?php } ?>
                </select>
            </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
            <div class="form-group">
                <label for="">Jenis Tiket</label>
                <select name="tiket_tipe" id="" class="form-control">
                    <option disabled selected>Pilih Tipe</option>
                    <?php
                $GetJENIS = $this->db->get('tb_tipe');
                foreach ($GetJENIS->result() as $show) {
                    ?>
                    <option <?php echo $tiket->tiket_tipe == $show->tipe_id ? 'selected' : ''; ?> value="<?php echo $show->tipe_id; ?>"><?php echo $show->tipe_nama; ?></option>
                    <?php } ?>
                </select>
            </div>
            </div>
        </div>
            <script type="text/javascript" src="<?php echo base_url('assets/backend/tinymce/tinymce.min.js') ?>"></script>
        <script>
            tinymce.init({
                selector: '#textarea',
                plugins: [
                    "advlist autolink lists link charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime nonbreaking save table contextmenu directionality",
                    "emoticons template paste textcolor colorpicker textpattern",
                ],
                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link",
            });
        </script>
        <div class="form-group">
            <label for="">Deskripsi</label>
            <textarea id="textarea" name="tiket_desc" rows="20"> <?php echo $tiket->tiket_desc ?></textarea>
        </div>
        <div class="form-group">
    <div class="file-upload mb-3">
        <div class="file-select file-input">
            <div class="file-select-button" id="fileName">Upload Gambar Baru</div>
            <!-- <div class="file-select-name" id="noFile"><i class="ti-upload text-black fs-30 me-2 mt-2"></i></div> -->
            <input type="file" name="tiket_image[]" id="chooseFile" onchange="readURL(this)" multiple>
        </div>
    </div>
    <center>
        <div id="preview_images"></div>
    </center>
    <script type="text/javascript">
        function readURL(input) {
            if (input.files) {
                var preview = document.getElementById('preview_images');
                preview.innerHTML = ''; 
                for (var i = 0; i < input.files.length; i++) {
                    var file = input.files[i];
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxWidth = '150px';
                        img.style.maxHeight = '150px';
                        img.style.margin = '10px';
                        img.style.border = '1px solid #ddd';
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                }
            }
        }
    </script>        </div>
        <div class="form-group">
            <button type="submit" id="btns1" name="buttonklik" value="simpan" class="btn btn-warning btn-md btn-block font-weight-bold">UPDATE TIKET</button>
            <button disabled type="button" id="btns2" class="btn btn-warning btn-md btn-block font-weight-bold">PROSES UPDATE TIKET</button>
        </div>
        <?php echo form_close() ?>
        <script>
            jQuery(document).ready(function($) {
                $('#btns2').hide();
                $('#update-tiket').submit(function(event) {
                    event.preventDefault();
                    tinymce.triggerSave();
                    $('#btns1').hide();
                    $('#btns2').show();
                    $.ajax({
                            url: '<?php echo site_url('postdata/user_post/Helpdesk_tiket/updateTiket') ?>',
                            type: 'post',
                            dataType: 'json',
                            data: new FormData(this),
                            contentType: false,
                            cache: false,
                            processData: false,
                        })
                        .done(function(data) {
                            updateCSRF(data.csrf_data);

                            Swal.fire(
                                data.heading,
                                data.message,
                                data.type
                            ).then(function() {
                                updateCSRF(data.csrf_data);
                                if (data.status) {
                                    window.location.href = '<?php echo site_url('tiket-pending') ?>';
                                }
                            });
                            $('#btns1').show();
                            $('#btns2').hide();
                        })
                });
            });

        </script>
    </div>
</div>