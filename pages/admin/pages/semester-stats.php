<main class="main" id="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <section class="section dashboard">

        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">

                <div class="row">

                    <!-- Student Actions -->
                    <?php
                        include_once "../global-includes/components/student-actions-this-semester-stats.php";
                    ?>
                    <!-- End Student Actions -->

                    <!-- Teacher Actions -->
                    <?php
                        include_once "../global-includes/components/teacher-actions-this-semester-stats.php";
                    ?>
                    <!-- End Teacher Actions -->

                </div>

            </div>

        </div>

    </section>
    <!-- End Dashboard Main -->

</main>