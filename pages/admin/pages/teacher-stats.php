<main class="main" id="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Dashboard Main -->
    <section class="section dashboard">

        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">

                <div class="row">

                    <!-- Teacher Actions -->
                    <?php
                        include_once "../global-includes/components/filtered-teacher-action-stats.php";
                    ?>
                    <!-- End Teacher Actions -->

                </div>

                <div class="row">

                    <!-- Current Teacher Actions -->
                    <?php
                        include_once "../global-includes/components/teacher-teacher-action-stats.php";
                    ?>
                    <!-- End Teacher Actions -->

                </div>

            </div>
            <!-- Left Side Columns -->

        </div>

    </section>
    <!-- End Dashboard Main -->

</main>