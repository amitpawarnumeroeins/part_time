<?php $page_title="Manage Applied User";

include('includes/header.php');

include('includes/function.php');
include('language/language.php');



$tableName = "tbl_jobs";
$targetpage = "user_job_details.php";
$limit = 10;

$query = "SELECT COUNT(*) as num FROM $tableName WHERE user_alloted=".$_GET['user_id'];
$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query));
$total_pages = $total_pages['num'];

$stages = 3;
$page = 0;
if (isset($_GET['page'])) {
    $page = mysqli_real_escape_string($mysqli, $_GET['page']);
}
if ($page) {
    $start = ($page - 1) * $limit;
} else {
    $start = 0;
}


$users_qry = "SELECT * FROM tbl_jobs
		WHERE user_alloted=".$_GET['user_id']."
		 ORDER BY tbl_jobs.`id` DESC LIMIT $start, $limit";

$users_result = mysqli_query($mysqli, $users_qry);

?>


    <div class="row">
        <div class="col-xs-12">
            <a href="manage_users.php">
                <h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size: 20px;color:#e91e63;"><i class="fa fa-arrow-left"></i> Back</h4>
            </a>
            <div class="card mrg_bottom">
                <div class="page_title_block">
                    <div class="col-md-5 col-xs-12">
                        <div class="page_title"><?=$_GET['user_name']?>'s Job Details</div>
                    </div>

                </div>
                <div class="clearfix"></div>
                <div class="col-md-12 mrg-top">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="text-center">Job Name</th>
                            <th class="text-center">Company Name</th>
                            <th class="text-center">Dates</th>
                            <th class="text-center">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 0;
                        while ($users_row = mysqli_fetch_assoc($users_result)) {
                            ?>
                            <tr>
                                <td class="text-center">
                                    <?=$users_row['job_name']; ?>
                                </td>
                                <td class="text-center">
                                    <?=$users_row['job_company_name']; ?><br>
                                    <?=$users_row['job_company_website']; ?><br>
                                    <?=$users_row['job_phone_number']; ?><br>
                                    <?=$users_row['job_mail']; ?>
                                </td>
                                <td class="text-center">
                                    <b>Start Date: </b><?=date("d-m-Y h:m", ($users_row['job_start_time'] / 1000) ); ?><br>
                                    <b>End Date: </b><?=date("d-m-Y h:m", ($users_row['job_end_time'] / 1000) ); ?><br>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $jobStatus = $users_row['job_status'];
                                    switch ($jobStatus)
                                    {
                                        case 0:
                                            $statusName = "Pending";
                                            break;
                                        case 1:
                                            $statusName = "Completed";
                                            break;
                                        case 2:
                                            $statusName = "Rejected";
                                            break;
                                        case 3:
                                            $statusName = "Failed";
                                            break;
                                        case 0:
                                            $statusName = "Active/Working";
                                            break;
                                        default:
                                            $statusName = "Pending";
                                    }

                                    echo $statusName ?>

                                </td>
                            </tr>
                            <?php

                            $i++;
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="pagination_item_block">
                        <nav>
                            <?php
                            include("pagination.php");
                            ?>
                        </nav>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

<?php include('includes/footer.php'); ?>