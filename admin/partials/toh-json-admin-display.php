<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.keishaperry.com/
 * @since      1.0.0
 *
 * @package    Toh_Json
 * @subpackage Toh_Json/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<!-- UIkit CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.25/css/uikit.min.css" />

<!-- UIkit JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.25/js/uikit.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.25/js/uikit-icons.min.js"></script>

<div class="uk-container uk-padding-large">
<form id="scrapeBuilderForm" class="uk-width-1-1 uk-grid uk-grid-large" uk-grid uk-height-match action="<?php echo admin_url( 'admin-post.php' ); ?>">
    <div class="uk-margin">    
    <?php if (current_user_can('administrator')) :?>
    <?php $next_version = $this->get_next_version(); ?>

            <p class="uk-text-small">Next version is: <b><?php echo $next_version; ?></b></p>
        <div class="uk-margin">
            <label><input type="checkbox" />Override version?</label>
        </div>
        <div class="uk-margin">
            <input type="hidden" name="action" value="create_json_record">
            <input type="text" value="<?php echo $next_version; ?>"/>
        </div>
    <?php endif; ?>
    </div>
    <div class="uk-margin uk-margin-top-large">
        <button type="submit" id="add-scrape" class="uk-button uk-button-large uk-button-secondary">Generate JSON cache</button>
    </div>
</form>
<?php $json_records = $this->get_bonus_json_records(); ?>
<table class="uk-container uk-table uk-table-divider">
    <thead>
        <tr>
            <th>Version</th>
            <th>Created at</th>
            <th>Created by</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
<?php foreach($json_records as $record) : ?>
<tr>
    <th><?php echo $record->version; ?></th>
    <th><?php echo $record->created_at; ?></th>
    <th><?php echo $record->created_by; ?></th>
    <th><a href="<?php echo site_url("/wp-json/toh/v1/bonus-data?v=".$record->version);?>">File view</a></th>
    <th><a class="uk-link-reset" title="doesn't work yet..." href="">File download</a></th>
</tr>
<?php endforeach; ?>

    </tbody>
    
</table>
<form class="uk-width-1-1 uk-margin" action="<?php echo admin_url( 'admin-post.php' ); ?>">
    <input type="hidden" name="action" value="trigger_scrape">
    <input type="text" name="limit" value="0">
    <?php submit_button( 'Scrape live data' ); ?>
</form>
</div>