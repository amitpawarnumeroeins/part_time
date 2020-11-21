<?php $page_title="Manage Reviews & Ratings";

include('includes/header.php');

include('includes/function.php');
include('language/language.php');


if (isset($_POST['search'])) {

    $keyword = filter_var($_POST['search_value'], FILTER_SANITIZE_STRING);

    $reviews_ratings_qry = "SELECT * FROM tbl_reviews_ratings WHERE tbl_reviews_ratings.`name` LIKE '%$keyword%' ORDER BY tbl_reviews_ratings.`id` DESC";

    $result = mysqli_query($mysqli, $reviews_ratings_qry);

} else {

    $tableName = "tbl_reviews_ratings";
    $targetpage = "manage_reviews_ratings.php";
    $limit = 15;

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


    $reviews_ratings_qry = "SELECT * FROM tbl_reviews_ratings
		 ORDER BY tbl_reviews_ratings.`id` DESC LIMIT $start, $limit";

    $result = mysqli_query($mysqli, $reviews_ratings_qry);
}

?>


<div class="row">
    <div class="col-xs-12">
        <div class="card mrg_bottom">
            <div class="page_title_block">
                <div class="col-md-5 col-xs-12">
                    <div class="page_title">Manage Reviews & Ratings</div>
                </div>
                <div class="col-md-7 col-xs-12">
                    <div class="search_list">
                        <div class="search_block">
                            <form method="post" action="">
                                <input class="form-control input-sm" placeholder="Search..." aria-controls="DataTables_Table_0" type="search" name="search_value" required>
                                <button type="submit" name="search" class="btn-search"><i class="fa fa-search"></i></button>
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
                        <th>Reviews</th>
                        <th>Ratings</th>
                        <th>User Name</th>
                        <th>Reviewer</th>
                        <th class="cat_action_list">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 0;
                    while ($row = mysqli_fetch_array($result)) {

                        ?>
                        <tr>
                            <td class="text-capitalize"><?php echo $row['review']; ?></td>
                            <td><?php echo $row['rating']; ?></td>
                            <td><?php echo user_info($row['user_id'],"name"); ?></td>
                            <td><?php echo user_info($row['reviewer_id'],"name"); ?></td>
                            <td>
                              <a href="" data-id="<?php echo $row['id']; ?>" data-toggle="tooltip" data-tooltip="Delete" class="btn btn-danger btn_delete"><i class="fa fa-trash"></i></a></a></td>
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
        var _table = 'tbl_reviews_ratings';

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


    // for deletion
    $(".btn_delete").click(function(e) {
        e.preventDefault();

        var _ids = $(this).data("id");

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
                        type: 'post',
                        url: 'processData.php',
                        dataType: 'json',
                        data: {
                            'id': _ids,
                            'action': 'multi_delete',
                            'tbl_nm': 'tbl_reviews_ratings'
                        },
                        success: function(res) {
                            console.log(res);
                            if (res.status == '1') {
                                swal({
                                    title: "Successfully",
                                    text: "reviews_ratings is deleted...",
                                    type: "success"
                                }, function() {
                                    location.reload();
                                });
                            } else if (res.status == '-2') {
                                swal(res.message);
                            }
                        }
                    });
                } else {
                    swal.close();
                }
            });
    });
</script>