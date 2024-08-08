<?php
$this->template->title->set('Tambah Tiket Baru');
?>

<div class="card">
    <div class="card-header">
    <div class="card-title display-6 font-weight-bold text-dark">Tambah Catatan Baru</div>
    </div>
    <div class="card-body">
        <?php echo form_open_multipart('', 'id="new-notes"') ?>
        <div class="form-group">
            <label for="">Judul Catatan</label>
            <input type="text" class="form-control" name="notes_judul" placeholder="Judul Tiket" autocomplete="off">
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
            <textarea id="textarea" name="notes_desc" rows="20"></textarea>
        </div>
        <div class="form-group">
    <div class="file-upload mb-3">
        <div class="file-select file-input">
            <div class="file-select-button" id="fileName">Upload Gambar</div>
            <!-- <div class="file-select-name" id="noFile"><i class="ti-upload text-black fs-30 me-2 mt-2"></i></div> -->
            <input type="file" name="notes_image[]" id="chooseFile" onchange="readURL(this)" multiple>
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
            <button type="submit" id="btns1" name="buttonklik" value="simpan" class="btn btn-warning btn-md btn-block font-weight-bold">SIMPAN CATATAN</button>
            <button disabled type="button" id="btns2" class="btn btn-warning btn-md btn-block font-weight-bold">PROSES SIMPAN CATATAN</button>
        </div>
        <?php echo form_close() ?>
        <script>
            jQuery(document).ready(function($) {
                $('#btns2').hide();
                $('#new-notes').submit(function(event) {
                    var formData = new FormData(this);
                    console.log(...formData);
                    event.preventDefault();
                    tinymce.triggerSave();
                    $('#btns1').hide();
                    $('#btns2').show();
                    $.ajax({
                            url: '<?php echo site_url('postdata/user_post/Helpdesk_notes/tambahnotes') ?>',
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
                                    window.location.href = '<?php echo site_url('notes') ?>';
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