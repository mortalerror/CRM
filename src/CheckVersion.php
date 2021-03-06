<?php
/*******************************************************************************
 *
 *  filename     : CheckVersion.php
 *  website      : http://www.churchcrm.io
 *  description  : This file checks that the ChurchCRM MySQL database is in
 *                   sync with the PHP code.
 *
 *
 *  Contributors:
 *  2006-2007 Ed Davis
 *
 *
 *  Copyright Contributors
 *
 ******************************************************************************/

// Include the function library
require 'Include/Config.php';
require 'Include/Functions.php';

use ChurchCRM\Service\SystemService;
use ChurchCRM\dto\SystemURLs;

$systemService = new SystemService();
if ($systemService->isDBCurrent()) {  //either the DB is good, or the upgrade was successful.
    Redirect('Menu.php');
    exit;
} else {        //the upgrade failed!

    $UpgradeException = 'null';
    try {
        if ($systemService->upgradeDatabaseVersion()) {
            $_SESSION['sSoftwareInstalledVersion'] = $systemService->getInstalledVersion();
            Redirect('Menu.php');
            exit;
        }
    } catch (Exception $ex) {
        $UpgradeException = $ex;
    }
    $dbVersion = $systemService->getDBVersion();
    //Set the page title
    $sPageTitle = gettext('Software Version Check');
    require 'Include/HeaderNotLoggedIn.php'; ?>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="error-page">
        <h2 class="headline text-red">500</h2>

        <div class="error-content">
          <h3><i class="fa fa-warning text-red"></i><?= gettext('Oops! Something went wrong') ?>.</h3>

          <p>
            <?= gettext('There is an incompatibility between database schema and installed software. You are seeing this message because there is a software bug or an incomplete upgrade.') ?></p>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="error-page">
        <!-- /.error-page -->
        <div class="box box-danger">
          <div class="box-body">
            <?php
              if ($UpgradeException) {
                  ?>
            <h3>There was an error upgrading your ChurchCRM database:</h3>
            <p><b><?php echo $UpgradeException->getMessage(); ?></b></p>
            <pre style="max-height:200px; overflow:scroll"> <?php print_r($UpgradeException->getTrace()); ?></pre>


                <?php
              } ?>
            <p>
              <?= gettext('Please check the following resources for assistance') ?>:
            <ul>
              <li><a href="https://github.com/ChurchCRM/CRM/issues" target="_blank"><?= gettext('GitHub issues') ?></a></li>
              <li><a href="https://gitter.im/ChurchCRM/CRM" target="_blank"><?= gettext('Developer Chat') ?></a></li>
              <li><a href="<?= SystemURLs::getSupportURL() ?>" target="_blank"><?= gettext('Docs') ?></a></li>
            </ul>
            </p>
          </div>
          <div class="box-footer">
            <p>
              <?= gettext('Software Database Version') ?> = <?= $dbVersion ?> <br/>
              <?= gettext('Software Version') ?> = <?= $_SESSION['sSoftwareInstalledVersion'] ?>
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->


  <?php
}

require 'Include/FooterNotLoggedIn.php';

?>
