<?php
/**
 *
 *
 */
require_once 'class.field_factory.php';

/**
 * Description of class
 *
 * @author Srdjan
 */
class WPToolset_Field_Colorpicker extends FieldFactory
{
    public function init()
    {
        if ( !is_admin() ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script(
                'iris',
                admin_url( 'js/iris.min.js' ),
                array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
                false,
                1
            );
            wp_enqueue_script(
                'wp-color-picker',
                admin_url( 'js/color-picker.min.js' ),
                array( 'iris' ),
                false,
                1
            );
            $colorpicker_l10n = array(
                'clear' => __( 'Clear' ),
                'defaultString' => __( 'Default', 'wpv-views' ),
                'pick' => __( 'Select', 'wpv-views' )." Color"
            );
            wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
        }
        wp_register_script(
            'wptoolset-field-colorpicker',
            WPTOOLSET_FORMS_RELPATH . '/js/colorpicker.js',
            array('iris'),
            WPTOOLSET_FORMS_VERSION,
            true
        );
        wp_enqueue_script( 'wptoolset-field-colorpicker' );
        $this->set_placeholder_as_attribute();
    }

    static public function registerScripts()
    {
    }

    public function enqueueScripts()
    {

    }

    public function addTypeValidation($validation) {
        $validation['hexadecimal'] = array(
            'args' => array(
                'hexadecimal'
            ),
            'message' => __('Please use a valid hexadecimal value.', 'wpv-views' ),
        );
        return $validation;
    }

    public function metaform()
    {
        $validation = $this->getValidationData();
        $validation = $this->addTypeValidation($validation);
        $this->setValidationData($validation);

        $attributes = $this->getAttr();
        if ( isset($attributes['class'] ) ) {
            $attributes['class'] .= ' ';
        } else {
            $attributes['class'] = '';
        }
        $attributes['class'] = 'js-wpt-colorpicker';

        $form = array();
        $form['name'] = array(
            '#type'			=> 'textfield',
            '#title'		=> $this->getTitle(),
            '#description'	=> $this->getDescription(),
            '#value'		=> $this->getValue(),
            '#name'			=> $this->getName(),
            '#attributes'	=> $attributes,
            '#validate'		=> $validation,
            '#after'		=> '',
            '#repetitive'	=> $this->isRepetitive(),
			'wpml_action'	=> $this->getWPMLAction(),
        );
        return $form;
    }

    public static function filterValidationValue($value)
    {
        if ( isset( $value['datepicker'] ) ) {
            return $value['datepicker'];
        }
        return $value;
    }
}
