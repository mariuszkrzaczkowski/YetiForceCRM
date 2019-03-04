<?php
/**
 * Index file.
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */
$config = [
	'baseURL' => \AppConfig::main('site_URL'),
	'publicDir' => '/dist',
];
?>
<!DOCTYPE html>
<html>

<head>
  <title><%= htmlWebpackPlugin.options.productName %></title>

  <meta charset="utf-8">
  <meta name="description" content="<%= htmlWebpackPlugin.options.productDescription %>">
  <meta name="format-detection" content="telephone=no">
  <meta name="msapplication-tap-highlight" content="no">
  <meta name="viewport"
    content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width<% if (htmlWebpackPlugin.options.ctx.mode.cordova) { %>, viewport-fit=cover<% } %>">

  <link rel="icon" href="/dist/statics/quasar-logo.png" type="image/x-icon">
  <link rel="icon" type="image/png" sizes="32x32" href="/dist/statics/icons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/dist/statics/icons/favicon-16x16.png">
  <script>window.CONFIG = <?php echo json_encode($config); ?>;</script>
  <script src="<%= htmlWebpackPlugin.options.modulesFile %>"></script>
</head>

<body>
  <!-- DO NOT touch the following DIV -->
  <div id="q-app"></div>
</body>

</html>
