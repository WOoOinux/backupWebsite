<?php
    // Adresse de l'hébergement de la base de données
    $dbHost = "";
    
    // Nom de la base de données
    $dbName = "";
    
    // Utilisateur de la base de données
    $dbUser = "";
    
    // Mot de passe de la base de données
    $dbPassword = "";
    
    // Nom du site à sauvegarder (éviter les espaces et les carractères accentués)
    $siteName = "";
    
    // Format de la date de sauvegarde qui sera utilisé pour le nom de l'archive
    $backupDate = date("Y-m-d-H-i-s");
    
    // Chemin relatif vers le répertoire contenant les fichiers du site à sauvegarder
    $backupSitePath = "";
    
    // Durée de vie de la sauvegarde (en secondes)
    $backupTimeToLive = 84600;
    
    // Destinataire des emails de notification
    $mailTo = "john.doe@fai.tld";
?>
