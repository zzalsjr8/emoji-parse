<?php

// /* #########################################################
// DATABASE CONFIGURATION
// ######################################################### */

$config['db'] = array(
    'host'      => 'localhost',
    'username'  => 'root',
    'password'  => 'yourpasswordhere',
    'dbname'    => 'emojis'
);

$pdo = new PDO('mysql:host=' .$config['db']['host']. ';dbname=' .$config['db']['dbname'], $config['db']['username'], $config['db']['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$pdo->setAttribute(PDO::NULL_EMPTY_STRING, true);
$pdo->exec("SET CHARACTER SET utf8mb4");
$pdo->exec("set names utf8mb4");

// /* #########################################################
// VARIABLES
// ######################################################### */

$filename = "emojis.js";

// /* #########################################################
// 01 --- CATEGORY
// ######################################################### */

$sql = "

SELECT
    c.category,
    c.tag,
    c.`sequence` seq,
    c.emoji,
    c.id,
    COUNT(*) ct
FROM
    emojis e
JOIN categories c ON
    e.category_id = c.id
GROUP BY
    c.category,
    c.tag,
    c.`sequence`,
    c.emoji,
    c.id
ORDER BY
    c.`sequence`;


";
$i_cat = 0;
$arr_cat = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$arr_cat_count = count($arr_cat);

$js_data_emojis = "const emojiData = [";

// 01 loop through category results
foreach($arr_cat as $cat) {

	if($i_cat < ($arr_cat_count-1)) {
		$comma_cat = ",";
	} else {
		$comma_cat = "";
	}

	$category = $cat['category'];
	$tag = $cat['tag'];
	$seq = $cat['seq'];
	$emoji = $cat['emoji'];
	$id = $cat['id'];
	$ct = $cat['ct'];

	//  01 START CATEGORY 

	$js_data_emojis = $js_data_emojis . "{ 
	type: 'category',
	name: '$category',
	tag: '$tag',
	emoji: '$emoji',
	children: ["; //  02 SUBCAT CHILDREN 

	/* #########################################################
	02 --- SUBCATEGORY
	######################################################### */

	$sql_sub = "select e.subgroup, count(*) ct from emojis e join categories c on e.category_id = c.id where c.id = :id group by e.subgroup";
	$i_sub = 0;

	// Prepare the SQL query with a named placeholder
	$stmt = $pdo->prepare($sql_sub);

	// Bind the parameter to the named placeholder
	$stmt->bindParam(":id", $id);

	// Execute the query
	$stmt->execute();

	// Fetch results
	$arr_sub = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$arr_sub_count = count($arr_sub);

	// 02 loop through subcategory results
	foreach($arr_sub as $sub) {

		if($i_sub < ($arr_sub_count-1)) {
			$comma_sub = ",";
		} else {
			$comma_sub = "";
		}

		$subgroup = $sub['subgroup'];
		$ct = $sub['ct'];

		$js_data_emojis = $js_data_emojis . "{ 
		type: 'subcategory',
		name: '$subgroup',
		children: ["; //  03 EMOJI CHILDREN

		/* #########################################################
		03 --- EMOJIS
		######################################################### */

		$sql_emj = "select unicode, name, emoji, tag, version from emojis where subgroup = :subgroup";
		$i_emj = 0;

		// Prepare the SQL query with a named placeholder
		$stmt = $pdo->prepare($sql_emj);

		// Bind the parameter to the named placeholder
		$stmt->bindParam(":subgroup", $subgroup);

		// Execute the query
		$stmt->execute();

		// Fetch results
		$arr_emj = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$arr_emj_count = count($arr_emj);

		// loop through results
		foreach($arr_emj as $emj) {

			if($i_emj < ($arr_emj_count-1)) {
				$comma_emj = ",";
			} else {
				$comma_emj = "";
			}

			$name = $emj['name'];
			$unicode = $emj['unicode'];
			$emoji = $emj['emoji'];
			$tag = $emj['tag'];
			$version = $emj['version'];

			$js_data_emojis = $js_data_emojis . "{ 
			type: 'emoji',
			name: '$unicode',
			emoji: '$emoji',
			tag: '$tag',
			description: '$name',
			version: 'E$version'";

			$js_data_emojis = $js_data_emojis . "\n\t\t\t}". $comma_emj; //  03 END EMOJI

		} // for-each-emoji-end

		$js_data_emojis = $js_data_emojis . "]"; //  03 END EMOJI CHILDREN
		$js_data_emojis = $js_data_emojis . "}". $comma_sub; //  02 END SUBCATEGORY
		$i_sub++;  

	} // for-each-subcat-end

	$js_data_emojis = $js_data_emojis . "]"; //  02 END SUBCAT CHILDREN
	$js_data_emojis = $js_data_emojis . "}". $comma_cat; //  01 END CATEGORY 
	$i_cat++;

} // for-each-cat-end

$js_data_emojis = $js_data_emojis . "];\n\n\n";

// echo $js_data_emojis;

// -----------------------------------VERSIONS----------------------------
// SQL order string as number
// https://stackoverflow.com/a/11808592

$sql = "
select e.version
, count(*) ct
from emojis e 
join categories c on e.category_id = c.id 
group by e.version
order by 0 + e.version;
";

$arr_versions = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$arr_versions_count = count($arr_versions);
$i_ver = 0;
$js_data_versions = "const emojiVersions = [";

foreach($arr_versions as $version) {

	if($i_ver < ($arr_versions_count-1)) {
		$comma_ver = ",";
	} else {
		$comma_ver = "";
	}

	$js_data_versions = $js_data_versions . "'" . 'E' . $version['version'] . "|" . $version['ct'] . "'" . $comma_ver;
	$i_ver++;

}

$js_data_versions = $js_data_versions . "];\n\n\n";

// print $js_data_versions;

// -----------------------------------IMAGES----------------------------

$sql = "select emoji from emojis";

$arr_emoji_images = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$arr_emoji_images_count = count($arr_emoji_images);
$i_emoji = 0;
$js_data_emoji_images = "const emojiImages = [";

foreach($arr_emoji_images as $emoji) {

	if($i_emoji < ($arr_emoji_images_count-1)) {
		$comma_emoji = ",";
	} else {
		$comma_emoji = "";
	}

	$js_data_emoji_images = $js_data_emoji_images . "'" . $emoji['emoji'] . "'" . $comma_emoji;
	$i_emoji++;

}

$js_data_emoji_images = $js_data_emoji_images . "];\n\n\n";

// -----------------------------------CDN DATA----------------------------

$js_data_cdn_versions = "const emojiDesigns = { designs: [ {design:\"native-font\", name:\"Native Emoji Font\", url:\"https://emojipedia.org/animated-noto-color-emoji\"}, {design:\"animated-noto-color-emoji\", name:\"Animated Noto Color Emoji\", url:\"https://emojipedia.org/animated-noto-color-emoji\"}, {design:\"apple\", name:\"Apple\", url:\"https://emojipedia.org/apple\"}, {design:\"au-kddi\", name:\"Au Kddi\", url:\"https://emojipedia.org/au-kddi\"}, {design:\"docomo\", name:\"Docomo\", url:\"https://emojipedia.org/docomo\"}, {design:\"emojidex\", name:\"Emojidex\", url:\"https://emojipedia.org/emojidex\"}, {design:\"facebook\", name:\"Facebook\", url:\"https://emojipedia.org/facebook\"}, {design:\"google\", name:\"Google\", url:\"https://emojipedia.org/google\"}, {design:\"htc\", name:\"Htc\", url:\"https://emojipedia.org/htc\"}, {design:\"huawei\", name:\"Huawei\", url:\"https://emojipedia.org/huawei\"}, {design:\"icons8\", name:\"Icons8\", url:\"https://emojipedia.org/icons8\"}, {design:\"joypixels\", name:\"Joypixels\", url:\"https://emojipedia.org/joypixels\"}, {design:\"joypixels-animations\", name:\"Joypixels Animations\", url:\"https://emojipedia.org/joypixels-animations\"}, {design:\"lg\", name:\"Lg\", url:\"https://emojipedia.org/lg\"}, {design:\"messenger\", name:\"Messenger\", url:\"https://emojipedia.org/messenger\"}, {design:\"microsoft\", name:\"Microsoft\", url:\"https://emojipedia.org/microsoft\"}, {design:\"microsoft-3D-fluent\", name:\"Microsoft 3d Fluent\", url:\"https://emojipedia.org/microsoft-3D-fluent\"}, {design:\"microsoft-teams\", name:\"Microsoft Teams\", url:\"https://emojipedia.org/microsoft-teams\"}, {design:\"mozilla\", name:\"Mozilla\", url:\"https://emojipedia.org/mozilla\"}, {design:\"noto-emoji\", name:\"Noto Emoji\", url:\"https://emojipedia.org/noto-emoji\"}, {design:\"openmoji\", name:\"Openmoji\", url:\"https://emojipedia.org/openmoji\"}, {design:\"samsung\", name:\"Samsung\", url:\"https://emojipedia.org/samsung\"}, {design:\"serenityos\", name:\"Serenityos\", url:\"https://emojipedia.org/serenityos\"}, {design:\"skype\", name:\"Skype\", url:\"https://emojipedia.org/skype\"}, {design:\"softbank\", name:\"Softbank\", url:\"https://emojipedia.org/softbank\"}, {design:\"sony\", name:\"Sony\", url:\"https://emojipedia.org/sony\"}, {design:\"telegram\", name:\"Telegram\", url:\"https://emojipedia.org/telegram\"}, {design:\"toss-face\", name:\"Toss Face\", url:\"https://emojipedia.org/toss-face\"}, {design:\"twitter\", name:\"Twitter\", url:\"https://emojipedia.org/twitter\"}, {design:\"twitter-emoji-stickers\", name:\"Twitter Emoji Stickers\", url:\"https://emojipedia.org/twitter-emoji-stickers\"}, {design:\"whatsapp\", name:\"Whatsapp\", url:\"https://emojipedia.org/whatsapp\"} ] };";

// -----------------------------------WRITE TO FILE----------------------------

$file_contents = $js_data_emojis . $js_data_versions . $js_data_emoji_images . $js_data_cdn_versions;

// $file_contents = preg_replace("/\s+/", " ", $file_contents);

$handle = file_put_contents($filename, $file_contents);

// -----------------------------------MAKE FILE WRITEABLE----------------------------

chmod($filename, 0664);

// -----------------------------------OUTPUT----------------------------

function human_filesize($bytes, $decimals = 2) {
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor > 0) $sz = 'KMGT';
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . " " . @$sz[$factor - 1] . 'B';
}

print "<h1>Create JavaScript Data</h1>

<hr>

<p>Purpose: Create a JavaScript file with the emoji data in a format that can be used by the front-end.</p>

<p>Output: file: <code>$filename</code> (size: " . human_filesize(filesize($filename), 2) . " / last modified: " . date ("F d Y H:i:s.", filemtime($filename)) . ")</p>

<p>Contents:</p>

<ol>
	<li><code>emojiData</code> - All emojis organized by category and subcategory</li>
	<li><code>emojiVersions</code> - List of Unicode versions with the number of emojis in each version</li>
	<li><code>emojiImages</code> - All emoji images</li>
	<li><code>emojiDesigns</code> - Designs supported by https://github.com/oddmario/emoji-cdn
</ol>

<p><a href='" . $filename . "'>View the JavaScript file</a></p>

<hr>
";

?>