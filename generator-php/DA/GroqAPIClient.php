<?php

namespace DA;

class GroqAPIClient {
    private string $apiKey;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function prompt_json(string $prompt, string $systemPrompt) {
        $cRequest = new CurlRequest();
        $cRequest->addHeader("Content-Type: application/json");
        $cRequest->addHeader("Authorization: Bearer {$this->apiKey}");

        $postData = [
            "messages" => [
                ["role" => "system", "content" => $systemPrompt],
                ["role" => "user", "content" => $prompt]
            ],
            "model" => "llama-3.1-70b-versatile",
            "temperature" => 0.1,
            "max_tokens" => 4096,
            "top_p" => 1,
            "stream" => false,
            "response_format" => [
                "type" => "json_object"
            ],
            "stop" => null
        ];
        $response = $cRequest->post("https://api.groq.com/openai/v1/chat/completions", $postData, "json");
        $jsonResponse = json_decode($response[0], true);

        if (!isset($jsonResponse["choices"])) {
            throw new \Exception($response[0]);
        }

        return $jsonResponse["choices"][0]["message"]["content"];
    }
}
