<?php
/**
 * Header Sticky extension class.

 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class Ag_Elementor_Header_Sticky {

	/*
	 * Instance of this class
	 */
	private static $instance = null;


	public function __construct() {

		// Add new controls to advanced tab globally
		add_action( 'elementor/element/after_section_end', array( $this, 'register' ), 25, 3 );
		add_action( 'elementor/element/after_section_end', array( $this, 'register' ), 25, 3 );

	}

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function register( $element, $section_id ) {

		if ( 'section_effects' !== $section_id ) {
			return;
		}

		if ( in_array( $element->get_name(), array( 'section', 'column', 'common', 'container' ), true ) ) {

			$element->start_controls_section(
				'section_ag_header_sticky',
				array(
					'label' => __( 'Header Sticky', 'ag-theme-builder' ),
					'tab'   => Controls_Manager::TAB_ADVANCED,
				)
			);

			$element->add_control(
				'ag_header_by_default',
				array(
					'label'        => __( 'Default', 'ag-theme-builder' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Hide', 'ag-theme-builder' ),
					'label_off'    => __( 'Show', 'ag-theme-builder' ),
					'return_value' => 'none',
					'selectors'    => array(
						'header.xtb-header-sticky:not(.xtb-appear) {{WRAPPER}}' => 'display: none;',
					),
				)
			);

			$element->add_control(
				'ag_header_on_sticky',
				array(
					'label'        => __( 'Sticky', 'ag-theme-builder' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Hide', 'ag-theme-builder' ),
					'label_off'    => __( 'Show', 'ag-theme-builder' ),
					'return_value' => 'none',
					'selectors'    => array(
						'header.xtb-header-sticky.xtb-appear {{WRAPPER}}' => 'display: none;',
					),
				)
			);

			if ( in_array( $element->get_name(), array( 'section', 'container' ), true ) ) {
				$element->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'      => 'ag_header_sticky_background',
						'label'     => __( 'Background', 'ag-theme-builder' ),
						'types'     => array( 'classic', 'gradient' ),
						'exclude'   => array( 'image' ),
						'selector'  => '.xtb-header-sticky.xtb-appear {{WRAPPER}}',
						'separator' => 'before',
					)
				);

				$element->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'ag_header_sticky_shadow',
						'label'    => __( 'Box Shadow', 'ag-theme-builder' ),
						'selector' => '.xtb-header-sticky.xtb-appear {{WRAPPER}}',
					)
				);

				$element->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'     => 'ag_header_sticky_border',
						'label'    => __( 'Border', 'ag-theme-builder' ),
						'selector' => '.xtb-header-sticky.xtb-appear {{WRAPPER}}',
					)
				);

				$element->add_responsive_control(
					'ag_header_sticky_padding',
					array(
						'label'      => __( 'Padding', 'ag-theme-builder' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%', 'em' ),
						'selectors'  => array(
							'.xtb-header-sticky.xtb-appear {{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$element->add_responsive_control(
					'ag_header_sticky_margin',
					array(
						'label'      => __( 'Margin', 'ag-theme-builder' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%', 'em' ),
						'selectors'  => array(
							'.xtb-header-sticky.xtb-appear {{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

			}

			$element->end_controls_section();
		}
	}
}

Ag_Elementor_Header_Sticky::get_instance();
