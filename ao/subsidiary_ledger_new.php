<?php
session_start();
error_reporting(E_ALL ^ E_DEPRECATED);
$hostname = "apecsystem";
$database = "dummy_apec";
$username = "admin";
$password = "admin#123";

$connect = mysql_connect($hostname, $username, $password)
or die('Could not connect: ' . mysql_error());
  //select database
mysql_select_db($database, $connect);
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET utf8");
mysql_query("SET COLLATION_CONNECTION = 'utf8_unicode_ci'");

require('../assets/fpdf/code128.php');
	class PDF extends FPDF
	{
	function Footer()
		{
			// Go to 1.5 cm from bottom
			$this->SetY(-15);
			// Select Arial italic 8
		}
	}
if(!isset($_GET['ref'])){
	
$pdf=new PDF_Code128('L','mm','Letter');
$pdf->SetFont('Arial','B',10);
$pdf->AddPage();

$pdf->Image('../assets/images/logo.jpg',10,7,25);
$pdf->SetFont('Arial','B',12);
$pdf->SetX(5);
$pdf->Cell(270,5,'ALBAY POWER AND ENERGY CORP',0,0,'C');
$pdf->Ln(5);
$pdf->SetX(5);
$pdf->SetFont('Arial','',10);
$pdf->Cell(270,5,'W. Vinzon St., Albay Dist., Leg. City',0,0,'C');
$pdf->Ln(1);
$pdf->SetX(5);
//$pdf->Cell(200,5,'VAT Reg. TIN 008-661-918-000, Leg. City',0,0,'C');

$pdf->SetFont('Arial','B',14);
$pdf->Ln(7);
$pdf->SetX(5);
$pdf->Cell(270,5,'Consumer Subsidiary Ledger',0,0,'C');

$pdf->Line(9,30,270,30);
$pdf->Line(9,31,270,31);


$pdf->Ln(100);
$pdf->SetX(5);
$pdf->Cell(270,5,'No Selected Account Number',0,0,'C');	
}else{
	
$BillAnalyst = strtoupper($_SESSION['active_user']);
$account = $_GET['ref'];




$pdf=new PDF_Code128('L','mm','Letter');
$pdf->SetFont('Arial','B',10);
$pdf->AddPage();

$get_info = mysql_query("SELECT * FROM consumers WHERE AccountNumber=$account")or die("SQL Error 1: " . mysql_error());
while($info = mysql_fetch_array($get_info)){
	$name = $info['AccountName'];	
	$name = iconv('UTF-8', 'windows-1252',$name);
	$address = $info['Address'];	
	$address = iconv('UTF-8', 'windows-1252', $address);	
	$branch = $info['Branch'];
	$branch =strtolower($branch);
	$aleco = $info['AlecoAccount'];	
	$type = $info['CustomerType'];	
	$meter = $info['MeterNumber'];	
	$multiplier = $info['Multiplier'];	
	$status = $info['Status'];	
}


$month =Array();
$current_year = date("Y");
$month[1] = "January";
$month[2] = "February";
$month[3] = "March";
$month[4] = "April";
$month[5] = "May";
$month[6] = "June";
$month[7] = "July";
$month[8] = "August";
$month[9] = "September";
$month[10] = "October";
$month[11] = "November";
$month[12] = "December";

$sales_month = Array();
$sales_month_n = Array();
$sales_invoice = Array();
$sales_prev_reading_date= Array();
$sales_prev_reading= Array();
$sales_reading_date= Array();
$sales_reading= Array();
$sales_kwh= Array();
$sales_amount= Array();
$sales_payment= Array();
$sales_payment_date= Array();
$sales_status= Array();
$sales_adjusted= Array();
$sales_dcm= Array();
$sales_remarks= Array();
$sales_mrd_field_findings= Array();
$sales_mrd_field_remarks= Array();
$sales_mrd_field_reading= Array();
$dcm_checker= Array();
$sales_dcm_soa= Array();
$valid_year= Array();
	
$dsp_sales_amount =Array();
$dsp_sales_payment =Array();
$show_sales_amount =Array();
$sales_surcharge =Array();

$get_year = mysql_query("SELECT DISTINCT(year) FROM record_holder")or die("SQL Error 1: " . mysql_error());
$db_year= Array();
while($d_year = mysql_fetch_array($get_year , MYSQL_ASSOC)){

	$db_year[] = $d_year['year'];	
}

$last_year = 0;
foreach($db_year as $s){
			$last_year = $s;
			$year = $s;
			
			$valid_months = Array();
			$get_months = mysql_query("SELECT DISTINCT(month_value) FROM valid_months WHERE year_value='$year' ORDER BY (month_value+0) ASC")or die("SQL Error 1: " . mysql_error());	
					
			while($g_months = mysql_fetch_array($get_months, MYSQL_ASSOC)){
							 
							$valid_months[]= $g_months['month_value'];
						 
				 }
			for($i=1;$i<=12;$i++){
			
			$table = "sales_".$branch."_".$s;
			$ledger = "ledger_".$branch."_".$s;
			
			$SQLQuery = "SELECT a.BillMonth ,a.PreviousReading ,a.PreviousReadingDate,a.PresentReading,a.PresentReadingDate, a.Invoice , a.Kwhu , a.AmountDue ,a.IsDCM,a.Dcm_Soa_Number, a.Remarks , a.MRDFieldFindings,a.MRDRemarks,a.MRDReading,b.TotalAmount,b.Surcharge, b.Status,b.Adjusted,b.Dcm,b.LedgerRemarks,b.PaymentDate FROM $table a LEFT OUTER JOIN $ledger b ON b.AccountNumber = a.AcctNo  AND b.BillMonth = a.BillMonth AND b.BillYear = a.BillYear WHERE a.AcctNo = '$account' AND (b.BillYear = '$s' OR a.BillYear = '$s') AND (b.BillMonth = '$i' OR a.BillMonth = '$i') ORDER BY (a.BillMonth+0) ASC";
			$query = mysql_query($SQLQuery)or die("SQL Error 1: " . mysql_error());
			$count_query = mysql_num_rows($query);
			
			
			if($count_query==0){
			
			
				$SQLQuery = "SELECT a.BillMonth ,a.PreviousReading ,a.PreviousReadingDate,a.PresentReading,a.PresentReadingDate, a.Invoice , a.Kwhu , a.AmountDue ,a.IsDCM,a.Dcm_Soa_Number, a.Remarks , a.MRDFieldFindings,a.MRDRemarks,a.MRDReading,b.TotalAmount,b.Surcharge, b.Status,b.Adjusted,b.Dcm,b.LedgerRemarks,b.PaymentDate FROM $ledger b LEFT OUTER JOIN $table a ON a.AcctNo  =  b.AccountNumber AND a.BillMonth = b.BillMonth AND a.BillYear = b.BillYear WHERE b.AccountNumber = '$account' AND (b.BillYear = '$s' OR a.BillYear = '$s') AND (b.BillMonth = '$i' OR a.BillMonth = '$i') ORDER BY (a.BillMonth+0) ASC";
				$query = mysql_query($SQLQuery)or die("SQL Error 1: " . mysql_error());
				$count_query = mysql_num_rows($query);	
				
						if($count_query==0){
							
						$sales_month[$year][$i] = $month[$i];
						$sales_month_n[$year][$i] =$i;
						$sales_invoice[$year][$i] = '-';
						$sales_prev_reading_date[$year][$i]=  '-';
						$sales_prev_reading[$year][$i]=  '-';
						$sales_reading_date[$year][$i]=  '-';
						$sales_reading[$year][$i]=  '-';
						$sales_kwh[$year][$i]=  '-';
						$sales_amount[$year][$i]=  '-';
						$sales_payment[$year][$i]=  '-';
						$sales_payment_date[$year][$i]=  '-';
						$sales_surcharge[$year][$i]=  '-';
						$sales_status[$year][$i]=  '-';
						$sales_adjusted[$year][$i]=  '-';
						$sales_dcm[$year][$i]=  '-';
						$sales_remarks[$year][$i]=  '-';
						$sales_mrd_field_findings[$year][$i]=  '-';
						$sales_mrd_field_remarks[$year][$i]=  '-';
						$sales_mrd_field_reading[$year][$i]=  '-';
						$dcm_checker[$year][$i]=  '0';	
						$sales_dcm_soa[$year][$i] = "-";	
						$sales_dcm_remarks[$s][$i] ='-';
						$latest_reading_date ="-";
						$latest_reading= "-";
						}else{
							
						$valid_year[$s][] = $i;
						while($row=mysql_fetch_array($query, MYSQL_ASSOC)){
						$sales_month[$year][$i] = $month[$i];
						$sales_month_n[$year][$i] =$row['BillMonth'];
						$sales_invoice[$year][$i] = $row['Invoice'];
						$sales_prev_reading_date[$year][$i]= $row['PreviousReadingDate'];
						$sales_prev_reading[$year][$i]= $row['PreviousReading'];
						$sales_reading_date[$year][$i]= $row['PresentReadingDate'];
						$sales_reading[$year][$i]= $row['PresentReading'];
						$sales_kwh[$year][$i]= $row['Kwhu'];
						$sales_amount[$year][$i]= $row['AmountDue'];
						$sales_payment[$year][$i]= $row['TotalAmount'];
						$sales_surcharge[$year][$i]= $row['Surcharge'];
						$sales_payment_date[$year][$i]= $row['PaymentDate'];
						$sales_status[$year][$i]= $row['Status'];
						$sales_adjusted[$year][$i]= $row['Adjusted'];
						$sales_dcm[$year][$i]= $row['Dcm'];
						$sales_remarks[$year][$i]= $row['Remarks'];
						$sales_mrd_field_findings[$year][$i]= $row['MRDFieldFindings'];
						$sales_mrd_field_remarks[$year][$i]= $row['MRDRemarks'];
						$sales_mrd_field_reading[$year][$i]= $row['MRDReading'];
						$dcm_checker[$year][$i]= $row['IsDCM'];
						$sales_dcm_soa[$year][$i] = "-";	
						$sales_dcm_remarks[$s][$i] ='-';
						$latest_reading_date = $row['PresentReadingDate'];
						$latest_reading= $row['PresentReading'];
						
						if($dcm_checker[$year][$i]==1){
							
							
								$SQLQuery_1 = "SELECT * FROM dcm_record WHERE Dcm_AccountNumber = '$account' AND Dcm_Year = '$year' AND Dcm_Month = '$i'";
								$query_1 = mysql_query($SQLQuery_1)or die("SQL Error 1: " . mysql_error());
								
								while($row_1=mysql_fetch_array($query_1, MYSQL_ASSOC)){
								$sales_dcm_soa[$year][$i] = $row_1['Dcm_Dcm_No'];
								$sales_prev_reading_date[$year][$i]= $row_1['Dcm_SH_Prev_Reading_Date'];
								$sales_prev_reading[$year][$i]= $row_1['Dcm_SH_Prev_Reading'];
								$sales_reading_date[$year][$i]= $row_1['Dcm_SH_Pres_Reading_Date'];
								$sales_reading[$year][$i]= $row_1['Dcm_SH_Pres_Reading'];
								$sales_kwh[$year][$i]= $row_1['Dcm_SH_Total_Kwh'];
								$sales_amount[$year][$i]= $row_1['Dcm_SH_Amount_Due'];
								$sales_remarks[$year][$i]= $row_1['Remarks'];
								$sales_dcm_remarks[$s][$i] ='ADJUSTED';
								$latest_reading_date =  $row_1['Dcm_SH_Pres_Reading_Date'];
								$latest_reading= $row_1['Dcm_SH_Pres_Reading'];
								}
							
							
							
							}elseif($dcm_checker[$year][$i]==2){
							
								$SQLQuery_2 = "SELECT * FROM backbilling_record WHERE AcctNo = '$account' AND BillYear = '$year' AND BillMonth = '$i'";
								$query_2 = mysql_query($SQLQuery_2)or die("SQL Error 1: " . mysql_error());
								
								while($row_2=mysql_fetch_array($query_2, MYSQL_ASSOC)){
								$sales_dcm_soa[$year][$i] = $row_2['Invoice'];
								$sales_prev_reading_date[$year][$i]= $row_2['PreviousReadingDate'];
								$sales_prev_reading[$year][$i]= $row_2['PreviousReading'];
								$sales_reading_date[$year][$i]= $row_2['PresentReadingDate'];
								$sales_reading[$year][$i]= $row_2['PresentReading'];
								$sales_kwh[$year][$i]= $row_2['Kwhu'];
								$sales_amount[$year][$i]= $row_2['AmountDue'];
								$sales_remarks[$year][$i]= $row_2['Remarks'];
								$sales_mrd_field_findings[$year][$i]= $row_2['MRDFieldFindings'];
								$sales_mrd_field_remarks[$year][$i]= $row_2['MRDRemarks'];
								$sales_mrd_field_reading[$year][$i]= $row_2['MRDReading'];
								$sales_dcm_remarks[$s][$i] ='BACKBILLED';
								$latest_reading_date = $sales_reading_date[$year][$i];
								$latest_reading= $sales_reading[$s][$i];	
								}
						
							}
						
							
						}	
							
					
					}
			
		
							
			}else{
				
					$valid_year[$s][] = $i;
					//$valid_counter+=1;
							while($row=mysql_fetch_array($query, MYSQL_ASSOC)){
							$sales_month[$year][$i] = $month[$i];
							$sales_month_n[$year][$i] =$row['BillMonth'];
							$sales_invoice[$year][$i] = $row['Invoice'];
							$sales_prev_reading_date[$year][$i]= $row['PreviousReadingDate'];
							$sales_prev_reading[$year][$i]= $row['PreviousReading'];
							$sales_reading_date[$year][$i]= $row['PresentReadingDate'];
							$sales_reading[$year][$i]= $row['PresentReading'];
							$sales_kwh[$year][$i]= $row['Kwhu'];
							$sales_amount[$year][$i]= $row['AmountDue'];
							$sales_payment[$year][$i]= $row['TotalAmount'];
							$sales_surcharge[$year][$i]= $row['Surcharge'];
							$sales_payment_date[$year][$i]= $row['PaymentDate'];
							$sales_status[$year][$i]= $row['Status'];
							$sales_adjusted[$year][$i]= $row['Adjusted'];
							$sales_dcm[$year][$i]= $row['Dcm'];
							$sales_remarks[$year][$i]= $row['Remarks'];
							$sales_mrd_field_findings[$year][$i]= $row['MRDFieldFindings'];
							$sales_mrd_field_remarks[$year][$i]= $row['MRDRemarks'];
							$sales_mrd_field_reading[$year][$i]= $row['MRDReading'];
							$dcm_checker[$year][$i]= $row['IsDCM'];
							$sales_dcm_soa[$year][$i] = "-";	
							$sales_dcm_remarks[$s][$i] ='-';
							$latest_reading_date = $sales_reading_date[$year][$i];
							$latest_reading= $sales_reading[$s][$i];
							
							if($dcm_checker[$year][$i]==1){
								
								
									$SQLQuery_1 = "SELECT * FROM dcm_record WHERE Dcm_AccountNumber = '$account' AND Dcm_Year = '$year' AND Dcm_Month = '$i'";
									$query_1 = mysql_query($SQLQuery_1)or die("SQL Error 1: " . mysql_error());
									
									while($row_1=mysql_fetch_array($query_1, MYSQL_ASSOC)){
									$sales_dcm_soa[$year][$i] = $row_1['Dcm_Dcm_No'];
									$sales_prev_reading_date[$year][$i]= $row_1['Dcm_SH_Prev_Reading_Date'];
									$sales_prev_reading[$year][$i]= $row_1['Dcm_SH_Prev_Reading'];
									$sales_reading_date[$year][$i]= $row_1['Dcm_SH_Pres_Reading_Date'];
									$sales_reading[$year][$i]= $row_1['Dcm_SH_Pres_Reading'];
									$sales_kwh[$year][$i]= $row_1['Dcm_SH_Total_Kwh'];
									$sales_amount[$year][$i]= $row_1['Dcm_SH_Amount_Due'];
									$sales_remarks[$year][$i]= $row_1['Remarks'];
									
									$sales_dcm_remarks[$s][$i] ='ADJUSTED';
								   
								   }
								   
							$latest_reading_date = $sales_reading_date[$year][$i];
							$latest_reading= $sales_reading[$s][$i];	   
								
								
								
							}elseif($dcm_checker[$year][$i]==2){
								
									$SQLQuery_2 = "SELECT * FROM backbilling_record WHERE AcctNo = '$account' AND BillYear = '$year' AND BillMonth = '$i'";
									$query_2 = mysql_query($SQLQuery_2)or die("SQL Error 1: " . mysql_error());
									
									while($row_2=mysql_fetch_array($query_2, MYSQL_ASSOC)){
									$sales_dcm_soa[$year][$i] = $row_2['Invoice'];
									$sales_prev_reading_date[$year][$i]= $row_2['PreviousReadingDate'];
									$sales_prev_reading[$year][$i]= $row_2['PreviousReading'];
									$sales_reading_date[$year][$i]= $row_2['PresentReadingDate'];
									$sales_reading[$year][$i]= $row_2['PresentReading'];
									$sales_kwh[$year][$i]= $row_2['Kwhu'];
									$sales_amount[$year][$i]= $row_2['AmountDue'];
									$sales_remarks[$year][$i]= $row_2['Remarks'];
									$sales_mrd_field_findings[$year][$i]= $row_2['MRDFieldFindings'];
									$sales_mrd_field_remarks[$year][$i]= $row_2['MRDRemarks'];
									$sales_mrd_field_reading[$year][$i]= $row_2['MRDReading'];
									$sales_dcm_remarks[$s][$i] ='BACKBILLED';
									}
									
									$latest_reading_date = $row_2['PresentReadingDate'];
									$latest_reading= $sales_reading[$s][$i];
							
							}
						
							

						}
					
					
					}

						if($sales_invoice[$year][$i] =='' || $sales_invoice[$year][$i] =='-' || $sales_invoice[$year][$i] ==' '){
							
							$sales_invoice[$year][$i] ='-';
						}
						
						if($sales_prev_reading_date[$year][$i] =='' || $sales_prev_reading_date[$year][$i] =='-'){
							
							$sales_prev_reading_date[$year][$i] ='-';
						}else{
							
												if(strpos($sales_prev_reading_date[$s][$i], '/') !== false){
														$perpiece = Array();
														$sales_prev_reading_date[$s][$i] = str_replace("/", "-",$sales_prev_reading_date[$s][$i]);	
														$perpiece = explode("-",$sales_prev_reading_date[$s][$i]);
														$c = 0;
														if($perpiece[0]<=9){
															
															if(strlen($perpiece[0])==1){
															$perpiece[0] = "0".$perpiece[0];
															}
															
														}
														
														
														if($perpiece[1]<=9){
															if(strlen($perpiece[1])==1){
															$perpiece[1] = "0".$perpiece[1];

															}
															
														}
														
														if($perpiece[2]<=9){
															if(strlen($perpiece[2])==1){
															$perpiece[2] = "0".$perpiece[2];	
																
															}
															
														}
														$sales_prev_reading_date[$s][$i] = $perpiece[0]."-".$perpiece[1]."-".$perpiece[2];
													}
							
						}
						
						if($sales_prev_reading[$year][$i] =='' || $sales_prev_reading[$year][$i] =='-'){
							
							$sales_prev_reading[$year][$i] ='-';
						}
						
						if($sales_reading_date[$year][$i] =='' || $sales_reading_date[$year][$i] =='-'){
							
							$sales_reading_date[$year][$i] ='-';
						}else{
							
							if(strpos($sales_reading_date[$s][$i], '/') !== false){
														$perpiece = Array();
														$sales_reading_date[$s][$i] = str_replace("/", "-",$sales_reading_date[$s][$i]);	
														$perpiece = explode("-",$sales_reading_date[$s][$i]);
														$c = 0;
														if($perpiece[0]<=9){
															
															if(strlen($perpiece[0])==1){
															$perpiece[0] = "0".$perpiece[0];
															}
															
														}
														
														
														if($perpiece[1]<=9){
															if(strlen($perpiece[1])==1){
															$perpiece[1] = "0".$perpiece[1];

															}
															
														}
														
														if($perpiece[2]<=9){
															if(strlen($perpiece[2])==1){
															$perpiece[2] = "0".$perpiece[2];	
																
															}
															
														}
														$sales_reading_date[$s][$i] = $perpiece[0]."-".$perpiece[1]."-".$perpiece[2];
													}
							
						}
						
						if($sales_reading[$year][$i] =='' || $sales_reading[$year][$i] =='-'){
							
							$sales_reading[$year][$i] ='-';
						}
						
						if($sales_kwh[$year][$i] =='' || $sales_kwh[$year][$i] =='-'){
							
							if($sales_invoice[$year][$i] !='-'){
								
							$sales_kwh[$year][$i] ='0';	
							
							}else{
							$sales_kwh[$year][$i] ='-';		
							}
						}
					
						if($sales_amount[$year][$i] =='' || $sales_amount[$year][$i] =='-' || $sales_amount[$year][$i] =='0'|| $sales_amount[$year][$i] =='0.00' ){
							
							$sales_amount[$year][$i] ='-';
							$show_sales_amount[$year][$i] ='-';
						}else{
							
							$show_sales_amount[$year][$i] = number_format((float)$sales_amount[$s][$i], 2, '.', ',');
						}
						
						if($sales_payment[$year][$i] =='' || $sales_payment[$year][$i] =='-'){
							
							if($sales_invoice[$year][$i] !='-'){
								
							$sales_payment[$year][$i] ='0';	
							
							}else{
							$sales_payment[$year][$i] ='-';		
							}
						}
						if($sales_payment_date[$year][$i] =='' || $sales_payment_date[$year][$i] =='-'){
							
							$sales_payment_date[$year][$i] ='-';
						}else{
							
								if(strpos($sales_payment_date[$s][$i], '/') !== false){
														$perpiece = Array();
														$sales_payment_date[$s][$i] = str_replace("/", "-",$sales_payment_date[$s][$i]);	
														$perpiece = explode("-",$sales_payment_date[$s][$i]);
														$c = 0;
														if($perpiece[0]<=9){
															
															if(strlen($perpiece[0])==1){
															$perpiece[0] = "0".$perpiece[0];
															}
															
														}
														
														
														if($perpiece[1]<=9){
															if(strlen($perpiece[1])==1){
															$perpiece[1] = "0".$perpiece[1];

															}
															
														}
														
														if($perpiece[2]<=9){
															if(strlen($perpiece[2])==1){
															$perpiece[2] = "0".$perpiece[2];	
																
															}
															
														}
														$sales_payment_date[$s][$i] = $perpiece[0]."-".$perpiece[1]."-".$perpiece[2];
													}
							
							
						}
						
						if($sales_status[$year][$i] =='' || $sales_status[$year][$i] =='-'){
							if($sales_invoice[$year][$i] !='-'){
								
							$sales_status[$year][$i] ='No Payment';	
							
							}else{
							$sales_status[$year][$i] ='-';		
							}
							
						}
						
						if($sales_adjusted[$year][$i] =='' || $sales_adjusted[$year][$i] =='-'){
							
							$sales_adjusted[$year][$i] ='-';
						}
						
						
						if($sales_surcharge[$year][$i] =='' || $sales_surcharge[$year][$i] =='-'|| $sales_surcharge[$year][$i] =='0.00'){
							
							$sales_surcharge[$year][$i] ='-';
						}
						
						
						if($sales_dcm[$year][$i] =='' || $sales_dcm[$year][$i] =='-'){
							
							$sales_dcm[$year][$i] ='-';
						}
						
						if($sales_remarks[$year][$i] =='' || $sales_remarks[$year][$i] =='-'){
							
							$sales_remarks[$year][$i] ='-';
						}
						
						if($sales_mrd_field_findings[$year][$i] =='' || $sales_mrd_field_findings[$year][$i] =='-' ){
							
							$sales_mrd_field_findings[$year][$i] ='-';
						}
						
						if($sales_mrd_field_remarks[$year][$i] =='' || $sales_mrd_field_remarks[$year][$i] =='-'){
							
							$sales_mrd_field_remarks[$year][$i] ='-';
						}
						
						if($sales_mrd_field_reading[$year][$i] =='' || $sales_mrd_field_reading[$year][$i] =='-'){
							
							$sales_mrd_field_reading[$year][$i] ='-';
						}
						
						if($sales_dcm_remarks[$year][$i] =='' || $sales_dcm_remarks[$year][$i] =='-'){
							
							$sales_dcm_remarks[$year][$i] ='-';
						}
						
				
				$lates_previous_holder = $i;
			}
	}
	
	$sales_balance = Array();
	$show_balance = Array();
	$show_status = Array();
	$total_invoice_amount = 0;
	$total_paid_amount = 0;
	$total_balance = 0;
	foreach($db_year as $s){
		
		foreach($valid_year[$s] as $i){
			
			$total_invoice_amount += $sales_amount[$s][$i];
			$show_total_invoice_amount = number_format((float)$total_invoice_amount, 2, '.', ',');
			$tomatch_status =strtoupper($sales_status[$s][$i]);
			if (strpos($tomatch_status, 'PAID') !== false){
			$show_status[$s][$i]="Fully Paid";
			$sales_balance[$s][$i]="0.00";
			
			$show_balance[$s][$i] = number_format((float)$sales_balance[$s][$i], 2, '.', ',');
			$total_paid_amount +=$sales_amount[$s][$i];
			}else if(strpos($tomatch_status, 'REMAINING') !== false){
				$show_status[$s][$i]="Rem. Bal";
				$sales_balance[$s][$i]=$sales_amount[$s][$i]-$sales_payment[$s][$i];
				if($sales_balance[$s][$i]<=0){
				$show_status[$s][$i]="Fully Paid";	
				}else{
				$show_status[$s][$i]="Rem. Bal";	
					
				}
				$show_balance[$s][$i] = number_format((float)$sales_balance[$s][$i], 2, '.', ',');
				$total_paid_amount +=$sales_payment[$s][$i];
			}else if(strpos($tomatch_status, 'NO PAY') !== false){
				$show_status[$s][$i]="No Payment";
				$sales_balance[$s][$i]=$sales_amount[$s][$i];
				$show_balance[$s][$i] = number_format((float)$sales_balance[$s][$i], 2, '.', ',');
				$total_paid_amount +=0.00;
				
				
			}else{
				
				if($sales_amount[$s][$i]!='' || $sales_amount[$s][$i]>0){
				$sales_balance[$s][$i]=$sales_amount[$s][$i];
				$show_balance[$s][$i] = number_format((float)$sales_balance[$s][$i], 2, '.', ',');
		
				$show_status[$s][$i]="-";
				
				$total_paid_amount +=0.00;			
				
				}else{
				$sales_balance[$s][$i]='-';
				$show_balance[$s][$i]='-';
				$show_status[$s][$i]="-";
				
				$total_paid_amount +=0.00;
					
				}
				
			}
	 
			$total_balance+=$sales_balance[$s][$i];
			
		}
		
	}
$total_paid_amount = number_format((float)$total_paid_amount, 2, '.', ',');		
$show_total_balance= number_format((float)$total_balance, 2, '.', ',');	
$show_total_balance = "Php ".$show_total_balance;		
$pdf->Image('../assets/images/logo.jpg',10,7,25);
$pdf->SetFont('Arial','B',12);
$pdf->SetX(5);
$pdf->Cell(270,5,'ALBAY POWER AND ENERGY CORP',0,0,'C');
$pdf->Ln(5);
$pdf->SetX(5);
$pdf->SetFont('Arial','',10);
$pdf->Cell(270,5,'W. Vinzon St., Albay Dist., Leg. City',0,0,'C');
$pdf->Ln(1);
$pdf->SetX(5);
//$pdf->Cell(200,5,'VAT Reg. TIN 008-661-918-000, Leg. City',0,0,'C');


$pdf->SetFont('Arial','B',14);
$pdf->Ln(7);
$pdf->SetX(5);
$pdf->Cell(270,5,'Consumer Account Ledger',0,0,'C');

$pdf->Line(9,30,198,30);

$pdf->Ln(8);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,10,'ACCOUNT NUMBER',0);

