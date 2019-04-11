<?php

  session_start();

  /***                                          ***\

    File name: delete.php
    Author: Quint GLover

    Copyright (c) 2017 University of North Alabama

  \***                                          ***/

  //default settings
  ini_set('magic_quotes_gpc', 'Off');
  date_default_timezone_set('America/Chicago');
  ini_set('display_errors', 'On');

  //read .ini file into array
  $_ini = parse_ini_file("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/etc/config.ini", true);

  //When connecting to the server, set names to utf8 encoding
  $_opt = array
  (
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
  ); 

  //If on localhost, create root PDO. Else, connect to the server with config.ini username and password.
  if($_SERVER['SERVER_NAME'] == 'localhost')
  {
    $_dsn = "{$_ini['database']['dialect']}:host=127.0.0.1;dbname=" . strtolower($_ini['database']['schema']);
  
    $_pdo = new PDO($_dsn, 'root', '', $_opt);
  }
  else
  {
    $_pdo = new PDO($_dsn, $_ini['database']['username'], $_ini['database']['password'], $_opt);
  }


  //DOWNLOAD FILE=============================================================
  if(isset($_GET['fileID']))
  {
    $_downloadSql = "SELECT `file`.`fileName` AS `fileName`, `file`.`fileType` AS `fileType` FROM `file` WHERE `file`.`fileID` = :fileID";
    $_downloadStmnt = $_pdo->prepare($_downloadSql);

    $_params = 
      array
      (
        ':fileID' => $_GET['fileID']
      );

    if($_downloadStmnt->execute($_params))
    {
      $fileToDownload = $_downloadStmnt->fetch(PDO::FETCH_ASSOC);

      $file = $fileToDownload['fileName'] . "." . $fileToDownload['fileType'];
    }
    else
    {
      echo "something went wrong with the file download statment";
    }

    $file = basename($file);
    $file = $_SERVER['DOCUMENT_ROOT'] . '/jobs-new/uploads/'.$file;

    if(!$file)
    {
      die('file not found');
    }
    else
    {
      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header("Content-Disposition: attachment; filename={$file}");
      header("Content-Type: application/zip");
      header("Content-Transfer-Encoding: binary");

      // read the file from disk
      readfile($file);
      exit;
    }

  }
  else
  {
    echo "<script type='text/javascript'>alert('There was an error downloading the file');</script>";
    header('Location: ' .$_SERVER['HTTP_REFERER']);
  }
  //END DOWNLOAD FILE=========================================================

?>