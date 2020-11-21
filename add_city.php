<?php if(isset($_GET['city_id'])){ 
	$page_title= 'Edit City';
}
else{ 
	$page_title='Add City'; 
}

$current_page="City";


include("includes/header.php");

require("includes/function.php");
require("language/language.php");

require_once("thumbnail_images.class.php");


if (isset($_POST['submit']) and isset($_GET['add'])) {

    $region_country_id = explode("-",$_POST['region_country_id']);
    $data = array(
        'city_name'  =>  filter_var($_POST['city_name'], FILTER_SANITIZE_STRING),
        'country_id'  =>  filter_var($region_country_id[0], FILTER_SANITIZE_STRING),
        'region_id'  =>  filter_var($region_country_id[1], FILTER_SANITIZE_STRING)
    );

	$qry = Insert('tbl_city', $data);

	
	$_SESSION['msg'] = "10";
	header("Location:manage_city.php");
	exit;
}

if (isset($_GET['city_id'])) {

	$qry = "SELECT * FROM tbl_city WHERE c_id='" . $_GET['city_id'] . "'";
	$result = mysqli_query($mysqli, $qry);
	$row = mysqli_fetch_assoc($result);
}

if (isset($_POST['submit']) AND isset($_POST['city_id'])) {

    $region_country_id = explode("-",$_POST['region_country_id']);
	$data = array(
		'city_name'  =>  filter_var($_POST['city_name'], FILTER_SANITIZE_STRING),
        'country_id'  =>  filter_var($region_country_id[0], FILTER_SANITIZE_STRING),
        'region_id'  =>  filter_var($region_country_id[1], FILTER_SANITIZE_STRING)
	);

	$city_edit = Update('tbl_city', $data, "WHERE c_id = '" . $_POST['city_id'] . "'");
	
	$_SESSION['msg'] = "11";
	header("Location:add_city.php?city_id=" . $_POST['city_id']);
	exit;
}


$qryCountryRegion = "SELECT tbl_country.id as country_id, tbl_country.name as country_name,  tbl_region.id as region_id, tbl_region.name as region_name FROM tbl_country,tbl_region WHERE tbl_country.id = tbl_region.country_id AND  tbl_country.status='1' AND  tbl_region.status='1'";
$resultCountryRegion = mysqli_query($mysqli, $qryCountryRegion);
//echo mysqli_num_rows($resultCountryRegion);
//echo 123;
?>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="page_title_block">
				<div class="col-md-5 col-xs-12">
					<div class="page_title"><?php if (isset($_GET['city_id'])) { ?>Edit<?php } else { ?>Add<?php } ?> City</div>
				</div>
				<div class="col-sm-6" align="left" style="float: right;width:11%;margin-top:28px;">
					<a href="manage_city.php">
						<h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size: 20px;color:#e91e63;"><i class="fa fa-arrow-left"></i> Back</h4>
					</a>
				</div>
			</div>
			<div class="clearfix"></div>
				<div class="card-body mrg_bottom">
					<form action="" name="addeditcategory" method="post" class="form form-horizontal" enctype="multipart/form-data">
						<input type="hidden" name="city_id" value="<?php echo $_GET['city_id']; ?>" />

						<div class="section">
							<div class="section-body">
								<div class="form-group">
									<label class="col-md-3 control-label">City Name :-

									</label>
									<div class="col-md-6">
										<input type="text" name="city_name" id="city_name" value="<?php if (isset($_GET['city_id'])) {
											echo $row['city_name'];
										} ?>" class="form-control" required>
									</div>
								</div>
                                <div class="form-group" style="margin-bottom: 10px">
                                    <label class="col-md-3 control-label">Country : Region :-

                                    </label>
                                    <div class="col-md-6">
                                        <select type="text" name="region_country_id" id="region_country_id" class="form-control input" required>
                                            <option value="">Please Select Country - Region </option>
                                                <?php
                                                while ($rowCountryRegion = mysqli_fetch_assoc($resultCountryRegion))
                                                {
                                                    $idCountry = $rowCountryRegion["country_id"];
                                                    $nameCountry = $rowCountryRegion["country_name"];
                                                    $idRegion = $rowCountryRegion["region_id"];
                                                    $nameRegion = $rowCountryRegion["region_name"];
                                                    $selected = "";
                                                    if($row["country_id"]== $idCountry AND $row["region_id"]== $idRegion)
                                                    {
                                                        $selected = "selected = selected";
                                                    }
                                                    echo "<option value='$idCountry-$idRegion' $selected>$nameCountry : $nameRegion</option>";
                                                }

                                                ?>
                                        </select>
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