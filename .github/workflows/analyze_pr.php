<?php

// @codingStandardsIgnoreFile

function addCommentToPr($repo, $prNumber, $reviewComment)
{
    $githubToken = getenv("GITHUB_TOKEN");
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

    if ($response === false) {
        error_log(curl_error($ch));
        echo "Wystąpił błąd podczas dodawania komentarza!";
        return;
    }

    echo "Dodano komentarz do PR z analizą bezpieczeństwa!";
}

function addCommentToJira($prNumber, $reviewComment)
{
    $jiraBaseUrl = getenv("JIRA_BASE_URL"); // np. https://twojprojekt.atlassian.net
    $jiraUser = getenv("JIRA_USER"); // E-mail użytkownika Jira
    $jiraApiToken = getenv("JIRA_API_TOKEN"); // Token API Jira
    $jiraTicket = getenv("JIRA_TICKET"); // Numer ticketa, np. "PROJ-123"

    if (!$jiraApiToken) {
        return;
    }

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

    if ($response === false) {
        error_log(curl_error($ch));
        echo "Wystąpił błąd podczas dodawania komentarza!";
        return;
    }

    echo "Dodano komentarz do ticketa Jira!";
}

function getUrlContent($url) {
    // Inicjalizacja cURL
    $ch = curl_init();

    // Ustawienia cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Zwróć odpowiedź jako string
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Obsługuje przekierowania
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);     // Weryfikacja SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);  // Weryfikacja certyfikatu SSL

    // Pobierz odpowiedź
    $response = curl_exec($ch);

    // Sprawdzenie błędów cURL
    if(curl_errno($ch)) {
        echo 'Błąd cURL: ' . curl_error($ch);
    }

    // Zamknij cURL
    curl_close($ch);

    return $response;
}

function getChangedFiles($repo, $prNumber)
{
    $githubToken = getenv("GITHUB_TOKEN");
    $url = "https://api.github.com/repos/$repo/pulls/$prNumber/files";

    $headers = [
        "Authorization: token $githubToken",
        "Accept: application/vnd.github.v3+json",
        "User-Agent: Analyze PR Application"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $files = json_decode($response, true);
    $changedFiles = [];

    if (!isset($files)) {
        $files = [];
    }

    foreach ($files as $file) {
        if (preg_match('/\.(php|xml|phtml|js)$/i', $file["filename"])) {
            $parsedUrl = parse_url($file["raw_url"]);
            if ($parsedUrl['scheme'] === 'https' && $parsedUrl['host'] === 'github.com') {
                $fileContent = getUrlContent($file["raw_url"]);
                $changedFiles[] = "File: {$file['filename']}\n$fileContent";
            } else {
                echo 'Incorrent file: ' . $file["raw_url"] . "\n";
            }
        }
    }

    return $changedFiles;
}

function getReviewComment($changedFiles)
{
    $openaiApiKey = getenv("OPENAI_API_KEY");

    // Przygotowanie zapytania do OpenAI
        $prompt = "Jesteś ekspertem od PHP i bezpieczeństwa aplikacji. Przeanalizuj poniższe zmiany w kodzie pod kątem:
    - Błędów składniowych i logicznych
    - Optymalizacji kodu
    - Potencjalnych podatności: SQL Injection (SQLi), Cross-Site Scripting (XSS), Remote Code Execution (RCE), Insecure File Handling

    Podaj konkretne problemy i sposoby ich naprawy.

    Zmiany w kodzie:\n\n" . implode("\n\n", $changedFiles);

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
    return htmlspecialchars($reviewComment,  ENT_NOQUOTES, 'UTF-8');
}

// Pobranie zmiennych środowiskowych
$repo = getenv("GITHUB_REPOSITORY");
$prNumber = getenv("PR_NUMBER");

$changedFiles = getChangedFiles($repo, $prNumber);

if (empty($changedFiles)) {
    echo "Brak plików do analizy.";
    exit(0);
}

$reviewComment = getReviewComment($changedFiles);
addCommentToJira($prNumber, $reviewComment);
addCommentToPr($repo, $prNumber, $reviewComment);

echo "### Automatyczna analiza kodu w PR #$prNumber\n\n$reviewComment";
