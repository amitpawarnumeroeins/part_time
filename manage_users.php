<?php $page_title="Manage Users";

include('includes/header.php');
include('includes/function.php');
include('language/language.php');

function get_job_info($userId)
{
    global $mysqli;

    $qry_job_status = "SELECT SUM(if(job_status = 4, 1, 0)) AS active, SUM(if(job_status = 3, 1, 0)) AS failed, SUM(if(job_status = 1, 1, 0)) AS completed FROM tbl_jobs WHERE `user_alloted` ='" . $userId . "'";
    $res_job_status = mysqli_fetch_array(mysqli_query($mysqli, $qry_job_status));

    $qry_job_apply = "SELECT SUM(if(seen = 0, 1, 0)) AS applied, SUM(if(seen = 1, 1, 0)) AS awarded FROM tbl_apply WHERE `user_id` ='" . $userId . "'";
    $res_job_apply = mysqli_fetch_array(mysqli_query($mysqli, $qry_job_apply));

    $dataUserJob = array(
        "applied"=>$res_job_apply["applied"],
        "awarded"=>$res_job_apply["awarded"],
        "active"=>$res_job_status["active"],
        "failed"=>$res_job_status["failed"],
        "completed"=>$res_job_status["completed"]
    );
    return  $dataUserJob;
}

if(isset($_GET['user_type'])){

    $user_type = filter_var($_GET['user_type'], FILTER_SANITIZE_STRING);

    $tableName="tbl_users";
    $targetpage = "manage_users.php?user_type=".$user_type;
    $limit = 12;

    $query = "SELECT COUNT(*) as num FROM $tableName WHERE tbl_users.`id` <> 0 AND `user_type`='$user_type' AND status!=0";
    $total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query));
    $total_pages = $total_pages['num'];

    $stages = 3;
    $page=0;
    if(isset($_GET['page'])){
        $page = mysqli_real_escape_string($mysqli,$_GET['page']);
    }
    if($page){
        $start = ($page - 1) * $limit;
    }else{
        $start = 0;
    }

    $users_qry = "SELECT * FROM tbl_users WHERE tbl_users.`id` <> 0 AND `user_type`=$user_type  AND status!=0
       		ORDER BY tbl_users.`id` DESC LIMIT $start, $limit";

    $users_result = mysqli_query($mysqli, $users_qry);

}

else if (isset($_POST['user_search'])) {

    $keyword = filter_var($_POST['search_value'], FILTER_SANITIZE_STRING);

    $user_qry = "SELECT * FROM tbl_users WHERE tbl_users.`name` LIKE '%$keyword' or tbl_users.`email` LIKE '%$keyword' AND status!=0 AND tbl_users.`id` <> 0 ORDER BY tbl_users.`id` DESC";

    $users_result = mysqli_query($mysqli, $user_qry);

} else {

    $tableName = "tbl_users";
    $targetpage = "manage_users.php";
    $limit = 15;

    $query = "SELECT COUNT(*) as num FROM $tableName WHERE tbl_users.`id` <> 0  AND status!=0";
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

    $users_qry = "SELECT * FROM tbl_users WHERE tbl_users.`id` <> 0  AND status!=0
       ORDER BY tbl_users.`id` DESC LIMIT $start, $limit";

    $users_result = mysqli_query($mysqli, $users_qry);
}

?>

