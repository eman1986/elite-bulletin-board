<!DOCTYPE html>
<html lang="en">
<head>
	<title>Database Error</title>
	<style type="text/css">
		body {
			background-color:	#fff;
			margin:				40px;
			font-family:		Lucida Grande, Verdana, Sans-serif;
			font-size:			12px;
			color:				#000;
		}

		#content  {
			border:				#999 1px solid;
			background-color:	#fff;
			padding:			20px 20px 12px 20px;
		}

		h1 {
			font-weight:		normal;
			font-size:			14px;
			color:				#990000;
			margin: 			0 0 4px 0;
		}

		code {
			font-family: Consolas, Monaco, Courier New, Courier, monospace;
			font-size: 12px;
			background-color: #f9f9f9;
			border: 1px solid #D0D0D0;
			color: #002166;
			display: block;
			margin: 14px 0 14px 0;
			padding: 12px 10px 12px 10px;
		}
	</style>
</head>
<body>
	<div id="content">
		<h1><?php echo $heading; ?></h1>
		<p><?php echo $message; ?></p>
	</div>
</body>
</html>
