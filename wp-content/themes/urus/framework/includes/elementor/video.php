<?php
if( !class_exists('Urus_Elementor_Video')){
	class Urus_Elementor_Video extends Urus_Elementor{
		public $name ='urus-video';
		public $title ='Urus Video';
		public $icon ='eicon-youtube';
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
			$this->start_controls_section(
				'content_section',
				[
					'label' => esc_html__( 'Content', 'urus' ),
				]
			);
			$this->add_control(
				'title',
				[
					'label' => esc_html__( 'Title', 'urus' ),
					'type' => \Elementor\Controls_Manager::TEXT
				]
			);
			$this->add_control(
				'source',
				[
					'label' => esc_html__( 'Source video', 'urus' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'youtube' => esc_html__( 'Youtube', 'urus' ),
						'custom' => esc_html__( 'Self Hosted', 'urus' ),
					],
					'default' => 'youtube',
				]
			);
			$this->add_control(
				'source_type',
				[
					'label' => esc_html__( 'Type of video', 'urus' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'video/mp4' => esc_html__( 'MP4', 'urus' ),
						'video/webm' => esc_html__( 'WebM', 'urus' ),
						'video/ogg' => esc_html__( 'Ogg', 'urus' ),
					],
					'default' => 'video/mp4',
					'condition' => array(
						'source' => 'custom'
					),
				]
			);
			$this->add_control(
				'url',
				[
					'label' => esc_html__( 'External link', 'urus' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'description' => esc_html__("Example for external link: http://example.com/video.mp4.  Example for youtube link: https://www.youtube.com/watch?v=KREnGJ1234", 'urus')
				]
			);
			$this->add_control(
				'background_img',
				[
					'label' => __( 'Choose Image', 'urus' ),
					'type' => \Elementor\Controls_Manager::MEDIA,
					'dynamic' => [
						'active' => true,
					],
					'default' => [
						'url' => \Elementor\Utils::get_placeholder_image_src(),
					],
				]
			);
			$this->add_control(
				'width',
				[
					'label' => esc_html__( 'Video width', 'urus' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'description' => esc_html__("Example: 100%, 1000px, 1000em, 1000rem (area unit of css)", "urus"),
				]
			);
			$this->add_control(
				'show_control',
				[
					'label' => __( 'Show control video', 'urus' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'default' => esc_html("Control default", 'urus'),
						'custom' => esc_html("Control custom", 'urus')
					)
				]
			);
			$this->add_control(
				'video_loop',
				[
					'label' => __( 'Video loop', 'urus' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'On', 'urus' ),
					'label_off' => __( 'Off', 'urus' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			$this->end_controls_section();
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
			$atts = $this->get_settings_for_display();
			$css_class    = array( 'urus-video' );
			$ytb_video = (isset($atts['source']) && $atts['source'] == "youtube") ? true : false;
			$show_control = "&controls=1";
			if (isset($atts['show_control'])){
				if ($atts['show_control'] != "default"){
					$show_control = "&controls=0";
				}
			}
			$video_width = (isset($atts['width']) && $atts['width']) ? $atts['width'] : 500;
			$unit = preg_replace('/\d/','', $video_width);
			$video_width = preg_replace('/\D/','', $video_width);
			$unit = empty($unit) ? "px" : $unit;
			$video_height = "height=". round($video_width / 1.77, 2).$unit;
			$video_height = is_numeric(strpos($unit, "%")) ? "" : $video_height;
			$video_width = "width=".$video_width.$unit;
			$image = isset($atts['background_img']) && $atts['background_img'] ? Urus_Helper::resize_image($atts['background_img']['id'],false,false,false,false) : "";
			if ($ytb_video && (is_numeric(strpos($atts['url'], "//youtube.com")) || is_numeric(strpos($atts['url'], "//www.youtube.com")))){
				$video_loop = (isset($atts['video_loop']) && $atts['video_loop']) ? "&loop=1" : "";
				parse_str( parse_url( $atts['url'], PHP_URL_QUERY ), $array_url_params );
				$ytb_video_id = $array_url_params['v'];
				$atts['url'] = "https://www.youtube.com/embed/".$ytb_video_id."?enablejsapi=1&cc_load_policy=0&modestbranding=0&showinfo=0&origin=".get_site_url().$show_control.$video_loop;
			}
			else{
				$show_control = $atts['show_control'] != "default" ? "" : "controls";
				$video_loop = (isset($atts['video_loop']) && $atts['video_loop'] != "yes") ? "loop" : "";
			}
			?>
			<div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
				<?php if ($atts['title']):?>
					<h3 class="block-title"><?php echo  esc_html($atts['title']);?></h3>
				<?php endif;?>
				<div class="urus_shortcode_video" <?php echo esc_attr($video_width) ?>>
					<?php if($atts['url']): ?>
						<?php if ($ytb_video == true): ?>
							<iframe class="player" frameborder="0" data-videoId="<?php echo isset($ytb_video_id) ? esc_attr($ytb_video_id) : ""; ?>" src="<?php echo esc_url($atts['url']) ?>" <?php echo esc_attr($video_width." ".$video_height) ?> allowfullscreen></iframe>
						<?php else: ?>
							<video <?php echo esc_attr($video_width." ".$video_height." ".$show_control." ".$video_loop) ?> preload="auto">
								<source src="<?php echo esc_url($atts['url']) ?>" type="<?php echo esc_attr($atts['source_type']) ?>">
							</video>
						<?php endif; ?>
					<?php endif; ?>
					<?php if (!empty($image) && isset($image['img'])): ?>
						<a href="javascript:void(0);" class="video-background">
							<?php echo Urus_Helper::escaped_html($image['img']);?>
						</a>
					<?php endif;  ?>
					<?php if($atts['show_control'] != "default"): ?>
						<div class="buttons">
							<button class="play"><?php echo esc_html('Play', 'urus') ?></button>
							<button class="pause"><?php echo esc_html('Pause', 'urus') ?></button>
						</div>
					<?php endif ?>
				</div>
			</div>
		<?php
		}
	}
}