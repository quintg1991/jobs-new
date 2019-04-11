<?php

  session_start();

  /***                                          ***\

    File name: dashboard.php
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

// START OF ALL OF THE FUN STUFF=====================================================================================================================================================================================

  if(!isset($_POST['submit']))
  {
    //Existing Job was clicked
    if(isset($_GET['projID']))
    {
      $_projectId = explode('-', $_GET['projID']);
      $_projYear = addslashes($_projectId[1]);
      $_yearNum = addslashes($_projectId[0]);

      $_sql = "SELECT `project`.`projName` AS `projName`, `project`.`projStatus` AS `projStatus`, `project`.`projRush` AS `projRush`, `project`.`dateDue` AS `dateDue`, `project`.`projPrinting` AS `projPrinting`, `project`.`dateRequested` AS `dateRequested`, `project`.`dateComplete` AS `dateComplete`, `project`.`projUserID` AS `projUserID`, `project`.`projUserID2` AS `projUserID2`, `project`.`yearNum` AS `yearNum`, `project`.`reqName` AS `reqName`, `project`.`projClass` AS `projClass`,`project`.`projType` AS `projType`, `project`.`reqID` AS `reqID`, `project`.`reqPhone` AS `reqPhone`, `project`.`dateProof1` AS `dateProof1`, `project`.`dateProof2` AS `dateProof2`, `project`.`dateProof3` AS `dateProof3`, `project`.`proofOut` AS `proofOut`, `project`.`reqEmail` AS `reqEmail`, `project`.`detailColorCover` AS `detailColorCover`, `project`.`detailColorInside` AS `detailColorInside`, `project`.`detailSize` AS `detailSize`, `project`.`detailPages` AS `detailPages`, `project`.`copyNeeded` AS `copyNeeded`, `project`.`copyReceived` AS `copyReceived`, `project`.`copySource` AS `copySource`, `project`.`photographyNeeded` AS `photographyNeeded`, `project`.`photographySource` AS `photographySource`, `project`.`photographyReceived` AS `photographyReceived`, `project`.`homeArt` AS `homeArt` FROM `project` WHERE `project`.`projYear`=:projYear AND `project`.`yearNum`=:yearNum";

      $_stmnt = $_pdo->prepare($_sql);

      $_params =
        array
        (
          ':projYear' => $_projYear,
          ':yearNum' => $_yearNum
        );

      if($_stmnt->execute($_params))
      {
        $job = $_stmnt->fetch(PDO::FETCH_ASSOC);
      }
      else
      {
        $job = array();
      }
    }
    else
    {
      //New Job was clicked -- blank form
      $_projYear = substr(date("Y"), 2);
      $_yearNum = 0;

      $_yearNumSql = "SELECT MAX(`project`.`yearNum`) AS `maxYearNum` FROM `project`";

      $_yearNumStmnt = $_pdo->prepare($_yearNumSql);

      if($_yearNumStmnt->execute())
      {
        $_yearNumArr = $_yearNumStmnt->fetch(PDO::FETCH_ASSOC);
        $_yearNum = reset($_yearNumArr) + 1;
        $job = array();
      }
      else
      {
        $_yearNumArr = array(); //Do I need to kill here?
      }
    }
  }
  // if $_POST is set
  else
  {
    // Have project ID, update existing job
    if(isset($_GET['projID']))
    {
      $_projectId = explode('-', $_GET['projID']);
      $_projYear = addslashes($_projectId[1]);
      $_yearNum = addslashes($_projectId[0]);

     $_sql = "UPDATE `project` SET `project`.`projName`=:projName, `project`.`projType`=:projType, `project`.`projClass`=:projClass, `project`.`projPrinting`=:projPrinting, `project`.`projStatus`=:projStatus, `project`.`projRush`=:projRush, `project`.`projUserID`=:projUserID, `project`.`projUserID2`=:projUserID2, `project`.`reqID`=:reqID, `project`.`reqName`=:reqName, `project`.`reqEmail`=:reqEmail, `project`.`reqPhone`=:reqPhone, `project`.`dateComplete`=:dateComplete, `project`.`dateRequested`=:dateRequested, `project`.`dateProof1`=:dateProof1, `project`.`dateProof2`=:dateProof2, `project`.`dateProof3`=:dateProof3, `project`.`dateDue`=:dateDue, `project`.`proofOut`=:proofOut, `project`.`detailSize`=:detailSize, `project`.`detailPages`=:detailPages, `project`.`detailColorCover`=:detailColorCover, `project`.`detailColorInside`=:detailColorInside, `project`.`copyNeeded`=:copyNeeded, `project`.`copySource`=:copySource, `project`.`photographyNeeded`=:photographyNeeded, `project`.`photographySource`=:photographySource, `project`.`homeArt`=:homeArt, `project`.`copyReceived`=:copyReceived, `project`.`photographyReceived`=:photographyReceived WHERE `project`.`projYear`=:projYear AND `project`.`yearNum`=:yearNum";      

      $_stmnt = $_pdo->prepare($_sql);

      echo $_POST['dateProof1'];
      echo '<br />';

      echo $_sql;
      echo '<br />';
      echo '<br />';

      $_params =
        array
        (
          ':projName' => $_POST['projName'],
          ':projType' => $_POST['projType'],
          ':projClass' => $_POST['projClass'],
          ':projPrinting' => $_POST['projPrinting'],
          ':projStatus' => $_POST['projStatus'],
          ':projRush' => (isset($_POST['projRush']) ? 1 : 0),
          ':projUserID' => $_POST['projUserID'],
          ':projUserID2' => $_POST['projUserID2'],
          ':reqID' => $_POST['reqID'],
          ':reqName' => $_POST['reqName'],
          ':reqEmail' => $_POST['reqEmail'],
          ':reqPhone' => $_POST['reqPhone'],
          ':dateComplete' => (isset($_POST['dateComplete']) ? date("Y-m-d", strtotime($_POST['dateComplete'])) : ''),
          ':dateRequested' => (isset($_POST['dateRequested']) ? date("Y-m-d", strtotime($_POST['dateRequested'])) : ''),
          ':dateProof1' => (isset($_POST['dateProof1']) ? date("Y-m-d", strtotime($_POST['dateProof1'])) : ''),
          ':dateProof2' => (isset($_POST['dateProof2']) ? date("Y-m-d", strtotime($_POST['dateProof2'])) : ''),
          ':dateProof3' => (isset($_POST['dateProof3']) ? date("Y-m-d", strtotime($_POST['dateProof3'])) : ''),
          ':dateDue' => (isset($_POST['dateDue']) ? date("Y-m-d", strtotime($_POST['dateDue'])) : ''),
          ':proofOut' => $_POST['proofOut'],
          ':detailSize' => $_POST['detailSize'],
          ':detailPages' => $_POST['detailPages'],
          ':detailColorCover' => $_POST['detailColorCover'],
          ':detailColorInside' => $_POST['detailColorInside'],
          ':copyNeeded' => $_POST['copyNeeded'],
          ':copySource' => $_POST['copySource'],
          ':photographyNeeded' => $_POST['photographyNeeded'],
          ':photographySource' => $_POST['photographySource'],
          ':projYear' => $_projYear,
          ':yearNum' => $_yearNum,
          ':homeArt' => (isset($_POST['homeArt']) ? 1 : 0),
          ':copyReceived' => (isset($_POST['copyReceived']) ? 1 : 0),
          ':photographyReceived' => (isset($_POST['photographyReceived']) ? 1 : 0)
        );

      print_r($_params);
      echo '<br />';
      echo '<br />';

      if($_stmnt->execute($_params))
      {
        header('Location: tasks.php');
      }
      else
      {

        echo "update stmnt did not execute <br />";
      }

      // we should never make it this far, but just in case we will die with dignity.
      die('Something went wrong...:(');
    }
    // No project ID, create new job with input data
    else
    {

      //SQL statments to generate projYear and yearNum
      $_projYear = substr(date("Y"), 2);
      $_projYear = $_projYear;
      $_yearNum = 0;

      $_yearNumSql = "SELECT MAX(`project`.`yearNum`) AS `maxYearNum` FROM `project`";

      $_yearNumStmnt = $_pdo->prepare($_yearNumSql);

      if($_yearNumStmnt->execute())
      {
        $_yearNumArr = $_yearNumStmnt->fetch(PDO::FETCH_ASSOC);
        $_yearNum = reset($_yearNumArr) + 1;
        $_yearNum = (string)$_yearNum;
      }
      else
      {
        //We should never make it this far
        die('Something went terribly wrong...');
      }

     $_sql = "INSERT INTO `project` (`projYear`, `yearNum`, `projName`, `projType`, `projClass`, `projPrinting`, `projStatus`, `projRush`, `projUserID`, `projUserID2`, `reqID`, `reqName`, `reqEmail`, `reqPhone`, `dateComplete`, `dateRequested`, `dateProof1`, `dateProof2`, `dateProof3`, `dateDue`, `proofOut`, `detailSize`, `detailPages`, `detailColorCover`, `detailColorInside`, `copyNeeded`, `copySource`, `copyReceived`, `photographyNeeded`, `photographySource`, `photographyReceived`, `homeArt`)  VALUES (:projYear, :yearNum, :projName, :projType, :projClass, :projPrinting, :projStatus, :projRush, :projUserID, :projUserID2, :reqID, :reqName, :reqEmail, :reqPhone, :dateComplete, :dateRequested, :dateProof1, :dateProof2, :dateProof3, :dateDue, :proofOut, :detailSize, :detailPages, :detailColorCover, :detailColorInside, :copyNeeded, :copySource, :copyReceived, :photographyNeeded, :photographySource, :photographyReceived, :homeArt)";

     $_stmnt = $_pdo->prepare($_sql);

     $date = date("Y-m-d", strtotime($_POST['dateComplete']));
     
     //date("Y-m-d H:i:s")
     //date("Y-m-d H:i:s", strtotime($_myDateString))
     //date("Y-m-d H:i:s", mktime(date("Y"), date("m"), date("d"), date("H"), date("i"), date("s")))
      var_dump($_yearNum);

      $_params =
        array
        (
          ':projYear' => $_projYear,
          ':yearNum' => $_yearNum,
          ':projName' => (isset($_POST['projName']) ? $_POST['projName'] : ''),
          ':projType' => (isset($_POST['projType']) ? $_POST['projType'] : ''),
          ':projClass' => (isset($_POST['projClass']) ? $_POST['projClass'] : ''),
          ':projPrinting' => (isset($_POST['projPrinting']) ? $_POST['projPrinting'] : ''),
          ':projStatus' => (isset($_POST['projStatus']) ? $_POST['projStatus'] : ''),
          ':projRush' => (isset($_POST['projRush']) ? '1' : '0'),
          ':projUserID' => (isset($_POST['projUserID']) ? $_POST['projUserID'] : ''),
          ':projUserID2' => (isset($_POST['projUserID2']) ? $_POST['projUserID2'] : ''),
          ':reqID' => (isset($_POST['reqID']) ? $_POST['reqID'] : ''),
          ':reqName' => (isset($_POST['reqName']) ? $_POST['reqName'] : ''),
          ':reqEmail' => (isset($_POST['reqEmail']) ? $_POST['reqEmail'] : ''),
          ':reqPhone' => (isset($_POST['reqPhone']) ? $_POST['reqPhone'] : ''),
          ':dateComplete' => (isset($_POST['dateComplete']) ? date("Y-m-d", strtotime($_POST['dateComplete'])) : '0000-00-00'),
          ':dateRequested' => (isset($_POST['dateRequested']) ? date("Y-m-d", strtotime($_POST['dateRequested'])) : '0000-00-00'),
          ':dateProof1' => (isset($_POST['dateProof1']) ? date("Y-m-d", strtotime($_POST['dateProof1'])) : '0000-00-00'),
          ':dateProof2' => (isset($_POST['dateProof2']) ? date("Y-m-d", strtotime($_POST['dateProof2'])) : '0000-00-00'),
          ':dateProof3' => (isset($_POST['dateProof3']) ? date("Y-m-d", strtotime($_POST['dateProof3'])) : '0000-00-00'),
          ':dateDue' => (isset($_POST['dateDue']) ? date("Y-m-d", strtotime($_POST['dateDue'])) : '0000-00-00'),
          ':proofOut' => (isset($_POST['proofOut']) ? $_POST['proofOut'] : ''),
          ':detailSize' => (isset($_POST['detailSize']) ? $_POST['detailSize'] : ''),
          ':detailPages' => (isset($_POST['detailPages']) ? $_POST['detailPages'] : ''),
          ':detailColorCover' => (isset($_POST['detailColorCover']) ? $_POST['detailColorCover'] : ''),
          ':detailColorInside' => (isset($_POST['detailColorInside']) ? $_POST['detailColorInside'] : ''),
          ':copyNeeded' => (isset($_POST['copyNeeded']) ? $_POST['copyNeeded'] : ''),
          ':copySource' => (isset($_POST['copySource']) ? $_POST['copySource'] : ''),
          ':copyReceived' => (isset($_POST['copyReceived']) ? '1' : '0'),
          ':photographyNeeded' => (isset($_POST['photographyNeeded']) ? $_POST['photographyNeeded'] : ''),
          ':photographySource' => (isset($_POST['photographySource']) ? $_POST['photographySource'] : ''),
          ':photographyReceived' => (isset($_POST['photographyReceived']) ? '1' : '0'),
          ':homeArt' => (isset($_POST['homeArt']) ? '1' : '0'),
        ); 

      if($_stmnt->execute($_params))
      {
        header("location: tasks.php");
      }
      else
      {
        echo "something went wrong";
        echo "<br />";
        echo "<br />";
        print_r($_stmnt->errorInfo());
        echo '<br />';
        echo '<br />';
        var_dump($_params);
        echo "<br />";
        echo "<br />";
        print_r($_stmnt);
      }
    }
  }

  // JOB NOTES QUERY ==================================================================

  if(isset($_GET['projID']))
  {

    // Select all relevant data from the notes table
    $_jobNoteSql = "SELECT `note`.`noteID` AS `noteID`, `note`.`projID` AS `projID`, `note`.`note` AS `note`, `note`.`dateTime` AS `dateTime`, `note`.`noteID` AS `noteID` FROM `note` WHERE `note`.`projID`=:projID AND `note`.`deleted` != '1'";

    $_jobNoteStmnt = $_pdo->prepare($_jobNoteSql);

    $_params = 
      array
      (
        ':projID' => $_GET['projID']
      );

    if($_jobNoteStmnt->execute($_params))
    {
      $note = $_jobNoteStmnt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
      $note = array();
    }
  }
  else
  {
    //We should never get here
  }

  // If a new note was added
  if(isset($_POST['noteSubmit']))
  {
    $_noteInsertSql = "INSERT INTO `note` (`projID`, `note`, `dateTime`,  `deleted`) VALUES (:projID, :note, :dateInsert, :deleted)";
    $_noteInsertStmnt = $_pdo->prepare($_noteInsertSql);

    $_params = 
      array
      (
        ':projID' => $_GET['projID'],
        ':note' => $_POST['jobNote'],
        ':dateInsert' => date('Y-m-d H:i:s'),
        ':deleted' => ''
      );

    if($_noteInsertStmnt->execute($_params))
    {
      header("Refresh:0");
    }
    else
    {
      var_dump($_params);
      echo "something went wrong, but noteSubmit isset";
    }
  }
  // END JOB NOTES QUERY ==============================================================


  // USER LIST SQL QUERY -- dynamically query the database and show all users==========
  $_usrLstSql = "SELECT `users`.`username` AS `username`, `users`.`initials` AS `initials`, `users`.`id` AS `id` FROM `users` ORDER BY `users`.`username`ASC";
  $_usrLstStmnt = $_pdo->prepare($_usrLstSql);

  if($_usrLstStmnt->execute())
  {
    $_usrLst = $_usrLstStmnt->fetchAll(PDO::FETCH_ASSOC);
  }
  else
  {
    $_usrLst = array();
  }
  // END USER LIST QUERY===============================================================
?>



<!doctype html>
<html>
<head>
  <title>Job Detail</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="/jobs-new/css/style.css">

    <!-- Functions -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://www.una.edu/js/jquery-1.11.1.min.js"></script>

    <script type="text/javascript" src="fg-menu/fg.menu.js"></script>
    <link type="text/css" href="fg-menu/fg.menu.css" media="screen" rel="stylesheet" />
    <link type="text/css" href="fg-menu/theme/ui.all.css" media="screen" rel="stylesheet" />

    <script>
      $(function(){
        $.get('requesterMenu.html', function(data){
          $('#requesterMenu').menu({ content: data, backLink: false });
        });
        
      });
    </script> 

</head>
<body>

  <?php
    include("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/includes/header.php")
  ?>

  <br />

  <div class="container">

    <h3>Job Detail View</h3>


    <div class="row">
      <div class="col">

        <form method="POST">
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>
                    Rush
                  </th>
                  <th>
                    Job Name
                  </th>
                  <th>
                    Status
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="form-check form-check-inline">
                      <label class="form-check-label">
                          <input type="checkbox" id="projRush" name="projRush" value="1"
                            <?php echo isset($_GET['projID']) ? ($job['projRush'] == '1' ? 'checked="checked"' : '') : '' ?> />
                      </label>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <?php echo $job('projName');
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                        <div class="form-group">
                          <select class="form-control" id="projStatus" name="projStatus">
                            
                            <option <?php echo ($_SESSION['role'] != 1) ? 'disabled="disabled"' : '' ?> value="">-</option>
                            <option <?php echo isset($_GET['projID']) ? ($job['projStatus'] == 'Assigned' ? 'selected="selected"' : '') : ''; ?>>Assigned</option>
                            <option <?php echo isset($_GET['projID']) ? ($job['projStatus'] == 'In Progress' ? 'selected="selected"' : '') : ''; ?>>In Progress</option>
                            <option <?php echo isset($_GET['projID']) ? ($job['projStatus'] == 'Proof Out' ? 'selected="selected"' : '') : ''; ?>>Proof Out</option>
                            <option <?php echo isset($_GET['projID']) ? ($job['projStatus'] == 'Finished' ? 'selected="selected"' : '') : ''; ?>>Finished</option>
                            <option <?php echo isset($_GET['projID']) ? ($job['projStatus'] == 'Unassigned' ? 'selected="selected"' : '') : ''; ?>>Unassigned</option>
                            <option <?php echo isset($_GET['projID']) ? ($job['projStatus'] == 'Archive' ? 'selected="selected"' : '') : ''; ?>>Archive</option>
                          </select>
                        </div>
                      </div>
                  </td>
              </tbody>
            </table>

            <table class="table table-sm">
              <thead>
                <tr>
                  <th>
                    DUE DATE
                  </th>
                  <th>
                    TO PRINTER/COMPLETE
                  </th>
                  <th>
                    DATE REQUESTED
                  </th>
                  <th>
                    ASSIGNED TO
                  </th>
                  <th>
                    JOB #
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <input class="form-control" id="dateDue" type="date" name="dateDue" <?php echo isset($_GET['projID']) ? isset($job['dateDue']) ? 'value="' .$job['dateDue']. '"' : 'value=""' : 'value=""' ?>>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <input class="form-control" id="dateComplete" type="date" name="dateComplete" <?php echo isset($_GET['projID']) ? isset($job['dateComplete']) ? 'value="' .$job['dateComplete']. '"' : 'value=" "' : 'value=""' ?>>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <input class="form-control" id="dateRequested" type="date" name="dateRequested" <?php echo isset($_GET['projID']) ? isset($job['dateRequested']) ? 'value="' .$job['dateRequested']. '"' : 'value=" "' : 'value=""' ?>>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <select class="form-control" id="projUserID" name="projUserID">
                          <option value="">-</option>
                          <?php

                            if(count($_usrLst) > 0)
                            {
                              foreach ($_usrLst as $key => $value)
                              {
                                echo '<option class="list-group-item" value="'. $value['initials'].'" '.(isset($_GET['projID']) ? ($value['initials'] == $job['projUserID']) ? 'selected="selected"' : '' : '').  ' >' . $value['username'] . '</option>';
                              }
                            }
                          ?>
                        </select>
                        <select class="form-control" id="projUserID2" name="projUserID2">
                          <option value="">-</option>
                          <?php

                            if(count($_usrLst) > 0)
                            {
                              foreach ($_usrLst as $key => $value)
                              {
                                echo '<option class="list-group-item" value="'. $value['initials'].'" '.(isset($_GET['projID']) ? ($value['initials'] == $job['projUserID2']) ? 'selected="selected"' : '' : '').  ' >' . $value['username'] . '</option>';
                              }
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="form-group">
                      <input class="form-control" id="yearNum" type="text" name="" <?php echo isset($job['yearNum']) ? 'value="' .$job['yearNum']. '" readonly="readonly" ' : 'value=" "' ?>
                       >
                    </div>
                  </td>
                </tr>
              </tbody>
              <thead>
                <tr>
                  <th>
                    Contact Name
                  </th>
                  <th>
                    Job Class
                  </th>
                  <th>
                    Printing
                  </th>
                  <th>
                    Job Type
                  </th>
                  <th>
                    Requester
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <input class="form-control" id="reqName" type="text" name="reqName" <?php echo isset($job['reqName']) ? 'value="' .$job['reqName']. '"' : 'value=""' ?>
                      </div>
                    </div>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <select class="form-control" id="projClass" name="projClass">
                          
                          <option value="">-</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projClass'] == 'New Job' ? 'selected="selected"' : '') : '' ?>>New Job</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projClass'] == 'Reprint' ? 'selected="selected"' : '') : '' ?>>Reprint</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projClass'] == 'Reprint With Changes' ? 'selected="selected"' : '') : '' ?>>Reprint With Changes</option>
                        </select>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <select class="form-control" id="projPrinting" name="projPrinting">
                          <option value="">-</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projPrinting'] == 'In-House' ? 'selected="selected"' : '') : '' ?>>In-House</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projPrinting'] == 'Out-Source' ? 'selected="selected"' : '') : '' ?>>Out-Source</option>
                        </select>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <select class="form-control" id="projType" name="projType">
                          <option value="">-</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Ad' ? 'selected="selected"' : '') : '' ?>>Ad</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Booklet' ? 'selected="selected"' : '') : '' ?>>Booklet</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Brochure' ? 'selected="selected"' : '') : '' ?>>Brochure</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Business card' ? 'selected="selected"' : '') : '' ?>>Business card</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Certificates' ? 'selected="selected"' : '') : '' ?>>Certificates</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Envelope' ? 'selected="selected"' : '') : '' ?>>Envelope</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Flyer' ? 'selected="selected"' : '') : '' ?>>Flyer</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Invitation' ? 'selected="selected"' : '') : '' ?>>Invitation</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Letterhead' ? 'selected="selected"' : '') : '' ?>>Letterhead</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Logo' ? 'selected="selected"' : '') : '' ?>>Logo</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Newsletter' ? 'selected="selected"' : '') : '' ?>>Newsletter</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Postcard' ? 'selected="selected"' : '') : '' ?>>Postcard</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Poster' ? 'selected="selected"' : '') : '' ?>>Poster</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Press Release Needed' ? 'selected="selected"' : '') : '' ?>>Press Release Needed</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Program' ? 'selected="selected"' : '') : '' ?>>Program</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Signs' ? 'selected="selected"' : '') : '' ?>>Signs</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'WEB Design' ? 'selected="selected"' : '') : '' ?>>WEB Design</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['projType'] == 'Other' ? 'selected="selected"' : '') : '' ?>>Other</option>
                        </select>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <input class="form-control" type="text" id="reqID" name="reqID" <?php echo isset($job['reqID']) ? 'value="' .$job['reqID']. '"' : 'value=""' ?>>
                      </div>
                    </div>
                    <a tabindex="0" href="../requesterMenu.html" span style="color:#777;float:right;" id="requesterMenu">...lookup</a>
                  </td>
                </tr>
              </tbody>
              <thead">
                <tr>
                  <th>
                    Phone Number
                  </th>
                  <th>
                    1st Proof
                  </th>
                  <th>
                    2nd Proof
                  </th>
                  <th>
                    3rd Proof
                  </th>
                  <th>
                    Proof Out
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <input class="form-control" id="reqPhone" type="text" name="reqPhone" <?php echo isset($job['reqPhone']) ? 'value="' .$job['reqPhone']. '"' : 'value=""' ?>>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <input class="form-control" id="dateProof1" type="date" name="dateProof1" <?php echo isset($job['dateProof1']) ? 'value="' .$job['dateProof1']. '"' : 'value=""' ?> placeholder="">
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <input class="form-control" id="dateProof2" type="date" name="dateProof2" <?php echo isset($job['dateProof2']) ? 'value="' .$job['dateProof2']. '"' : 'value=""' ?>>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <input class="form-control" id="dateProof3" type="date" name="dateProof3" <?php echo isset($job['dateProof3']) ? 'value="' .$job['dateProof3']. '"' : 'value=""' ?>>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <select class="form-control" id="proofOut" name="proofOut">
                          <option value="">-</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['proofOut'] == '1' ? 'selected="selected"' : '') : '' ?>>1</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['proofOut'] == '2' ? 'selected="selected"' : '') : '' ?>>2</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['proofOut'] == '3' ? 'selected="selected"' : '') : '' ?>">3</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['proofOut'] == '4' ? 'selected="selected"' : '') : '' ?>">4</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['proofOut'] == '5' ? 'selected="selected"' : '') : '' ?>">5</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['proofOut'] == '6' ? 'selected="selected"' : '') : '' ?>">6</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['proofOut'] == '7' ? 'selected="selected"' : '') : '' ?>">7</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['proofOut'] == '8' ? 'selected="selected"' : '') : '' ?>">8</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['proofOut'] == '9' ? 'selected="selected"' : '') : '' ?>">9</option>
                        </select>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
              <thead>
                <tr>
                  <th>
                    Email
                  </th>
                  <th>
                    Color: Font / Cover
                  </th>
                  <th>
                    Color: Back / Insides
                  </th>
                  <th>
                    Size
                  </th>
                  <th>
                    # Pages
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <input class="form-control" id="reqEmail" type="text" name="reqEmail" <?php echo isset($job['reqEmail']) ? 'value="' .$job['reqEmail']. '"' : 'value=""' ?> />
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <select class="form-control" id="detailColorCover" name="detailColorCover">
                          <option value="">-</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['detailColorCover'] == 'Black Ink' ? 'selected="selected"' : '') : '' ?>>Black Ink</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['detailColorCover'] == '1 Color' ? 'selected="selected"' : '') : '' ?>>1 Color</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['detailColorCover'] == '2 Color' ? 'selected="selected"' : '') : '' ?>>2 Color</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['detailColorCover'] == '3 Color' ? 'selected="selected"' : '') : '' ?>>3 Color</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['detailColorCover'] == '4 Color' ? 'selected="selected"' : '') : '' ?>>4 Color</option>
                        </select>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                       <select class="form-control" id="detailColorInside" name="detailColorInside">
                          <option value="">-</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['detailColorInside'] == 'Black Ink' ? 'selected="selected"' : '') : '' ?>>Black Ink</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['detailColorInside'] == '1 Color' ? 'selected="selected"' : '') : '' ?>>1 Color</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['detailColorInside'] == '2 Color' ? 'selected="selected"' : '') : '' ?>>2 Color</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['detailColorInside'] == '3 Color' ? 'selected="selected"' : '') : '' ?>>3 Color</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['detailColorInside'] == '4 Color' ? 'selected="selected"' : '') : '' ?>>4 Color</option>
                        </select>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <input class="form-control" id="detailSize" type="text" name="detailSize" <?php echo isset($job['detailSize']) ? 'value="' .$job['detailSize']. '"' : 'value=""' ?>>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <input class="form-control" id="detailPages" type="text" name="detailPages" <?php echo isset($job['detailPages']) ? 'value="' .$job['detailPages']. '"' : 'value=""' ?>>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
              <thead>
                <tr>
                  <th>
                    Copy Needed
                  </th>
                  <th>
                    Supplying Copy
                  </th>
                  <th>
                    Photography Needed
                  </th>
                  <th>
                    Supplying Photos
                  </th>
                  <th>
                    Home Art Needed
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <select class="form-control" id="copyNeeded" name="copyNeeded">
                          <option value="">-</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['copyNeeded'] == 'Yes' ? 'selected="selected"' : '') : '' ?>>Yes</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['copyNeeded'] == 'No' ? 'selected="selected"' : '') : '' ?>>No</option>
                        </select>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <span class="input-group-addon">
                          <input type="checkbox" id="copyReceived" name="copyReceived"
                            <?php echo isset($_GET['projID']) ? ($job['copyReceived'] == '1' ? 'checked="checked"' : '') : '' ?> />
                        </span>
                        <select class="form-control" id="copySource" name="copySource">
                          <option value="">-</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['copySource'] == 'via Email' ? 'selected="selected"' : '') : '' ?>>via Email</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['copySource'] == 'via CD' ? 'selected="selected"' : '') : '' ?>>via CD</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['copySource'] == 'Josh' ? 'selected="selected"' : '') : '' ?>>Josh</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['copySource'] == 'Student Writer' ? 'selected="selected"' : '') : '' ?>>Student Writer</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['copySource'] == 'Hard Copy' ? 'selected="selected"' : '') : '' ?>>Hard Copy</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['copySource'] == 'Other' ? 'selected="selected"' : '') : '' ?>>Other</option>
                        </select>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <select class="form-control" id="photographyNeeded" name="photographyNeeded">
                          <option value="">-</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['photographyNeeded'] == 'Yes' ? 'selected="selected"' : '') : '' ?>>Yes</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['photographyNeeded'] == 'No' ? 'selected="selected"' : '') : '' ?>>No</option>
                        </select>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="container-responsive">
                      <div class="form-group">
                        <span class="input-group-addon">
                          <input type="checkbox" id="photographyReceived" name="photographyReceived"
                            <?php echo isset($_GET['projID']) ? ($job['photographyReceived'] == '1' ? 'checked="checked"' : '') : '' ?> />
                        </span>
                        <select class="form-control" id="photographySource" name="photographySource">
                          <option value="">-</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['photographySource'] == 'via Email' ? 'selected="selected"' : '') : '' ?>>via Email</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['photographySource'] == 'via CD' ? 'selected="selected"' : '') : '' ?>>via CD</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['photographySource'] == 'Josh' ? 'selected="selected"' : '') : '' ?>>Josh</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['photographySource'] == 'Student Writer' ? 'selected="selected"' : '') : '' ?>>Student Writer</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['photographySource'] == 'Hard Copy' ? 'selected="selected"' : '') : '' ?>>Hard Copy</option>
                          <option <?php echo isset($_GET['projID']) ? ($job['photographySource'] == 'Other' ? 'selected="selected"' : '') : '' ?>>Other</option>
                        </select>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="form-check form-check-inline">
                      <label class="form-check-label">
                          <input type="checkbox" id="homeArt" name="homeArt" value="1"
                            <?php echo isset($_GET['projID']) ? ($job['homeArt'] == '1' ? 'checked="checked"' : '') : '' ?> />
                      </label>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <br />
          <br />

          <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>
                      Date
                    </th>
                    <th>
                      Note
                    </th>
                    <th>
                      Delete
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <?php

                    if(isset($_GET['projID']))
                    {
                      foreach($note as $n)
                      {
                        echo '<tr>';
                        echo '<td><strong>' . date("Y-m-d", strtotime($n['dateTime'])) . '</strong></td>';
                        echo '<td>' . $n['note'] . '</td>';
                        echo '<td><a class="btn btn-default" href="delete.php?noteID=' . $n['noteID'] . '"><span class="fa fa-trash-o"></a></td>';
                      }
                    }
                  ?>
                </tbody>
              </table>
            </div>

            <p><b>Job Notes</b></p>

            <div class="input-group">
              <input class="form-control" id="jobNote" type="text" name="jobNote"  value="" />
              <span class="input-group-btn">
                <button class="btn btn-secondary" id="noteSubmit" name="noteSubmit" type="submit">
                  ADD
                </button>
              </span>
            </div>

            <br />

            <div class="form-group">
              <button class="btn btn-success" id="submit" name="submit" type="submit">
                Submit Changes
              </button>            
            </div>
        </form>
      </div>
    </div>
  </div>



</body>
</html>



