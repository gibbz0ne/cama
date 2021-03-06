<?php
error_reporting(E_ALL ^ E_DEPRECATED);
include "../class/connect.class.php";
require('../assets/fpdf/fpdf.php');

$con = new getConnection();
$db = $con->PDO();

$woNo = $_GET["ref"];
$pdf=new FPDF('P','mm','Letter');
$pdf->SetFont('Arial','B',10);
$pdf->AddPage();


//$pdf->Image('../assets/images/logo.jpg',30,15,25);
if(isset($_GET["ref"])){
	foreach($db->query("SELECT *FROM tbl_work_order a
						LEFT OUTER JOIN tbl_applications b ON a.appId = b.appId
						LEFT OUTER JOIN tbl_temp_consumers c ON b.cid = c.cid 
						WHERE a.wo = '$woNo'") as $row)
		$d = explode(" ", $row["woDate"]);
		$date = date("M, d Y", strtotime($d[0]));

	$pdf->Ln(10);
	$pdf->SetX(10);
	$pdf->Cell(196, 15, "", 1);
	$pdf->Ln(1);
	$pdf->SetX(70);
	$pdf->Cell(39,10,'ALBAY POWER AND ENERGY CORPORATION', 0);
	$pdf->SetFont('Arial','',10);
	$pdf->Ln(4);
	$pdf->SetX(81);
	$pdf->Cell(39,10,'W. Vinzon St., Albay Dist., Leg. City',0);
	$pdf->Ln(10);
	$pdf->SetX(10);
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(196, 6, "WORK ORDER", 1, 0, "C");
	$pdf->SetFont('Arial','',9);
	$pdf->SetX(10);
	$pdf->Ln();
	$pdf->Cell(40, 6, "WORK ORDER NO.:", 1, 0, "L");
	$pdf->Cell(100, 6, $row["wo"], 1, 0, "L");
	$pdf->Cell(30, 6, "DATE:", 1, 0, "L");
	$pdf->Cell(26, 6, $d[0], 1, 0, "L");
	$pdf->SetX(10);
	$pdf->Ln();
	$pdf->Cell(40, 6, "LOCATION:", 1, 0, "L");
	$pdf->Cell(100, 6, iconv('UTF-8', 'windows-1252', $row["AddressT"]), 1, 0, "L");
	$pdf->Cell(30, 12, "WAREHOUSE NO:", 1, 0, "L");
	$pdf->Cell(26, 12, "", 1, 0, "L");
	$pdf->Ln(6);
	$pdf->Cell(40, 6, "ACCOUNT NAME/NO:", 1, 0, "L");
	$pdf->Cell(100, 6, iconv('UTF-8', 'windows-1252', $row["AccountNameT"])." / ".$row["AccountNumberT"], 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(40, 6, "TASK DESCRIPTION:", 1, 0, "L");
	$pdf->Cell(100, 6, "", 0, 0, "L");
	$pdf->Cell(30, 6, "Safety:", 1, 0, "L");
	$pdf->Cell(26, 6, "", 1, 0, "L");
	$pdf->SetX(10);
	$pdf->Cell(196, 31, "", 1, 0, "L");
	$pdf->Ln(6);
	$pdf->Cell(10, 6, "");
	$pdf->Cell(40, 6, "Feeder/Substation: ", 0, "L");
	$pdf->SetX(150);
	$pdf->Cell(30, 6, "By APEC:", 1, 0, "L");
	$pdf->Cell(26, 6, "", 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(10, 6, "");
	$pdf->Cell(40, 6, "Assembly: ", 0, "L");
	$pdf->SetX(150);
	$pdf->Cell(30, 6, "By Contact:", 1, 0, "L");
	$pdf->Cell(26, 6, "", 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(10, 6, "");
	$pdf->Cell(40, 6, "Damaged: ",0, "L");
	$pdf->SetX(150);
	$pdf->Cell(30, 6, "Target Date:", 1, 0, "L");
	$pdf->Cell(26, 6, "", 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(10, 6, "");
	$pdf->Cell(40, 6, "Scope of Work: ",0, "L");
	$pdf->Cell(40, 6, $row["scope"], 0, "L");
	$pdf->Ln(7);
	$pdf->SetX(10);
	$pdf->Cell(30, 6, "Personnel: ", 1, 0, "L");
	$pdf->Cell(166, 6, "", 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(30, 6, "Duration (Hr.): ", 1, 0, "L");
	$pdf->Cell(166, 6, "", 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(30, 6, "Total Man Hour: ", 1, 0, "L");
	$pdf->Cell(166, 6, "", 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(30, 6, "Estimated Cost: ", 1, 0, "L");
	$pdf->Cell(166, 6, "", 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(65, 6, "Issued By: ", 1, 0, "L");
	$pdf->Cell(65, 6, "Issued To: ", 1, 0, "L");
	$pdf->Cell(66, 6, "Approved By: ", 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(65, 10, "", 1, 0, "L");
	$pdf->Cell(65, 10, "", 1, 0, "L");
	$pdf->Cell(66, 10, "", 1, 0, "L");

	$pdf->Ln(20);
	$pdf->SetX(10);
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(196, 7, "CLOSED OUT", 1, 0, "C");
	$pdf->SetFont('Arial','',9);
	$pdf->SetX(10);
	$pdf->Ln();
	$pdf->Cell(40, 6, "WORK ORDER NO.:", 1, 0, "L");
	$pdf->Cell(100, 6, $row["wo"], 1, 0, "L");
	$pdf->Cell(30, 6, "DATE:", 1, 0, "L");
	$pdf->Cell(26, 6, $d[0], 1, 0, "L");
	$pdf->SetX(10);
	$pdf->Ln();
	$pdf->Cell(40, 6, "LOCATION:", 1, 0, "L");
	$pdf->Cell(100, 6, iconv('UTF-8', 'windows-1252', $row["AddressT"]), 1, 0, "L");
	$pdf->Cell(30, 12, "WAREHOUSE NO:", 1, 0, "L");
	$pdf->Cell(26, 12, "", 1, 0, "L");
	$pdf->Ln(6);
	$pdf->Cell(40, 6, "ACCOUNT NAME/NO:", 1, 0, "L");
	$pdf->Cell(100, 6, iconv('UTF-8', 'windows-1252', $row["AccountNameT"])." / ".$row["AccountNumberT"], 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(40, 6, "Actual Activity:", 1, 0, "L");
	$pdf->Cell(100, 6, "", 0, 0, "L");
	$pdf->Cell(30, 6, "Deffered:", 1, 0, "L");
	$pdf->Cell(26, 6, "", 1, 0, "L");
	$pdf->SetX(10);
	$pdf->Cell(196, 31, "", 1, 0, "L");
	$pdf->Ln(6);
	
	$pdf->SetX(150);
	$pdf->Cell(30, 6, "Re-scheduled:", 1, 0, "L");
	$pdf->Cell(26, 6, "", 1, 0, "L");
	$pdf->Ln();
	$pdf->SetX(150);
	$pdf->Cell(30, 6, "Cancelled:", 1, 0, "L");
	$pdf->Cell(26, 6, "", 1, 0, "L");
	$pdf->Ln();
	$pdf->SetX(150);
	$pdf->Cell(30, 6, "Completed:", 1, 0, "L");
	$pdf->Cell(26, 6, "", 1, 0, "L");
	$pdf->Ln();
	$pdf->Ln(7);
	$pdf->SetX(10);
	$pdf->Cell(30, 6, "Personnel: ", 1, 0, "L");
	$pdf->Cell(166, 6, "", 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(30, 6, "Duration (Hr.): ", 1, 0, "L");
	$pdf->Cell(166, 6, "", 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(30, 6, "Total Man Hour: ", 1, 0, "L");
	$pdf->Cell(166, 6, "", 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(30, 6, "Actual Cost: ", 1, 0, "L");
	$pdf->Cell(166, 6, "", 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(65, 6, "Accomplished By: ", 1, 0, "L");
	$pdf->Cell(65, 6, "Inspected By: ", 1, 0, "L");
	$pdf->Cell(66, 6, "Close By: ", 1, 0, "L");
	$pdf->Ln();
	$pdf->Cell(65, 10, "", 1, 0, "L");
	$pdf->Cell(65, 10, "", 1, 0, "L");
	$pdf->Cell(66, 10, "", 1, 0, "L");
}

ob_end_clean();
$pdf->Output();
?>