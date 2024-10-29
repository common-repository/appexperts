<tr class="form-field">
    <th scope="row" valign="top">
        <label for="aeci_taxonomy_image">
            <?php __('Image', 'app-expert') ?>
        </label>
    </th>
    <td>
        <img class="aeci-taxonomy-image" src="<?php echo $image_url ?>"/>
        <br/>
        <input type="text" name="aeci_taxonomy_image"
               id="aeci_taxonomy_image" value="<?php echo $image_url ?>" /><br />
        <button class="ae_upload_image_button button"><?php  _e('Upload/Add image', 'app-expert'); ?></button>
        <button class="ae_remove_image_button button"><?php  _e('Remove image', 'app-expert');?></button>
    </td>

</tr>