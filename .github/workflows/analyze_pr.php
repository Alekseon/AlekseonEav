<?php

// @codingStandardsIgnoreFile

// Pobranie zmiennych środowiskowych
$githubToken = getenv("GITHUB_TOKEN");
$openaiApiKey = getenv("OPENAI_API_KEY");
$repo = getenv("GITHUB_REPOSITORY");
$prNumber = getenv("PR_NUMBER");
$jiraBaseUrl = getenv("JIRA_BASE_URL"); // np. https://twojprojekt.atlassian.net
$jiraTicket = getenv("JIRA_TICKET"); // Numer ticketa, np. "PROJ-123"
$jiraUser = getenv("JIRA_USER"); // E-mail użytkownika Jira
$jiraApiToken = getenv("JIRA_API_TOKEN"); // Token API Jira

$headers = [
    "Authorization: token $githubToken",
    "Accept: application/vnd.github.v3+json",
    "User-Agent: Analyze PR Application"
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

if (!$files) {
    $files = [];
}

foreach ($files as $file) {
    if (preg_match('/\.(php|xml|phtml|js)$/i', $file["filename"])) {
        $fileContent = file_get_contents($file["raw_url"]);
        $changedFiles[] = "File: {$file['filename']}\n$fileContent";
    }
}

if (empty($changedFiles)) {
    echo "Brak plików PHP do analizy.";
    exit(0);
}

// Przygotowanie zapytania do OpenAI
$prompt = "Jesteś ekspertem od PHP i bezpieczeństwa aplikacji. Przeanalizuj poniższe zmiany w kodzie pod kątem:
- Błędów składniowych i logicznych
- Optymalizacji kodu
- Potencjalnych podatności: SQL Injection (SQLi), Cross-Site Scripting (XSS), Remote Code Execution (RCE), Insecure File Handling

Podaj konkretne problemy i sposoby ich naprawy.

Zmiany w kodzie:\n\n" . implode("\n\n", $changedFiles);

//$prompt = mb_convert_encoding($prompt, 'UTF-8', 'auto');

$data = [
    "model" => "gpt-4o-mini",
    "messages" => [
        ["role" => "system", "content" => "Jesteś ekspertem od PHP i cyberbezpieczeństwa."],
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

// Dodanie komentarza do PR

$headers = [
    "Authorization: token $githubToken",
    "Accept: application/vnd.github.v3+json",
    "User-Agent: Analyze PR Application"
];

$commentUrl = "https://api.github.com/repos/$repo/issues/$prNumber/comments";
$commentData = json_encode(["body" => $reviewComment]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $commentUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $commentData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "Dodano komentarz do PR z analizą bezpieczeństwa!";

echo "### Automatyczna analiza kodu w PR #$prNumber\n\n$reviewComment";
