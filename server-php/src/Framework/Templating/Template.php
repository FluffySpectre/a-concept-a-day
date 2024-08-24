<?php

namespace DA\Framework\Templating;

/**
 * Provides functions for a simple template engine
 *
 * @author Björn Bosse
 */
class Template {
    protected $file;
    protected $values = array();
    protected $langValues = array();

    /**
     * Constructor
     *
     * @param string $template The template file
     * @param array $values Assoc-Array of Keys and Values
     */
    public function __construct($template, $values = array()) {
        $this->file = $template;
        $this->values = $values;
    }

    /**
     * Sets a variable which will be replaced in the template
     *
     * @param string $key   The name of the variable
     * @param string $value The value of the variable
     */
    public function set($key, $value) {
        $this->values[$key] = $value;
    }

    /**
     * Adds a JavaScript to the template
     * @param string $js JavaScript source
     * @param string $tag  Tag to replace
     */
    public function addJavaScript($js, $tag = "script") {
        $this->set($tag, $js);
    }

    /**
     * Adds a JavaScript file to the template
     * @param string $file  Filename of the template
     * @param string $tag   Tag to replace
     */
    public function addJavaScriptFile($file, $tag = "script") {
        $js = file_get_contents($file);
        $js = $this->convertToUTF8($js);
        $this->set($tag, $js);
    }
    
    /**
     * Adds CSS to the template
     * @param string $js Stylesheet source
     * @param string $tag  Tag to replace
     */
    public function addCSS($css, $tag = "style") {
        $this->set($tag, $css);
    }

    /**
     * Adds a CSS file to the template
     * @param string $file  Filename of the template
     * @param string $tag   Tag to replace
     */
    public function addCSSFile($file, $tag = "style") {
        $css = file_get_contents($file);
        $css = $this->convertToUTF8($css);
        $this->set($tag, $css);
    }
    
    /**
     * Sets the language values of the template
     * @param array $strings The language strings
     */
    public function setLanguage($strings) {
        $this->langValues = $strings;
    }

    /**
     * Renders the template
     *
     * @return string The rendered template
     */
    public function output() {
        if (!file_exists($this->file)) {
            return "Error loading template file ($this->file).";
        }
        $output = file_get_contents($this->file);
        $output = $this->convertToUTF8($output);

        foreach ($this->values as $key => $value) {
            $tagToReplace = "{@$key}";
            $output = str_replace($tagToReplace, $value, $output);
        }

        foreach ($this->langValues as $key => $value) {
            $tagToReplace = "{@lang_$key}";
            $output = str_replace($tagToReplace, $value, $output);
        }

        return $output;
    }

    /**
     * Merges two or more templates together
     *
     * @param array $templates The array of templates to merge
     * @param string $separator  The separator between the templates
     * @return string          The merged and rendered templates
     */
    public static function merge($templates, $separator = "\n") {
        $output = "";

        foreach ($templates as $template) {
            $content = (get_class($template) !== "Template") ? "Error, incorrect type - expected Template." : $template->output();
            $output .= $content . $separator;
        }

        return $output;
    }

    /**
     * Converts an ISO-8859-1 string into an UTF-8 string. This function basically
     * mimics the functionality of the deprecated utf8_encode()-function
     * 
     * @param string $string The string to convert
     * @return bool|string Returns the converted UTF-8 string or false, if the conversion failed
     */
    private function convertToUTF8($string) {
        return iconv("ISO-8859-1", "UTF-8", $string);
    }
}
