<html>
	<head>
		<title>Social Study {{ $mail_title ?? '' }}</title>
	</head>
	<body>
		<p>Hi {{ $name }},</p>
		<p>{{ $mail_body_text }}</p>
		<table>
			<tr><th>{{ $otp_token }}</th></tr>
		</table>
	</body>
</html>