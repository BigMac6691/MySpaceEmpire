<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>My Space Empire - Create Account</title>
		<link rel="stylesheet" href="css/main.css" media="screen" />
		<script src="js/jscolor/jscolor.js"></script>
		<script>
			window.onload = function()
			{
				document.getElementById("color_1").color.fromString("FF0000");
				document.getElementById("color_2").color.fromString("00FF00");
				document.getElementById("color_3").color.fromString("0000FF");
			}
		
			var ICONS = [{svg : '<text x="50" y="50" style="text-anchor: middle; dominant-baseline: central; fill:silver;">ICON</text><rect width="100%" height="100%" style="stroke:gold; stroke-width:2; fill:none;" />'}, 
						 	 {svg : '<rect width="100%" height="100%" style="fill:#color1;" /><line x1="25" y1="0" x2="25" y2="100" style="stroke:#color2; stroke-width: 3" /><line x1="0" y1="50" x2="100" y2="50" style="stroke:#color2; stroke-width: 3" /><circle cx="25" cy="50" r="15" style="fill:#color3;" />'}];
			var ICONS_INDEX = 0;
			var FIELD_VALID = {alias : false, email : false, password : false, player_icon_svg : false};

			function nextIcon(direction) 
			{
				ICONS_INDEX = (ICONS_INDEX + direction) % ICONS.length;

				if (ICONS_INDEX > 0) 
					document.getElementById("icon_id").innerHTML = ICONS_INDEX + " of " + (ICONS.length - 1);
				else
					document.getElementById("icon_id").innerHTML = "Use Custom SVG";
					
				updateSVG();
			}

			function displayIcon(svg) 
			{
				var icon = document.getElementById('player_icon');
				
				svg = svg.replace(/color1/g, document.getElementById("color_1").value);
				svg = svg.replace(/color2/g, document.getElementById("color_2").value);
				svg = svg.replace(/color3/g, document.getElementById("color_3").value);

				icon.innerHTML = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100' style='width:100%; height:100%;'>" + svg + "</svg>";
				updateCreateButton();
			}

			function updateSVG() 
			{
				var pis = document.getElementById('player_icon_svg');
				var svg = pis.value.length == 0 ? ICONS[ICONS_INDEX]['svg'] : (ICONS_INDEX == 0 ? pis.value : ICONS[ICONS_INDEX]['svg']);
				
				FIELD_VALID.player_icon_svg = ICONS_INDEX > 0 ? true : (document.getElementById("player_icon_svg").value.length > 0);
				
				displayIcon(svg);
			}
			
			var VALIDATORS = new Array();
			VALIDATORS['alias'] = {rule : /^[A-Za-z0-9_]{5,30}$/, msg : "Accepting 5 to 30 letters, numbers and underscores only."};
			VALIDATORS['email'] = {rule : /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i, msg : "Accepting a validly formatted email address."};
			VALIDATORS['password'] = {rule : /^(?=.*\d)(?=.*[a-zA-Z]).{8,30}$/, msg : "Accepting 8 to 30 characters, must contain a letter and a number."};
			
			function checkForDuplicates(src)
			{
				FIELD_VALID[src.id] = false;
				updateCreateButton(); 
				
				if(!VALIDATORS[src.id]['rule'].test(src.value))
				{
					document.getElementById(src.id + "_msg").innerHTML = VALIDATORS[src.id]['msg'];
					src.focus(); // Not currently working in FF, workd in Chrome, others not tested.
					
					return false;
				}
				
				document.getElementById(src.id + "_msg").innerHTML = "Checking availability...";
				
				var JSONObject = {requestType : "check_" + src.id};
				JSONObject[src.id] = src.value;
				
				var req = new AJAX("ajax/ajax_validate_account.php", handleResponse);
				
				req.doPost("parms=" + JSON.stringify(JSONObject));
			}
			
			function checkPassword(src)
			{
				if(!VALIDATORS[src.id]['rule'].test(src.value))
				{
					document.getElementById(src.id + "_msg").innerHTML = VALIDATORS[src.id]['msg'];
					FIELD_VALID[src.id] = false;
					src.focus(); // Not currently working in FF, workd in Chrome, others not tested.
				}
				else
				{
					document.getElementById(src.id + "_msg").innerHTML = "";
					FIELD_VALID[src.id] = true;
				}
				
				updateCreateButton();
			}
			
			function handleResponse(resp)
			{
				var json = JSON.parse(resp);
				
				FIELD_VALID[json.msg] = json.resp[0]['cnt'] == 0;
				updateCreateButton();
				
				document.getElementById(json.msg + "_msg").innerHTML = json.resp[0]['cnt'] > 0 ? "Sorry that " + json.msg + " is already taken." : "";
			}
			
			function createAccount()
			{
				var keys = ["alias", "email", "password"];
				var error = false;
				
				for(var i = 0; i < keys.length; i++)
				{
					var elem = document.getElementById(keys[i]);
					
					if(VALIDATORS[keys[i]]['rule'].test(elem.value))
						document.getElementById(keys[i] + "_msg").innerHTML = "";
					else
					{
						document.getElementById(keys[i] + "_msg").innerHTML = VALIDATORS[keys[i]]['msg'];
						error = true;
					}
				}
				
				if(ICONS_INDEX == 0 && document.getElementById("player_icon_svg").value.length < 5)
				{
					alert("Please select a default icon or enter valid SVG for a custom icon.");
					error = true;
				}
				
				if(error)
					return false;
					
				var svg = ICONS_INDEX > 0 ? ICONS[ICONS_INDEX]['svg'] : document.getElementById('player_icon_svg').value;
				
				svg = svg.replace(/color1/g, document.getElementById("color_1").value);
				svg = svg.replace(/color2/g, document.getElementById("color_2").value);
				svg = svg.replace(/color3/g, document.getElementById("color_3").value);
				
				document.getElementById('player_icon_svg').value = svg;
				document.getElementById("account_data").submit();
			}
			
			function updateCreateButton()
			{
				for(var key in FIELD_VALID)
				{
					if(FIELD_VALID.hasOwnProperty(key) && !FIELD_VALID[key])
					{
						document.getElementById("create_button").disabled = true;
						return;
					}
				}
				
				document.getElementById("create_button").disabled = false;
			}
			
			<?php include("../includes/ajax.js"); ?>
		</script>
	</head>
	<body>
		<header>
			<h1 style="color: red; margin-top: 1em; float: left;">My Space Empire</h1>
		</header>
		<div style="clear: both;"></div>
		<div style="margin-top: 2em; margin-left: 5em;">
			<h1 style="color: gold; text-align: center;">Create Account</h1>
			<form name="account_data" id="account_data" action="user_home.php" method="post">
				<div style="float: left;">
					<p>
						<img src="images/icons/user_info.png" width="48px" height="48px" />
						<input style="padding: 0.5em;" type="text" size="30" name="alias" id="alias" placeholder="alias" onchange="checkForDuplicates(this);" />
						<span id="alias_msg"></span>
					</p>
					<p>
						<img src="images/icons/mail.png" width="48px" height="48px" />
						<input style="padding: 0.5em;" type="text" size="30" name="email" id="email" placeholder="email" onchange="checkForDuplicates(this);" />
						<span id="email_msg"></span>
					</p>
					<p>
						<img src="images/icons/lock.png" width="48px" height="48px" />
						<input style="padding: 0.5em;" type="password" size="30" name="password" id="password" placeholder="password" onchange="checkPassword(this);" />
						<span id="password_msg"></span>
					</p>
				</div>
				<div style="float: right; margin-right: 5em; margin-bottom: 1em;">
					<p>Color 1<input class="color {pickerClosable:true,adjust:true}" name="color_1" id="color_1" onchange="updateSVG();"></p>
					<p style="margin: 0.5em 0;">Color 2<input class="color {pickerClosable:true,adjust:true}" name="color_2" id="color_2" value="00FF00" onchange="updateSVG();"></p>
					<p>Color 3<input class="color {pickerClosable:true,adjust:true}" name="color_3" id="color_3" onchange="updateSVG();"></p>
				</div>
				<div style="clear: both;"></div>
				<div>
					<div style="margin-right: 1em; width: 15%; height: 15%; float: left;">
						<p id="icon_id" style="text-align: center;">Use Custom SVG</p>
						<div id="player_icon">
							<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100' style='width:100%; height:100%;'>
								<text x="50" y="50" style="text-anchor: middle; dominant-baseline: central; fill:silver;">ICON</text>
								<rect width="100%" height="100%" style="stroke:gold; stroke-width:2; fill:none;" />
							</svg>
						</div>
						<div style="text-align: center;">
							<button type="button" onclick="nextIcon(-1);">&lt;</button>
							<button type="button" onclick="nextIcon(+1);">&gt;</button>
						</div>
					</div>
					<div style="width: 70%; float: left;">
						<p><b>&lt;symbol viewBox=&quot;0 0 100 100&quot;&gt;</b></p>
						<div>
							<textarea name="player_icon_svg" id="player_icon_svg" rows="15" style="margin-left:3em; width: 100%;"></textarea>
						</div>
						<p style="float: left;"><b>&lt;/symbol&gt;</b></p>
						<button type="button" style="margin-right:-3em; float: right;" onclick="updateSVG();"/>Update Icon SVG</button>
					</div>
				</div>
				<div style="clear: both;"></div>
				<button id="create_button" type="button" style="margin: 0.2em;" onclick="createAccount();" disabled>Create Account</button>
			</form>
		</div>
	</body>
</html>