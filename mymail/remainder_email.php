<?php
//Include required PHPMailer files
	include('../config/dbconfig.php');
	require 'includes/PHPMailer.php';
	require 'includes/SMTP.php';
	require 'includes/Exception.php';
//Define name spaces
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	if(isset($_POST['send_remainder_mail'])){
		$serial =$_POST['edit_serial'];

		$query="SELECT * FROM pushed WHERE pushed_serial='$serial'";
		$query_run = mysqli_query($con, $query);
		$retrive = mysqli_fetch_array($query_run);
		$up_push=$retrive['dateofpushed'];

		$regid=$retrive['appointmentid'];
		$again_retrive="SELECT patientid,vaccineid,doseno FROM registers_for WHERE reg_serial = $regid AND status =2;";
		$again_retrive_run = mysqli_query($con,$again_retrive);
		$retrive_2= mysqli_fetch_array($again_retrive_run);

		$p_id=$retrive_2['patientid'];
		$v_id=$retrive_2['vaccineid'];
		$dose= $retrive_2['doseno'] + 1;
       
        if($v_id==2){
            $up_push=date('Y-m-d', strtotime($up_push. ' + 28 days'));
        }
        else if($v_id==3){
            $up_push=date('Y-m-d', strtotime($up_push. ' + 28 days'));
        }
        else if($v_id==4){
            $up_push=date('Y-m-d', strtotime($up_push. ' + 28 days'));
        }
        else if($v_id==5){
            $up_push=date('Y-m-d', strtotime($up_push. ' + 56 days'));
        }
        else if($v_id==6){
            $up_push=date('Y-m-d', strtotime($up_push. ' + 180 days'));
        }
        else if($v_id==7){
            if($retrive_2['doseno']==1){
                $up_push=date('Y-m-d', strtotime($up_push. ' + 28 days'));
            }
            else if($retrive_2['doseno']==2){
                $up_push=date('Y-m-d', strtotime($up_push. ' + 180 days'));
            }
            else if($retrive_2['doseno']==3){
                $up_push=date('Y-m-d', strtotime($up_push. ' + 365 days'));
            }
            else if($retrive_2['doseno']==4){
                $up_push=date('Y-m-d', strtotime($up_push. ' + 365 days'));
            }
        }
        else if($v_id==8){
            $up_push=date('Y-m-d', strtotime($up_push. ' + 60 days'));
        }
		$person="SELECT u_firstname,u_lastname,u_email FROM user WHERE u_id='$p_id'";
		$person_run = mysqli_query($con, $person);

        $vaccine="SELECT  v_name FROM vaccine WHERE v_id='$v_id'";
		$vaccine_run = mysqli_query($con, $vaccine);
        $v_name = mysqli_fetch_array($vaccine_run);
        $vname=$v_name['v_name'];

		if(mysqli_num_rows($person_run) > 0)        
            {
                while($row = mysqli_fetch_assoc($person_run))
                    {  
                        $name = $row['u_firstname'];
                        $lname = $row['u_lastname'];
                        $full_name = $name." ".$lname;

						//Create instance of PHPMailer
							$mail = new PHPMailer();
						//Set mailer to use smtp
							$mail->isSMTP();
						//Define smtp host
							$mail->Host = "smtp.gmail.com";
						//Enable smtp authentication
							$mail->SMTPAuth = true;
						//Set smtp encryption type (ssl/tls)
							$mail->SMTPSecure = "tls";
						//Port to connect smtp
							$mail->Port = "587";
						//Set gmail username
							$mail->Username = "safevax@gmail.com";
						//Set gmail password
							$mail->Password = "ffnamlglefyfpymd";
						//Email subject
							$mail->Subject = "Reminder from XYZ Health Care: Get your next dose of $vname on time";
						//Set sender email
							$mail->setFrom('safevax@gmail.com');
						//Enable HTML
							$mail->isHTML(true);
						//Attachment
							//$mail->addAttachment('img/attachment.png');
						//Email body
							$mail->Body = "<p>Dear $full_name ,<br>
                                            This is a reminder email in the case that your upcoming dose of $vname vaccine i.e $dose no dose is needed to be taken on $up_push. Get your dose on time and stay protected. Don't forget to make appointment for this upcoming dose.<br><br>
							              Regards<br>
										  XYZ Health Care<br></p>";
						//Add recipient
							$email=$row['u_email'];
							$mail->addAddress($email);
						//Finally send email
							if ( $mail->send() ) {
									$_SESSION['status'] = "Mail sent.";
									$_SESSION['status_code'] = "success";	
									$query_1 = "UPDATE pushed SET reminder=1 WHERE pushed_serial = '$serial';";
									$query_run_1 =mysqli_query($con,$query_1);
								}
								else{
									$_SESSION['status'] = "Mail couldn't be sent.";
									$_SESSION['status_code'] = "error";
									
								}
							}
						//Closing smtp connection
							$mail->smtpClose();
					}
			header("Location: ../admin/send_reminder.php");
			exit(0);
			

	}
	else{
	header("Location: ../admin/send_reminder.php");
	exit(0);
	}

?>