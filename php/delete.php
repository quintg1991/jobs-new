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

  if(!isset($_GET['id']))
  {
    header('Location: userManager.php');
  }

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


  // DELETE USERS SQL=================================================
  if(isset($_GET['id']))
  {
    $_sql = "DELETE FROM `users` WHERE `users`.`id`=:id";
    $_stmnt = $_pdo->prepare($_sql);

    $_params =
      array
      (
        ':id' => $_GET['id']
      );

    if($_stmnt->execute($_params))
    {
      header('Location: userManager.php');
    }
    else
    {
      header('Location: userManager.php?err=1');
      //echo $_pdo->errorInfo();
    }
  }
  // END DELETE USERS SQL==============================================

  // DELETE NOTE SQL===================================================
  if(isset($_GET['noteID']))
  {
    $_noteSql = "UPDATE `note` SET `note`.`deleted` = '1' WHERE `note`.`noteID`=:noteID";
    $_noteStmnt = $_pdo->prepare($_noteSql);

    $_noteParams = 
      array
      (
        ':noteID' => $_GET['noteID']
      );

    if($_noteStmnt->execute($_noteParams))
    {
      header('Location: ' .$_SERVER['HTTP_REFERER']);
    }
    else
    {
      echo "There was an error deleting the note.";
    }
  }
  // END DELETE NOT SQL================================================

  // DELETE FILE SQL===================================================
  if(isset($_GET['fileID']))
  {
    $_deleteFileSql = "SELECT `file`.`fileName` AS `fileName`, `file`.`fileType` AS `fileType` FROM `file` WHERE `file`.`fileID` = :fileID";
    $_deleteFileStmnt = $_pdo->prepare($_deleteFileSql);

    $_params = 
      array
      (
        ':fileID' => $_GET['fileID']
      );

    if($_deleteFileStmnt->execute($_params))
    {
      $fileToDelete = $_deleteFileStmnt->fetch(PDO::FETCH_ASSOC);

      //Glue fileName and fileType together
      /*foreach($fileToDelete as $f)
      {
        $file = $f['fileName'] .".". $f['fileType'];
      }*/

      $file = $fileToDelete['fileName'] . "." . $fileToDelete['fileType'];
    }
    else
    {
      echo "something went wrong with the file download statment";
    }

    if(unlink($_SERVER['DOCUMENT_ROOT'] . '/jobs-new/uploads/' . $file))
    {    
      $_fileSql = "DELETE FROM `file` WHERE `file`.`fileID`=:fileID";
      $_fileStatment = $_pdo->prepare($_fileSql);

      $_fileParams = 
        array
        (
          ':fileID' => $_GET['fileID']
        );

      if($_fileStatment->execute($_fileParams))
      {
        header('Location: ' .$_SERVER['HTTP_REFERER']);
      }
      else
      {
        echo "There was an error deleting the file";
      }
    }
    else
    {
      // there was an issue removing the file...
    }
  }
  //END DELETE FILE SQL================================================


