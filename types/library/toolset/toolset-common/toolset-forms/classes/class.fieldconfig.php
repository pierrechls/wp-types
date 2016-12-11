<?php

/**
 *
 *
 */
if (!class_exists("FieldConfig")) {

    /**
     * Description of FieldConfig
     *
     * @author ontheGoSystem
     */
    class FieldConfig {

        private $id;
        private $name = "cred[post_title]";
        private $value;
        private $type = 'textfield';
        private $title = 'Post title';
        private $repetitive = false;
        private $display = '';
        private $description = '';
        private $config = array();
        private $options = array();
        private $default_value = '';
        private $validation = array();
        private $attr;
        private $add_time = false;
        private $form_settings;

        public function __construct() {
            
        }

        public function getForm_settings() {
            return $this->form_settings;
        }

        public function setForm_settings($form_settings) {
            $this->form_settings = $form_settings;
        }

        public function setRepetitive($repetitive) {
            $this->repetitive = $repetitive;
        }

        public function isRepetitive() {
            return $this->repetitive;
        }

        public function set_add_time($addtime) {
            $this->add_time = $addtime;
        }

        public function setAttr($attr) {
            $this->attr = $attr;
        }

        public function getAttr() {
            return $this->attr;
        }

        public function setDefaultValue($value) {
            $this->default_value = $value;
        }       
 
        public function setOptions($name, $type, $values, $attrs) {
            $arr = array();
            switch ($type) {
                case 'checkbox':
                    $arr = $attrs;
                    break;
                case 'checkboxes':
                    $actual_titles = isset($attrs['actual_titles']) ? $attrs['actual_titles'] : array();
                    foreach ($actual_titles as $refvalue => $title) {
                        $_value = $attrs['actual_values'][$refvalue];
                        $arr[$refvalue] = array('value' => $refvalue, 'title' => $title, 'name' => $name, 'data-value' => $_value);
                        if (in_array($refvalue, $attrs['default'])) {
                            $arr[$refvalue]['checked'] = true;
                        }
                    }
                    break;
                case 'select':
                    $values = isset($attrs['options']) ? $attrs['options'] : array();
                    foreach ($values as $refvalue => $title) {
                        $arr[$refvalue] = array(
                            'value' => $refvalue,
                            'title' => $title,
                            'types-value' => $attrs['actual_options'][$refvalue],
                        );
                    }
                    break;
                case 'radios':
                    $actual_titles = isset($attrs['actual_titles']) ? $attrs['actual_titles'] : array();
                    foreach ($actual_titles as $refvalue => $title) {
                        $arr[$refvalue] = array(
                            'value' => $attrs['actual_values'][$refvalue],
                            'title' => $title,
                            'checked' => false,
                            'name' => $refvalue,
                            'types-value' => $refvalue,
                        );
                    }
                    break;
                default:
                    return;
                    break;
            }
            $this->options = $arr;
        }

        public function createConfig() {
            $base_name = "cred";
            $this->config = array(
                'id' => $this->getId(),
                'type' => $this->getType(),
                'title' => $this->getTitle(),
                'options' => $this->getOptions(),
                'default_value' => $this->getDefaultValue(),
                'description' => $this->getDescription(),
                'repetitive' => $this->isRepetitive(),
                /* 'name' => $base_name."[".$this->getType()."]", */
                'name' => $this->getName(),
                'value' => $this->getValue(),
                'add_time' => $this->getAddTime(),
                'validation' => array(),
                'display' => $this->getDisplay(),
                'attribute' => $this->getAttr(),
                'form_settings' => $this->getForm_settings()
            );
            return $this->config;
        }

        public function getAddTime() {
            return $this->add_time;
        }

        public function getType() {
            return $this->type;
        }

        public function setType($type) {
            $this->type = $type;
        }

        public function getOptions() {
            return $this->options;
        }

        public function getDefaultValue() {
            return $this->default_value;
        }

        public function getTitle() {
            return $this->title;
        }

        public function getDisplay() {
            return $this->display;
        }

        public function setTitle($title) {
            $this->title = $title;
        }

        public function getDescription() {
            return $this->description;
        }

        public function setDescription($description) {
            $this->description = $description;
        }

        public function getName() {
            return $this->name;
        }

        public function setName($name) {
            $this->name = $name;
        }

        public function getValue() {
            return $this->value;
        }

        public function setValue($value) {
            $this->value = $value;
        }

        public function getValidation() {
            return !empty($this->validation) ? $this->validation : array();
        }

        public function setValidation($validation) {
            $this->validation = $validation;
        }

        public function getConfig() {
            return $this->config;
        }

        public function setConfig($config) {
            $this->config = $config;
        }

        public function getId() {
            return $this->id;
        }

        public function setId($id) {
            $this->id = $id;
        }

        public function setDisplay($display) {
            $this->display = $display;
        }

    }

}

