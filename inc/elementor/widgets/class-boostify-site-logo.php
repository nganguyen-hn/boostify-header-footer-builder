<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/**
 * Site Logo
 *
 * Elementor widget for Site Logo.
 */
class Boostify_Site_Logo extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'ht-hf-site-logo';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Logo', 'boostify' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-site-logo';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'ht_hf_builder' );
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Logo', 'boostify' ),
			)
		);

		$this->add_control(
			'use',
			[
				'label'   => __( 'Use', 'boostify' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'site',
				'options' => [
					'site'   => 'Site Logo',
					'custom' => 'Custom Logo',
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label'     => __( 'Logo Custom', 'boostify' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => [
					'use' => 'custom',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'        => 'thumbnail',
				'default'     => 'full',
				'label'       => esc_html__( 'Logo Size', 'boostify' ),
				'description' => esc_html__( 'Custom Logo size when selected image.', 'boostify' ),
				'condition'   => [
					'use' => 'custom',
				],
			]
		);

		$this->add_control(
			'align',
			[
				'label'     => esc_html__( 'Align', 'boostify' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
				'selectors' => [
					'{{WRAPPER}} .boostify-menu' => 'justify-content: {{VALUE}};',
				],
				'options'   => [
					'flex-start' => [
						'icon'  => 'eicon-h-align-left',
						'title' => 'Left',
					],
					'center'     => [
						'icon'  => 'eicon-h-align-center',
						'title' => 'Center',
					],
					'flex-end'   => [
						'icon'  => 'eicon-h-align-right',
						'title' => 'Right',
					],
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
			<div class="boostify-site-logo-header">
			<?php
			if ( 'site' === $settings['use'] || empty( $settings['image']['id'] ) ) {

				if ( has_custom_logo() ) :
					the_custom_logo();
				else :
					if ( is_user_logged_in() ) {
						echo esc_html__( 'Please go to customize choose logo for site', 'boostify' );
					} else {
						?>
						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						<?php
					}
				endif;
			} else {

				$url = Elementor\Group_Control_Image_Size::get_attachment_image_src( $settings['image']['id'], 'thumbnail', $settings );
				?>
				<img src="<?php echo esc_url( $url ); ?>" alt="Logo" class="custom-logo">
				<?php

			}

			?>
		</div>
		<?php
	}

}
