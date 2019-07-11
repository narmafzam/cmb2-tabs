<?php

namespace Tab;

class Tab
{
    const VERSION = '1.0.3';

    protected static $instance;

    public function init()
    {
        $this->addAction();
    }

    public function addAction()
    {
        add_action( 'admin_enqueue_scripts', array( $this, 'setupAdminScripts' ) );
        add_action( 'doing_dark_mode', array( $this, 'setupDarkMode' ) );
        add_action( 'cmb2_before_form', array( $this, 'beforeForm' ), 10, 4 );
        add_action( 'cmb2_after_form', array( $this, 'afterForm' ), 10, 4 );
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new Tab();
        }
        return self::$instance;
    }

    public function beforeForm( $cmb_id, $object_id, $object_type, $cmb ) {
        if( $cmb->prop( 'tabs' ) && is_array( $cmb->prop( 'tabs' ) ) ) : ?>
            <div class="cmb-tabs-wrap cmb-tabs-<?php echo ( ( $cmb->prop( 'vertical_tabs' ) ) ? 'vertical' : 'horizontal' ) ?>">
            <div class="cmb-tabs">

                <?php foreach( $cmb->prop( 'tabs' ) as $tab ) :
                    $fields_selector = array();

                    foreach( $tab['fields'] as $tab_field )  :
                        $fields_selector[] = '.' . 'cmb2-id-' . str_replace( '_', '-', sanitize_html_class( $tab_field ) ) . ':not(.cmb2-tab-ignore)';
                    endforeach;

                    $fields_selector = apply_filters( 'cmb2_tabs_tab_fields_selector', $fields_selector, $tab, $cmb_id, $object_id, $object_type, $cmb );
                    $fields_selector = apply_filters( 'cmb2_tabs_tab_' . $tab['id'] . '_fields_selector', $fields_selector, $tab, $cmb_id, $object_id, $object_type, $cmb );
                    ?>

                    <div id="<?php echo $cmb_id . '-tab-' . $tab['id']; ?>" class="cmb-tab" data-fields="<?php echo implode( ', ', $fields_selector ); ?>">

                        <?php if( isset( $tab['icon'] ) && ! empty( $tab['icon'] ) ) :
                            $tab['icon'] = strpos($tab['icon'], 'dashicons') !== false ? 'dashicons ' . $tab['icon'] : $tab['icon']?>
                            <span class="cmb-tab-icon"><i class="<?php echo $tab['icon']; ?>"></i></span>
                        <?php endif; ?>

                        <?php if( isset( $tab['title'] ) && ! empty( $tab['title'] ) ) : ?>
                            <span class="cmb-tab-title"><?php echo $tab['title']; ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

            </div>
        <?php endif;
    }

    public function afterForm( $cmb_id, $object_id, $object_type, $cmb ) {
        if( $cmb->prop( 'tabs' ) && is_array( $cmb->prop( 'tabs' ) ) ) : ?></div><?php endif;
    }

    public function setupAdminScripts() {
        wp_register_script( 'cmb-tabs', $this->getPathUrl(__DIR__ . '/../../asset/js/tabs.js'), array( 'jquery' ), self::VERSION, true );
        wp_enqueue_script( 'cmb-tabs' );

        wp_enqueue_style( 'cmb-tabs', $this->getPathUrl( __DIR__ . '/../../asset/css/tabs.css'), array(), self::VERSION );
        wp_enqueue_style( 'cmb-tabs' );
    }

    public function setupDarkMode() {
        wp_enqueue_style( 'cmb-tabs-dark-mode', $this->getPathUrl( __DIR__ . '/../../asset/css/dark-mode.css'), array(), self::VERSION );
        wp_enqueue_style( 'cmb-tabs-dark-mode' );
    }

    public function getPathUrl($path)
    {
        $url  = '';
        if (defined('WP_SITEURL')) {
            $url = WP_SITEURL ;
        } elseif (function_exists('get_site_url')) {
            $url = get_site_url();
        }
        if (function_exists('get_home_path')) {
            $url = rtrim($url, '/') . '/' . str_replace(get_home_path(), '', realpath($path));
        } else {
            $url = rtrim($url, '/') . str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath($path));
        }
        return $url;
    }
}