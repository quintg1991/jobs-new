<?php
 session_start();

  /***                                          ***\

    File name: tasks.php
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

  // USER LIST SQL QUERY -- dynamically query the database and show all users on
  //      the left hand nav bar========================================================
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

  // DUE THIS WEEK QUERY===============================================================
  if(isset($_GET['usrID']))
  {
    $usrID = $_GET['usrID'];

    $_thisWeekNum = date("W");
    $_thisWeekSql = "SELECT `project`.`projStatus` AS `projStatus` `project`.`projYear` AS `projYear`, `project`.`yearNum` AS `yearNum`, `project`.`projName` AS `projName`, `project`.`projUserID` AS `userID`, dateComplete, WEEK(dateComplete, 3), dateProof1, WEEK(dateProof1, 3), dateProof2, WEEK(dateProof2, 3), dateProof3, WEEK(dateProof3, 3), dateDue, WEEK(dateDue, 3)
    FROM Project 
    WHERE 
      (
      WEEK(dateComplete, 3) = '{$_thisWeekNum}' OR 
      (WEEK(dateProof1, 3) = '{$_thisWeekNum}' AND proofOut < 1) OR 
      (WEEK(dateProof2, 3) = '{$_thisWeekNum}' AND proofOut < 2) OR 
      (WEEK(dateProof3, 3) = '{$_thisWeekNum}' AND proofOut < 3) OR 
      WEEK(dateDue, 3) = '{$_thisWeekNum}'
      )
    AND projStatus != 'archive'
    AND projStatus != 'deleted'
    AND projStatus != 'finished'
    WHERE `project`.`projUserID` = '$usrID';
    ORDER BY dateDue DESC
    LIMIT 50";
    $_thisWeekStmnt = $_pdo->prepare($_thisWeekSql);

    if($_thisWeekStmnt->execute())
    {
      $_thisWeekLst = $_thisWeekStmnt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
      $_thisWeekLst = array();
    }
  }
  elseif(isset($_GET['status']))
  {
    $status = $_GET['status'];

    $_thisWeekNum = date("W");
    $_thisWeekSql = "SELECT `project`.`projStatus` AS `projStatus`, `project`.`projYear` AS `projYear`, `project`.`yearNum` AS `yearNum`, `project`.`projName` AS `projName`, `project`.`projUserID` AS `userID`, dateComplete, WEEK(dateComplete, 3), dateProof1, WEEK(dateProof1, 3), dateProof2, WEEK(dateProof2, 3), dateProof3, WEEK(dateProof3, 3), dateDue, WEEK(dateDue, 3)
    FROM Project 
    WHERE 
    (
      WEEK(dateComplete, 3) = '{$_thisWeekNum}' OR 
      (WEEK(dateProof1, 3) = '{$_thisWeekNum}' AND proofOut < 1) OR 
      (WEEK(dateProof2, 3) = '{$_thisWeekNum}' AND proofOut < 2) OR 
      (WEEK(dateProof3, 3) = '{$_thisWeekNum}' AND proofOut < 3) OR 
      WEEK(dateDue, 3) = '{$_thisWeekNum}'
    )
    AND projStatus != 'archive'
    AND projStatus != 'deleted'
    AND projStatus != 'finished'
    WHERE `project`.`projStatus` = '$status';
    ORDER BY dateDue DESC
    LIMIT 50";
    $_thisWeekStmnt = $_pdo->prepare($_thisWeekSql);

    if($_thisWeekStmnt->execute())
    {
      $_thisWeekLst = $_thisWeekStmnt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
      $_thisWeekLst = array();
    }
  }
  else
  {
    $_thisWeekNum = date("W");
    $_thisWeekSql = "SELECT `project`.`projStatus` AS `projStatus`, `project`.`projRush` AS `projRush`, `project`.`projYear` AS `projYear`, `project`.`yearNum` AS `yearNum`, `project`.`projName` AS `projName`, `project`.`projUserID` AS `userID`, dateComplete, WEEK(dateComplete, 3), dateProof1, WEEK(dateProof1, 3), dateProof2, WEEK(dateProof2, 3), dateProof3, WEEK(dateProof3, 3), dateDue, WEEK(dateDue, 3)
    FROM Project 
    WHERE 
    (
    WEEK(dateComplete, 3) = '{$_thisWeekNum}' OR 
    (WEEK(dateProof1, 3) = '{$_thisWeekNum}' AND proofOut < 1) OR 
    (WEEK(dateProof2, 3) = '{$_thisWeekNum}' AND proofOut < 2) OR 
    (WEEK(dateProof3, 3) = '{$_thisWeekNum}' AND proofOut < 3) OR 
    WEEK(dateDue, 3) = '{$_thisWeekNum}'
    )
      AND projStatus != 'archive'
      AND projStatus != 'deleted'
      AND projStatus != 'finished'
      ORDER BY dateDue DESC
      LIMIT 50";
    $_thisWeekStmnt = $_pdo->prepare($_thisWeekSql);

    if($_thisWeekStmnt->execute())
    {
      $_thisWeekLst = $_thisWeekStmnt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
      $_thisWeekLst = array();
    }
  }
  // END DUE THIS WEEK QUERY===========================================================

  // ALL JOBS DB QUERY=================================================================
  if(isset($_GET['usrID']))
  {
    $usrID = $_GET['usrID'];

    $_allJobsSql = "SELECT `project`.`projStatus` AS `projStatus`, `project`.`dateDue` AS `dateDue`, `project`.`projName` AS `projName`, `project`.`projUserID` AS `projUserID`, `project`.`projYear` AS `projYear`, `project`.`yearNum` AS `yearNum`, `project`.`projRush` AS `projRush` FROM `project` WHERE `project`.`projUserID` = '$usrID' OR `project`.`projUserID2` = '$usrID' ORDER BY `project`.`dateDue` DESC LIMIT 50";
    $_allJobsStmnt = $_pdo->prepare($_allJobsSql);

    if($_allJobsStmnt->execute())
    {
      $_allJobsLst = $_allJobsStmnt->fetchAll(PDO::FETCH_ASSOC);
      // echo "there is nothing to display";
    }
    else
    {
      $_allJobsLst = array();
    }
  }
  elseif(isset($_GET['status'])) 
  {
    $status = $_GET['status'];
      
    $_allJobsSql = "SELECT `project`.`projStatus` AS `projStatus`, `project`.`dateDue` AS `dateDue`, `project`.`projName` AS `projName`, `project`.`projUserID` AS `projUserID`, `project`.`projYear` AS `projYear`, `project`.`yearNum` AS `yearNum`, `project`.`projRush` AS `projRush` FROM `project` WHERE `project`.`projStatus` = '$status' ORDER BY `project`.`dateDue` DESC LIMIT 50";
    $_allJobsStmnt = $_pdo->prepare($_allJobsSql);

    if($_allJobsStmnt->execute())
    {
      $_allJobsLst = $_allJobsStmnt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
      $_allJobsLst = array();
    }
  }
  elseif(isset($_GET['searchQuery']))
  {
    $searchQuery = $_GET['searchQuery'];

    $searchQuery = htmlspecialchars($searchQuery);

    $_querySql =  "SELECT * FROM `project` WHERE  (`projName` LIKE '%" .$searchQuery."%') ORDER BY `project`.`dateDue` DESC LIMIT 50";

    $_queryStmnt = $_pdo->prepare($_querySql);

    if($_queryStmnt->execute())
    {
      $_allJobsLst = $_queryStmnt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
      $_allJobsLst = array();
    }
  }
  else
  {
    $_allJobsSql = "SELECT `project`.`projStatus` AS `projStatus`, `project`.`dateDue` AS `dateDue`, `project`.`projName` AS `projName`, `project`.`projUserID` AS `projUserID`, `project`.`projYear` AS `projYear`, `project`.`yearNum` AS `yearNum`, `project`.`projStatus` AS `projStatus`, `project`.`projRush` AS `projRush` FROM `project` ORDER BY `project`.`dateDue` DESC LIMIT 50";
      $_allJobsStmnt = $_pdo->prepare($_allJobsSql);

      if($_allJobsStmnt->execute())
      {
        $_allJobsLst = $_allJobsStmnt->fetchAll(PDO::FETCH_ASSOC);
      }
      else
      {
        $_allJobsLst = array();
      }
    }
  // END ALL JOBS DB QUERY=============================================================                  

?>


<!doctype html>
<html>
  <head>
    <title>
      Open Jobs
    </title>
      <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" />
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" />
      <link rel="stylesheet" type="text/css" href="/jobs-new/css/style.css">

      <!-- Functions -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
      <script>
      </script>  

  </head>
  <body>

    <?php
      include("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/includes/header.php")
    ?>

    <!--============= Body content ========================-->

    <div class navbar navbar-default>
      <div class="container">
        <p>
          <a class="btn btn-success" href="detail.php">
            <span class="glyphicon glyphicon-plus">
            </span>
            New Job
          </a>
        </p>
      </div>    
    </div>

    <br />

    <div class="container">
      <div class="row">
        
        <?php
          include("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/includes/sidebar.php")
        ?>

        <div class="col-xs-8">
          <div class="row">
            <div class="col">
              <h3>DUE THIS WEEK:</h3>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>
                        Due Date
                      </th>
                      <th>
                        <!-- Left Intentionally Blank: Project Rush -->
                      </th>
                      <th>
                        Project Name (click for Job details)
                      </th>
                      <th>
                        Current Status
                      </th>

                      <?php
                        if($_SESSION['role'] == "admin")
                        {
                          echo '<th>
                                  Change Status
                                </th>';
                        }
                      ?>

                    </tr>
                  </thead>
                  <tbody>

                    <?php
                      if($_thisWeekLst > 0)
                      {
                        foreach($_thisWeekLst as $week)
                        {
                          echo '<tr>';
                          echo '<td>' . $week['dateDue'] . '</td>';
                          echo '<td>';
                          if($week['projRush'] == '1')
                          {
                            echo '<i class="fa fa-exclamation fa-2x" aria-hidden="true"></i>';
                          }
                          echo '</td>';
                          echo '<td>
                                  <a href="detail.php?projID=' .$week['yearNum']. '-' .$week['projYear'].'">
                                    (' .$week['userID']. '-' .$week['yearNum']. '-' .$week['projYear']. ') ' . $week['projName'] . 
                                  '</a>
                                </td>';

                          echo '<td>' . $week['projStatus'] . '</td>';

                          if($_SESSION['role'] == "admin")
                          {      
                            echo '<td>
                                    <a class="btn btn-default" data-placement="right" data-toggle="tooltip" href="changestatus.php?projID=' .$week['yearNum']. '-' .$week['projYear'].'&newstatus=unassigned" title="Unassign">
                                      <span class="fa fa-minus-square-o">
                                      </span>
                                    </a>
                                    <a class="btn btn-info" data-placement="right" data-toggle="tooltip" href="changestatus.php?projID=' .$week['yearNum']. '-' .$week['projYear'].'&newstatus=assigned" title="Assign">
                                      <span class="fa fa-plus-square-o">
                                      </span>
                                    </a>
                                    <a class="btn btn-success" data-placement="right" data-toggle="tooltip" href="changestatus.php?projID=' .$week['yearNum']. '-' .$week['projYear'].'&newstatus=inProgress" title="In Progress">
                                        <span class="fa fa-spinner">
                                      </span>
                                    </a>
                                    <a class="btn btn-warning" data-placement="right" data-toggle="tooltip" href="changestatus.php?projID=' .$week['yearNum']. '-' .$week['projYear'].'&newstatus=proofOut" title="Proof Out">
                                      <span class="fa fa-file-o">
                                      </span>
                                    </a>
                                    <a class="btn btn-default" data-placement="right" data-toggle="tooltip" href="changestatus.php?projID=' .$week['yearNum']. '-' .$week['projYear'].'&newstatus=finished" title="Finished">
                                      <span class="fa fa-check">
                                      </span>
                                    </a>
                                    <a class="btn btn-default" data-placement="right" data-toggle="tooltip" href="changestatus.php?projID=' .$week['yearNum']. '-' .$week['projYear'].'&newstatus=archive" title="Archive">
                                      <span class="fa fa-archive">
                                      </span>
                                    </a>
                                  </td>';
                          }
                        }
                      }
                      else
                      {
                        echo '<tr>';
                        echo '<td colspan="4"> No jobs to display</td>';
                        echo '</tr>';
                      }

                    ?>

                  </tbody>
                </table>
              </div>
              <h3>ALL JOBS:</h3>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>
                        Due Date
                      </th>
                      <th>
                        <!-- Left intentionally blank: Project Rush -->
                      </th>
                      <th>
                        Project Name (click for Job details)
                      </th>
                      <th>
                        Current Status
                      </th>

                      <?php
                        if($_SESSION['role'] == "admin")
                        {
                          echo '<th>
                                  Change Status
                                </th>';
                        }
                      ?>

                    </tr>
                  </thead>
                  <tbody>

                    <?php
                      if($_allJobsLst > 0)
                      {
                        foreach($_allJobsLst as $job)
                        {
                          echo '<tr>';
                          echo '<td>' . $job['dateDue'] . '</td>';
                          echo '<td>';
                          if($job['projRush'] == '1')
                          {
                            echo '<i class="fa fa-exclamation fa-2x" aria-hidden="true"></i>';
                          }
                          echo '</td>'; 
                          echo '<td><a href="detail.php?projID=' .$job['yearNum']. '-' .$job['projYear'].'">
                                      (' .$job['projUserID']. '-' .$job['yearNum']. '-' .$job['projYear']. ') ' . $job['projName'] . 
                                   '</a>
                                </td>';
                          echo '<td>' . $job['projStatus'] . '</td>';

                          if($_SESSION['role'] == "admin")
                          {
                            echo '<td>
                                    <a class="btn btn-default" data-placement="right" data-toggle="tooltip" href="changestatus.php?projID=' .$job['yearNum']. '-' .$job['projYear'].'&newstatus=unassigned" title="Unassign">
                                      <span class="fa fa-minus-square-o">
                                      </span>
                                    </a>
                                    <a class="btn btn-info" data-placement="right" data-toggle="tooltip" href="changestatus.php?projID=' .$job['yearNum']. '-' .$job['projYear'].'&newstatus=assigned" title="Assign">
                                      <span class="fa fa-plus-square-o">
                                      </span>
                                    </a>
                                    <a class="btn btn-success" data-placement="right" data-toggle="tooltip" href="changestatus.php?projID=' .$job['yearNum']. '-' .$job['projYear'].'&newstatus=inProgress" title="In Progress">
                                        <span class="fa fa-spinner">
                                      </span>
                                    </a>
                                    <a class="btn btn-warning" data-placement="right" data-toggle="tooltip" href="changestatus.php?projID=' .$job['yearNum']. '-' .$job['projYear'].'&newstatus=proofOut" title="Proof Out">
                                      <span class="fa fa-file-o">
                                      </span>
                                    </a>
                                    <a class="btn btn-default" data-placement="right" data-toggle="tooltip" href="changestatus.php?projID=' .$job['yearNum']. '-' .$job['projYear'].'&newstatus=finished" title="Finished">
                                      <span class="fa fa-check">
                                      </span>
                                    </a>
                                    <a class="btn btn-default" data-placement="right" data-toggle="tooltip" href="changestatus.php?projID=' .$job['yearNum']. '-' .$job['projYear'].'&newstatus=archive" title="Archive">
                                      <span class="fa fa-archive">
                                      </span>
                                    </a>
                                  </td>';
                          }
                        }
                      }
                      else
                      {
                        echo '<tr>';
                        echo '<td colspan="4"> No jobs to display</td>';
                        echo '</tr>';
                      }

                    ?>
                    
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--============= End body content ========================-->

    <?php
      include("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/includes/footer.php")
    ?>

  </body>
</html>