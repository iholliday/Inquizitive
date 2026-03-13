<?php
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$isEmbeddedInDashboard = defined('IN_DASHBOARD_SHELL');

if (!$isAjax && !$isEmbeddedInDashboard) {
  $DASH_INCLUDE = __FILE__;
  require __DIR__ . '/../dashboardNavigation.php';
  exit;
}

?>

results dashboard page