<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>My Space Empire - Create Account</title>
		<link rel="stylesheet" href="css/main.css" type="text/css" media="screen" />
		<script src="js/jscolor/jscolor.js"></script>
		<script>
			var ICONS = [{svg : '<text x="50" y="50" style="text-anchor: middle; dominant-baseline: central; fill:silver;">ICON</text><rect width="100%" height="100%" style="stroke:gold; stroke-width:2; fill:none;" />'}, 
						 {svg : '<rect width="100%" height="100%" style="fill:#color1;" /><line x1="25" y1="0" x2="25" y2="100" style="stroke:#color2; stroke-width: 3" /><line x1="0" y1="50" x2="100" y2="50" style="stroke:#color2; stroke-width: 3" /><circle cx="25" cy="50" r="15" style="fill:#color3;" />'}];
			var ICONS_INDEX = 0;

			function nextIcon(direction) 
			{
				ICONS_INDEX = (ICONS_INDEX + direction) % ICONS.length;

				var svg = ICONS[ICONS_INDEX]['svg'];

				if (ICONS_INDEX > 0) 
				{
					svg = svg.replace(/color1/g, document.getElementById("color_1").value);
					svg = svg.replace(/color2/g, document.getElementById("color_2").value);
					svg = svg.replace(/color3/g, document.getElementById("color_3").value);
					
					document.getElementById("icon_id").innerHTML = ICONS_INDEX + " of " + (ICONS.length - 1);
				}
				else
					document.getElementById("icon_id").innerHTML = "Use Custom SVG";

				displayIcon(svg);
			}

			function updateColor() 
			{
				var svg = ICONS[ICONS_INDEX]['svg'];

				if (ICONS_INDEX > 0) 
				{
					svg = svg.replace(/color1/g, document.getElementById("color_1").value);
					svg = svg.replace(/color2/g, document.getElementById("color_2").value);
					svg = svg.replace(/color3/g, document.getElementById("color_3").value);
				}

				displayIcon(svg);
			}

			function displayIcon(svg) 
			{
				var i = document.getElementById('player_icon');

				i.innerHTML = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100' style='width:100%; height:100%;'>" + svg + "</svg>";
			}

			function updateSVG() 
			{
				var s = document.getElementById('player_icon_svg');

				displayIcon(s.value);
			}
		</script>
	</head>
	<body>
		<header>
			<h1 style="color: red; margin-top: 1em; float: left;">My Space Empire</h1>
		</header>
		<div style="clear: both;"></div>
		<div style="margin-top: 2em; margin-left: 5em;">
			<h1 style="color: gold; text-align: center;">Create Account</h1>
			<div style="float: left;">
				<p>
					<img src="images/icons/user_info.png" width="48px" height="48px" />
					<input style="padding: 0.5em;" type="text" size="30" name="alias" id="alias" placeholder="alias" />
					<span id="alias_msg"></span>
				</p>
				<p>
					<img src="images/icons/mail.png" width="48px" height="48px" />
					<input style="padding: 0.5em;" type="text" size="30" name="user_id" id="user_id" placeholder="email" />
					<span id="email_msg"></span>
				</p>
				<p>
					<img src="images/icons/lock.png" width="48px" height="48px" />
					<input style="padding: 0.5em;" type="password" size="30" name="password" id="password" placeholder="password" />
					<span id="password_msg">Must contain at least one letter and one number and be at least 8 characters long.</span>
				</p>
			</div>
			<div style="float: right; margin-right: 5em; margin-bottom: 1em;">
				<p>Color 1<input class="color {pickerClosable:true}" id="color_1" onchange="updateColor();"></p>
				<p style="margin: 0.5em 0;">Color 2<input class="color {pickerClosable:true}" id="color_2" onchange="updateColor();"></p>
				<p>Color 3<input class="color {pickerClosable:true}" id="color_3" onchange="updateColor();"></p>
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
						<button onclick="nextIcon(-1);">&lt;</button>
						<button onclick="nextIcon(+1);">&gt;</button>
					</div>
				</div>
				<div style="width: 70%; float: left;">
					<p><b>&lt;symbol viewBox=&quot;0 0 100 100&quot;&gt;</b></p>
					<div>
						<textarea id="player_icon_svg" rows="15" style="margin-left:3em; width: 100%;"></textarea>
					</div>
					<p style="float: left;"><b>&lt;/symbol&gt;</b></p>
					<button style="margin-right:-3em; float: right;" onclick="updateSVG();"/>Update Icon SVG</button>
				</div>
			</div>
			<div style="clear: both;"></div>
			<button style="margin: 0.2em;" onclick="alert('Attempt to create account.')">Create Account</button>
		</div>
	</body>
</html>