$pdf->Cell(5,10,':',0);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(90,10,$account,0);
$pdf->SetFont('Arial','',10);


$pdf->Line(9,30,270,30);

$pdf->SetFont('Arial','',10);
$pdf->Cell(35,10,'ALECO ACCOUNT',0);

$pdf->Cell(5,10,':',0);

$pdf->Cell(80,10,$aleco,0);
$pdf->SetFont('Arial','',10);

$pdf->Ln(6);
$pdf->Cell(40,10,'CONSUMER NAME',0);
$pdf->Cell(5,10,':',0);
$pdf->Cell(90,10,$name,0);


$pdf->SetFont('Arial','',10);
$pdf->Cell(35,10,'METER NO.',0);
$pdf->Cell(5,10,':',0);

$pdf->Cell(80,10,$meter,0);
$pdf->SetFont('Arial','',10);


$pdf->Ln(6);
$pdf->Cell(40,10,'CONSUMER ADDRESS',0);
$pdf->Cell(5,10,':',0);
$pdf->Cell(90,10,$address,0);

$pdf->SetFont('Arial','',10);
$pdf->Cell(35,10,'MULTIPLIER',0);
$pdf->Cell(5,10,':',0);

$pdf->Cell(80,10,$multiplier,0);
$pdf->SetFont('Arial','',10);


