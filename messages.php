<?php $page_title="Manage City";

include('includes/header.php');

include('includes/function.php');
include('language/language.php');

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
?>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone No</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    echo 123;
                        include ("plugin/dbconfig.php");
                        $ref = "user/";
                        $fetchdata = $database->getReference($ref)->getValue();

                    ?>
                        <tr>
                            <td>Id</td>
                            <td>Name</td>
                            <td>Email</td>
                            <td>Phone No</td>
                            <td>Edit</td>
                            <td>Delete</td>
                        </tr>
                    </tbody>
                    <?php

                    print_r($fetchdata);
                        echo 123;?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>