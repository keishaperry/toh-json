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
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.25/js/uikit.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.25/js/uikit-icons.min.js"></script> -->

<div class="uk-container-expand">

<h2>Import Bonus Data</h2>

<div>
<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
<input type="hidden" name="action" value="import_kml">
<input type="submit" value="Import KML Sources">
</form>
<p uk-margin>
    <form class="uk-width-1-1 uk-margin" action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <input type="hidden" name="action" value="trigger_scrape_db">
        <input type="hidden" name="db_tablename" value="dogs">
        <button type="submit" class="uk-button uk-button-default uk-button-small">Import War Dogs DB</button>
    </form>
    <form class="uk-width-1-1 uk-margin" action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <input type="hidden" name="action" value="trigger_scrape_db">
        <input type="hidden" name="db_tablename" value="doughboys">
        <button type="submit" class="uk-button uk-button-default uk-button-small">Import Doughboys DB</button>
    </form>
    <form class="uk-width-1-1 uk-margin" action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <input type="hidden" name="action" value="trigger_scrape_db">
        <input type="hidden" name="db_tablename" value="goldstars">
        <button type="submit" class="uk-button uk-button-default uk-button-small">Import Gold Stars DB</button>
    </form>
    <form class="uk-width-1-1 uk-margin" action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <input type="hidden" name="action" value="trigger_scrape_db">
        <input type="hidden" name="db_tablename" value="hueys">
        <button type="submit" class="uk-button uk-button-default uk-button-small">Import Hueys DB</button>
    </form>
    <form class="uk-width-1-1 uk-margin" action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <input type="hidden" name="action" value="trigger_scrape_db">
        <input type="hidden" name="db_tablename" value="parks">
        <button type="submit" class="uk-button uk-button-default uk-button-small">Import Parks DB</button>
    </form>
    <form class="uk-width-1-1 uk-margin" action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <input type="hidden" name="action" value="trigger_scrape_db">
        <input type="hidden" name="db_tablename" value="madonnas">
        <button type="submit" class="uk-button uk-button-default uk-button-small">Import Madonnas DB</button>
    </form>
</p>
<p uk-margin>
    <form class="uk-width-1-1 uk-margin" action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <input type="hidden" name="action" value="trigger_purge_db">
        <button type="submit" class="uk-button uk-button-default uk-button-small">Purge SQL DB Bonuses</button>
    </form>
</p>
</div>
</div>
