<?php $page_title="Active jobs";

include('includes/header.php');

include('includes/function.php');
include('language/language.php');
function convertDateTime($unixTime) {
    $seconds = $unixTime / 1000;
    return date("d-M-y", $seconds);
}
if (isset($_POST['user_search'])) {

    $keyword = filter_var($_POST['search_value'], FILTER_SANITIZE_STRING);

    $user_qry ="SELECT * FROM tbl_apply 
		 LEFT JOIN tbl_jobs ON tbl_jobs.`id`= tbl_apply.`job_id`
         LEFT JOIN tbl_users ON tbl_users.`id`= tbl_apply.`user_id`
        WHERE tbl_apply.`seen` = 1
			 AND  (tbl_users.`name` LIKE '$keyword' OR tbl_users.`email` LIKE '$keyword') 
			 ORDER BY tbl_users.`id` DESC";

    $users_result = mysqli_query($mysqli, $user_qry);

} else {


    $tableName = "tbl_apply";
    $targetpage = "manage_applied_users.php";
    $limit = 10;

    $query = "SELECT COUNT(*) as num FROM $tableName";
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


    $users_qry = "SELECT * FROM tbl_apply
		 LEFT JOIN tbl_jobs ON tbl_jobs.`id`= tbl_apply.`job_id`
		 LEFT JOIN tbl_users ON tbl_users.`id`= tbl_apply.`user_id`
		 WHERE tbl_apply.`seen` = 1
		 ORDER BY tbl_apply.`ap_id` DESC LIMIT $start, $limit";

    $users_result = mysqli_query($mysqli, $users_qry);
}
if (isset($_GET['apply_id'])) {

    Delete('tbl_apply', 'ap_id=' . $_GET['apply_id'] . '');

    $_SESSION['msg'] = "12";
    header("Location:manage_applied_users.php");
    exit;
}




?>


<div class="row">
    <div class="col-xs-12">
        <div class="card mrg_bottom">
            <div class="page_title_block">
                <div class="col-md-5 col-xs-12">
                    <div class="page_title"><?=$page_title?></div>
                </div>
                <div class="col-md-7 col-xs-12">
                    <div class="search_list">
                        <div class="search_block">
                            <form method="post" action="">
                                <input class="form-control input-sm" placeholder="Search..." aria-controls="DataTables_Table_0" type="search" name="search_value" required>
                                <button type="submit" name="user_search" class="btn-search"><i class="fa fa-search"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>

            <div class="col-md-12 mrg-top">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th class="text-center">Job Title</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Applied On</th>
                        <th class="text-center">Start Date</th>
                        <th class="text-center">End Date</th>
                        <th class="text-center">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 0;
                    while ($users_row = mysqli_fetch_array($users_result)) { ?>
                        <tr>
                            <td class="text-center"><?=$users_row['job_name']; ?></td>
                            <td class="text-center">
                                <b><?=$users_row['name']; ?></b><br>
                                <small><?=$users_row['email']; ?></small><br>
                                <small> <?=$users_row['phone']; ?></small><br>
                                <?php if (isset($users_row['user_resume'])) { ?><a href="<?php echo 'uploads/' . $users_row['user_resume']; ?>" class="btn btn-success btn-xs" target="_blank" style="padding: 5px 10px;">Resume</a><?php } ?>
                            </td>
                            <td class="text-center"><?php echo $users_row['apply_date']; ?></td>
                            <td class="text-center"><?php echo convertDateTime($users_row['job_start_time']); ?></td>
                            <td class="text-center"><?php if($users_row['job_end_time']) echo convertDateTime($users_row['job_end_time']); else echo "-" ?></td>
                            <td class="text-center">
                                <?php
                                switch ($users_row['job_status'])
                                {
                                    case 1:
                                        $status = "Completed";
                                        $statusColor = "success";
                                        break;
                                    case 2:
                                        $status = "Rejected";
                                        $statusColor = "danger";
                                        break;
                                    case 3:
                                        $status = "Failed";
                                        $statusColor = "danger";
                                        break;
                                    case 4:
                                        $status = "Alloted/Working";
                                        $statusColor = "warning";
                                        break;
                                    default:
                                        $status = "pending";
                                        $statusColor = "primary";

                                }

                                echo "<b class='text-$statusColor'>$status</b>"
                                ?>


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
                        <?php if (!isset($_POST["user_search"])) {
                            include("pagination.php");
                        } ?>
                    </nav>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>


<script type="text/javascript">

    $(".toggle_btn_a").on("click", function(e) {
        e.preventDefault();

        var _for = $(this).data("action");
        var _id = $(this).data("id");
        var _column = $(this).data("column");
        var _table = 'tbl_apply';

        $.ajax({
            type: 'post',
            url: 'processData.php',
            dataType: 'json',
            data: {id: _id,for_action: _for,column: _column,table: _table,'action': 'toggle_status','tbl_id': 'ap_id'},
            success: function(res) {
                console.log(res);
                if (res.status == '1') {
                    location.reload();
                }
            }
        });

    });


    // for multiple deletes
    $(".btn_delete_all").click(function(e){
        var _ids = $.map($('.post_ids:checked'), function(c){return c.value; });

        if(_ids!='')
        {
            swal({
                    title: "Are you sure to delete this records?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger btn_edit",
                    cancelButtonClass: "btn-warning btn_edit",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                },

                function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            type:'post',
                            url:'processData.php',
                            dataType:'json',
                            data:{ids:_ids,'action':'removeapply'},
                            success:function(res){
                                console.log(res);
                                if(res.status=='1'){
                                    swal({
                                        title: "Successfully",
                                        text: "Data has been deleted...",
                                        type: "success"
                                    },function() {
                                        location.reload();
                                    });
                                }
                                else{
                                    swal("Something went to wrong !");
                                }
                            }
                        });
                    }
                    else{
                        swal.close();
                    }

                });
        }
        else{
            swal("Sorry no records selected !!")
        }
    });

    $(".btn_delete").click(function(e){
        e.preventDefault();

        var _ids=$(this).data("id");
        swal({
                title: "Are you sure to delete?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger btn_edit",
                cancelButtonClass: "btn-warning btn_edit",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            },

            function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type:'post',
                        url:'processData.php',
                        dataType:'json',
                        data:{ids:_ids,'action':'removeapply'},
                        success:function(res){
                            console.log(res);
                            if(res.status=='1'){
                                swal({
                                    title: "Successfully",
                                    text: "Applied has been deleted...",
                                    type: "success"
                                },function() {
                                    location.reload();
                                });
                            }
                            else{
                                swal("Something went to wrong !");
                            }
                        }
                    });
                }
                else{
                    swal.close();
                }

            });

    });
</script>