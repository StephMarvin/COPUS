<!-- Sidebar -->
<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link" href="home.php?page=dashboard">
        <i class="bi bi-grid"></i>
        <span> Dashboard </span>
      </a>
    </li>
    <!-- End Dashboard -->

    <!-- Observe -->
    <li class="nav-item">

      <?php
        $observe_pages = [
          "observe-now"
        ];

        $observe_page_active = in_array($page_name, $observe_pages);
      ?>

      <a class="nav-link <?= $observe_page_active ? '' : 'collapsed' ;?>" data-bs-target="#observe-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-eye-fill"></i> <span> Observe </span> <i class="bi bi-chevron-down ms-auto"></i>
      </a>

      <ul id="observe-nav" class="nav-content collapse <?= $observe_page_active ? 'show' : '' ;?>" data-bs-parent="#sidebar-nav">

        <li class="<?= ($page_name === "observe-now") ? 'active-page' : '' ;?>">
          <a href="home.php?page=observe-now">
            <i class="bi bi-list-check"></i><span> Observe Now </span>
          </a>
        </li>

      </ul>
    </li>
    <!-- End Observe -->

    <!-- Reports -->
    <li class="nav-item">

      <?php
        $observation_pages = [
          "observations-this-semester", "observation-summary",
          "all-observations", "observation-details"
        ];

        $observation_page_active = in_array($page_name, $observation_pages);
      ?>

      <a class="nav-link <?= $observation_page_active ? '' : 'collapsed' ;?>" data-bs-target="#observations-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-clipboard-data-fill"></i> <span> Observed Classes </span> <i class="bi bi-chevron-down ms-auto"></i>
      </a>

      <ul id="observations-nav" class="nav-content collapse <?= $observation_page_active ? 'show' : '' ;?>" data-bs-parent="#sidebar-nav">

        <li class="<?= ($page_name === "observations-this-semester" || $page_name === "observation-summary") ? 'active-page' : '' ;?>">
          <a href="home.php?page=observations-this-semester">
            <i class="bi bi-calendar-week"></i><span> This Semester </span>
          </a>
        </li>

        <li class="<?= ($page_name === "all-observations" || $page_name === "observation-details") ? 'active-page' : '' ;?>">
          <a href="home.php?page=all-observations">
            <i class="bi bi-clock-history"></i><span> All Records </span>
          </a>
        </li>

      </ul>
    </li>
    <!-- Reports -->

    <!-- Actions Statistics -->
    <li class="nav-item">

      <?php
        $statistics_page = [
          "semester-stats",
          "teacher-stats",
          "student-stats"
        ];

        $stats_page_active = in_array($page_name, $statistics_page);
      ?>

      <a class="nav-link <?= $stats_page_active ? '' : 'collapsed'; ?>" data-bs-target="#statistics-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-bar-chart-fill"></i> <span> Action Statistics </span> <i class="bi bi-chevron-down ms-auto"></i>
      </a>

      <ul id="statistics-nav" class="nav-content collapse <?= $stats_page_active ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">

        <li class="<?= ($page_name === 'semester-stats') ? 'active-page' : ''; ?>">
          <a href="home.php?page=semester-stats">
            <i class="bi bi-calendar-range"></i><span> Semester Stats </span>
          </a>
        </li>

        <li class="<?= ($page_name === 'student-stats') ? 'active-page' : ''; ?>">
          <a href="home.php?page=student-stats">
            <i class="bi bi-people-fill"></i><span> Student Action Stats </span>
          </a>
        </li>

        <li class="<?= ($page_name === 'teacher-stats') ? 'active-page' : ''; ?>">
          <a href="home.php?page=teacher-stats">
            <i class="bi bi-person-video3"></i><span> Teacher Action Stats </span>
          </a>
        </li>

      </ul>
    </li>
    <!-- Actions -->

    <li class="nav-heading"> My Account </li>

    <!-- My Profile -->
    <li class="nav-item">
      <a class="nav-link <?= ($page_name) === 'user-profile' ? '' : 'collapsed'; ?>" href="home.php?page=user-profile">
        <i class="bi bi-person-fill-check"></i>
        <span> My Profile </span>
      </a>
    </li>
    <!-- End My Profile -->

    <!-- Log Out -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="logout.php">
        <i class="bi bi-box-arrow-left"></i>
        <span> Log Out </span>
      </a>
    </li>
    <!-- End Log Out -->

  </ul>

</aside>
<!-- End Sidebar -->