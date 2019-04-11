<div class="col-xs-4">
  <form class="form-inline">
    <div class="input-group" method="GET">
      <input type="text" class="form-control" id="searchQuery" name="searchQuery" placeholder="Press Enter to Search...">
            <span class="input-group-addon">
              <i class="fa fa-search"></i>
            </span>
    </div>
  </form>

  <br />

  <?php    
    //================== Number of all jobs ===========================================
    $_numAllSql = "SELECT `project`.`projStatus` FROM `project` WHERE `project`.`projStatus` != 'unassigned' AND `project`.`projStatus` != 'archive' AND `project`.`projStatus` != 'deleted'";
    $_numAssignedSql = "SELECT `project`.`projStatus` FROM `project` WHERE `project`.`projStatus` = `assigned`";



    $_numAllStmnt = $_pdo->prepare($_numAllSql);

    if($_numAllStmnt->execute())
    { 
      $_numAll = $_numAllStmnt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
      $_numAll = array();
    }

    //================== Number of assigned jobs =====================================
    $_numAssignedSql = "SELECT `project`.`projStatus` FROM `project` WHERE `project`.`projStatus` = 'assigned'";

    $_numAssignedStmnt = $_pdo->prepare($_numAssignedSql);

    if($_numAssignedStmnt->execute())
    {
      $_numAssigned = $_numAssignedStmnt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
      $_numAssigned = array();
    }

    //================= Number of In Progress jobs ===================================
    $_numInProgSql = "SELECT `project`.`projStatus` FROM `project` WHERE `project`.`projStatus` = 'inProgress'";

    $_numInProgStmnt = $_pdo->prepare($_numInProgSql);

    if($_numInProgStmnt->execute())
    {
      $_numInProg = $_numInProgStmnt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
      $_numInProg = array();
    }

    //================ Number of Proof out jobs =======================================
    $_proofOutSql = "SELECT `project`.`projStatus` FROM `project` WHERE `project`.`projStatus` = 'proofOut'";

    $_proofOutStmnt = $_pdo->prepare($_proofOutSql);

    if($_proofOutStmnt->execute())
    {
      $_proofOut = $_proofOutStmnt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
      $_proofOut = array();
    }

    //================ Number of Finished jobs ========================================
    $_finishedSql = "SELECT `project`.`projStatus` FROM `project` WHERE `project`.`projStatus` = 'finished'";

    $_finishedStmnt = $_pdo->prepare($_finishedSql);

    if($_finishedStmnt->execute())
    {
      $_finished = $_finishedStmnt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
      $_finished = array();
    }

    //================ Number of unasigned jobs =======================================
    $_unassignedSql = "SELECT `project`.`projStatus` FROM `project` WHERE `project`.`projUserID` = 'unassigned'";

    $_unassignedStmnt = $_pdo->prepare($_unassignedSql);

    if($_unassignedStmnt->execute())
    {
      $_unassigned = $_unassignedStmnt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
      array();
    }

    //================= Number of Archived jobs =======================================
    $_archivedSql = "SELECT `project`.`projStatus` FROM `project` WHERE `project`.`projStatus` = 'archive'";

    $_archivedStmnt = $_pdo->prepare($_archivedSql);

    if($_archivedStmnt->execute())
    {
      $_archived = $_archivedStmnt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
      array();
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
    // END USER LIST QUERY=============================================================
  ?>


  <div class="list-group">
    <a href="tasks.php" class="list-group-item">See All Open Jobs (<?php echo count($_numAll) ?>)</a>
    <a href="tasks.php?status=assigned" class="list-group-item list-group-item-info">Assigned (<?php echo count($_numAssigned) ?>)</a>
    <a href="tasks.php?status=inProgress" class="list-group-item list-group-item-success">In Progress (<?php echo count($_numInProg) ?>)</a>
    <a href="tasks.php?status=proofOut" class="list-group-item list-group-item-warning">Proof Out (<?php echo count($_proofOut) ?>)</a>
    <a href="tasks.php?status=finished" class="list-group-item">Finished (<?php echo count($_finished) ?>)</a>

    <?php
      if(count($_usrLst) > 0)
      {
        // foreach ($_usrLst as $key => $value)
        // {
        //   $_userNumSql = "SELECT `project`.`projStatus` AS projStatus, `project`.`projUserID` AS `projUserID` FROM `project` WHERE (projUserID='{$key}' OR projUserID2='{$key}') AND `project`.`projStatus` != 'unassigned' AND `project`.`projStatus` != 'archive' AND `project`.`projStatus` != 'deleted'";

        //   $_userNumStmnt = $_pdo->prepare($_userNumSql);

        //   if($_userNumStmnt->execute())
        //   { 
        //     $_userNum = $_userNumStmnt->fetchAll(PDO::FETCH_ASSOC);

        //     echo '<a href="tasks.php?usrID='. $value['id'] .'" class="list-group-item">(' . $value['initials'] . ') ' . $value['username'] . ' (' .count($_userNum). ')</a>';
        //   }
        //   else
        //   {
        //     echo "";
        //   }
        // }

        foreach($_usrLst as $n)
        {
          $_usrSql = "SELECT `project`.`projStatus` AS `projStatus`, `project`.`projUserID` AS `projUserID`, `project`.`projUserID2` AS `projUserID2` FROM `project` WHERE (`projUserID`=:projUser) OR (`projUserID2`=:projUser) AND `project`.`projStatus` != 'unassigned' AND `project`.`projStatus` != 'archive' AND `project`.`projStatus` != 'deleted'";

          $_usrStmnt = $_pdo->prepare($_usrSql);

          $_params =
            array
            (
              ':projUser' => $n['initials']
            );



          if($_usrStmnt->execute($_params))
          {
            $usrArr = $_usrStmnt->fetchAll(PDO::FETCH_ASSOC);

            echo '<a href="tasks.php?usrID=' .$n['initials'].' " class="list-group-item">('.$n['initials'].') '.$n['username'].'  (' .count($usrArr).')</a>';

          }
          else
          {
            die('goodbye cruel world');
          }
        }

      
      }
    ?>

    <a href="tasks.php?status=unassigned" class="list-group-item">Unassigned (<?php echo count($_unassigned) ?>)</a>
    <a href="tasks.php?status=archive" class="list-group-item">Archive (<?php echo count($_archived) ?>)</a>
  </div>
</div>