<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">FAQ</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="accordionExample">
                    <?php
                    $this->db->where('faq_status', 'aktif');
                    $getFaq = $this->db->get('tb_faq');  
                    foreach ($getFaq->result() as $index => $faq) {
                    ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                <button class="accordion-button font-weight-800 text-dark" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse<?php echo $index; ?>" 
                                        aria-expanded="false" 
                                        aria-controls="collapse<?php echo $index; ?>">
                                   <?php echo $index + 1; ?>. <?php echo $faq->faq_question; ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $index; ?>" 
                                 class="accordion-collapse collapse" 
                                 aria-labelledby="heading<?php echo $index; ?>" 
                                 data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <?php echo $faq->faq_answer; ?>
                                    <br>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
