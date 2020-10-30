<?php $page_title = "Manage Transactions";

include('includes/header.php');
include('includes/function.php');
include('language/language.php');


if (isset($_GET['filter'])) {

    $type = filter_var($_GET['type'], FILTER_SANITIZE_STRING);
    $mode = filter_var($_GET['mode'], FILTER_SANITIZE_STRING);
    $status = filter_var($_GET['status'], FILTER_SANITIZE_STRING);
    $trans_type = filter_var($_GET['trans_type'], FILTER_SANITIZE_STRING);

    $tableName = "tbl_transaction_details";

    $filter = "";
    $filterSql = "";
    if ($type) {
        $filter .= "type=$type&";
        $filterSql .= " AND tbl_transaction_details.`type`='$type' ";
    }

    if ($mode) {
        $filter .= "mode=$mode&";
        $filterSql .= " AND `mode`='$mode' ";
    }

    if ($status) {
        $filter .= "status=$status&";
        $filterSql .= " AND tbl_transaction_details.`status`='$status' ";
    }

    if ($trans_type) {
        $filter .= "trans_type=$trans_type&";
        $filterSql .= " AND `trans_type`='$trans_type' ";
    }

    $targetpage = "manage_transactions.php?$filter";
    $limit = 20;

    $query = "SELECT COUNT(*) as num FROM tbl_transaction_details WHERE `status`='$transaction_type'";

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

    $transaction_qry = "SELECT tbl_transaction_details.*,tbl_users.name as user_name FROM tbl_transaction_details
		LEFT JOIN tbl_users ON tbl_users.`id`= tbl_transaction_details.`user_id` 
		WHERE 1 $filterSql
		ORDER BY tbl_transaction_details.`id` DESC LIMIT $start, $limit";

    $transaction_result = mysqli_query($mysqli, $transaction_qry);

} else {

    $tableName = "tbl_transaction_details";
    $targetpage = "manage_transactions.php";
    $limit = 20;

    $query = "SELECT COUNT(*) as num FROM $tableName WHERE 1";
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

    $transaction_qry = "SELECT tbl_transaction_details.*,tbl_users.name as user_name FROM tbl_transaction_details
		LEFT JOIN tbl_users ON tbl_users.`id`= tbl_transaction_details.`user_id` 
		WHERE 1 
		ORDER BY tbl_transaction_details.`id` DESC LIMIT $start, $limit";

    $transaction_result = mysqli_query($mysqli, $transaction_qry);
}

