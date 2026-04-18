<!-- Page Title -->
<div class="pagetitle">

  <?php if (isset($_SESSION["query-status"]) && $_SESSION["query-status"] !== ""): ?>
      <div class="alert alert-<?php echo $_SESSION["query-status"]["status"]; ?> text-center" id="notification" role="alert">
          <?php echo $_SESSION["query-status"]["message"]; ?>
      </div>
      <?php unset($_SESSION["query-status"]); ?>
  <?php endif; ?>

  <div class="d-flex justify-content-between">

    <div>
      <h1> <?php echo htmlspecialchars($page_titles[$page_name]); ?> </h1>

      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="home.php?page=dashboard"> Home </a></li>
          <li class="breadcrumb-item active"> <?php echo htmlspecialchars($page_titles[$page_name]); ?> </li>
        </ol>
      </nav>
      
      <?php
        if($generated_password === "Yes") { ?>

          <div class="alert alert-danger">
            <span class="text-danger">
              You are currently using <span class="fw-bold">system generated password</span>.
              <a href="home.php?page=user-profile&update-password=true" class="fw-bold text-decoration-underline text-danger">Click here to change now.</a>
            </span>
          </div>

      <?php 
        } 
      ?>

    </div>

    <div>
      <h1 id="date-time" class="mt-2"> Date and Time: </h1>
    </div>

  </div>

</div>
<!-- End Page Title -->