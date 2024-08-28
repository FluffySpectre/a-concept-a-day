<?php

use DA\GroqAPIClient;
use DA\Config;

require __DIR__ . "/autoload.php";

set_time_limit(300); // Set the maximum execution time to 5 minutes

// Load the previous algorithms list from file
$prevAlgorithms = "";
if (file_exists(__DIR__ . "/previous_algorithms.txt")) {
    $prevAlgorithms = file_get_contents(__DIR__ . "/previous_algorithms.txt");
}

// Load the system prompt and prompt from file and replace variables
$prompt = file_get_contents(__DIR__ . "/prompt.txt");
$variables = ["{ANSWER_LANGUAGE}" => Config::get("ANSWER_LANGUAGE", "English"), "{PREV_ALGORITHMS_LIST}" => $prevAlgorithms];
$prompt = strtr($prompt, $variables);

$systemPrompt = file_get_contents(__DIR__ . "/system_prompt.txt");

$groqClient = new GroqAPIClient(Config::get("API_KEY"));

$retries = 10;
$lastError = "";
while ($retries-- > 0) {
    try {
        $response = $groqClient->prompt_json($prompt, $systemPrompt);
        $response = str_replace(["```python\n", "```python", "```\n", "```"], "", $response);

        $jsonResponse = json_decode($response, true);
        $contents = [
            ["title" => "Summary", "content" => trim($jsonResponse["summary"]), "type" => "text"],
            ["title" => "Use Case", "content" => trim($jsonResponse["example"]), "type" => "text"],
            ["title" => "Steps", "content" => trim($jsonResponse["step_description"]), "type" => "text"],
            ["title" => "Complexity", "content" => trim($jsonResponse["complexity"]), "type" => "text"],
            ["title" => "Code Example", "content" => trim($jsonResponse["example_code"]), "type" => "code"]
        ];
        $finalAlgorithm = ["name" => trim($jsonResponse["name"]), "content" => $contents, "date" => time()];

        file_put_contents(__DIR__ . "/../server-php/algorithms/" . date("Y-m-d") . ".json", json_encode($finalAlgorithm, JSON_PRETTY_PRINT));
        file_put_contents(__DIR__ . "/previous_algorithms.txt", $prevAlgorithms . "\n" . trim($jsonResponse["name"]));
    } catch (Exception $e) {
        $lastError = $e->getMessage();
    }
}

if ($retries > 0) {
    echo "Successfully generated ". date("Y-m-d").".json";
} else {
    echo "Failed to generate ". date("Y-m-d").".json<br><br>Error:<br>" . $lastError;
}
