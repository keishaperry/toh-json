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
<?php
$json = json_decode($this->curl_prod_json());
var_dump($json);
?>

<table class="uk-container uk-table uk-table-divider">
    <thead>
        <tr>
            <th>Name</th>
            <th>Bonus Code</th>
            <th>Table Heading</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pending_bonuses as $bonus) : $metadata = get_post_meta($bonus->ID);?>
           
        <tr>
            <td><?php echo $bonus->post_title;?></td>
            <td><?php echo $metadata["_toh_bonusCode"][0];?></td>
            <td><?php echo $metadata["_toh_category"][0];?></td>
            <td><?php echo $metadata["_toh_city"][0];?>, <?php echo $metadata["_toh_state"][0];?></td>
            <td><button class="uk-button uk-button-small">Approve</button></td>
            <!--
							'_toh_value' => sanitize_text_field($bonus->value),
							'_toh_address' => sanitize_text_field($bonus->address),
							'_toh_city' => sanitize_text_field($bonus->city),
							'_toh_state' => sanitize_text_field($bonus->state),
							'_toh_GPS' => sanitize_text_field($bonus->GPS),
							'_toh_Access' => sanitize_text_field($bonus->Access),
							'_toh_imageName' => sanitize_text_field($bonus->imageName),
							'_toh_flavor' => sanitize_text_field($bonus->flavor),
							'_toh_madeinamerica' => sanitize_text_field($bonus->madeinamerica),
            -->
        </tr>
        <?php endforeach; ?>

    </tbody>
</table>
<form action="<?php echo admin_url( 'admin-post.php' ); ?>">
    <input type="hidden" name="action" value="trigger_scrape">
    <input type="text" name="limit" value="0">
    <?php submit_button( 'Scrape live data' ); ?>
</form>