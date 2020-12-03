<div class="form-group">
	<label for="prop_title"><?php echo houzez_option('cl_prop_title', 'Property Title').houzez_required_field('title'); ?></label>

	<input class="form-control" <?php houzez_required_field_2('title'); ?> name="prop_title" id="prop_title" value="<?php
    if (houzez_edit_property()) {
        global $property_data;
        echo esc_attr($property_data->post_title);
    }
    ?>" placeholder="<?php echo houzez_option('cl_prop_title_plac', 'Enter your property title'); ?>" type="text">
</div>