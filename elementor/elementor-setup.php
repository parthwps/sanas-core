<?php
if (!defined('ABSPATH')) {
    exit;
}

global $void_widgets;
$void_widgets = array_map('basename', glob(dirname(__FILE__) . '/widgets/*.php'));
global $void_sections;
$void_sections = array_map('basename', glob(dirname(__FILE__) . '/sections/*.php'));

add_action('elementor/editor/after_enqueue_scripts', 'sanas_core_editor_scripts');
function sanas_core_editor_scripts() {
    // Enqueue your scripts here
}

add_action('elementor/frontend/after_register_scripts', 'sanas_core_register_script');
function sanas_core_register_script() {
    // Register your scripts here
}

function sanas_elementor_widget_categories($elements_manager) {
    $elements_manager->add_category(
        'sanas',
        [
            'title' => esc_html__('Sanas Elements', 'sanas-core'),
        ], 2
    );
}
add_action('elementor/elements/categories_registered', 'sanas_elementor_widget_categories');

class SanasElementorWidget {
    private static $instance = null;

    public static function get_instance() {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function init() {
        add_action('elementor/widgets/widgets_registered', array($this, 'sanas_elementor_widget'));
    }

    public function sanas_elementor_widget() {
        global $void_widgets;
        if (defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')) {
            foreach ($void_widgets as $key => $value) {
                require_once __DIR__ . '/widgets/' . $value;
            }
        }
    }
}

SanasElementorWidget::get_instance()->init();
