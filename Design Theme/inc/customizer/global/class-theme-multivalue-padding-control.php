<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Theme_Multivalue_Padding_Control' ) ) {

    class Theme_Multivalue_Padding_Control extends WP_Customize_Control {
        public $type = 'theme-multivalue-padding';

        public function enqueue() {
            wp_enqueue_style( 'theme-multivalue-padding-control', get_template_directory_uri() . '/inc/customizer/controls/multivalue-padding-control.css', [], '1.0' );
            wp_enqueue_script( 'theme-multivalue-padding-control', get_template_directory_uri() . '/inc/customizer/controls/multivalue-padding-control.js', ['jquery', 'customize-controls'], '1.0', true );
        }

        public function render_content() {
            if ( empty( $this->label ) ) return;
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            </label>

            <div class="theme-padding-wrapper" data-linked="true">
                <div class="theme-padding-grid">
                    <table>
                        <tr>
                            <td><input type="number" class="padding-top" placeholder="Top"></td>
                            <td><button type="button" class="link-toggle linked" title="Unlink values">🔗</button></td>
                            <td><input type="number" class="padding-right" placeholder="Right"></td>
                        </tr>
                        <tr>
                            <td><input type="number" class="padding-bottom" placeholder="Bottom"></td>
                            <td colspan="2"><input type="number" class="padding-left" placeholder="Left"></td>
                        </tr>
                    </table>

                    <select class="padding-unit">
                        <option value="px">px</option>
                        <option value="%">%</option>
                        <option value="em">em</option>
                        <option value="rem">rem</option>
                    </select>
                </div>
                <input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" />
            </div>
            <?php
        }
    }
}
