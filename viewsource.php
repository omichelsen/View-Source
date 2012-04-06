<?php 
include_once('../includes/geshi.php');

function markupLinks($html,$uri)
{
	preg_match('/https?:\/\/\S+?(?=\/)/i', $uri, $matches);
	$baseUri = $matches[0];

	$regex = array('/(?<=&quot;)(https?:\/\/\S+)(?=&quot;)/i',	// Absolute paths: &quot;http://asdf.com/asdf.php?id=x&ad=y&quot;
				   '/(?<=&quot;)(\/\S+)(?=&quot;)/i');			// Relative paths: &quot;/scripts/regex-1.1.min.js&quot;
	
	$replc = array('<a href="$1">$1</a>',
				   "<a href=\"$baseUri$1\">$1</a>");

	return preg_replace($regex, $replc, $html);
}

$uri = $_GET['uri'];

if ($_POST)
{
	$data = $_POST['DOM'];
	$htmlenc = urldecode($data);
	
	$geshi = new GeSHi($htmlenc, 'html5');
	$geshi->enable_keyword_links(false);
	$geshi->enable_classes();
	
	$htmlenc = $geshi->parse_code();
	
	// Substitute tabs for 4 spaces
	$htmlenc = str_replace("\t", '    ', $htmlenc);
	
	// Trim trailing spaces
	$htmlenc = preg_replace("/[ \t]+$/", '', $htmlenc);
	
	// Markup URIs and paths as links
	$htmlenc = markupLinks($htmlenc, $uri);	
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width" />
	<title>Source of <?php echo htmlspecialchars($uri); ?></title>
	<style>
		pre {
			overflow: auto;
			white-space: pre-wrap;
			word-wrap: break-word;
		}
		<?php echo $geshi->get_stylesheet(); ?>
	</style>
</head>
<body>
<?php echo $htmlenc; ?>
</body>
</html>