$pdf->Ln(6);
$pdf->Cell(40,10,'CUSTOMER TYPE',0);
$pdf->Cell(5,10,':',0);
$pdf->Cell(90,10,$type,0);


$pdf->SetFont('Arial','',10);
$pdf->Cell(35,10,'STATUS',0);
$pdf->Cell(5,10,':',0);

$pdf->Cell(80,10,$status,0);
$pdf->SetFont('Arial','',10);

$pdf->Line(9,60,270,60);
$pdf->Line(9,61,270,61);



$pdf->Ln(15);
$pdf->Cell(40,7,'PRESENT READING',0);
$pdf->Cell(5,7,':',0);
$pdf->Cell(35,7,$sales_reading[$last_year][count($valid_year[$last_year])],0);
//$pdf->Cell(35,7,"-",0,0);

$pdf->Cell(50,7,'PRESENT READING DATE',0);
$pdf->Cell(5,7,':',0);
$pdf->Cell(35,7,$sales_reading_date[$last_year][count($valid_year[$last_year])],0);
//$pdf->Cell(35,7,"-",0,0);

$pdf->Cell(50,7,'TOTAL INVOICE AMOUNT',0);
$pdf->Cell(5,7,':',0);
$pdf->Cell(35,7,$show_total_invoice_amount,0,0,'L');

