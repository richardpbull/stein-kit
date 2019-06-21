<?php
/**
 * Instagram widget.
 *
 * @package Stein Kit
 * @since   1.0.0
 */

defined('ABSPATH') or die('Cheatin\' Uh?');

if (! class_exists('Stein_Toolkit_Instagram_Widget') && class_exists('WP_Widget'))
{
    class Stein_Toolkit_Instagram_Widget extends WP_Widget
    {
        /**
         * The class constructor.
         * 
         * @since  1.0.0
         * @access protected
         * @return void
         */
        public function __construct()
        {
            parent::__construct('stein_toolkit_instagram', esc_html__('Instagram', 'stein-kit'), array(
                'classname' => 'widget_stein_toolkit_instagram',
                'description' => esc_html__('Displays Instagram feed by username.', 'stein-kit'),
            ));
        }

        /**
         * Widget default args.
         * 
         * @since  1.0.0
         * @access protected
         * @return void
         */
        public function defaults()
        {
            return apply_filters('stein_toolkit_instagram_widget_defaults', array(
                'title' => null,
                'username' => null,
                'columns' => 3,
                'limit' => 9,
                'button_text' => null,
                'button_icon' => false,
            ));
        }

        /**
         * Update widget backend settings.
         * 
         * @since  1.0.0
         * @access protected
         * @param  array $new
         * @param  array $instance
         * @return array
         */
        public function update($new, $old)
        {
            $instance = (array) $old;
            $new = wp_parse_args((array) $new, $this->defaults());

            $instance['title'] = sanitize_text_field($new['title']);
            $instance['username'] = sanitize_text_field($new['username']);
            $instance['limit'] = absint($new['limit']);
            $instance['columns'] = absint($new['columns']);
            $instance['button_text'] = sanitize_text_field($new['button_text']);
            $instance['button_icon'] = ! empty($new['button_icon']) ? true : false;

            if ($instance['columns'] > 10) {
                $instance['columns'] = 10;
            }

            if ($instance['columns'] < 1) {
                $instance['columns'] = 1;
            }

            if ($instance['limit'] > 12) {
                $instance['limit'] = 12;
            }

            if ($instance['limit'] < 1) {
                $instance['limit'] = 1;
            }

            return $instance;
        }

        /**
         * Outputs the settings update form.
         * 
         * @since  1.0.0
         * @access protected
         * @param  array $instance
         * @return void
         */
        public function form($instance)
        {
            $instance = wp_parse_args((array) $instance, $this->defaults());
            $columns = array_fill(1, 12, null);

            foreach ($columns as $key => $value) {
                $columns[$key] = wp_sprintf(_n('%s column', '%s columns', $key, 'stein-kit'), $key);
            }
            ?>
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                        <?php esc_html_e('Title:', 'stein-kit'); ?>
                    </label>
                    
                    <input id="<?php echo esc_attr($this->get_field_id('title')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>">
                </p>

                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('username')); ?>">
                        <?php esc_html_e('Username:', 'stein-kit'); ?>
                    </label>
                    
                    <input id="<?php echo esc_attr($this->get_field_id('username')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('username')); ?>" type="text" value="<?php echo esc_attr($instance['username']); ?>">
                </p>

                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>">
                        <?php esc_html_e('Limit:', 'stein-kit'); ?>
                    </label>
                    
                    <input id="<?php echo esc_attr($this->get_field_id('limit')); ?>" class="tiny-text" name="<?php echo esc_attr($this->get_field_name('limit')); ?>" type="number" min="1" max="12" value="<?php echo esc_attr($instance['limit']); ?>">
                </p>

                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('columns')); ?>">
                        <?php esc_html_e('Columns:', 'stein-kit'); ?>
                    </label>

                    <select id="<?php echo esc_attr($this->get_field_id('columns')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('columns')); ?>">
                        <?php foreach($columns as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>"<?php selected($instance['columns'] == $value) ;?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('button_text')); ?>">
                        <?php esc_html_e('Button text:', 'stein-kit'); ?>
                    </label>
                    
                    <input id="<?php echo esc_attr($this->get_field_id('button_text')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('button_text')); ?>" type="text" value="<?php echo esc_attr($instance['button_text']); ?>">
                </p>

                <p>
                    <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('button_icon')); ?>" name="<?php echo esc_attr($this->get_field_name('button_icon')); ?>"<?php checked($instance['button_icon']); ?>>
                    <label for="<?php echo esc_attr($this->get_field_id('button_icon')); ?>"><?php esc_html_e('Enable button icon?', 'stein-kit'); ?></label>
                </p>
            <?php
        }

        /**
         * Outputs the widget content,
         * 
         * @since  1.0.0
         * @access protected
         * @param  array $args
         * @param  array $instance
         * @return void
         */
        public function widget($args, $instance)
        {
            $instance = wp_parse_args((array) $instance, $this->defaults());
            
            echo apply_filters('before_widget', $args['before_widget'], $instance, $this->id_base);

            if ($title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base)) {
                echo apply_filters('before_title', $args['before_title'], $instance, $this->id_base);
                echo esc_html($title);
                echo apply_filters('after_title', $args['after_title'], $instance, $this->id_base);
            }

            $posts = (new Stein_Toolkit_Instagram($instance['username']))->posts();
            $button_text = $instance['button_text'];

            if ($button_text && ! empty($instance['button_icon'])) {
                $button_text = "<i class=\"si si-instagram tw-mr-2\"></i>{$button_text}";
            }
            
            if (! empty($posts)) {
                $posts = array_slice($posts, 0, $instance['limit']);
                ?>
                    <ul class="instagram-items tw-list-reset tw-flex tw-flex-wrap tw-grid tw-grid-columns-<?php echo esc_attr($instance['columns']); ?>" style="grid-gap: 8px;">
                        <?php foreach($posts as $post) : ?>
                            <li class="instagram-item tw-flex-auto">
                                <a href="<?php echo esc_url($post['url']); ?>" class="instagram-image tw-block tw-bg-alt tw-relative tw-aspect-ratio-1/1 hover_tw-opacity-85 tw-transition-opacity tw-transition-duration-200">
                                    <?php
                                    $srcset = array();

                                    foreach ($post['thumbnails'] as $width => $thumbnail) {
                                        $srcset[] = "{$thumbnail} {$width}w";
                                    }
                                    ?>
                                    <img class="tw-object-cover tw-absolute tw-left-0 tw-top-0 tw-w-full tw-h-full lazyload" src="<?php echo esc_url($post['thumbnails']['150']); ?>" data-srcset="<?php echo esc_attr(implode(', ', $srcset)); ?>" sizes="auto" alt="<?php echo esc_attr($post['id']); ?>">
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <?php if (! empty($button_text)) : ?>
                    <a href="<?php echo esc_url('https://www.instagram.com/' . $instance['username']); ?>" class="btn btn-accent tw-w-full tw-mt-6"><?php echo do_shortcode($button_text); ?></a>
                    <?php endif; ?>
                <?php
            } else {
                echo wp_sprintf(
                    '<p class="tw-mb-0">%s</p>',
                    esc_html__('No data available.', 'stein-kit')
                );
            }
            
            echo apply_filters('after_widget', $args['after_widget'], $instance, $this->id_base);
        }
    }
}

/**
 * Register the widget.
 *
 * @package Stein Kit
 * @since   1.0.0
 */
add_action('widgets_init', function () {
    register_widget('Stein_Toolkit_Instagram_Widget');
});