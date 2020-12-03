<div class="form-group">
	<label for="energy_class">
		<?php echo houzez_option('cl_energy_cls', 'Energy Class' ).houzez_required_field('energy_class'); ?>
	</label>

	<select name="energy_class" id="energy_class" <?php houzez_required_field_2('energy_class'); ?> class="selectpicker form-control bs-select-hidden" title="<?php echo houzez_option('cl_energy_cls_plac', 'Select'); ?>" data-live-search="false" data-selected-text-format="count" data-actions-box="true">
		<option value=""><?php echo houzez_option('cl_energy_cls_plac', 'Select Energy Class'); ?></option>
        <option <?php selected(houzez_get_field_meta('energy_class'), 'A+'); ?> value="A+">A+</option>
        <option <?php selected(houzez_get_field_meta('energy_class'), 'A'); ?> value="A">A</option>
        <option <?php selected(houzez_get_field_meta('energy_class'), 'B'); ?> value="B">B</option>
        <option <?php selected(houzez_get_field_meta('energy_class'), 'C'); ?> value="C">C</option>
        <option <?php selected(houzez_get_field_meta('energy_class'), 'D'); ?> value="D">D</option>
        <option <?php selected(houzez_get_field_meta('energy_class'), 'E'); ?> value="E">E</option>
        <option <?php selected(houzez_get_field_meta('energy_class'), 'F'); ?> value="F">F</option>
        <option <?php selected(houzez_get_field_meta('energy_class'), 'G'); ?> value="G">G</option>
	</select><!-- selectpicker -->
</div>