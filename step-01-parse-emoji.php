<?php

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// DATABASE
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

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

$file_name = 'unicode-latest.txt';
$text_file_url = 'https://www.unicode.org/Public/emoji/latest/emoji-test.txt';

if (!file_exists($file_name)) {
	file_put_contents($file_name, file_get_contents($text_file_url));
}
$blocks = explode("\n"."\n", file_get_contents($file_name));

unset($blocks[0]);

$emoji = [];

print "<h1>Parsing Emoji Data</h1>";

foreach ($blocks as $chunk) {

	$top = explode("\n", trim($chunk))[0];

	if (substr($top, 0, strlen('# group:')) == '# group:') {
		$group = trim(str_replace('# group:', '', $top));
	} elseif (substr($top, 0, strlen('# subgroup:')) == '# subgroup:') {

		$lines = explode("\n", $chunk);
		unset($lines[0]);
		
		foreach ($lines as $line) {
			
			$subgroup = trim(str_replace('# subgroup:', '', $top));
			$linegroup = explode(';', $line);
			$parts = explode('#', $linegroup[1]);
			// dump($parts);
			
			/*
			things get messy for these lines:
			
			# subgroup: keycap
			0023 FE0F 20E3                             ; fully-qualified     # #️⃣ E0.6 keycap: #
			0023 20E3                                  ; unqualified         # #⃣ E0.6 keycap: #
			
			because they end with a hash symbol
			and the part array looks like this:
			
			array(
				0=> unqualified
				1=> 
				2=> >⃣ E0.6 keycap:
				3=>
			)
			
			therefore count array elements
			if there are four, get emojiname from 3rd element (compared to 2nd otherwise)
			and append a hash character at the end of the emojiname
			*/

			if(count($parts) == 4) {
				
				$icon = explode(' ', trim($parts[2]), 2);
				$var_emojiname = 	trim($icon[1]);
				$var_emojiname .= ' #';
				
			} else {
				
				$icon = explode(' ', trim($parts[1]), 2);
				$var_emojiname = 	trim($icon[1]);
				
			}
	
			$var_codepoint = 	trim($linegroup[0]);
			$var_status = 		trim($parts[0]);
			$var_group = 		trim($group);
			$var_sub_group = 	trim($subgroup);
			$var_emoji = 		trim($icon[0]);

			/*
			strip out the description of the emoji
			which appears as e.g. "E0.6 eight-spoked asterisk"
			splits out "0.6" as version, and "eight-spoked asterisk" as emoji name
			*/
			
			$var_space = strpos($var_emojiname, " ");
			$c1 = substr($var_emojiname, 0, $var_space);
			$var_version = str_replace("E", "", $c1);
			$var_emojiname_new = trim(substr($var_emojiname, $var_space, 500));

			if (isset($var_codepoint)) {
				$sql2 = "INSERT INTO emojis (`unicode`, `group`, `subgroup`, `name`, `status`, `emoji`, `version`) VALUES (:unicode, :group, :subgroup, :name, :status, :emoji, :version)";
				$stmt2 = $pdo->prepare($sql2);
				$stmt2->bindParam(':unicode', $var_codepoint);
				$stmt2->bindParam(':group', $var_group);
				$stmt2->bindParam(':subgroup', $var_sub_group);
				$stmt2->bindParam(':name', $var_emojiname_new);
				$stmt2->bindParam(':status', $var_status);
				$stmt2->bindParam(':emoji', $var_emoji);
				$stmt2->bindParam(':version', $var_version);
				$stmt2->execute();
				$id = $pdo->lastInsertId();	
				print "<code>db_id: " . $id . " / parts: " . count($parts) . " / " . $var_emojiname . " / " . $var_emoji . "  / " . $var_codepoint . " / " . $var_status . "<br></code>";
			}
		}

	} // subgroup

} // foreach

print "<h1>Deletions</h1>";

print "<h2>Delete Non Fully-Qualified Emojis</h2>";
print "<pre style='color:blue'>delete from emojis where status <> 'fully-qualified'; -- delete non fully-qualified versions</pre>";

$del = $pdo->prepare('delete from emojis where status <> "fully-qualified"');
$del->execute();
$count = $del->rowCount();
print "Deleted $count rows.\n";

print "<h2>Delete Skin Tone Variants</h2>";
print "<pre style='color:blue'>delete from emojis where name like '% 1F3F%'; -- delete skin tone variants</pre>";

$del = $pdo->prepare("delete from emojis where unicode like '% 1F3F%'");
$del->execute();
$count = $del->rowCount();
print "Deleted $count rows.\n";

print "<h1>Updating Emoji Categories</h1>";

