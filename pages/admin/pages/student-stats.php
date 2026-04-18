
<main class="main" id="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Dashboard Main -->
    <section class="section dashboard">

        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">

                <div class="row">

                    <!-- Student Actions -->
                    <?php
                        include_once "../global-includes/components/filtered-student-action-stats.php";
                    ?>
                    <!-- End student Actions -->

                </div>

                <div class="row">

                    <!-- Current Teacher Actions -->
                    <?php
                        include_once "../global-includes/components/teacher-student-action-stats.php";
                    ?>
                    <!-- End Teacher Actions -->

                </div>

            </div>
            <!-- Left Side Columns -->

        </div>

    </section>
    <!-- End Dashboard Main -->

</main>
