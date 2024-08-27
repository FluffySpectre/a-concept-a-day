<?php

namespace DA\Framework\Templating;

/**
 * Provides functions for a simple template engine
 *
 * @author Björn Bosse
 */
class TemplateRenderer {
    protected $viewDir;
    protected $viewFile;
    protected $cssFiles = [];
    protected $jsFiles = [];

    public function __construct($viewDir) {
        $this->viewDir = rtrim($viewDir, "/") . "/";
        $this->viewFile = $this->viewDir . "view.php";

        // Automatically add CSS and JS files from the respective directories
        $this->loadAssets();
    }

    protected function loadAssets() {
        // Load CSS files from the "css" directory
        $cssDir = $this->viewDir . "css/";
        if (is_dir($cssDir)) {
            foreach (glob($cssDir . "*.css") as $cssFile) {
                $this->addCss($cssFile);
            }
        }

        // Load JavaScript files from the "js" directory
        $jsDir = $this->viewDir . "js/";
        if (is_dir($jsDir)) {
            foreach (glob($jsDir . "*.js") as $jsFile) {
                $this->addJs($jsFile);
            }
        }
    }

    public function addCss($cssFile) {
        $this->cssFiles[] = $cssFile;
    }

    public function addJs($jsFile) {
        $this->jsFiles[] = $jsFile;
    }

    protected function includeCss() {
        $output = "<style>" . PHP_EOL;
        foreach ($this->cssFiles as $cssFile) {
            if (file_exists($cssFile)) {
                $output .= file_get_contents($cssFile) . PHP_EOL;
            } else {
                throw new \Exception("CSS file not found: " . $cssFile);
            }
        }
        $output .= "</style>" . PHP_EOL;
        return $output;
    }

    protected function includeJs() {
        $output = "<script>" . PHP_EOL;
        foreach ($this->jsFiles as $jsFile) {
            if (file_exists($jsFile)) {
                $output .= file_get_contents($jsFile) . PHP_EOL;
            } else {
                throw new \Exception("JavaScript file not found: " . $jsFile);
            }
        }
        $output .= "</script>" . PHP_EOL;
        return $output;
    }

    public function render($data = []) {
        if (!file_exists($this->viewFile)) {
            throw new \Exception("View file not found: " . $this->viewFile);
        }

        // Extract data array to variables
        extract($data);

        // Prepare CSS and JS to be included
        $cssIncludes = $this->includeCss();
        $jsIncludes = $this->includeJs();

        // Start output buffering
        ob_start();

        // Include the view file (the output will be captured)
        include $this->viewFile;

        // Get the captured output
        $output = ob_get_clean();

        return $output;
    }
}
