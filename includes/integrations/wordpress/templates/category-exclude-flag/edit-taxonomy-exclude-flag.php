<tr class="form-field">
    <th scope="row" valign="top">
    <input type="checkbox" id="ae_taxonomy_exclude_flag" value="true" name="<?php echo App_Expert_Taxonomy_Exclude_Helper::$taxonomy_exclude_flag ?>" <?php if($flag){echo " checked='checked'";} ?> >
    <label style="display: inline-block;" for="ae_taxonomy_exclude_flag"><?php _e('Exclude from AppExperts', 'app-expert') ?></label>
    </th>
</tr>