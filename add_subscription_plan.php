<?php if(isset($_GET['id'])){
    $page_title= 'Edit Subscription Plan';
}
else{
    $page_title='Add Subscription Plan';
}

$current_page="Subscription Plan";


include("includes/header.php");

require("includes/function.php");
require("language/language.php");

require_once("thumbnail_images.class.php");


if (isset($_POST['submit']) and isset($_GET['add'])) {

    $data = array(
        'name'  =>  filter_var($_POST['name'], FILTER_SANITIZE_STRING),
        'price'  =>  filter_var($_POST['price'], FILTER_SANITIZE_STRING),
        'credits'  =>  filter_var($_POST['credits'], FILTER_SANITIZE_STRING)
    );

    $qry = Insert('tbl_subscription_plan', $data);


    $_SESSION['msg'] = "10";
    header("Location:manage_subscription_plan.php");
    exit;
}

if (isset($_GET['id'])) {

    $qry = "SELECT * FROM tbl_subscription_plan WHERE id='" . $_GET['id'] . "'";
    $result = mysqli_query($mysqli, $qry);
    $row = mysqli_fetch_assoc($result);
}
if (isset($_POST['submit']) AND isset($_POST['id'])) {

    $data = array(
        'name'  =>  filter_var($_POST['name'], FILTER_SANITIZE_STRING),
        'price'  =>  filter_var($_POST['price'], FILTER_SANITIZE_STRING),
        'credits'  =>  filter_var($_POST['credits'], FILTER_SANITIZE_STRING)
    );

    $edit = Update('tbl_subscription_plan', $data, "WHERE id = '" . $_POST['id'] . "'");

    $_SESSION['msg'] = "11";
    header("Location:add_subscription_plan.php?id=" . $_POST['id']);
    exit;
}


?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="page_title_block">
                    <div class="col-md-5 col-xs-12">
                        <div class="page_title"><?php if (isset($_GET['id'])) { ?>Edit<?php } else { ?>Add<?php } ?> Subscription Plan</div>
                    </div>
                    <div class="col-sm-6" align="left" style="float: right;width:11%;margin-top:28px;">
                        <a href="manage_subscription_plan.php">
                            <h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size: 20px;color:#e91e63;"><i class="fa fa-arrow-left"></i> Back</h4>
                        </a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="card-body mrg_bottom">
                    <form action="" name="addeditcategory" method="post" class="form form-horizontal" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />

                        <div class="section">
                            <div class="section-body">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Plan Name :-

                                    </label>
                                    <div class="col-md-6">
                                        <input type="text" name="name" id="name" value="<?php if (isset($_GET['id'])) {
                                            echo $row['name'];
                                        } ?>" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Plan Price :-

                                    </label>
                                    <div class="col-md-6">
                                        <input type="text" name="price" id="price" value="<?php if (isset($_GET['id'])) {
                                            echo $row['price'];
                                        } ?>" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Plan Credits (Job Posts):-

                                    </label>
                                    <div class="col-md-6">
                                        <input type="text" name="credits" id="credits" value="<?php if (isset($_GET['id'])) {
                                            echo $row['credits'];
                                        } ?>" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-9 col-md-offset-3">
                                        <button type="submit" name="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php include("includes/footer.php"); ?>