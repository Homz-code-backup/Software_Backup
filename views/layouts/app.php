<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'ERP' ?></title>

    <!-- Favicons -->
    <link href="<?= BASE_PATH ?>/public/assets/icons/hzi.png" rel="icon">
    <link href="<?= BASE_PATH ?>/public/assets/icons/hzi.png" rel="apple-touch-icon">

    <!-- Vendor CSS -->
    <link href="<?= BASE_PATH ?>/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" >
    <link href="<?= BASE_PATH ?>/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" >
    <link href="<?= BASE_PATH ?>/public/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" >
    <link href="<?= BASE_PATH ?>/public/assets/vendor/simple-datatables/style.css" rel="stylesheet" >

    <!-- Main CSS -->
    <link href="<?= BASE_PATH ?>/public/assets/css/style.css" rel="stylesheet">
</head>
<body class="toggle-sidebar gradient-body">


<?php include __DIR__ . '/sidebar.php'; ?>
<?php include __DIR__ . '/header.php'; ?>

<main id="main" class="main">
    <?php include $view; ?>
</main>
<?php include __DIR__ . '/footer.php'; ?>

  <script>
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("yourPassword");
    const toggleIcon = document.getElementById("toggleIcon");

    togglePassword.addEventListener("click", function () {
      const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);
      toggleIcon.classList.toggle("bi-eye");
      toggleIcon.classList.toggle("bi-eye-slash");
    });
  </script>
</body>
</html>
