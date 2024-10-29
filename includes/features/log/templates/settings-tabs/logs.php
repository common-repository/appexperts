<?php
$date = !empty($_POST['log_date'])?$_POST['log_date']:date('Y-m-d');
$log_file_path = App_Expert_Logger::get_log_dir_path() . "/log-{$date}.log";
$log_file_url  = App_Expert_Logger::get_log_dir_url()  . "/log-{$date}.log";
$myfile = fopen($log_file_path, "r");
if($myfile){
    $txt = fread($myfile,filesize($log_file_path));
    $txt = htmlspecialchars($txt);
    $txt = nl2br($txt);
    fclose($myfile);
}

?>
<div class="wrap app-exp-container-wrapper">
    <form method="post">
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc" style="width: 80px;vertical-align: middle;">
                    <label for="log_date"><?php _e('Log Date',"app-expert")?></label>
                </th>
                <td class="forminp forminp-text">
                    <div style="width: 100%;">
                        <input style="width: 40%;margin:5px 0;" required type="date" name="log_date" value="<?php echo $date ?>">
                        <input type="submit" name="log_submit" class="button-primary" value="<?php _e('Search','app-expert')?>">
                        <?php if(!empty($txt)){?>
                            <a href="<?php echo $log_file_url?>" download style="float: right;margin: 10px 5px;"><?php _e('Download','app-expert')?></a>
                        <?php }?>
                    </div>
                </td>
            </tr>

        </table>
    </form>
    <div style="text-align: center;background: #fff;margin: 5px;padding: 5px;min-height: 50vh">
        <?php if(empty($txt)){?>
            <p>
                <?php _e('No log data found on selected date',"app-expert")?>
            </p>
            <?php
        }
            else {
                echo "<div style='text-align: left; word-wrap: break-word;'>$txt</div>";

            } ?>
    </div>
</div>