<style>
    .btn.btn-success {
        border-color: #18aa4a;
        border-bottom-color: #18aa4a;
        background-color: #18aa4a;
        box-shadow: 0 2px 3px rgba(41, 199, 95, 0.3);
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="card mrg_bottom">
            <div class="page_title_block">
                <div class="col-md-5 col-xs-12">
                    <div class="page_title">Manage Users</div>
                </div>
                <div class="col-md-7 col-xs-12">
                    <div class="search_list">
                        <div class="search_block">
                            <form method="post" action="">
                                <input class="form-control input-sm" placeholder="Search..." aria-controls="DataTables_Table_0" type="search" name="search_value" required>
                                <button type="submit" name="user_search" class="btn-search"><i class="fa fa-search"></i></button>
                            </form>
                        </div>
                        <div class="add_btn_primary"> <a href="add_user.php?add">Add User</a> </div>
                    </div>
                </div>
                <div style="padding: 0px 0px 5px;float: left;margin-right:10px;margin-left:20px">
                    <select name="user_type" class="form-control select2 filter" required style="padding: 5px 40px;height: 40px;">
                        <option value="">All</option>
                        <option value="1" <?php if(isset($_GET['user_type']) && $_GET['user_type']=='1'){ echo 'selected';} ?>>Job Seeker</option>
                        <option value="2" <?php if(isset($_GET['user_type']) && $_GET['user_type']=='2'){ echo 'selected';} ?>>Job Provider</option>
                    </select>
                </div>
                <div class="col-md-4 col-xs-12 text-right" style="float: right;">
                    <button type="submit" class="btn btn-danger btn_delete" style="margin-bottom:20px;" name="delete_rec" value="delete_wall"><i class="fa fa-trash"></i> Delete All</button>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 mrg-top">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th nowrap=""  class="text-center">
                            <div class="checkbox" style="margin-top: 0px;margin-bottom: 0px;">
                                <input type="checkbox" name="checkall" id="checkall" value="">
                                <label for="checkall"></label>
                            </div>
                        </th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Jobs</th>
                        <th class="text-center">Extra</th>
                        <th class="cat_action_list text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 0;
                    while ($users_row = mysqli_fetch_array($users_result)) { ?>
                        <tr>
                            <td nowrap=""  class="text-center">
                                <div class="checkbox" style="margin: 0px;float: left;">
                                    <input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i; ?>" value="<?php echo $users_row['id']; ?>" class="post_ids">
                                    <label for="checkbox<?php echo $i; ?>">
                                    </label>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="user_profile.php?user_id=<?= $users_row['id'] ?>">
                                    <?php echo $users_row['name']; ?>
                                </a><br>
                                    <?php echo $users_row['email']; ?><br>
                                    <?php echo $users_row['country_code'].$users_row['phone']; ?>
                                <br>
                                <?php
                                if ($users_row['user_type'] == 1) {
                                    echo $user_type = 'Seeker';
                                } else {
                                    echo $user_type = 'Provider';
                                }
                                ?>
                            </td>
                            <td  class="text-center" style="text-align: center">
                                <?php
                                if ($users_row['status'] == "2") { ?>
                                    Active
                                <?php } else{ ?>
                                    Disable <br>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($users_row['user_type'] == 1) {
                                $dataUserJob = get_job_info($users_row["id"]);

                                echo $dataUserJob["applied"]?"Applied: ".$dataUserJob["applied"]: "Applied: "."0";
                                echo "<br>";
                                echo $dataUserJob["active"]?"Active: ".$dataUserJob["active"]: "Active: "."0";
                                echo "<br>";
                                echo $dataUserJob["completed"]?"Completed: ".$dataUserJob["completed"]: "Completed: "."0";
                                ?>
                                <br>
                                <a href="user_job_details.php?user_id=<?= $users_row['id'] ?>&user_name=<?= $users_row['name'] ?>">
                                    details
                                </a>
                                <?php }else{
                                    echo "-";
                                }?>
                            </td>
                            <td class="text-center">
                                Wallet :<?=$users_row['current_wallet_amount'];?>

                                <?php
                                if($users_row['subscription_plan_id'])
                                {
                                    $queryPlan = "SELECT * FROM tbl_subscription_plan WHERE `id`=".$users_row['subscription_plan_id'];
                                    $resultPlan = mysqli_query($mysqli, $queryPlan);
                                    if(mysqli_num_rows($resultPlan))
                                    {
                                        $rowPlan = mysqli_fetch_assoc($resultPlan);
                                        $subscriptionPlanName = $rowPlan["name"];
                                        echo "Subscription: $subscriptionPlanName (".$users_row['credits_remaining']." credits remaining)";
                                    }
                                }
                                ?>

                            </td>
                            <td class="text-center">
                                <!--<a href="user_profile.php?user_id=<?php /*echo $users_row['id']; */?>" class="btn btn-success btn_cust" data-toggle="tooltip" data-tooltip="User Profile"><i class="fa fa-eye"></i></a>
-->
                                <a href="add_user.php?user_id=<?php echo $users_row['id']; ?>" class="btn btn-primary btn_cust" data-toggle="tooltip" data-tooltip="Edit"><i class="fa fa-edit"></i></a>

                                <a href="javascript:void(0)" data-id="<?php echo $users_row['id']; ?>" class="btn btn-danger btn_delete_a btn_cust" data-toggle="tooltip" data-tooltip="Delete !"><i class=" fa fa-trash"></i></a>

                                <?php
                                if ($users_row['status'] == "2") { ?>
                                    <a id="btn1" title="Click To Disable" class="toggle_btn_a btn btn-danger btn_cust" style="" href="javascript:void(0)" data-id="<?= $users_row['id'] ?>" data-action="deactive" data-column="status"><span class=""><i class="fa fa-times" aria-hidden="true"></i><span></span></span></a>
                                <?php } else{ ?>
                                    <a  id="btn2" title="Click To Enable" class="toggle_btn_a btn btn-success btn_cust" style="" href="javascript:void(0)" data-id="<?= $users_row['id'] ?>" data-action="active" data-column="status"><span class=""><i class="fa fa-check" aria-hidden="true"></i><span></span></a>
                                <?php } ?>
                            </td>

                        </tr>
                        <?php $i++;
                    } ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12 col-xs-12">
                <div class="pagination_item_block">
                    <nav>
                        <?php if (!isset($_POST["search"])) {
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
        var _table = 'tbl_users';

        $.ajax({
            type: 'post',
            url: 'processData.php',
            dataType: 'json',
            data: {
                id: _id,
                for_action: _for,
                column: _column,
                table: _table,
                'action': 'toggle_status',
                'tbl_id': 'id'
            },
            success: function(res) {
                console.log(res);
                if (res.status == '1') {
                    location.reload();
                }
            }
        });

    });


    $(".btn_delete_a").click(function(e) {
        e.preventDefault();

        var _ids = $(this).data("id");

        swal({
                title: "Are you sure?",
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
                        type: 'post',
                        url: 'processData.php',
                        dataType: 'json',
                        data: {
                            id: _ids,
                            'action': 'multi_delete',
                            'tbl_nm': 'tbl_users'
                        },
                        success: function(res) {
                            console.log(res);
                            if (res.status == '1') {
                                swal({
                                    title: "Successfully",
                                    text: "User is deleted...",
                                    type: "success"
                                }, function() {
                                    location.reload();
                                });
                            }
                        }
                    });
                } else {
                    swal.close();
                }
            });
    });

    $("button[name='delete_rec']").click(function(e) {
        e.preventDefault();

        var _ids = $.map($('.post_ids:checked'), function(c) {
            return c.value;
        });

        if (_ids != '') {
            swal({
                    title: "Do you really want to perform?",
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
                            type: 'post',
                            url: 'processData.php',
                            dataType: 'json',
                            data: {
                                id: _ids,
                                'action': 'multi_delete',
                                'tbl_nm': 'tbl_users'
                            },
                            success: function(res) {
                                console.log(res);
                                $('.notifyjs-corner').empty();
                                if (res.status == '1') {
                                    swal({
                                        title: "Successfully",
                                        text: "You have successfully done",
                                        type: "success"
                                    }, function() {
                                        location.reload();
                                    });
                                }
                            }
                        });
                    } else {
                        swal.close();
                    }

                });
        } else {
            swal("Sorry no users selected !!")
        }
    });


    $(".filter").on("change", function(e) {
        var _val = $(this).val();
        if (_val != '') {
            window.location.href = "manage_users.php?filter_type=" + _val;
        } else {
            window.location.href = "manage_users.php";
        }
    });

    $("select[name='user_type']").on("change",function(e){
        if($(this).val()!='')
        {
            window.location.href="manage_users.php?user_type="+$(this).val();
        }else{
            window.location.href="manage_users.php";
        }
    });



    $("#checkall").click(function() {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });
</script>