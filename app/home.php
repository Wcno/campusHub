<?php
require_once '../includes/bootstrap.php';
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title></title>
  <link href="/css/common.css" rel="stylesheet" />
  <link href="/css/layout.css" rel="stylesheet" />
</head>

<body>
  <?php loadComponent('top-wrapper') ?>

  <main class="content-wrapper">
    <header>
      <h1>Title</h1>
    </header>
    <section>
      Tu contenido
    </section>
  </main>

  <?php loadComponent('bottom-wrapper') ?>
  <script src="/js/dashboardLayout.js" />
</body>

</html>
