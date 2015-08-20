<?php

class CrackerJackDataExtension extends DataExtension {
    const DefaultTabName = 'Root.Main';

    private static $enabled = true;

    /**
     * @return DataObject
     */
    public function __invoke() {
        return $this->owner;
    }

    public static function enabled() {
        return CrackerjackModule::get_config_setting(get_called_class(), 'enabled');
    }

    public static function enable() {
        Config::inst()->update(get_called_class(), 'enabled', true);
    }

    public static function disable() {
        Config::inst()->update(get_called_class(), 'enabled', false);
    }



    /**
     * Return a configuration setting optionally filtered by filterCallback.
     * @param               $name
     * @param null          $key
     * @param callable|null $filterCallback suitable for passing to array_filter
     * @return array|null|string
     */
    public static function own_config($name, $key = null, Callable $filterCallback = null) {
        $value = CrackerjackModule::get_config_setting(
            get_called_class(),
            $name,
            $key,
            Config::UNINHERITED
        );
        if (is_array($value) && $filterCallback) {
            return array_filter(
                $value,
                $filterCallback
            );
        }
        return $value;
    }

    public static function get_config_setting($name, $key = null, $options = null) {
        return CrackerjackModule::get_config_setting(get_called_class(), $name, $key, $options);
    }


    /**
     * Add a control to tab from config.class_tab_names depending on the class name of the extended object.
     *
     * @return mixed
     */
/* UNTESTED
    public function classTabName() {
        $tabName = CrackerjackModule::get_config_setting(get_called_class(), 'tab_name');

        // might have per-class/per-field tab name for the field
        $multipleNames = CrackerjackModule::get_config_setting(
            get_called_class(),
            'class_tab_names'
        ) ?: [];

        $ownerClass = get_class($this->owner);

        return CrackerjackModule::detokenise(
            $multipleNames
                ? (isset($multipleNames[$ownerClass])
                ? $multipleNames[$ownerClass]
                : $tabName)
                : $tabName,
            array(
                'id' => $this()->ID,
                'title' => $this()->Title,
                'class' => get_class(),
                'singular' => $this()->i18n_singular_name(),
                'plural' => $this()->i18n_plural_name()
            )
        );
    }
*/
    public function fieldTabName($fieldName) {
        $tabName = CrackerjackModule::get_config_setting(get_called_class(), 'tab_name') ?: self::DefaultTabName;

        // might have per-field tab name for the field
        $multipleNames = CrackerjackModule::get_config_setting(get_called_class(), 'field_tab_names') ?: [];

        return CrackerJackUtils::detokenise(
            $multipleNames
                ? (isset($multipleNames[$fieldName])
                    ? $multipleNames[$fieldName]
                    : $tabName)
                : $tabName,
            $this->metaData()
        );
    }

    public function fieldLabel($fieldName, $default = '') {
        $label = CrackerjackModule::get_localised_config_string(
            get_class(),
            $fieldName,
            $default ?: $fieldName,
            $this->metaData()
        );
        return CrackerJackUtils::decamel($label);
    }

    protected function metaData() {
        return array(
            'id' => $this()->ID,
            'title' => $this()->Title,
            'class' => get_class(),
            'singular' => $this()->i18n_singular_name(),
            'plural' => $this()->i18n_plural_name()
        );
    }

}
