<h2 style="color: #31602b"><?php _e('Manual Push Notification ', 'app-expert'); ?> </h2>

<div id="validation-messages-container"></div>
<div class="ae_manual_push_container">
<?php
    $errors = $this->validate_manual_push();
    $success =false;
    if(empty($errors)) $success = $this->save_manual_push();
    $langs = App_Expert_Language::get_active_languages();
?>
<?php if(!empty($errors)){?>

<?php foreach ($errors as $index => $error) :?>
    <?php if($index == 'title_error' || $index == 'content_error'){ ?>
        <div class="alert alert-danger">
            <p class="mb-1"><?php _e('Title and Content for ALL Language(s) are required', 'app-expert') ?></p>
        </div>
    <?php } ?>

    <?php if($index == 'title_length_error'){ ?>
        <div class="alert alert-danger">
            <p class="mb-1"><?php _e('Title should not exceed 50 character', 'app-expert') ?></p>
        </div>
    <?php } ?>
    <?php if($index == 'content_length_error'){ ?>
        <div class="alert alert-danger">
            <p class="mb-1"><?php _e('Content should not exceed 100 character', 'app-expert') ?></p>
        </div>
    <?php } ?>
    <?php if($index == 'target_error'){ ?>
        <div class="alert alert-danger">
            <p class="mb-1"><?php _e('Target should be a valid url', 'app-expert') ?></p>
        </div>
    <?php } ?>
<?php endforeach;?>
<?php } ?>
<?php if($success){ ?>
<div class="alert alert-success">
    <p><?php _e('Push notification is sent', 'app-expert') ?></p>
</div>
<?php } ?>

<form enctype="multipart/form-data" class="ae_manual_push" method="post" action="">
<ul class="nav nav-tabs" id="myTab" role="tablist">
<?php foreach ($langs as $index => $lang) :?>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?php echo $index === array_key_first($langs)?'active':'' ?>" id="<?php echo $lang['code'] ;?>-tab" data-bs-toggle="tab" data-bs-target="#<?php echo $lang['code'] ;?>-tab-pane" type="button" role="tab" aria-controls="<?php echo $lang['code'] ;?>-tab-pane" aria-selected="true"><?php echo $lang['code'] ;?></button>
    </li>
<?php endforeach;?>
</ul>
<div class="tab-content" id="myTabContent">
<?php foreach ($langs as $index => $lang) :?>
    <div class="tab-pane fade show <?php echo $index === array_key_first($langs)?'active':'' ?>" id="<?php echo $lang['code'] ;?>-tab-pane" role="tabpanel" aria-labelledby="<?php echo $lang['code'] ;?>-tab" tabindex="0">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Title', 'app-expert') ?> <span class="required">*</span></th>
                    <td>
                        <input class="width-100" type="text" name="title[<?php echo $lang['code'] ;?>]" value="<?php if(!empty($errors)){  echo $_POST['title'][$lang['code']] ; } ?>"/>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Content', 'app-expert') ?> <span class="required">*</span></th>
                    <td>
                        <textarea class="width-100" name="content[<?php echo $lang['code'] ;?>]"><?php if(!empty($errors)){ echo $_POST['content'][$lang['code']] ; }  ?></textarea>
                    </td>
                </tr>
            </table>
        </div>
<?php endforeach;?>
</div>

    <table class="form-table">
        <tr valign="top">
            <th scope="row"><?php _e('Target', 'app-expert') ?></th>
            <td>
                <input class="width-100"  type="text" name="target" value="<?php if(!empty($errors)){ echo $_POST['target']; } ?>"/>
                <p><?= __("add URL that needed to open once users click on the received push notifications.",'app-expert')?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php _e('Segments', 'app-expert') ?></th>
            <td>
                <select class="_ae_segments" name="segments[]" multiple >
                    <?php wp_dropdown_roles( '' ); ?>
                </select>
                <br><p class="text-danger"> <?php _e('Note: If no segments (roles) selected, Notification will be sent to all users.', 'app-expert') ?></p><br>
                <input type="checkbox" name="is_send_to_guest" value="1"> <?php _e('Send to guest', 'app-expert') ?><br>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php _e('Image', 'app-expert') ?></th>
            <td>
                <div>
                    <a href="#" class="misha-upl button button-default" data-ae-img-title="<?php _e('Upload image', 'app-expert') ?>" data-ae-btn-title="<?php _e('Use this image', 'app-expert') ?>"><?php _e('Upload image', 'app-expert') ?></a>
                    <a href="#" class="misha-rmv button button-default" style="display:none"><?php _e('Remove image', 'app-expert') ?></a>
                    <input type="hidden" name="image" value="">
                </div>
                <div class="alert alert-warning mt-2 hidden" id="ae_img_error">
                    <b><?php _e('Note:', 'app-expert') ?></b>
                    <?php _e('Image type should be:(JPEG, PNG, BMP) and size (1MB) to be shown in notification tray', 'app-expert') ?>
                </div>
            </td>
        </tr>

    </table>


    <p class="submit"><input type="submit" name="save_send_notification" class="button button-primary" value="<?php _e('Save and Send', 'app-expert') ?>"></p>

</form>
</div>