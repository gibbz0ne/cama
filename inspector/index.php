<?php
error_reporting(E_ALL ^ E_DEPRECATED);
session_start();
if(!isset($_SESSION['userId'])){
	header("Location:../index.php");
}
else {
	if($_SESSION['usertype'] != "inspector") {
		header("Location:../".$_SESSION['usertype']);
	}
}

include "../class/includes.class.php";
$include = new includes();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="description" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />	
		<link rel="shortcut icon" type="image/x-icon" href="../assets/images/icons/icon.png" />
		<title id="Description">CHANGE METER/NEW CONNECTION</title>
		
		<?PHP
			echo $include->includeCSS();
			echo $include->includeJS();
			echo $include->includeJSFn("inspector");
		?>
		
	</head>
	<body class="default">
		<div class="row push-right-m2">
			<div id="jqxMenu" >
				<ul>
					<li><img  src="../assets/images/icons/icol16/src/house.png" alt=""/><a href = "index.php"> Home</a></li>
					<li><img  src="../assets/images/icons/icol16/src/report.png" alt=""/><a href = "list.php"> Inspected</a></li>
					<!--li id = "reports"><img  src="../assets/images/icons/icol16/src/report.png" alt=""/> Reports</li-->
					<li id = "logout"><img src = "../assets/images/icons/icol16/src/lock_unlock.png"> Logout</li>
				</ul>
			</div>
			<div>
				<div id = "inspection_list"></div>
			</div>
			<div id = "confirmInspection">
				<div><img src = "../assets/images/icons/icol16/src/application.png"> Confirm Inspection</div>
				<div style = "background-color: #0A525A; color: #ffffff">
					<table align = "center"width = "95%">
						<tr>
							<td class = "text-center"><h5>MAIN PROTECTION AND RATING</h5></td>
							<td class = "text-center" colspan = "3"><h5>SERVICE ENTRANCE</h5></td>
						</tr>
						<tr>
							<td width = "40%" class = "text-center" colspan = "1"><div id = "pType" class = "form-control"></div></td>
							<td width = "20%" class = "text-center" colspan = "1"><div id = "eType" class = "form-control"></div></td>
							<td width = "20%" class = "text-center" colspan = "1"><input type = "text" id = "eSize" placeholder = "Entrance Size" class = "form-control"></td>
							<td width = "20%" class = "text-center" colspan = "2"><input type = "text" id = "wSize" placeholder = "Wire Size" class = "form-control"></td>
						</tr>
						<tr>
						</tr>
						<tr>
							<td class = "text-center"><input type = "text" id = "rating" placeholder = "Rating" class = "form-control"></td>
							<td class = "text-center" colspan = "1"><input type = "text" id = "servicePole" placeholder = "No. of Service Pole" class = "form-control"></div></td>
							<td class = "text-center" colspan = "1">
								<select id = "cType" class = "form-control">
									<option value = "R">R</option>
									<option value = "C">C</option>
									<option value = "E">E</option>
									<option value = "F">F</option>
									<option value = "H">H</option>
								</select>
							</td>
							<td class = "text-center" colspan = "2"><input type = "text" id = "length" placeholder = "Length" class = "form-control"></td>
						</tr>
						<tr>	
							<td colspan = "5" class = "text-center"><h5>METER</h5></td>
						</tr>
						<tr>
							<td colspan = "1"><input type = "text" id = "totalVa" class = "form-control" placeholder = "TOTAL VA"></td>
							<td colspan = "2"><input type = "text" id = "meter" class = "form-control" placeholder = "METER FORM"></td>
							<td colspan = "2"><div id = "mClass" class = "form-control"></div></td>
						</tr>
						<tr>
							<td colspan = "1"><div id = "substation" class = "form-control"></div></td>
							<td colspan = "2"><div id = "feeder" class = "form-control"></div></td>
							<td colspan = "2"><div id = "phase" class = "form-control"></div></td>
						</tr>
						<tr>
							<td colspan = "5" class = "text-center"><h5>INSPECTED BY:</h5></td>
						</tr>
						<tr>
							<td><input type = "text" id = "inspectedBy" class = "form-control" placeholder = "Inspected by"></td>
							<td colspan = "2"><input type = "text" id = "iRemarks" class = "form-control" placeholder = "Remarks"></td>
							<td colspan = "2"><div id = "inspectedDate" ></div></td>
						</tr>
					</table>
					<div style = "margin-top: 10px;" class = "col-sm-6">
						<button class = "btn btn-success btn-block" id = "approveApp">Approve</button>
					</div>
					<div style = "margin-top: 10px;" class = "col-sm-6">
						<button class = "btn btn-danger btn-block cancelApp">Cancel</button>
					</div>
				</div>
			</div>
			<div id = "rejectApp">
				<div><img src = "../assets/images/icons/icol16/src/application_delete.png"> Cancel Application</div>
				<div>
					<h5>Inspected By:</h5>
					<input type = "text" style = "margin-top: 7px;" id = "inspectedBy1" class = "form-control" placeholder = "Inspected by">
					<h5>Remarks:</h5>
					<input type = "text" id = "remarks1" style = "margin-top: 10px;" class = "form-control" placeholder = "Remarks">
					<div style = "margin-top: 10px;" class = "col-sm-6">
						<button class = "form-control btn btn-success" id = "approveApp1">Confirm</button>
					</div>
					<div style = "margin-top: 10px;" class = "col-sm-6">
						<button class = "form-control btn btn-danger cancelApp1" id="cancelC">Cancel</button>
					</div>
				</div>
			</div>
			<div id = "confirm2">
				<div><img src = "../assets/images/icons/icol16/src/accept.png"> Confirm</div>
				<div>
					<h4 class = "text-center">Submit Inspection Report</h4>
					<div style = "margin-top: 10px;" class = "col-sm-6">
						<input type = "button" class = "form-control btn btn-success" value = "Submit" id = "submit">
					</div>
					<div style = "margin-top: 10px;" class = "col-sm-6">
						<input type = "button" class = "form-control btn btn-danger cancelApp2" value = "Cancel">
					</div>
				</div>
			</div>
			
			 <div id = "reportList">
				<div><img src = "../assets/images/icons/icol16/src/report.png"> Inspection List</div>
				<div>
					<div id='jqxTabs'>
						<ul>
							<li>Approved</li>
							<li>Cancelled</li>
						</ul>
						<div>
						   <div id = "inspection_list2"></div>
						</div>	
						<div>
							<div id = "inspection_list3"></div>
						</div>
					</div>
				</div>
			</div>
			<div id="processing">
				<div><img src="../assets/images/icons/icol16/src/accept.png" style="margin-bottom:-5px;"><b><span style="margin-top:-24; margin-left:3px">Processing</span></b></div>
				<div>
					<div><img src="../assets/images/loader.gif">Please Wait
					
					</div>
				</div>
			</div>
		</div>
	</body>
</html>