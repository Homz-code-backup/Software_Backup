<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Select Home | Login</title>

  <!-- Favicons -->
  <link href="<?= BASE_PATH ?>/public/assets/img/favicon.png" rel="icon">
  <link href="<?= BASE_PATH ?>/public/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Nunito:300,400,600,700|Poppins:300,400,500,600,700"
    rel="stylesheet">

  <!-- Vendor CSS -->
  <link href="<?= BASE_PATH ?>/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= BASE_PATH ?>/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= BASE_PATH ?>/public/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="<?= BASE_PATH ?>/public/assets/vendor/remixicon/remixicon.css" rel="stylesheet">

  <!-- Main CSS -->
  <link href="<?= BASE_PATH ?>/public/assets/css/style.css" rel="stylesheet">

  <style>
    body {
      background-color: #000;
      opacity: 0.85;
    }

    footer {
      color: lightgray;
      font-size: 10px;
      text-align: center;
    }
  </style>
</head>

<body>

  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">

        <div class="container">

          <!-- Logo -->
          <div class="d-flex justify-content-center py-4">
            <img src="<?= BASE_PATH ?>/public/assets/img/selecth.jpeg" style="height:90px">
          </div>

          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center">

              <div class="card mb-3">
                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center fs-4">Login to Your Account</h5>
                  </div>
                  
<?php displayFlash(); ?>
                  <form class="row g-3 needs-validation" method="POST" action="<?= BASE_PATH ?>/reset-password">
                    <div class="col-12">
                      <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
                      <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                      <label for="password" class="form-label">New Password</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="password" name="password" id="password" class="form-control" required>
                        <div class="invalid-feedback">Please enter your password.</div>
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Reset Password</button>
                    </div>
                    <p class="text-center small"><a href="<?= BASE_PATH ?>/">Login</a></p>
                  </form>


                  <?php if (!empty($error)): ?>
                    <p style="color:red"><?= htmlspecialchars($error) ?></p>
                  <?php endif; ?>




                  <?php if (!empty($message)): ?>
                    <p><?= htmlspecialchars($message) ?></p>
                  <?php endif; ?>

                </div>
              </div>

            </div>
          </div>

        </div>
      </section>
    </div>
  </main>

  <footer>
    © 2016–<?= date('Y') ?> Select Home Interior Designers Private Limited
  </footer>

  <!-- Vendor JS -->
  <script src="<?= BASE_PATH ?>/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script>
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("yourPassword");
    const toggleIcon = document.getElementById("toggleIcon");

    togglePassword.addEventListener("click", () => {
      const type = passwordInput.type === "password" ? "text" : "password";
      passwordInput.type = type;
      toggleIcon.classList.toggle("bi-eye");
      toggleIcon.classList.toggle("bi-eye-slash");
    });
  </script>

</body>

</html>