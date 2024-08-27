<?php

namespace DA\App;

class AlgorithmRepository {
    protected string $algorithmsPath;

    public function __construct($path) {
        $this->algorithmsPath = $path;
    }

    public function getLatestAlgorithm() {
        $files = scandir($this->algorithmsPath);

        $newestDate = null;
        foreach ($files as $file) {
            if (substr($file, -5) === ".json") {
                $date = substr($file, 0, -5);
                if (!$newestDate || strtotime($date) > strtotime($newestDate)) {
                    $newestDate = $date;
                }
            }
        }

        return $this->getAlgorithmOfDate($newestDate);
    }

    public function getAlgorithmOfDate($date) {
        if (!$this->existsAlgorithmForDate($date)) {
            return null;
        }

        $filename = $this->algorithmsPath . "/" . $date . ".json";
        try {
            $data = file_get_contents($filename);
            return json_decode($data, true);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getAlgorithms() {
        $files = scandir($this->algorithmsPath);
    
        $jsonFiles = array_filter($files, function ($file) {
            return substr($file, -5) === ".json";
        });
    
        $algorithms = [];
        foreach ($jsonFiles as $file) {
            $filename = $this->algorithmsPath . "/" . $file;
            $algorithms[] = json_decode(file_get_contents($filename), true);
        }
    
        usort($algorithms, function ($a, $b) {
            return strtotime($b["date"]) - strtotime($a["date"]);
        });
    
        return $algorithms;
    }

    public function existsAlgorithmForDate($date) {
        try {
            $filename = $this->algorithmsPath . "/" . $date . ".json";
            if (file_exists($filename) && is_readable($filename)) {
                return true;
            }
        } catch (\Exception $e) {
            // Ignore
        }

        return false;
    }
}
