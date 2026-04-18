<?php
if( !class_exists('Urus_Elementor_Footer_Box')){
	class Urus_Elementor_Footer_Box extends Urus_Elementor{
		public $name ='footer-box';
		public $title ='Footer Box';
		public $icon ='eicon-columns';
		/**
		 * Register the widget controls.
		 *
		 * Adds different input fields to allow the user to change and customize the widget settings.
		 *
		 * @since 1.0.0
		 *
		 * @access protected
		 */
		protected function _register_controls() {

		}

		/**
		 * Render the widget output on the frontend.
		 *
		 * Written in PHP and used to generate the final HTML.
		 *
		 * @since 1.0.0
		 *
		 * @access protected
		 */
		protected function render() {
			do_action('urus_elementor_widget_footer');
		}

	}
}
