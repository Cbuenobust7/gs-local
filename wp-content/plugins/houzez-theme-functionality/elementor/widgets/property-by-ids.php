<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Property by ID Widget.
 * @since 1.5.6
 */
class Houzez_Elementor_Property_By_IDs extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 1.5.6
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'houzez_elementor_property_by_ids';
    }

    /**
     * Get widget title.
     * @since 1.5.6
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Property by IDs', 'houzez-theme-functionality' );
    }

    /**
     * Get widget icon.
     *
     * @since 1.5.6
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'fa fa-building-o';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 1.5.6
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'houzez-elements' ];
    }

    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.5.6
     * @access protected
     */
    protected function _register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label'     => esc_html__( 'Content', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'prop_grid_style',
            [
                'label'     => esc_html__( 'Grid Style', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'v_1'  => esc_html__( 'Property Card v1', 'houzez-theme-functionality'),
                    'v_2'    => esc_html__( 'Property Card v2', 'houzez-theme-functionality'),
                    'v_3'    => esc_html__( 'Property Card v3', 'houzez-theme-functionality'),
                    'v_5'    => esc_html__( 'Property Card v5', 'houzez-theme-functionality'),
                    'v_6'    => esc_html__( 'Property Card v6', 'houzez-theme-functionality')
                ],
                'description' => esc_html__('Choose grid style, default will be propety card v1', 'homey'),
                'default' => 'v_1',
            ]
        );

        $this->add_control(
            'property_ids',
            [
                'label'     => esc_html__( 'Properties IDs', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::TEXT,
                'description'   => esc_html__( 'Enter properties ids comma separated. Ex 12,305,34', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'columns',
            [
                'label'     => esc_html__( 'Columns', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    '3cols'  => esc_html__( '3 Columns', 'houzez-theme-functionality'),
                    '2cols'    => esc_html__( '2 Columns', 'houzez-theme-functionality')
                ],
                'description' => esc_html__('Note: Property card v4 will show only 2 columns', 'homey'),
                'default' => '3cols',
            ]
        );
        
        $this->end_controls_section();

    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.5.6
     * @access protected
     */
    protected function render() {

        $settings = $this->get_settings_for_display();
                
        $args['prop_grid_style'] =  $settings['prop_grid_style'];
        $args['property_ids']     =  $settings['property_ids'];
        $args['columns']     =  $settings['columns'];
       
        if( function_exists( 'houzez_property_by_ids' ) ) {
            echo houzez_property_by_ids( $args );
        }

    }

}

Plugin::instance()->widgets_manager->register_widget_type( new Houzez_Elementor_Property_By_IDs );