///////////////////////////////////////////////////////////

$pdf->Ln(6);
$pdf->Cell(40,7,'PREVIOUS READING',0);
$pdf->Cell(5,7,':',0);
$pdf->Cell(35,7,$sales_prev_reading[$last_year][count($valid_year[$last_year])],0);
//$pdf->Cell(35,7,"-",0,0);


$pdf->Cell(50,7,'PREVIOUS READING DATE',0);
$pdf->Cell(5,7,':',0);
$pdf->Cell(35,7,$sales_prev_reading_date[$last_year][count($valid_year[$last_year])],0);
//$pdf->Cell(35,7,"-",0);


$pdf->Cell(50,7,'TOTAL PAID AMOUNT',0);
$pdf->Cell(5,7,':',0);
$pdf->Cell(35,7,$total_paid_amount,0,0,'L');
//$pdf->Cell(35,7,"-",0,0);

////////////////////////////////////////////////////////////////////////////
$pdf->Ln(6);
$pdf->Cell(40,7,'CURRENT KWH USED',0);
$pdf->Cell(5,7,':',0);
$pdf->Cell(35,7,$sales_kwh[$last_year][count($valid_year[$last_year])],0);


$pdf->Cell(50,7,'CURRENT BILL AMOUNT',0);
$pdf->Cell(5,7,':',0);
$pdf->Cell(35,7,number_format((float)$sales_amount[$last_year][count($valid_year[$last_year])], 2, '.', ','),0);



