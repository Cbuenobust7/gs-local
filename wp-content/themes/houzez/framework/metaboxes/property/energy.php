<?php
/**
 * Add energy metabox tab
 *
 * @param $metabox_tabs
 *
 * @return array
 */
function houzez_energy_metabox_tab( $metabox_tabs ) {
	if ( is_array( $metabox_tabs ) ) {

		$metabox_tabs['energy'] = array(
			'label' => houzez_option('cls_energy_class', 'Energy Class'),
            'icon' => 'dashicons-lightbulb',
		);

	}
	return $metabox_tabs;
}
add_filter( 'houzez_property_metabox_tabs', 'houzez_energy_metabox_tab', 80 );


/**
 * Add energy metaboxes fields
 *
 * @param $metabox_fields
 *
 * @return array
 */
function houzez_energy_metabox_fields( $metabox_fields ) {
	$houzez_prefix = 'fave_';

	$fields = array(
		array(
            'id' => "{$houzez_prefix}energy_class",
            'name' => houzez_option('cl_energy_cls', 'Energy Class' ),
            'desc' => '',
            'type' => 'select',
            'std' => "global",
            'options' => array(
                ''    => houzez_option('cl_energy_cls_plac', 'Select Energy Class'),
                'A+' => 'A+',
                'A'     => 'A',
                'B'     => 'B',
                'C'     => 'C',
                'D'     => 'D',
                'E'     => 'E',
                'F'     => 'F',
                'G'     => 'G',
                'H'     => 'H',
            ),
            'columns' => 6,
            'tab' => 'energy'
        ),
        array(
            'id' => "{$houzez_prefix}energy_global_index",
            'name' => houzez_option('cl_energy_index', 'Global Energy Performance Index'),
            'placeholder' => houzez_option('cl_energy_index_plac', 'For example: 92.42 kWh / m²a'),
            'type' => 'text',
            'std' => "",
            'columns' => 6,
            'tab' => 'energy'
        ),
        array(
            'id' => "{$houzez_prefix}renewable_energy_global_index",
            'name' => houzez_option('cl_energy_renew_index', 'Renewable energy performance index'),
            'placeholder' => houzez_option('cl_energy_renew_index_plac', 'For example: 0.00 kWh / m²a'),
            'type' => 'text',
            'std' => "",
            'columns' => 6,
            'tab' => 'energy'
        ),
        array(
            'id' => "{$houzez_prefix}energy_performance",
            'name' => houzez_option('cl_energy_build_performance', 'Energy performance of the building'),
            'placeholder' => houzez_option('cl_energy_build_performance_plac'),
            'desc' => '',
            'type' => 'text',
            'std' => "",
            'columns' => 6,
            'tab' => 'energy'
        ),
        array(
            'id' => "{$houzez_prefix}epc_current_rating",
            'name' => houzez_option('cl_energy_ecp_rating', 'EPC Current Rating'),
            'placeholder' => houzez_option('cl_energy_ecp_rating_plac'),
            'type' => 'text',
            'std' => "",
            'columns' => 6,
            'tab' => 'energy'
        ),
        array(
            'id' => "{$houzez_prefix}epc_potential_rating",
            'name' => houzez_option('cl_energy_ecp_p', 'EPC Potential Rating'),
            'placeholder' => houzez_option('cl_energy_ecp_p_plac'),
            'type' => 'text',
            'std' => "",
            'columns' => 6,
            'tab' => 'energy'
        ),
	);

	return array_merge( $metabox_fields, $fields );

}
add_filter( 'houzez_property_metabox_fields', 'houzez_energy_metabox_fields', 80 );
