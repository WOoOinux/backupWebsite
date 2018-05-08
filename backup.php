<?php
   // Affectation des variables
   require_once "config.php";
   
   $appPath = realpath(dirname(__FILE__)) . "/";
   
   $backupDate = date("ymdHi");
   $backupPath = "datas/";
   $backupSqlFileName =   $siteName . "_" . $backupDate . ".sql";
   $backupZipFileName = $appPath . $backupPath . $siteName . "_" . $backupDate . ".zip";

   $mailheader = "From: " . $siteName . " <" . $mailTo . ">\r\n";
   $mailheader .= "Reply-to: " . $siteName . " <" . $mailTo . ">\r\n";
   $mailheader .= "Content-type: text/plain; charset=utf-8" . "\r\n";
   
   /*
    * Suppression des sauvegardes trop anciennes
    */
   $backups = scandir($appPath . $backupPath);
   
   foreach ($backups as $backup) {
      $backup = $appPath . $backupPath . $backup;
      $backupLifetime = time() - filemtime($backup);
      
      if ($backupLifetime >= $backupTimeToLive) {
         unlink($backup);
      }
   }
   
   /*
    * Archivage des fichiers et répertoires du site
    */
   $archive = new ZipArchive();
   if ($archive->open($backupZipFileName, ZipArchive::CREATE) == TRUE) {
      $files = new RecursiveIteratorIterator(
         new RecursiveDirectoryIterator($backupSitePath),
         RecursiveIteratorIterator::LEAVES_ONLY
      );
         
      foreach ($files as $name => $file) {
         if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($backupSitePath));
            $archive->addFile($filePath, $relativePath);
         }
      }
      
      $archive->close();
      
   } else {
      $mailSubject = "Erreur lors de la sauvegarde du site " . $siteName;
      
      $mailContent = "La sauvegarde du site " . $siteName . " a rencontré un problème lors de la création de l'archive zip le " . date('d/m/Y \à H\hi') . ".\r\n";
   }
   
   /*
   * Sauvegarde de la base de données
   */
   if ($archive->open($backupZipFileName) == TRUE) {
      // On dump la base de données dans un fichier sql
      system("mysqldump ---host=" . $dbHost . " --user=" . $dbUser . " --password=" . $dbPassword . " " . $dbName . " > " . $backupSqlFileName . " 2> /dev/null");
      // On insère le dump dans l'archive zip
      $archive->addFile( $backupSqlFileName);
      // On referme l'archive zip
      $archive->close();
   } else {
      $mailSubject = "Erreur lors de la sauvegarde du site " . $siteName;
      
      $mailContent = "La sauvegarde du site " . $siteName . " a rencontré un problème lors de l'ouverture de l'archive zip pour y insérer le dump sql le " . date('d/m/Y \à H\hi') . ".\r\n";
   }
   
   // On supprime le fichier sql qui a été ajouté à l'archive zip
   unlink($backupSqlFileName);
    
   /*
    * Notification par email
    */
   $mailSubject = "Sauvegarde du site " . $siteName;
   
   $mailContent = "La sauvegarde du site " . $siteName . " a bien été effectuée le " . date('d/m/Y \à H\hi') . ".\r\n";
   
   mail($mailTo, $mailSubject, $mailContent, $mailheader);
?>
