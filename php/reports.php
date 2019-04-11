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
  
  if($_SESSION['role'] != "admin")
  {
    header('Location: tasks.php');
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


  // Start of all of the fun stuff================================================


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

  // Reports query ===============================================================

  if(isset($_POST['submit']))
  {
    $searchParams = array("user" => $_POST['projUserID'], "fromDate" => $_POST['fromDate'], 'toDate' => $_POST['toDate']);

    $_reportQuery = "SELECT * FROM `project` WHERE `project`.`projUserID`= :projUserID AND `dateComplete` BETWEEN :fromDate AND :toDate";
    $_reportStmnt = $_pdo->prepare($_reportQuery);

    $_params = 
      array
      (
        ':projUserID' => $_POST['projUserID'],
        ':fromDate' => $_POST['fromDate'],
        ':toDate' => $_POST['toDate']
      );

    if($_reportStmnt->execute($_params))
    {
      $reportResults = $_reportStmnt->fetchAll(PDO::FETCH_ASSOC);
      
    }
  }
  else
  {
    // We get here when no submission has been issued. All should be well...
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

    <br />
    <br />

    <div class="container">
      <div class="row">
        <form method="POST">
          <div class="table-responsive">
           <table class="table table-sm">
             <thead>
               <tr>
                 <th>
                   User
                 </th>
                 <th>
                   Date From:
                 </th>
                 <th>
                   Date To:
                 </th>
                 <th>
                   <!-- Left Intentionally Blank -->
                 </th>
               </tr>
             </thead>
             <tbody>
               <tr>
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
                                echo '<option class="list-group-item" value="'. $value['initials'].'">' . $value['username'] . '</option>';
                              }
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                 </td>
                 <td>
                   <div class="container-responsive">
                      <div class="form-group">
                        <input class="form-control" id="fromDate" type="date" name="fromDate" required="required">
                      </div>
                    </div>
                 </td>
                 <td>
                   <div class="container-responsive">
                    <div class="form-group">
                        <input class="form-control" id="toDate" type="date" name="toDate" required="required">
                      </div>
                    </div>
                 </td>
                 <td>
                  <div class="form-group">
                    <button class="btn btn-success" id="submit" name="submit" type="submit">
                      Submit
                    </button>            
                  </div>
                 </td>
               </tr>
             </tbody>
           </table>
         </div>
        </form>

        <?php if(isset($_POST['submit'])){ ?>

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
                    if($reportResults > 0)
                    {
                      foreach($reportResults as $job)
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

        <?php } ?>

      </div>
    </div>

    <!--============= End body content ========================-->

    <?php
      include("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/includes/footer.php")
    ?>

  </body>
</html>