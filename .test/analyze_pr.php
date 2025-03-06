<?php

// Pobranie zmiennych œrodowiskowych
$githubToken = getenv("GITHUB_TOKEN");
$openaiApiKey = getenv("OPENAI_API_KEY");
$repo = getenv("GITHUB_REPOSITORY");
$prNumber = getenv("PR_NUMBER");
$jiraBaseUrl = getenv("JIRA_BASE_URL"); // np. https://twojprojekt.atlassian.net
$jiraTicket = getenv("JIRA_TICKET"); // Numer ticketa, np. "PROJ-123"
$jiraUser = getenv("JIRA_USER"); // E-mail u¿ytkownika Jira
$jiraApiToken = getenv("JIRA_API_TOKEN"); // Token API Jira

$headers = [
    "Authorization: token $githubToken",
    "Accept: application/vnd.github.v3+json"
];

// Pobranie zmienionych plików w PR
$url = "https://api.github.com/repos/$repo/pulls/$prNumber/files";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$files = json_decode($response, true);
$changedFiles = [];

foreach ($files as $file) {
    if (str_ends_with($file["filename"], ".php")) { // Analizujemy tylko pliki PHP
        $fileContent = file_get_contents($file["raw_url"]);
        $changedFiles[] = "File: {$file['filename']}\n$fileContent";
    }
}

if (empty($changedFiles)) {
    echo "Brak plików PHP do analizy.";
    exit(0);
}

// Przygotowanie zapytania do OpenAI
$prompt = "Jesteœ ekspertem od PHP i bezpieczeñstwa aplikacji. Przeanalizuj poni¿sze zmiany w kodzie pod k¹tem:
- B³êdów sk³adniowych i logicznych
- Optymalizacji kodu
- Potencjalnych podatnoœci: SQL Injection (SQLi), Cross-Site Scripting (XSS), Remote Code Execution (RCE), Insecure File Handling

Podaj konkretne problemy i sposoby ich naprawy.

Zmiany w kodzie:\n\n" . implode("\n\n", $changedFiles);

$data = [
    "model" => "gpt-4",
    "messages" => [
        ["role" => "system", "content" => "Jesteœ ekspertem od PHP i cyberbezpieczeñstwa."],
        ["role" => "user", "content" => $prompt]
    ]
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $openaiApiKey"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$openaiResponse = json_decode($response, true);
$reviewComment = $openaiResponse["choices"][0]["message"]["content"] ?? "Brak odpowiedzi z OpenAI.";

// Dodanie komentarza do ticketa w Jira
/**
$jiraComment = [
    "body" => "### Automatyczna analiza kodu w PR #$prNumber\n\n$reviewComment"
];

$ch = curl_init("$jiraBaseUrl/rest/api/3/issue/$jiraTicket/comment");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode("$jiraUser:$jiraApiToken")
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jiraComment));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "Dodano komentarz do ticketa Jira!";
**/

echo "### Automatyczna analiza kodu w PR #$prNumber\n\n$reviewComment";
