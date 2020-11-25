<?php $page_title = "Withdrawals Request";

include('includes/header.php');
include('includes/function.php');
include('language/language.php');


$tableName = "tbl_transaction_details";
$targetpage = "manage_withdrawals.php";
$limit = 20;

$query = "SELECT COUNT(*) as num FROM $tableName WHERE trans_for = 1 AND trans_type = 1 AND type = 2 AND status = 4 AND user_updated = 0";
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

$transaction_qry = "SELECT tbl_transaction_details.*,tbl_users.name as user_name,tbl_users.account_number, tbl_users.ifsc_code FROM tbl_transaction_details
            LEFT JOIN tbl_users ON tbl_users.`id`= tbl_transaction_details.`user_id` 
            WHERE trans_for = 1 AND trans_type = 1 AND tbl_transaction_details.type = 2 AND tbl_transaction_details.status = 4  AND user_updated = 0
            ORDER BY tbl_transaction_details.`id` DESC LIMIT $start, $limit";

$transaction_result = mysqli_query($mysqli, $transaction_qry);
if(mysqli_num_rows($transaction_result))
{


while ($row = mysqli_fetch_array($transaction_result)) {

    $id = $row["id"];
    $transaction_id = $row["transaction_id"];
    $user_id = $row["user_id"];
    $user_name = $row["user_name"];
    $account_number = $row["account_number"];
    $ifsc_code = $row["ifsc_code"];
    $amount = $row["amount"];
    $created_at = $row["created_at"];

    $transDataDisp .= <<<AAA
                            <tr>
                                <td class="text-center">$amount SAR</td>
                                <td class="text-center">$transaction_id</td>
                                <td class="text-center text-capitalize">$user_name</td>
                                <td class="text-center">A/C No.: $account_number <br>IFSC:  $ifsc_code</td>
                                <td class="text-center">$created_at</td>
                                <td class="text-center">                                 
                                <a title="Approve" class="toggle_btn_a" href="javascript:void(0)" data-id="$id" data-action="active" data-column="status"><span class="badge badge-success badge-icon"><i class="fa fa-check" aria-hidden="true"></i><span>Approve</span></span></a>
                                <a title="Reject" class="toggle_btn_a" href="javascript:void(0)" data-id="$id" data-action="deactive" data-column="status"><span class="badge badge-danger badge-icon"><i class="fa fa-times" aria-hidden="true"></i><span>Reject </span></span></a>

</td>
                            </tr>
AAA;
}
}else{
    $transDataDisp = <<<AAA
                            <tr>
                                <td class="text-center" colspan="5">No Withdrawal Request Right Now</td>
                            </tr>
AAA;
}
?>
<div class="row">
    <div class="col-xs-12">
        <div class="card mrg_bottom">
            <div class="page_title_block">
                <div class="col-md-5 col-xs-12">
                    <div class="page_title"><?= $page_title ?></div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 mrg-top">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Transaction Id</th>
                        <th>User</th>
                        <th>Created At</th>
                        <th>Action</th>
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
        var _table = 'tbl_transaction_details';

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
                            'tbl_nm': 'tbl_transaction_details'
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
                                'tbl_nm': 'tbl_transaction_details'
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