$pdf->Cell(50,7,'TOTAL/AR',0);
$pdf->Cell(5,7,':',0);
$pdf->Cell(35,7,$show_total_balance,0,0,'L');
//$pdf->Cell(35,7,"-",0,0);


////////////////////////////////////////

$pdf->Line(9,84,270,84);
$pdf->Line(9,85,270,85);

$pdf->Ln(2);





//$pdf->Line(9,93,270,93);
$pdf->SetFont('Arial','',8);
$pdf->Ln(11);
$pdf->Cell(120,7,'SALES',0,0,'C');
$pdf->Line(130,94,130,101);
$pdf->Cell(75,7,'PAYMENTS',0,0,'C');


$pdf->Line(208,94,208,101);

$pdf->Cell(48,7,'REMARKS',0,0,'C');

$pdf->Line(252,94,252,101);

$pdf->Line(9,94,270,94);
//$pdf->Line(9,101,198,101);

$pdf->Ln(7);
$pdf->Cell(13,6,'YEAR',0,0,'C');
$pdf->Cell(15,6,'MONTH',0,0,'C');
$pdf->Cell(18,6,'RDG DATE',0,0,'C');
$pdf->Cell(25,6,'INVOICE',0,0,'C');
$pdf->Cell(15,6,'READING',0,0,'R');
$pdf->Cell(15,6,'KWH',0,0,'R');
$pdf->Cell(18,6,'AMOUNT',0,0,'R');

