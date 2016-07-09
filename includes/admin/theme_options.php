<?php
/*
 *  @license © 2014, Amin Paks, T. (514) 441-2413, W. http://www.aminpaks.com
 */

class _theme_options {

    protected $prefix;
    protected $sections;
    protected $sections_title;
    protected static $instance = null;

    function __construct($param = null) {

        $this->prefix         = '_theme';
        $this->sections       = array( 'general' => array() );
        $this->sections_title = array( 'general' => 'General' );

        add_action('admin_menu', array( $this, 'admin_menu_init' ));
    }

    static function get_instance() {
        if (null === self::$instance) {
            return self::$instance = new self;
        }
        return self::$instance;
    }

    public function __get($id) {
        return $this->get_option($id);
    }

    public function get_option($id, $section = 'general') {
        if (isset($this->sections[ $section ][ $id ])) {

            return get_option($this->get_prefix($id, $section));
        }

        return false;
    }

    public function get_options($section) {
        if (isset($this->sections[ $section ])) {

            $return = array();

            foreach ($this->sections[ $section ] as $id => $data) {

                $return[ $id ]          = $data;
                $return[ $id ][ 'value' ] = get_option($this->get_prefix($id, $section));
            }

            if (count($return)) {

                return $return;
            }
            else {
                return false;
            }
        }
    }

    public function render_options_page() {
        ?>
        <div class="wrap1">
            <h2>Theme Special Options</h2>
            <form action="options.php" method="post">
                <?php settings_fields($this->get_prefix('-option-group')); ?>
                <?php do_settings_sections($this->get_prefix()); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function admin_menu_init() {
        $this->add_options_page();

        add_action('admin_init', array( $this, 'register_admin_settings' ));
    }

    public function add_options_page() {
        add_options_page('Theme Special Options', 'ThemeOptions', 'manage_options', $this->get_prefix(),
        array(
            $this, 'render_options_page' ));
    }

    public function section_callback($args) {
        
    }

    public function field_callback($args) {
        $section = $args[ 'section' ];
        $name    = esc_attr($args[ 'name' ]);
        $value   = $this->get_option($name, $section) !== false ? $this->get_option($name, $section) : esc_attr($args[ 'default_value' ]);
        $type    = esc_attr($args[ 'type' ]);

        $attrs = '';

        if (isset($args[ 'attrs' ]) && gettype($args[ 'attrs' ]) === 'array') {
            foreach ($args[ 'attrs' ] as $key => $val) {

                if (gettype($val) === 'object' || gettype($val) === 'array') {
                    continue;
                }

                if (empty($val)) {
                    continue;
                }

                $attrs .= " $key=\"$val\"";
            }
        }

        switch ($type) {
            case 'text':

                echo sprintf('<input name="%s" type="%s" value="%s"%s />', $this->get_prefix($name, $section), $type,
                $value, $attrs);

                break;

            case 'select':

                echo sprintf('<input name="%s" type="%s" value="%s"%s />', $this->get_prefix($name, $section), $type,
                $value, $attrs);

                break;

            case 'editable':

                echo sprintf('<div name="%s" contenteditable="true" %s value="%s" ></div>',
                $this->get_prefix($name, $section), $type, $attrs, $value);

                break;

            case 'textarea':

                echo sprintf('<textarea name="%s"%s >%s</textarea>', $this->get_prefix($name, $section), $attrs, $value);

                break;

            default:
                break;
        }
    }

    public function add_section($id, $title) {
        $this->sections[ $id ]       = array();
        $this->sections_title[ $id ] = $title;
    }

    public function add_field($id, $title, $default_value = '', $type = 'text', $section = 'general', $args = null,
    $extra = null) {
        $this->sections[ $section ][ $id ] = array(
            'name'          => $id,
            'title'         => $title,
            'type'          => $type,
            'section'       => $section,
            'default_value' => $default_value,
            'attrs'         => $args,
            'extra'         => $extra,
        );
    }

    protected function get_prefix($id = '', $section = '', $deliminor = '_') {
        if (empty($id)) {
            return $this->prefix;
        }
        elseif (empty($section)) {
            return sprintf('%s%s', $this->prefix, $id);
        }
        else {
            return sprintf('%s%4$s%s%4$s%s', $this->prefix, $section, $id, $deliminor);
        }
    }

    public function register_admin_settings() {
        foreach ($this->sections as $section_id => $section) {

            if (count($section) === 0) {
                continue;
            }

            add_settings_section($section_id, $this->sections_title[ $section_id ],
            array(
                $this, 'section_callback' ), $this->get_prefix());

            foreach ($section as $id => $args) {
                register_setting($this->get_prefix('-option-group'), $this->get_prefix($id, $section_id));

                add_settings_field($this->get_prefix($id, $section_id), $args[ 'title' ],
                array(
                    $this, 'field_callback' ), $this->get_prefix(), $section_id, $args);
            }
        }
    }

}
