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
     
    <!-- Account Management -->
    <li class="nav-item">

      <?php
        $accounts_pages = [
          "admin-accounts", "add-admin", "admin-details",
          "deans-accounts", "add-dean", "deans-details",
          "teachers-accounts", "add-teacher", "teacher-details",
          "observers-accounts", "add-observer", "observer-details"
        ];

        $account_page_active = in_array($page_name, $accounts_pages);
      ?>

      <a class="nav-link <?= $account_page_active ? '' : 'collapsed' ;?>" data-bs-target="#account-management-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-people-fill"></i> <span> Account Management </span> <i class="bi bi-chevron-down ms-auto"></i>
      </a>

      <ul id="account-management-nav" class="nav-content collapse <?= $account_page_active ? 'show' : '' ;?>" data-bs-parent="#sidebar-nav">

        <li class="<?= ($page_name === "admin-accounts" || $page_name === "add-admin" || $page_name === "admin-details") ? 'active-page' : '' ;?>">
          <a href="home.php?page=admin-accounts">
            <i class="bi bi-person-fill-gear"></i> <span> Admin Accounts </span>
          </a>
        </li>

        <li class="<?= ($page_name === "deans-accounts" || $page_name === "add-dean" || $page_name === "deans-details") ? 'active-page' : '' ;?>">
          <a href="home.php?page=deans-accounts">
            <i class="bi bi-person-vcard-fill"></i> <span> Deans Accounts </span>
          </a>
        </li>

        <li class="<?= ($page_name === "teachers-accounts" || $page_name === "add-teacher" || $page_name === "teacher-details") ? 'active-page' : '' ;?>">
          <a href="home.php?page=teachers-accounts">
            <i class="bi bi-person-fill-check"></i> <span> Teacher Accounts </span>
          </a>
        </li>

        <li class="<?= ($page_name === "observers-accounts" || $page_name === "add-observer" || $page_name === "observer-details") ? 'active-page' : '' ;?>">
          <a href="home.php?page=observers-accounts">
            <i class="bi bi-person-workspace"></i> <span> Observers Accounts </span>
          </a>
        </li>

      </ul>
    </li>
    <!-- End Account Management -->

    <!-- Reports -->
    <li class="nav-item">

      <?php
        $observation_pages = [
          "teacher-records", "teacher-observation-records",
          "observation-records", "observation-details",
          "summative-reports", "summative-report-details"
        ];

        $observation_page_active = in_array($page_name, $observation_pages);
      ?>

      <a class="nav-link <?= $observation_page_active ? '' : 'collapsed' ;?>" data-bs-target="#observations-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-clipboard-data-fill"></i> <span> Reports </span> <i class="bi bi-chevron-down ms-auto"></i>
      </a>

      <ul id="observations-nav" class="nav-content collapse <?= $observation_page_active ? 'show' : '' ;?>" data-bs-parent="#sidebar-nav">

        <li class="<?= ($page_name === "teacher-records" || $page_name === "teacher-observation-records") ? 'active-page' : '' ;?>">
          <a href="home.php?page=teacher-records">
            <i class="bi bi-graph-up-arrow"></i><span> Teacher Records </span>
          </a>
        </li>

        <li class="<?= ($page_name === "observation-records" || $page_name === "observation-details") ? 'active-page' : '' ;?>">
          <a href="home.php?page=observation-records">
            <i class="bi bi-calendar-week"></i><span> Observation Records </span>
          </a>
        </li>

        <li class="<?= ($page_name === "summative-reports" || $page_name === "summative-report-details") ? 'active-page' : '' ;?>">
          <a href="home.php?page=summative-reports">
            <i class="bi bi-clipboard-data"></i><span> Summative Reports </span>
          </a>
        </li>

      </ul>
    </li>
    <!-- Reports -->

    <!-- Academic Management -->
    <li class="nav-item">

      <?php
        $academic_pages = [
          "academic-year",
          "academic-settings", "academic-year-details",
          "subjects",
          "departments"
        ];

        $academic_page_active = in_array($page_name, $academic_pages);
      ?>

      <a class="nav-link <?= $academic_page_active ? '' : 'collapsed' ;?>" data-bs-target="#academic-management-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-diagram-3"></i> <span> Academic Structure </span> <i class="bi bi-chevron-down ms-auto"></i>
      </a>

      <ul id="academic-management-nav" class="nav-content collapse <?= $academic_page_active ? 'show' : '' ;?>" data-bs-parent="#sidebar-nav">

        <li class="<?= ($page_name === "academic-year") ? 'active-page' : '' ;?>">
          <a href="home.php?page=academic-year">
            <i class="bi bi-calendar-check-fill"></i><span> Academic Year </span>
          </a>
        </li>

        <li class="<?= ($page_name === "academic-settings" || $page_name === "academic-year-details") ? 'active-page' : '' ;?>">
          <a href="home.php?page=academic-settings">
            <i class="bi bi-gear-fill"></i><span> Semester Settings </span>
          </a>
        </li>

        <li class="<?= ($page_name === "departments") ? 'active-page' : '' ;?>">
          <a href="home.php?page=departments">
            <i class="bi bi-building"></i><span> Departments </span>
          </a>
        </li>

        <li class="<?= ($page_name === "subjects") ? 'active-page' : '' ;?>">
          <a href="home.php?page=subjects">
            <i class="bi bi-journal-bookmark-fill"></i><span> Subjects </span>
          </a>
        </li>

      </ul>
    </li>
    <!-- End Academic Management -->

    <!-- Actions -->
    <li class="nav-item">

      <?php
        $action_pages = [
          "teacher-actions",
          "student-actions"
        ];

        $action_page_active = in_array($page_name, $action_pages);
      ?>

      <a class="nav-link <?= $action_page_active ? '' : 'collapsed'; ?>" data-bs-target="#actions-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-activity"></i> <span> Student/Teacher Actions </span> <i class="bi bi-chevron-down ms-auto"></i>
      </a>

      <ul id="actions-nav" class="nav-content collapse <?= $action_page_active ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">

        <li class="<?= ($page_name === 'teacher-actions') ? 'active-page' : ''; ?>">
          <a href="home.php?page=teacher-actions">
            <i class="bi bi-person-video3"></i><span> Teacher Actions </span>
          </a>
        </li>

        <li class="<?= ($page_name === 'student-actions') ? 'active-page' : ''; ?>">
          <a href="home.php?page=student-actions">
            <i class="bi bi-person-vcard"></i><span> Student Actions </span>
          </a>
        </li>

      </ul>
    </li>
    <!-- Actions -->

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

    <!-- Archived Accounts -->
    <li class="nav-item">

      <?php
        $archives_pages = [
          "archived-admin-accounts",
          "archived-deans-accounts",
          "archived-teachers-accounts",
          "archived-observers-accounts"
        ];

        $archive_page_active = in_array($page_name, $archives_pages);
      ?>

      <a class="nav-link <?= $archive_page_active ? '' : 'collapsed' ;?>" data-bs-target="#archived-accounts-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-archive-fill"></i> <span> Archived Accounts </span> <i class="bi bi-chevron-down ms-auto"></i>
      </a>

      <ul id="archived-accounts-nav" class="nav-content collapse <?= $archive_page_active ? 'show' : '' ;?>" data-bs-parent="#sidebar-nav">

        <li class="<?= ($page_name === "archived-admin-accounts") ? 'active-page' : '' ;?>">
          <a href="home.php?page=archived-admin-accounts">
            <i class="bi bi-person-fill-lock"></i> <span> Admin Accounts </span>
          </a>
        </li>

        <li class="<?= ($page_name === "archived-deans-accounts") ? 'active-page' : '' ;?>">
          <a href="home.php?page=archived-deans-accounts">
            <i class="bi bi-person-fill-lock"></i> <span> Deans Accounts </span>
          </a>
        </li>

        <li class="<?= ($page_name === "archived-teachers-accounts") ? 'active-page' : '' ;?>">
          <a href="home.php?page=archived-teachers-accounts">
            <i class="bi bi-person-fill-lock"></i> <span> Teacher Accounts </span>
          </a>
        </li>

        <li class="<?= ($page_name === "archived-observers-accounts") ? 'active-page' : '' ;?>">
          <a href="home.php?page=archived-observers-accounts">
            <i class="bi bi-person-fill-lock"></i> <span> Observers Accounts </span>
          </a>
        </li>

      </ul>
    </li>
    <!-- End Archived Accounts -->

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