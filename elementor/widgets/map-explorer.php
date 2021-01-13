<?php
namespace Map_Explorer_Elementor_Extensions\Widgets;


use \Elementor\Controls_Manager;
use \Elementor\Core\Schemes;
use \Elementor\Group_Control_Typography;
use \Elementor\Widget_Button;
use \ElementorPro\Base\Base_Widget_Trait;
use \ElementorPro\Modules\QueryControl\Module;

class Map_Explorer extends \Elementor\Widget_Base {

	public function get_name() {
		return 'map-explorer';
	}

	public function get_title() {
		return __( 'Map Explorer', 'elementor-pro' );
	}

	/**
	 * Example icons: https://elementor.github.io/elementor-icons/
	 */
	public function get_icon() {
		return 'eicon-map-pin';
	}

	public function get_categories() {
		return [ 'basic' ];
	}

	public function get_keywords() {
		return [ 'map', 'explorer', 'osm' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_icon',
			[
				'label' => __( 'Map', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'mapbox_api_key',
			[
				'label' => __( 'Mapbox API Key', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				// 'default' => __( 'This is the heading', 'elementor-pro' ),
				// 'placeholder' => __( 'Enter your title', 'elementor-pro' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'mapbox_id',
			[
				'label' => __( 'Mapbox ID', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				// 'default' => __( 'This is the heading', 'elementor-pro' ),
				// 'placeholder' => __( 'Enter your title', 'elementor-pro' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'map_attribution',
			[
				'label' => __( 'Map Attribution', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>', 'elementor-pro' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Map', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'size',
			[
				'label' => __( 'Height', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'max' => 100,
						'step' => 1,
					],
					'vh' => [
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'size' => '100',
					'unit' => 'vh',
				],
				'selectors' => [
					'{{WRAPPER}} .map-explorer-wrapper' => 'height: {{SIZE}}{{UNIT}};',
				],
				'size_units' => [ 'px', '%', 'vh' ],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render Map
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// if( empty($settings['product_icon']) ) {
		// 	$settings['product_icon'] = 'Unknown';
		// }

		echo '<div class="map-explorer-wrapper"><div id="map_explorer"></div></div>';

	}

}