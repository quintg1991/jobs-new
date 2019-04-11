<?php

  session_start();

  /***                                          ***\

    File name: changestatus.php
    Author: Quint GLover

    Copyright (c) 2017 University of North Alabama

  \***                                          ***/

  //default settings
  ini_set('magic_quotes_gpc', 'Off');
  date_default_timezone_set('America/Chicago');
  ini_set('display_errors', 'On');
  

  if($_SESSION['role'] != "admin")
  {
    header('Location: tasks.php');
  }

  // make sure the call is coming from inside the application
  if(strpos($_SERVER['HTTP_REFERER'], 'https://www.una.edu/university-communications/jobs/') === 0 || strpos($_SERVER['HTTP_REFERER'], 'http://localhost/jobs-new/') === 0)
  {
    header("Location: {$_SERVER['HTTP_REFERER']}");

    include_once('db.php');

    //========== Begin processes =======================================================

    // $projID = explode('-', $_GET['projID']);
    // $yearNum = addslashes($_projectId[0]);
    // $projYear = addslashes($_projectId[1]);
    // $newStatus = addslashes($_GET['newStatus']);

    $_projectId = explode('-', $_GET['projID']);

    $_sql = "UPDATE `project` SET `project`.`projStatus`=:newStatus WHERE `project`.`projYear`=:projYear AND `project`.`yearNum`=:yearNum";

    $_stmnt = $_pdo->prepare($_sql);

    $_params =
      array
      (
        ':newStatus' => addslashes($_GET['newstatus']),
        ':projYear' => addslashes($_projectId[1]),
        ':yearNum' => addslashes($_projectId[0])
      );

    if($_stmnt->execute($_params))
    {
      // all is well
      header("Location: tasks.php");
    }
    else
    {
      // we got problems
      die($_stmnt->errorInfo());
      //echo 'something went wrong';
    }
  }

?>
