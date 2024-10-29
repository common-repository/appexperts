<h4 class="app-exp-section-title"><?php _e('SEE HOW IT WORKS.', 'app-expert'); ?></h4>
<div class="mt-5">
<h6 class="p-2 app-exp-section-hint text-center rounded"> <a class="app-exp-section-hint" href="<?php echo admin_url("admin.php?page=app_expert_license"); ?>"><?php _e('You can find license key here', 'app-expert'); ?></a></h6>
    <iframe style="width:100%;min-height:280px"
            src="https://www.youtube.com/embed/vKGvIERp1UA"
            title="YouTube video player"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen></iframe>
</div>
<div class="mt-5 m row  justify-content-md-center text-center app-experts-help">
    <div class="col-md-3">
       <a  href="https://appexperts.io/contact-us/" target="_blank">
           <div class="row">
               <div class="col-md-6 px-0"><img class="img-fluid" src="<?= $this->_current_feature->get_current_url()."assets/images/support-icon.png"?>" alt="support"></div>
               <div class="col-md-6 px-0 m-auto text-start"><?php _e('Support', 'app-expert'); ?></div>
           </div>
       </a>
    </div>
    <div class="col-md-3">
        <a href="https://appexperts.io/#faqs" target="_blank">
            <div class="row">
                <div class="col-md-6 px-0"><img class="img-fluid" src="<?= $this->_current_feature->get_current_url()."assets/images/faq-icon.png"?>" alt="support"></div>
                <div class="col-md-6 px-0 m-auto text-start"><?php _e('FAQs', 'app-expert'); ?></div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="https://appexperts.io/documentation/" target="_blank">
            <div class="row">
                <div class="col-md-6 px-0"><img class="img-fluid" src="<?= $this->_current_feature->get_current_url()."assets/images/docs-icon.png"?>" alt="support"> </div>
                <div class="col-md-6 px-0 m-auto text-start"><?php _e('Technical documentation', 'app-expert'); ?></div>
            </div>
        </a>
    </div>
</div>