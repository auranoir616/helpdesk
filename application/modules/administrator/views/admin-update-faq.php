<?php
$this->template->title->set('Update FAQ');
$idfaq = isset($_GET['idfaq']) ? intval($_GET['idfaq']) : 0;

$this->db->where('faq_id', $idfaq);
$getFaq = $this->db->get('tb_faq');
$faq = $getFaq->row();


?>

<div class="card">
    <div class="card-header">
    <div class="card-title display-6 font-weight-bold text-dark">Update FAQ</div>
    </div>
    <div class="card-body">
        <?php echo form_open_multipart('', 'id="update-tiket"') ?>
        <input type="hidden" name="faq_id" value="<?php echo $idfaq; ?>">
        <div class="form-group">
            <label for="">Pertanyaan</label>
            <input type="text" value="<?php echo $faq->faq_question ?>" class="form-control" name="faq_question" placeholder="Judul Tiket" autocomplete="off">
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
            <label for="">Jawaban</label>
            <textarea id="textarea" name="faq_answer" rows="20"> <?php echo $faq->faq_answer ?></textarea>
        </div>
        <div class="form-group">
            <button type="submit" id="btns1" name="buttonklik" value="simpan" class="btn btn-warning btn-md btn-block font-weight-bold">UPDATE FAQ</button>
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
                            url: '<?php echo site_url('postdata/admin_post/Helpdesk_admin/updatefaq') ?>',
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
                                    window.location.href = '<?php echo site_url('admin-faq') ?>';
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