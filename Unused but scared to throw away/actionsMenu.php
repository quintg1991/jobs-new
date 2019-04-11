<ul class="menuActions">
    <li class="title">ACTIONS:</li>
    <li><a href="index.php">VIEW ALL JOBS</a></li>
    <li><a href="detail.php">CREATE A JOB</a></li>
    <?php /*<li><a href="exportCSV.php?s=<?php echo $projStatus; ?>&amp;u=<?php echo $projUserID; ?>&amp;q=<?php echo $searchQuery; ?>">EXPORT JOB LIST (.csv)</a></li>*/ ?>
    <li><a href="exportCSV.php?queryParams=<?php echo $queryParams; ?>">EXPORT JOB LIST (.csv)</a></li>
    <li><a href="reports.php">REPORTS</a></li>
    <li id="search">
    <form action="index.php" method="get" name="search">
    <input type="test" name="q" value="<?php echo ($_GET['q'] != "" ? $_GET['q'] : "Search..."); ?>" onfocus="if (this.value='Search...'){this.value='';}" onblur="if (this.value == ''){this.value='Search...';}" /><a href="javascript:document.search.submit();">&gt;</a>
    </form>
    </li>
</ul>