<?php
    include "../../class/connect.class.php";

    $con = new getConnection();
    $db = $con->PDO();
	$userId = $_SESSION["userId"];

    if(isset($_POST["mr"])){
        $mr = $_POST["mr"];
        $items = $_POST["items"];
		
		$add = true;
        $i = 0;
        $ctr = 1;
		$array = array();	
		try{
			$db->beginTransaction();
			while($i<count($items)){
				$updateMr = $db->prepare("UPDATE tbl_mr SET isApproved = ? WHERE mrNo = ?");
				$updateMr->execute(array(1, $mr));
				
				if($ctr%3 == 0){
					array_push($array, $items[$i]);
					$select = $db->query("SELECT *FROM tbl_mr_content WHERE mrNo = '$mr' AND entry_id = '$items[$i]'");
					foreach($select as $row)
						$qty = $row["mrQuantity"];
					
					$update = $db->prepare("UPDATE tbl_mr_content SET issuedQuantity = ? WHERE mrNo = ? AND entry_id = ?");
					$update->execute(array($array[0], $mr, $array[2]));
					
					if($array[0] != $qty)
						$add = false;
					$array = array();
				}else
					array_push($array, $items[$i]);

				$i++;
				$ctr++;
			}
			
			$query = $db->query("SELECT *FROM tbl_mr_wo WHERE mrNo = '$mr'");
			
			if($query->rowCount() > 0){
				foreach($query as $row){
					$appId = $row["appId"];
					
					foreach( $db->query("SELECT *FROM tbl_applications JOIN tbl_temp_consumers USING(cid) WHERE appId = '$appId'") as $row2){
						
						$processed = $t = 0;
						$transaction = $db->query("SELECT * FROM tbl_transactions WHERE appid = '$appId' ORDER BY tid DESC LIMIT 1");
						if($transaction->rowCount() > 0){
							foreach($transaction as $row){
								$d = explode("-", $row["tid"]);
								$query2 = $db->query("SELECT *FROM tbl_transactions WHERE tid LIKE '%$d[0]%' ORDER BY tid DESC LIMIT 1");
								if($query2->rowCount() > 0){
									foreach($query2 as $row2){
										$d = explode("-", $row2["tid"]);
										$incr = $d[1]+1;
										$t = $d[0]."-".$incr;
										$processed = $row2["processedBy"];
									}
								}
							}
						}
						$date = date("Y-m-d H:i:s");
						$insert = $db->prepare("INSERT INTO tbl_transactions (tid, appId, cid, status, processedBy, approvedBy, dateApproved, dateProcessed)
												VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
						$insert->execute(array($t, $appId, $row2["cid"], 7, $processed, $userId, $date, $date));
						
						$update = $db->prepare("UPDATE tbl_transactions SET action = ?, approvedBy = ?, dateApproved = ? WHERE appId = ? AND status = ? ");
						$update->execute(array(1, $userId, $date, $appId, 6));
					}
				}
			}
			$db->commit();
		} catch(PDOException $e){
			$db->rollBack();
			echo $e;
		}
		echo "1";
    }
?>