<?php
error_reporting(E_ALL ^ E_DEPRECATED);
session_start();
if(!isset($_SESSION['username'])){
    header("Location:../index.php");
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
		?>
		
		<script>
		$(document).ready(function(){
			var mr = appId = "";
			$("#jqxMenu").jqxMenu({width: window.innerWidth-5, theme: "main-theme"});
			
			$("#mainSplitter").jqxSplitter({
				width: window.innerWidth-6, 
				height:window.innerHeight-40,
				resizable:true,
				theme: "main-theme",
				orientation: "horizontal",
				panels: [{ size:"50%",collapsible:false  }, 
				{ size: "50%",collapsible: false }] 
			});
			
			var initrowdetails = function (index, parentElement, gridElement, datarecord) {
				var tabsdiv = null;
				var information = null;
				var notes = null;
				tabsdiv = $($(parentElement).children()[0]);
				if (tabsdiv != null) {
					information = tabsdiv.find('.information');
					notes = tabsdiv.find('.notes');
					var title = tabsdiv.find('.title');
					title.text("Material Lists");
					var container = $('<div style="margin: 5px;"></div>')
					container.appendTo($(information));
					
					$.ajax({
						url: "sources/getItems.php",
						type: "post",
						data: {id: datarecord.mrNo},
						success: function(data){
							var leftcolumn = $('<div id = "ml-'+datarecord.mrNo+'"></div>');
							var panel = $('<div class = "pnl"></div>')
							container.append(panel);
							panel.append(leftcolumn);
							$(".pnl").jqxPanel({width: "95%", height: 140});
							$("#ml-"+datarecord.mrNo).html(data);
						}
					});
					
					$.ajax({
						url: "sources/getWo.php",
						type: "post",
						data: {id: datarecord.mrNo},
						success: function(data){
							var notescontainer = $('<div id = "wl-'+datarecord.mrNo+'" style="white-space: normal; margin: 5px;"><span>hello there</span></div>');
							var panel = $('<div id = "pnl-'+datarecord.mrNo+'"></div>')
							
							$(notes).append(panel);
							panel.append(notescontainer);
							$("#pnl-"+datarecord.mrNo).jqxPanel({width: "95%", height: 140});
							$("#wl-"+datarecord.mrNo).html(data);
						}
					})

					$(tabsdiv).jqxTabs({ width: 750, height: 210});
				}
			}
			
			var mrSource = {
				datatype: "json",
				datafields: [
					 {name: "ctr"},
					 {name: "mrNo"},
					 {name: "items"},
					 {name: "wos"},
					 {name: "purpose"},
					 {name: "date"},
				],
				url: "sources/mrList.php",
				async: false
			}
			
			var mrAdapter = new $.jqx.dataAdapter(mrSource);
			
			var woSource ={
				datatype: "json",
				datafields: [
					{name: "ctr"},
					{name: "wo"},
					{name: "consumer"},
					{name: "address"},
					{name: "acctNo"},
					{name: "date"},
				],
				url: "sources/woList.php",
				async: false
			}

			var woAdapter = new $.jqx.dataAdapter(woSource);
			
			var consumerList = {
				datatype: "json",
				datafields: [
					{ name: "ctr1"},
					{ name: "acctNo"},
					{ name: "appId"},
					{ name: "consumerName"},
					{ name: "address"},
					{ name: "meterNo"},
					{ name: "cid"},
					{ name: "mReading"},
					{ name: "mBrand"},
					{ name: "mClass"},
					{ name: "mSerial"},
					{ name: "mERC"},
					{ name: "mLabSeal"},
					{ name: "mTerminal"},
					{ name: "multiplier"},
				]
			}
			
			var mrSource = {
				datatype: "json",
				datafields: [
					{name: "ctr"},
					{name: "mrNo"},
					{name: "items"},
					{name: "wos"},
					{name: "purpose"},
					{name: "date"},
					{name: "mrNo"}
				],
				url: "sources/mrList2.php",
				async: false
			}
			var mrNo = "";
			var mrAdapter = new $.jqx.dataAdapter(mrSource);
			
			$("#mrList").on("rowdoubleclick", function(events){
				var rowindex = $("#mrList").jqxGrid("getselectedrowindex");
				var data = $("#mrList").jqxGrid("getrowdata", rowindex);
				mrNo = data.mrNo;
				
				consumerList.url = 'sources/getConsumerWo.php?mr='+data.mrNo;
				// selected_account = data.acctNo;
				var dataAdapter = new $.jqx.dataAdapter(consumerList);
				$('#consumerList').jqxGrid({source:dataAdapter});
				
				$("#consumerList").jqxGrid('clearselection');
				$("#assignMeter").jqxButton({disabled: true});
			});

			$("#consumerList").on("rowselect", function(event){
				var appId = event.args.row.appId;
				$.ajax({
					url: "sources/checkApproveMr.php",
					type: "post",
					data: {mrNo: mrNo, appId: appId},
					success: function(result){
						if(result == "1")
							$("#assignMeter").jqxButton({disabled: true});
						else
							$("#assignMeter").jqxButton({disabled: false});
					}
				})
			});
			
			$("#consumerList").on("contextmenu", function(event){
				return false;
			});
			
			$("#mrList").jqxGrid({
				width: "100%",
				height: "100%",
				theme: "main-theme",
				selectionmode: "singlerow",
				source: mrAdapter,
				showtoolbar: true,
				pageable: true, 
				rendertoolbar: function(toolbar){
					var container = $("<div style='margin: 5px;'></div>");
					toolbar.append(container);
					var input = $("<input class='form-control jqx-input jqx-widget-content jqx-rc-all' id='searchField' placeholder = 'Search' type='text' style='height: 23px; float: left; width: 223px;' />");
					container.append(input);
					$("#searchField").jqxInput({theme: "main-theme", width: 150});
					
					input.on('keydown', function (event) {
						var key = event.charCode ? event.charCode : event.keyCode ? event.keyCode : 0;
							
						if (key == 13 || key == 9) {
							$("#mrList").jqxGrid('clearfilters');
							// var searchColumnIndex = $("#dropdownlist").jqxDropDownList('selectedIndex');
							var datafield = "mrNo";
							
							var searchText = $("#searchField").val();
							var filtergroup = new $.jqx.filter();
							var filter_or_operator = 1;
							var filtervalue = searchText;
							var filtercondition = 'contains';
							var filter = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
							filtergroup.addfilter(filter_or_operator, filter);
							$("#mrList").jqxGrid('addfilter', datafield, filtergroup);
							$("#mrList").jqxGrid('applyfilters');
						}
					   
						if(key == 27){
							$("#mrList").jqxGrid('clearfilters');
							return true;
						}
					});
				},
				columns: [
					  { text: "#", datafield: "ctr", align: "center", cellsalign: "center", width: 50 },
					  { text: "MR-M No", datafield: "mrNo", align: "center", cellsalign: "center", width: 250 },
					  { text: "Items", datafield: "items", align: "center", cellsalign: "center", width: 150 },
					  { text: "WO's", datafield: "wos", align: "center", cellsalign: "center",width: 150 },
					  { text: "Purpose", datafield: "purpose", align: "center", cellsalign: "center",width: 200},
					  { text: "Date", datafield: "date", align: "center", cellsalign: "center"}
				  ]
			});
			
			$("#consumerList").jqxGrid({
				width: "100%",
				height: "100%",
				theme: "main-theme",
				selectionmode: "singlerow",
				showtoolbar: true,
				rendertoolbar: function(toolbar){
					var container = $("<div style='margin: 5px;'></div>");
					toolbar.append(container);
					container.append('<input id="assignMeter" type="button" value="Assign Meter" />');
					
					
					// container.append('<input id="approveMeter" style = "margin-left: 10px;" type="button" value="Approve Meters" />');
					$("#assignMeter").jqxButton({theme: "main-theme", width: 150, disabled: true});
					// $("#approveMeter").jqxButton({theme: "main-theme", width: 150, disabled: true});
					$("#assignMeter").on("click", function(event){
						var row = $("#consumerList").jqxGrid("getselectedrowindex");
						var data = $("#consumerList").jqxGrid("getrowdata", row);
						$.ajax({
							url: "sources/getConsumerWo.php",
							type: "post",
							dataType: "json",
							data: {cid: data.cid},
							success: function(data){
								$("#meterForm").jqxWindow("open");
								$("#consumerData").html(
									"Primary Account No: "+data.acctNo+"<br>Consumer Name: "+data.consumerName+"<br>Addresss: "+data.address);
								$("#meterForm input")[0].value = data.mReading;
								$("#meterForm input")[1].value = data.mBrand;
								$("#meterForm input")[2].value = data.mClass;
								$("#meterForm input")[3].value = data.mSerial;
								$("#meterForm input")[4].value = data.mERC;
								$("#meterForm input")[5].value = data.mLabSeal;
								$("#meterForm input")[6].value = data.mTerminal;
								$("#meterForm input")[7].value = data.multiplier;
							}
						});
					});
				},
				ready: function(){
					$("#consumerList").jqxGrid("hidecolumn", "cid");
					$("#consumerList").jqxGrid("hidecolumn", "appId");
				},
				columns: [
					  { text: "#", datafield: "ctr1", pinned: true, align: "center", cellsalign: "center", width: 50 },
					  { text: "Account Number", datafield: "acctNo", pinned: true,  align: "center", cellsalign: "center", width: 150 },
					  { text: "Consumer Name", datafield: "consumerName", pinned: true,  align: "center", cellsalign: "center", width: 300 },
					  { text: "Address", datafield: "address", pinned: true,  align: "center", cellsalign: "center",width: 300 },
					  { text: "Reading", datafield: "mReading", align: "center", cellsalign: "center",width: 100},
					  { text: "Brand", datafield: "mBrand", align: "center", cellsalign: "center",width: 100},
					  { text: "Meter Type", datafield: "mClass", align: "center", cellsalign: "center",width: 100},
					  { text: "Meter Serial", datafield: "mSerial", align: "center", cellsalign: "center",width: 100},
					  { text: "ERC Serial", datafield: "mERC", align: "center", cellsalign: "center",width: 100},
					  { text: "Meter Lab Seal", datafield: "mLabSeal", align: "center", cellsalign: "center",width: 100},
					  { text: "Terminal Seal", datafield: "mTerminal", align: "center", cellsalign: "center",width: 100},
					  { text: "Multiplier", datafield: "multiplier", align: "center", cellsalign: "center",width: 100},
					  { text: "CID", datafield: "cid", align: "center", cellsalign: "center"},
					  { text: "AppId", datafield: "appId", align: "center", cellsalign: "center"}
				  ]
			});
			
			$("#meterForm").jqxWindow({
				theme: "main-theme", maxHeight: 510, height: 510, width:  510, cancelButton: $("#cancel"), showCloseButton: true, draggable: false, resizable: false, isModal: true
				, autoOpen: false, modalOpacity: 0.50
			});
			
			$("#confirmModal").jqxWindow({
				theme: "main-theme", height: 170, width:  400, cancelButton: $("#cancel2"), showCloseButton: true, draggable: false, resizable: false, isModal: true, autoOpen: false, modalOpacity: 0.50
			});
			
			$("#confirmMr").jqxWindow({
				theme: "main-theme", height: 170, width:  400, cancelButton: $("#cancel3"),showCloseButton: true, draggable: false, resizable: false, isModal: true, autoOpen: false, modalOpacity: 0.50
			});
				
			$("#confirm").click(function(){
				$("#confirmModal").jqxWindow("open")
			});
			
			$("#materials").on("click", function(){
				alert("success");
			});
		
			$("#confirm2").click(function(){
				var row = $("#consumerList").jqxGrid("getselectedrowindex");
				var data = $("#consumerList").jqxGrid("getrowdata", row);
				
				$.ajax({
					url: "functions/issueMeter.php",
					type: "post",
					data: {cid: data.cid, mReading: $("#mReading").val(), mBrand: $("#mBrand").val(), mClass: $("#mClass").val(), mSerial: $("#mSerial").val(), mERC: $("#mERC").val(), mLabSeal: $("#mLabSeal").val(), mTerminal: $("#mTerminal").val(), multiplier: $("#mMultiplier").val()},
					success: function(data){
						if(data == 1){
							$("#confirmModal").jqxWindow("close");
							$("#meterForm").jqxWindow("close");
							consumerList.url = 'sources/getConsumerWo.php?mr='+mrNo;
							var dataAdapter = new $.jqx.dataAdapter(consumerList);
							$('#consumerList').jqxGrid({source:dataAdapter});
							$("#meterForm :input").val("");
							// $("#assignMeter").jqxButton({disabled: true});
						}
					}
				});
			});
			
			$("#logout").click(function(){
				$.ajax({
					url: "../logout.php",
					success: function(data){
						if(data == 1){
							window.location.href = "../index.php";
						}
					}
				});
			});
		});
		</script>
	</head>
	<body class="default">
		<div class = "row push-right-m2">
			<div class = "" id="jqxMenu">
				<ul>
					<ul>
						<li><img  src="../assets/images/icons/icol16/src/house.png" alt=""/><a href = "index.php"> Home</a></li>
						<li><img  src="../assets/images/icons/icol16/src/cog.png" alt=""/><a href = "mr.php">Meter</a></li>
						<li id = "logout"><img src = "../assets/images/icons/icol16/src/lock.png"> Logout</li>
					</ul>	
				</ul>
			</div>
			<div id = "mainSplitter">
				<div class="splitter-panel">
					<div id = "mrList"></div>
				</div>
				<div class="splitter-panel">
					<div id = "consumerList"></div>
				</div>
			</div>
	</div>
		<div id = "confirmModal">
			<div><img src = "../assets/images/icons/icol16/src/accept.png">CONFIRM</div>
			<div class = "text-center">
				<h4>Confirm Meter for Consumer?</h4><br><br>
				<div class = "col-sm-6"><button class = "btn btn-success btn-block" id = "confirm2">APPROVE</button></div>
				<div class = "col-sm-6"><button class = "btn btn-danger btn-block" id = "cancel2">CANCEL</button></div>
			</div>
		</div>
		<div id = "meterForm">
			<div><img src = "../assets/images/icons/icol16/src/accept.png"> CONFIRM</div>
			<div>
				<div id = "consumerData" class = "text-center"></div>
				<table class = "table table-condensed">
					<tr>
						<td>METER READING</td>
						<td><input type = "text" id = "mReading" class = "form-control"></td>
					</tr>
					<tr>
						<td>BRAND</td>
						<td><input type = "text" id = "mBrand" class = "form-control"></td>
					</tr>
					<tr>
						<td>CLASS/AMPERES</td>
						<td><input type = "text" id = "mClass" class = "form-control"></td>
					</tr>
					<tr>
						<td>SERIAL NO.</td>
						<td><input type = "text" id = "mSerial" class = "form-control"></td>
					</tr>
					<tr>
						<td>ERC SEAL NO.</td>
						<td><input type = "text" id = "mERC" class = "form-control"></td>
					</tr>
					<tr>
						<td>METER LAB SEAL</td>
						<td><input type = "text" id = "mLabSeal" class = "form-control"></td>
					</tr>
					<tr>
						<td>TERMINAL SEAL</td>
						<td><input type = "text" id = "mTerminal" class = "form-control"></td>
					</tr>
					<tr>
						<td>MULTIPLIER</td>
						<td><input type = "text" id = "mMultiplier" class = "form-control"></td>
					</tr>
				</table>
				<div class = "col-sm-6"><button  class = "btn btn-success btn-block" id = "confirm">CONFIRM</button></div>
				<div class = "col-sm-6"><button class = "btn btn-danger btn-block" id = "cancel">CANCEL</button></div>
			</div>
		</div>
		<div id="confirmMr">
			<div><img src="../assets/images/icons/icol16/src/accept.png"> Confirm</div>
			<div  class = "text-center">
				<h4>Confirm Meter/s?<br></h4><br>
				<div class = "col-sm-6"><button  class = "btn btn-success btn-block" id = "confirm3">CONFIRM</button></div>
				<div class = "col-sm-6"><button class = "btn btn-danger btn-block" id = "cancel3">CANCEL</button></div>
			</div>
		</div>
	</body>
</html>