while ($row = mysqli_fetch_array($transaction_result)) {

    $transaction_id = $row["transaction_id"];
    $type = $row["type"] == 1 ? "+Credit" : "-Debit";
    $typeColor = $row["type"] == 1 ? "text-success" : "text-danger";
    $user_id = $row["user_id"];
    $user_name = $row["user_name"];
    $amount = $row["amount"];
    $status = $row["status"];
    $mode = $row["mode"];
    $bank_trans_id = $row["bank_trans_id"];
    $bank_trans_response = $row["bank_trans_response"];
    $trans_type = $row["trans_type"] == 1 ? "Bank" : "Wallet";
    $user_updated = $row["user_updated"];
    $created_at = $row["created_at"];

    switch ($status) {
        case 1:
            $statusText = "Approved";
            $statusColor = "text-success";
            break;
        case 2:
            $statusText = "Rejected";
            $statusColor = "text-danger";
            break;
        case 3:
            $statusText = "Failed";
            $statusColor = "text-danger";
            break;
        case 4:
            $statusText = "Pending";
            $statusColor = "text-warning";
            break;
        default:
            "Pending";
            $statusColor = "text-warning";

    }

    $transDataDisp .= <<<AAA
                        <tr>
                            <td class="$typeColor text-center">$type</td>
                            <td class="text-center">$amount</td>
                            <td class="$statusColor text-center">$transaction_id <br> $statusText</td>
                            <td class="text-center text-capitalize">$user_name</td>
                            <td class="text-center">$trans_type <br> </td>
                            <td class="text-center">$created_at</td>
                        </tr>
AAA;
} ?>
<div class="row">
    <div class="col-xs-12">
        <div class="card mrg_bottom">
            <div class="page_title_block">
                <div class="col-md-5 col-xs-12">
                    <div class="page_title"><?= $page_title ?></div>
                </div>
                <div class="col-md-7 col-xs-12">
                    <div class="page_title">
                        <form class="form-inline" action="" method="get">
                            <div class="form-group mr10">
                                <label for="trans_type">Method</label>
                                <select name="trans_type" class="form-control">
                                    <option value="">All</option>
                                    <option value="1" <?php if (isset($_GET['trans_type']) && $_GET['trans_type'] == '1') {
                                        echo 'selected';
                                    } ?>>Bank
                                    </option>
                                    <option value="2" <?php if (isset($_GET['trans_type']) && $_GET['trans_type'] == '2') {
                                        echo 'selected';
                                    } ?>>Wallet
                                    </option>
                                </select>
                            </div>
                            <div class="form-group mr10">
                                <label for="trans_type">Type</label>
                                <select name="type" class="form-control">
                                    <option value="">All</option>
                                    <option value="1" <?php if (isset($_GET['type']) && $_GET['type'] == '1') {
                                        echo 'selected';
                                    } ?>>Credit
                                    </option>
                                    <option value="2" <?php if (isset($_GET['type']) && $_GET['type'] == '2') {
                                        echo 'selected';
                                    } ?>>Debit
                                    </option>
                                </select>
                            </div>
                            <div class="form-group mr10">
                                <label for="trans_type">Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="1" <?php if (isset($_GET['status']) && $_GET['status'] == '1') {
                                        echo 'selected';
                                    } ?>>Approved
                                    </option>
                                    <option value="2" <?php if (isset($_GET['status']) && $_GET['status'] == '2') {
                                        echo 'selected';
                                    } ?>>Rejected
                                    </option>
                                    <option value="3" <?php if (isset($_GET['status']) && $_GET['status'] == '3') {
                                        echo 'selected';
                                    } ?>>Failed
                                    </option>
                                    <option value="4" <?php if (isset($_GET['status']) && $_GET['status'] == '4') {
                                        echo 'selected';
                                    } ?>>Pending
                                    </option>
                                </select>
                            </div>
                            <!--                   <div class="form-group">
                            <label for="trans_type">Mode</label>
                            <select name="mode" class="form-control" >
                                <option value="">All</option>
                                <option value="1" <?php /*if(isset($_GET['mode']) && $_GET['status']=='1'){ echo 'selected';} */ ?>>mode</option>
                                <option value="2" <?php /*if(isset($_GET['mode']) && $_GET['status']=='2'){ echo 'selected';} */ ?>>mode</option>
                            </select>
                        </div>-->
                            <button type="submit" name="filter" class="btn-search bg-warning">Filter</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 mrg-top">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Transaction Id</th>
                        <th>User</th>
                        <th>Transaction Type</th>
                        <th>created_at</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?= $transDataDisp ?>
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
    $(".toggle_btn_a").on("click", function (e) {
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
            success: function (res) {
                console.log(res);
                if (res.status == '1') {
                    location.reload();
                }
            }
        });

    });


    $(".btn_delete_a").click(function (e) {
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
            function (isConfirm) {
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
                        success: function (res) {
                            console.log(res);
                            if (res.status == '1') {
                                swal({
                                    title: "Successfully",
                                    text: "User is deleted...",
                                    type: "success"
                                }, function () {
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

    $("button[name='delete_rec']").click(function (e) {
        e.preventDefault();

        var _ids = $.map($('.post_ids:checked'), function (c) {
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
                function (isConfirm) {
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
                            success: function (res) {
                                console.log(res);
                                $('.notifyjs-corner').empty();
                                if (res.status == '1') {
                                    swal({
                                        title: "Successfully",
                                        text: "You have successfully done",
                                        type: "success"
                                    }, function () {
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


    $(".filter").on("change", function (e) {
        var _val = $(this).val();
        if (_val != '') {
            window.location.href = "manage_users.php?filter_type=" + _val;
        } else {
            window.location.href = "manage_users.php";
        }
    });

    $("select[name='transaction_type']").on("change", function (e) {
        if ($(this).val() != '') {
            window.location.href = "manage_users.php?transaction_type=" + $(this).val();
        } else {
            window.location.href = "manage_users.php";
        }
    });


    $("#checkall").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });
</script>