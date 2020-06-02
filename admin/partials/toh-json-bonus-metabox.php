<!-- UIkit CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.25/css/uikit.min.css" />

<!-- UIkit JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.25/js/uikit.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.25/js/uikit-icons.min.js"></script>

<form class="uk-form-stacked uk-width-1-3@s">
<div class="uk-margin">
    <div class="uk-margin-small uk-grid-small uk-child-width-1-3 uk-grid" uk-grid="">

        <div class="">
            <label>Bonus Code</label>
            <input class="uk-input" type="text" name="bonusCode" id="bonusCode" placeholder="Bonus Code" value="<?php echo isset($meta["_toh_bonusCode"][0]) ? $meta["_toh_bonusCode"][0] : "" ; ?>">
        </div>
        <div>
            <label>Bonus Category</label>

            <input class="uk-input" type="text" name="category" id="category" placeholder="Category" value="<?php echo isset($meta["_toh_category"][0]) ? $meta["_toh_category"][0] : "" ; ?>">
        </div>
        <div>
            <input class="uk-input" type="text" name="region" id="region" placeholder="region" value="<?php echo isset($meta["_toh_region"][0]) ? $meta["_toh_region"][0] : "" ; ?>">
        </div>
        <div>
            <input class="uk-input" type="text" name="value" id="value" placeholder="0" value="<?php echo isset($meta["_toh_value"][0]) ? $meta["_toh_value"][0] : "" ; ?>">
        </div>
        <div class="uk-width-1-1">
            <input class="uk-input" type="text" name="address" id="address" placeholder="Address" value="<?php echo isset($meta["_toh_address"][0]) ? $meta["_toh_address"][0] : "" ; ?>" >
        </div>
        <div >
            <input class="uk-input" type="text" name="city" id="city" placeholder="City" value="<?php echo isset($meta["_toh_city"][0]) ? $meta["_toh_city"][0] : "" ; ?>">
        </div>
        <div >
            <input class="uk-input" type="text" name="state" id="state" placeholder="State" value="<?php echo isset($meta["_toh_state"][0]) ? $meta["_toh_state"][0] : "" ; ?>">
        </div>
        <div>
            <input class="uk-input" type="text" name="GPS" id="GPS" placeholder="GPS" value="<?php echo isset($meta["_toh_GPS"][0]) ? $meta["_toh_GPS"][0] : "" ; ?>">
        </div>
        <div class="uk-width-1-2">
            <input class="uk-input" type="text" name="Access" id="Access" placeholder="Access" value="<?php echo isset($meta["_toh_Access"][0]) ? $meta["_toh_Access"][0] : "" ; ?>">
        </div>
        <div class="uk-width-1-2">
            <input class="uk-input" type="text" name="imageName" id="imageName" placeholder="Image filename" value="<?php echo isset($meta["_toh_imageName"][0]) ? $meta["_toh_imageName"][0] : "" ; ?>">
        </div>
        <!--<div class="uk-width-1-1">
            <textarea class="uk-textarea" name="flavor" id="flavor" rows="6" placeholder="Flavor"><?php echo isset($meta["_toh_flavor"][0]) ? $meta["_toh_flavor"][0] : "" ; ?></textarea>
        </div>
        <div class="uk-width-1-1"><textarea class="uk-textarea" name="madeinamerica" id="madeinamerica" rows="3" placeholder="madeinamerica"><?php echo isset($meta["_toh_madeinamerica"][0]) ? $meta["_toh_madeinamerica"][0] : "" ; ?></textarea>
        </div>-->
    </div>
</form>