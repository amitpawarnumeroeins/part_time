<?php
include("includes/header.php");
include("includes/function.php");

$file_path = getBaseUrl().'api.php';
$ios_file_path = getBaseUrl().'ios_api.php';
?>
    <div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                Example API urls
            </div>
            <div class="card-body no-padding">
        <pre>
               <code class="html">
                <?php
                if(file_exists('api.php'))
                {
                    echo '<br><b>Main API URL</b>&nbsp; '.$file_path;
                }

                if(file_exists('ios_api.php'))
                {
                    // echo '<br><b>iOS API URL</b>&nbsp; '.$ios_file_path;
                }
                ?>
				     <br><b>Home</b>(Method: get_home)(Parameter: user_id)
                     <br><b>Latest Job</b>(Method: get_latest_job)(Parameter: page,user_id)
                     <br><b>Recent Job</b>(Method: get_recent_job)(Parameter: user_id,page)
                     <br><b>Category List</b>(Method: get_category)(Parameter: page)
                     <br><b>Jobs list by Cat ID</b>(Method: get_job_by_cat_id)(Parameter: user_id,cat_id,page)
                     <br><b>Category AND Company AND City List</b>(Method: get_list)
                     <br><b>Search Jobs</b>(Method: get_search_job)(Parameter: user_id,cat_id,city_id,job_company_name,search_text,job_type(Full Time, Half Time, Hourly),page)
                     <br><b>Search by Keyword</b>(Method: search_by_keyword)(Parameter: user_id,search_text,page)
                     <br><b>Single Job</b>(Method: get_single_job)(Parameter: job_id,user_id)
                     <br><b>Similar Jobs</b>(Method: get_similar_jobs)(Parameter: user_id,job_id,page)
					 <br><b>User Register(Job Seeker)(Method: user_register)(Parameter: user_type [1],name,email,password,phone,register_as [individual])
					 <br><b>User Register(Job Provider)(Method: user_register)(Parameter: user_type [2],name,email,password,phone,register_as [individual,company])(Parameter: (Company: company_name,company_email,mobile_no,company_address,company_desc,company_work_day,company_work_time,commercial_registration_number,company_website))
                     <br><b>User Login</b>(Method: user_login)(Parameter: email,password)
                     <br><b>User Profile</b>(Method: user_profile)(Parameter: id)
                     <br><b>User Profile Update(Job Seeker)</b>(Method: user_profile_update)(Parameter: user_id,name,email,password,phone,city,address,current_company_name,experiences,skills,gender[Male,Female],date_of_birth)(Optional: user_image,user_resume)
                    <br><b>User Profile Update(Job Provider)</b>(Method: user_profile_update)(Parameter: user_id,name,email,password,phone,city,address,current_company_name,experiences,skills,gender[Male,Female],date_of_birth(Parameter:(User Company Details:company_name,company_email,mobile_no,company_address,company_desc,company_work_day,company_work_time,commercial_registration_number,company_website,register_as [individual,company])(Optional: user_image,company_logo))
                     <br><b>Forgot Password</b>(Method: forgot_pass)(Parameter: email)
                     <br><b>Job Add</b>(Method: job_add)(Parameter: user_id,job_type,cat_id,city_id,job_name,job_designation,job_desc,job_salary,job_salary_mode,job_company_name,job_company_website,job_phone_number,job_mail,job_vacancy,job_address,job_qualification,job_skill,job_date,job_work_day,job_work_time,job_experince)(Send with data: job_image)
                     <br><b>Job Edit</b>(Method: edit_job)(Parameter: job_id,user_id,job_type,cat_id,city_id,job_name,job_designation,job_desc,job_salary,job_salary_mode,job_company_name,job_company_website,job_phone_number,job_mail,job_vacancy,job_address,job_qualification,job_skill,job_date,job_work_day,job_work_time,job_experince)(Send with data: job_image)
                     <br><b>Company Details</b>(Method: get_company_details)(Parameter: user_id)
                     <br><b>Job Delete</b>(Method: delete_job)(Parameter: delete_job_id)
                     <br><b>Jobs List</b>(Method: job_list)(Parameter: user_id,page)
                     <br><b>Saved Jobs</b>(Method: saved_job_add)(Parameter: saved_user_id,saved_job_id)
                     <br><b>Apply Jobs</b>(Method: apply_job_add)(Parameter: apply_user_id,apply_job_id)
                     <br><b>User Apply Job List</b>(Method: user_job_apply_list)(Parameter: apply_job_id)
                     <br><b>User Apply List</b>(Method: user_apply_list)(Parameter: user_id,page)
                     <br><b>User Apply Job Seen</b>(Method: user_apply_job_seen)(Parameter: apply_user_id,job_id)
                     <br><b>User Saved List</b>(Method: user_saved_list)(Parameter: user_id,page)
                     <br><b>App Update</b>(Method: get_app_update)
                     <br><b>App Details</b>(Method: get_app_details)(Parameter: user_id)
                     <br><b>Resend OTP</b>(Method: resend_otp)(Parameter: phone,country_code)

                     <br><b>Submit Account Details </b>(Method: submit_account_details)(Parameter: user_id, account_number, account_holder_name, linked_mobile, ifsc_code)
                     <br><b>Initiate Add Money To Wallet </b>(Method: add_to_wallet_init)(Parameter: user_id, amount, mode)
                     <br><b>Update Add Money To Wallet </b>(Method: add_to_wallet_update)(Parameter: transaction_id, user_id, status, bank_trans_id, bank_trans_response)
                     <br><b>Withdrawal From Wallet Init</b>(Method: withdrawal_from_wallet_init)(Parameter: user_id, amount)
                     <br><b>Withdrawal From Wallet Update </b>(Method: withdrawal_from_wallet_update)(Parameter: transaction_id, status, bank_trans_id, bank_trans_response)
                     <br><b>Wallet Transaction * </b>(Method: wallet_transaction_list)(Parameter: user_id)(Response: type //1-credit, 2-debit, amount, status //0-null, 1-approved, 2-rejected, 3-failed, 4-init/pending, mode//diffrent payment methods.. stripe,hyperpay, bank_trans_id, bank_trans_response, trans_type //1-bank, 2- wallet)
                     <br><b>Subscription Plan List </b>(Method: subscription_plan_list)(Parameter: -)
                     <br><b>Subscription Payment Initialize </b>(Method: subscription_payment_init)(Parameter: user_id, plan_id, mode)
                     <br><b>Subscription Payment Update </b>(Method: subscription_payment_update)(Parameter: transaction_id, user_id, plan_id, mode, status, bank_trans_id, bank_trans_response)
                     <br><b>Job Completed </b>(Method: job_completed)(Parameter: job_id)
                     <br><b>Notification List </b>(Method: get_notification)(Parameter: user_id)
                     <br><b>Location List </b>(Method: get_location_list)()
                     <br><b>Set User Reviews and Ratings </b>(Method: set_user_ratings)(Parameter: user_id, reviewer_id, rating, review)
                     <br><b>Extend Job Time </b>(Method: extend_job_date)(Parameter: job_id, job_end_time)
                            <br><b>Verify User*</b>(Method: verify_user)(Parameter: user_id)
			 </code>
             </pre>
            </div>
        </div>
    </div>
    <br />
    <div class="clearfix"></div>

<?php include("includes/footer.php"); ?>