print "<pre style='color:blue'>update emojis set category_id = 1 where `group` = 'Smileys & Emotion';</pre>";
$update = $pdo->prepare("update emojis set category_id = 1 where `group` = 'Smileys & Emotion'");
$update->execute();
$count = $update->rowCount();
print "Updated $count rows.\n";

print "<pre style='color:blue'>update emojis set category_id = 2 where `group` = 'People & Body';</pre>";
$update = $pdo->prepare("update emojis set category_id = 2 where `group` = 'People & Body'");
$update->execute();
$count = $update->rowCount();
print "Updated $count rows.\n";

print "<pre style='color:blue'>update emojis set category_id = 3 where `group` = 'Animals & Nature';</pre>";
$update = $pdo->prepare("update emojis set category_id = 3 where `group` = 'Animals & Nature'");
$update->execute();
$count = $update->rowCount();
print "Updated $count rows.\n";

print "<pre style='color:blue'>update emojis set category_id = 4 where `group` = 'Food & Drink';</pre>";
$update = $pdo->prepare("update emojis set category_id = 4 where `group` = 'Food & Drink'");
$update->execute();
$count = $update->rowCount();
print "Updated $count rows.\n";

print "<pre style='color:blue'>update emojis set category_id = 5 where `group` = 'Travel & Places';</pre>";
$update = $pdo->prepare("update emojis set category_id = 5 where `group` = 'Travel & Places'");
$update->execute();
$count = $update->rowCount();
print "Updated $count rows.\n";

print "<pre style='color:blue'>update emojis set category_id = 6 where `group` = 'Activities';</pre>";
$update = $pdo->prepare("update emojis set category_id = 6 where `group` = 'Activities'");
$update->execute();
$count = $update->rowCount();
print "Updated $count rows.\n";

print "<pre style='color:blue'>update emojis set category_id = 7 where `group` = 'Objects';</pre>";
$update = $pdo->prepare("update emojis set category_id = 7 where `group` = 'Objects'");
$update->execute();
$count = $update->rowCount();
print "Updated $count rows.\n";

print "<pre style='color:blue'>update emojis set category_id = 8 where `group` = 'Symbols';</pre>";
$update = $pdo->prepare("update emojis set category_id = 8 where `group` = 'Symbols'");
$update->execute();
$count = $update->rowCount();
print "Updated $count rows.\n";

print "<pre style='color:blue'>update emojis set category_id = 9 where `group` = 'Flags';</pre>";
$update = $pdo->prepare("update emojis set category_id = 9 where `group` = 'Flags'");
$update->execute();
$count = $update->rowCount();
print "Updated $count rows.\n";

print "<h1>Updating Emoji Tags</h1>";

/*
Update Emojis
*/

$sql = "select id, emoji, name from emojis";
$arr_emj = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$arr_emj_count = count($arr_emj);

function setEmojiTag($str){
	// $str = preg_replace("/[^A-Za-z0-9 ]/", '', $str);
    $str = strtolower($str);
	$str = str_replace(" ", "-", $str);
	$str = str_replace(".", "", $str);
	$str = str_replace("&", "-", $str);
    $str = str_replace(",", "-", $str);
    $str = str_replace("'", "-", $str);
    $str = str_replace("’", "", $str);
    $str = str_replace("“", "", $str);
    $str = str_replace("”", "", $str);
	$str = str_replace("/", "-", $str);
    $str = str_replace("\\", "-", $str);
    $str = str_replace("(", "", $str);
    $str = str_replace(")", "", $str);
    $str = str_replace("!", "", $str);
    $str = str_replace(">", "-", $str);
    $str = str_replace("<", "-", $str);
    $str = str_replace("é", "e", $str);
    $str = str_replace("ü", "u", $str);
    $str = str_replace("ã", "a", $str);
    $str = str_replace("ü", "u", $str);
    $str = str_replace(":", "-", $str);
    $str = str_replace("--", "-", $str);
    $str = str_replace("--", "-", $str);
    $str = str_replace("--", "-", $str);
    $str = str_replace("--", "-", $str);
    $str = str_replace("--", "-", $str);
	return $str;
}

$i = 1;

// loop through results
foreach($arr_emj as $emj) {

	$id = $emj['id'];
	$name = $emj['name'];
	$tag = setEmojiTag($name);
	print "<code>db_id: " . $id . " / name: " . $name . " / tag: " . $tag . "<br></code>";

	$sql = "update emojis set tag = :tag where id = :id";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':tag', $tag);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$i++;
		
} // for-each-emj-end

print "<pre style='color:blue'>Updated $i Emoji Records</pre>";
print "<h1>Done 😀</h1>";

?>