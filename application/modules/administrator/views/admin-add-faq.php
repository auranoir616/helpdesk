<?php
// $this->template->title->set('Tambah FAQ Baru');
?>

<div class="card">
    <div class="card-header">
    <div class="card-title display-6 font-weight-bold text-dark">Tambah FAQ baru</div>
    </div>
    <div class="card-body">
<?php echo form_open('', 'id="add-faq"'); ?>
    <div class="form-group">
        <label for="">Pertayaan</label>
        <input type="text" class="form-control" name="faq_question" autocomplete="off">
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
            <textarea id="textarea" name="faq_answer" rows="20">

            </textarea>
        </div>
        </div>
        </div>
    <div class="form-group">
        <button id='btn0101' type="submit" class="btn btn-primary btn-block">Simpan FAQ</button>
        <button id='btn0201' type="button" class="btn btn-primary btn-block" disabled>Saving Process</button>
    </div>
    <?php echo form_close(); ?>
    <script>
        $('#btn0201').hide();
        $('#add-faq').submit(function(event) {
            event.preventDefault();
            tinymce.triggerSave();

            $('#btn0101').hide();
            $('#btn0201').show();
            console.log($('#add-faq').serialize());
            $.ajax({
                    url: '<?php echo site_url('postdata/admin_post/Helpdesk_admin/tambahfaq') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: $('#add-faq').serialize(),
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
                        }else{
                            updateCSRF(data.csrf_data);

                        }
                    });
                    $('#btn0101').show();
                    $('#btn0201').hide();
                })
        });
    </script>
