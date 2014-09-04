<?php
    require("../../includes/ensure_admin_logon.php");
	 require ("../../classes/DBAdmin.php");
	 
	 $db = new DBAdmin();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>My Space Empire - Maintain Buildable Components</title>
		<link rel="stylesheet" href="../css/main.css" media="screen" />
		<script>
			<?php include("../../includes/ajax.js"); ?>
		
			function saveComponent()
			{
				var selector = document.getElementById("record_selector");
				var JSONObject = {requestType : "setRecordDetails", build_type_id : selector.options[selector.selectedIndex].value, name : document.getElementById("name").value, type_attributes : document.getElementById("type_attributes").value};
				var req = new AJAX("ajax_admin/ajax_buildable_component.php", handleResponse);
				
				req.doPost("parms=" + JSON.stringify(JSONObject), true);
			}
			
			function deleteComponent()
			{
				alert("deleteComponent");
			}
			
			function onSelectionChange(src)
			{
				var selector = document.getElementById("record_selector");

				if (selector.options[selector.selectedIndex].value == 0)
				{
					document.getElementById("name").value = "";
					document.getElementById("type_attributes").value = "";
				}
				else
				{
					var req = new AJAX("ajax_admin/ajax_buildable_component.php", handleResponse);
					var JSONObject = {requestType : "getRecordDetails", build_type_id : selector.options[selector.selectedIndex].value};
					
					req.doPost("parms=" + JSON.stringify(JSONObject), true);
				}
			}
			
			function handleResponse(resp)
			{
				var json = JSON.parse(resp);

				if (json.status == "ERROR")
					return;

				var selector = document.getElementById("record_selector");

				if (selector.options[selector.selectedIndex].value == 0)
					selector.options[selector.options.length] = new Option(records[0]['resp'], records[0]['resp'], false, true);
				else
				{
					document.getElementById("name").value = json.resp[0]["name"];
					document.getElementById("type_attributes").value = json.resp[0]["type_attributes"];
				}
			}
		</script>
	</head>
	<body>
		<header>
			<h1 style="color: red; margin-top: 1em; float: left;">My Space Empire</h1>
		</header>
		<div style="clear: both;"></div>
		<main style="margin-top: 1em;">
			<h2 style="color: gold; text-align: center;">Maintain Buildbale Components</h2>
				<select name="record_selector" id="record_selector" onchange="onSelectionChange(this);">
            	<option value="0">New Component</option>
                <?php
                $rows = $db->select("list_components", null);
                for($i = 0; $i < count($rows); $i++)
                	echo "<option value='".$rows[$i]['build_type_id']."'>".$rows[$i]['name']."</option>\n";
                ?>
            </select>
				<div style="width: 75%; text-align: right;">
					<label style="display: block; margin: 0.2em;">Name:<input type="text" id="name" name="name" size="30" /></label>
					<label style="display: block; margin: 0.2em;">Attributes:<textarea id="type_attributes" name="type_attributes" maxlength="500" rows="5" cols="100" placeholder="Valid JSON string."></textarea></label>
					
					<button type="button" onclick="saveComponent();">Save Component</button>
					<button type="button" onclick="deleteComponent();">Delete Component</button>
				</div>
			<a href="admin_main.php">Back to main menu.</a>
		</main>
	</body>
</html>