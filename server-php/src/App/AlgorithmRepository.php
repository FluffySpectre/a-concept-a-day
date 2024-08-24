<?php

namespace DA\App;

use \Exception;

class AlgorithmRepository
{
    public function getLatestAlgorithm()
    {
        $files = scandir("algorithms");

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

    public function getAlgorithmOfDate($date)
    {
        if (!$this->existsAlgorithmForDate($date)) {
            return null;
        }

        $filename = "algorithms/" . $date . ".json";
        try {
            $data = file_get_contents($filename);
            return json_decode($data, true);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getAlgorithms()
    {
        $folder = "algorithms";
        $files = scandir($folder);

        $jsonFiles = array_filter($files, function ($file) {
            return substr($file, -5) === ".json";
        });

        $promises = [];
        foreach ($jsonFiles as $file) {
            $filename = $folder . "/" . $file;
            $promises[] = file_get_contents($filename);
        }

        return array_map("json_decode", $promises, true);
    }

    public function existsAlgorithmForDate($date)
    {
        try {
            $filename = "algorithms/" . $date . ".json";
            if (file_exists($filename) && is_readable($filename)) {
                return true;
            }
        } catch (Exception $e) {
            // Ignore
        }

        return false;
    }
}

// Example usage:
// $repo = new AlgorithmRepository();
// $latesAlg = $repo->getLatestAlgorithm();
// $algos = $repo->getAlgorithms();

// print_r($latesAlg);
// print_r($algos);