$pdf->Cell(20,6,'PAYMENT',0,0,'R');

$pdf->Cell(16,6,'S / C',0,0,'R');
$pdf->Cell(3,6,' ',0,0,'C');
$pdf->Cell(17,6,'STATUS',0,0,'C');

$pdf->Cell(20,6,'PYMT DATE',0,0,'R');
$pdf->Cell(5,6,' ',0,0,'C');

$pdf->Cell(22,6,'DCM / B.BILLED',0,0,'R');
$pdf->Cell(18,6,'DCM / SOA#',0,0,'R');
$pdf->Cell(18,6,'BALANCE',0,0,'R');
$pdf->Line(9,101,270,101);
//$pdf->Line(9,108,198,108);
$pdf->Ln(6);	


	
$starting_line = 107;

foreach($db_year as $s){
		
		foreach($valid_year[$s] as $i){
		
		$pdf->Cell(13,6,$s,0,0,'C');
		$pdf->Cell(15,6,$sales_month[$s][$i],0,0,'C');
		
		$pdf->Cell(18,6,$sales_reading_date[$s][$i],0,0,'R');
		$pdf->Cell(25,6,$sales_invoice[$s][$i],0,0,'R');
		$pdf->Cell(15,6,$sales_reading[$s][$i],0,0,'R');
		$pdf->Cell(15,6,$sales_kwh[$s][$i],0,0,'R');
		$pdf->Cell(18,6,$show_sales_amount[$s][$i],0,0,'R');

		$pdf->Cell(20,6,$sales_payment[$s][$i],0,0,'R');
		
		$pdf->Cell(16,6,$sales_surcharge[$s][$i],0,0,'R');
	
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->Cell(17,6,$sales_status[$s][$i],0,0,'C');
	
		
	$pdf->Cell(20,6,$sales_payment_date[$s][$i],0,0,'R');
	//$pdf->Cell(20,6,"-",1,0,'R');
		
		
		$pdf->Cell(5,6,' ',0,0,'C');
		$pdf->Cell(22,6,$sales_dcm_remarks[$s][$i],0,0,'C');	
		
		$pdf->Cell(20,6,$sales_dcm_soa[$s][$i],0,0,'C');	
		
			
		$pdf->Cell(15,6,$show_balance[$s][$i],0,0,'R');	
	//	$pdf->Cell(18,6,"-",1,0,'R');	
	//	$pdf->Cell(18,6,"-",1,0,'R');
	//	$pdf->Cell(25,3,'_________________________________________________________________________________________________________________________',0,0);
		
		//$pdf->Line(9,$starting_line,270,$starting_line);
		//$pdf->Line(9,191,270,191);
		$pdf->Ln(7);
		if($starting_line==191){
			
					$starting_line =15;	
		
					$pdf->AddPage();
					

					//$pdf->Line(235,94,235,101);

					$pdf->Line(9,15,270,15);
					//$pdf->Line(9,101,198,101);

					$pdf->Ln(7);
					$pdf->Cell(13,6,'YEAR',0,0,'C');
					$pdf->Cell(15,6,'MONTH',0,0,'C');
					$pdf->Cell(18,6,'RDG DATE',0,0,'C');
					$pdf->Cell(25,6,'INVOICE',0,0,'C');
					$pdf->Cell(15,6,'READING',0,0,'R');
					$pdf->Cell(15,6,'KWH',0,0,'R');
					$pdf->Cell(18,6,'AMOUNT',0,0,'R');

					$pdf->Cell(20,6,'PAYMENT',0,0,'R');
				
					$pdf->Cell(16,6,'S / C',0,0,'R');
					$pdf->Cell(3,6,' ',0,0,'C');
					$pdf->Cell(17,6,'STATUS',0,0,'C');
					$pdf->Cell(20,6,'PYMT DATE',0,0,'R');
					$pdf->Cell(5,6,' ',0,0,'C');
					$pdf->Cell(22,6,'DCM / B.BILLED',0,0,'R');
					$pdf->Cell(18,6,'DCM / SOA#',0,0,'R');
					$pdf->Cell(18,6,'BALANCE',0,0,'R');
					$pdf->Line(9,22,270,22);
					//$pdf->Line(9,108,198,108);
					$pdf->Ln(6);	
		
					$starting_line +=13;	
				}else{
			$starting_line +=7;	
			}
		
		}
		

}	
$pdf->Line(9,$starting_line-5,270,$starting_line-5);
$pdf->Line(9,$starting_line-4,270,$starting_line-4);

	
$pdf->Ln(4);
$pdf->SetFont('Arial','',9);
$pdf->Cell(70,7,'TOTAL INVOICE',0,0,'L');	
$pdf->SetFont('Arial','',10);
$pdf->Cell(190,7,$show_total_invoice_amount ,0,0,'R');
$pdf->SetFont('Arial','',9);
$pdf->Line(9,$starting_line+5,270,$starting_line+5);
$pdf->Ln(3);
//$pdf->Cell(190,3,'_________________________________________________________________________________________________________________________',0,0);
$pdf->Ln(4);
$pdf->Cell(70,7,'TOTAL PAYMENTS',0,0,'L');	
$pdf->SetFont('Arial','',10);
$pdf->Cell(190,7,$total_paid_amount ,0,0,'R');
$pdf->SetFont('Arial','',9);
$pdf->Line(9,$starting_line+13,270,$starting_line+13);
$pdf->Ln(3);
//$pdf->Cell(190,3,'_________________________________________________________________________________________________________________________',0,0);
$pdf->Ln(4);
$pdf->Cell(70,7,'TOTAL BALANCE',0,0,'L');	
$pdf->SetFont('Arial','',10);
$pdf->Cell(190,7,$show_total_balance ,0,0,'R');
$pdf->Line(9,$starting_line+22,270,$starting_line+22);
$pdf->Line(9,$starting_line+23,270,$starting_line+23);
$pdf->Ln(3);


	
}	

ob_end_clean();
$pdf->Output();


?>