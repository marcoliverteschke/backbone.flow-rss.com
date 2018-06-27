<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php if(isset($title_prefix)) { echo $title_prefix; } ?>Flow RSS</title>
		<meta name="viewport" content="width=device-width">
		<meta name="description" content="Flow RSS">
		<meta name="author" content="Marc-Oliver Teschke">
		<meta http-equiv="Content-Security-Policy" content="default-src 'self' 'unsafe-eval' 'unsafe-inline'; img-src https://*;">
		<link rel="stylesheet" href="/vendor/components/normalize.css/normalize.css">
		<link rel="stylesheet" href="/css/styles.css">
		<link rel="icon" href="/favicon.png" type="image/png" />
		<link rel="apple-touch-icon" type="image/x-icon" href="/apple-touch-icon.png"/>
		<script src="/vendor/components/jquery/jquery.min.js"></script>
		<script src="/vendor/components/underscore/underscore-min.js"></script>
		<script src="/vendor/components/backbone/backbone-min.js"></script>
		<script src="/vendor/moment/moment/moment.js"></script>
	</head>
	<body>
		<main><?php echo $body_content ?></main>
	</body>
</html>
