
<?PHP
	include "../../class/connect.class.php";
	$conn = new getConnection();
	$db = $conn->PDO();
	
	// $res = $db->query("select * from tbl_connection_type");
	// $rowCT = $res->fetchAll(PDO::FETCH_ASSOC);
?>
<form id="testForm" action="">
    <div id = "info" align="center" style="overflow-y: scroll; border: thin solid; height: 510px;">
        <div style = "margin-top: 10px;" align="center" class = "col-sm-12">
            <h4>Consumer Details</h4>
        </div>
        <div class="row">
            <!--iv class = "col-sm-6" style="height: 198px;">
                <div onclick="performClick('uploader');" id="image">
                    <div style="overflow:hidden; width:100%; height:100%;">
                        <img src="#" id="conPic" alt="Image goes here." />
                    </div>
                </div>
                <input type="file" accept="image/*" style="width:100%;" id="uploader" name="uploader" onchange="PreviewImage()"/>
            </div-->
            <div style="margin-bottom: 5px;" class = "col-sm-12">
                <input type = "text" id = "fname" name = "fname" class = "form-control input-sm" placeholder = "First Name">
            </div>
            <div style="margin-bottom: 5px;" class = "col-sm-12">
                <input type = "text" id = "mname" name = "mname" class = "form-control input-sm" placeholder = "Middle Name">
            </div>
            <div style="margin-bottom: 5px;" class = "col-sm-12">
                <input type = "text" id = "lname" name = "lname" class = "form-control input-sm" placeholder = "Last Name">
            </div>
            <div style="margin-bottom: 5px;" class = "col-sm-12">
                <input type = "text" id = "ename" name = "ename" class = "form-control input-sm" placeholder = "Extension Name">
            </div>
			<div style="margin-bottom: 5px;" class = "col-sm-6">
                <input type = "text" id = "remarks" name = "remarks" class = "form-control input-sm" placeholder = "Remarks">
            </div>
            <div style="margin-bottom: 5px;" class = "col-sm-6">
                <input type = "text" id = "bookNo" name = "bookNo" class = "form-control input-sm" placeholder = "Book">
            </div>
            <div style="margin-bottom: 5px;" class = "col-sm-6">
                <div id = "civilStatus" name = "civilStatus" class = "form-control input-sm">
                </div>
            </div>
            <div style="margin-bottom: 5px;" class = "col-sm-6">
                <input type = "text" id = "spouseName" name = "spouseName" class = "form-control input-sm" placeholder = "Name of Spouse">
            </div>
        </div>
        <hr style="width:95%; margin:10px;"/>
        <div class="row">
            <div style="margin-bottom: 5px;" class = "col-sm-6">
                <div id = "municipality"  name = "municipality" class = "form-control input-sm">
                </div>
            </div>
            <div style="margin-bottom: 5px; padding: 0;" class = "col-sm-6">
                <div id = "brgy"  name = "brgy" class = "form-control input-sm">
                </div>
            </div>
            <div style="margin-bottom: 5px;" class = "col-sm-6">
                <input type = "text" id = "purok" name = "purok" class = "form-control input-sm" placeholder = "Purok">
            </div>
            <div style="margin-bottom: 5px;" class = "col-sm-6">
                <input type = "text" id = "hno" name = "hno" class = "form-control input-sm" placeholder = "House number/ Street">
            </div>
         </div>   
        <hr style="width:95%; margin:10px;"/>
        <div class="row">    
            <div class = "col-sm-6">
                <input type = "text" id = "phone" name = "phone" class = "form-control input-sm" placeholder = "Phone Number">
            </div>
            <div class = "col-sm-6">
                <input type = "text" id = "email" name = "email" class = "form-control input-sm" placeholder = "Email Address">
            </div>
        </div>
        <hr style="width:95%; margin:10px;"/>
		<div class = "row">
			<div class = "col-sm-6">
				<div id = "customerType" name = "customerType" class = "form-control input-sm"></div>
						<!--?PHP
							foreach($rowCT as $connection) {
								echo '<div id="c-'.$connection["conId"].'" name="c-'.$connection["conId"].'" class="connection" style="color:#ffffff;">&nbsp;'.$connection["conDesc"].'</div>';
							}
						?-->
			</div>
			<div class = "col-sm-6">
						<!--div id="subCat">
						</div-->
							<div id = "isBapa" name = "isBapa" class = "form-control input-sm"></div>
			</div>
		</div>
		<div class = "row">
			<div class = "col-sm-6">
				<input type = "checkbox" id = "isTemp" name = "isTemp" class = "form-control">Temporary
			</div>
			<div class = "col-sm-6"><br>
				<input id = "tempDate" name = "tempDate" class = "form-control input-sm" placeholder = "Month/s" disabled>
			</div>
		</div>
	</div>
    <div style = "margin-top: 10px;" class = "col-sm-6">
        <input id="addApp" type = "button" class = "form-control btn btn-success" value = "Add">
    </div>
    <div style = "margin-top: 10px;" class = "col-sm-6">
        <input id = "canApp" type = "button" class = "form-control btn btn-danger" value = "Close">
    </div>
</form>