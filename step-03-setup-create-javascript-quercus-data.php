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

$filename = "emojis-quercus.js";


// /* #########################################################
// FUNCTIONS
// ######################################################### */


/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Function: tidy_name
Created: 15-FEB-2025
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

function tidy_name($str){
	$str = strtolower($str);
    $str = str_replace(" ", "-", $str);
    $str = str_replace(",", "-", $str);
    $str = str_replace("“", "", $str);
    $str = str_replace("”", "", $str);
    $str = str_replace("!", "", $str);
    $str = str_replace("ç", "c", $str);
    $str = str_replace("Å", "a", $str);
    $str = str_replace("ã", "a", $str);
    $str = str_replace("í", "i", $str);
    $str = str_replace("é", "e", $str);
    $str = str_replace("ü", "u", $str);
    $str = str_replace("&", "-", $str);
    $str = str_replace("*", "asterisk", $str);
    $str = str_replace("#", "number", $str);
    $str = str_replace(";", "-", $str);
    $str = str_replace(".", "", $str);
    $str = str_replace("’", "-", $str);
    $str = str_replace("'", "-", $str);
    $str = str_replace("/", "-", $str);
    $str = str_replace(":", "", $str);
    $str = str_replace("\\", "", $str);
    $str = str_replace(">", "", $str);
    $str = str_replace("<", "", $str);
    $str = str_replace("(", "", $str);
    $str = str_replace(")", "", $str);
	$str = str_replace("--", "-", $str);
	$str = str_replace("--", "-", $str);
	$str = str_replace("--", "-", $str);
	$str = str_replace("--", "-", $str);
	$str = str_replace("--", "-", $str);
	return($str);
}

// /* #########################################################
// CREATE JAVASCRIPT FILE
// ######################################################### */

// @@@@@@@@@@@@@@@@@
// >> CAT SQL
// @@@@@@@@@@@@@@@@@

$sql1 = "

	select c.category cat_name
		 , c.id cat_id
		 , c.emoji
		 , count(*) emoji_count
	  from emojis e
	  join categories c on c.id = e.category_id
	 where e.status = 'fully-qualified'
	   and e.name not like '%skin tone%'
  group by c.category
	  	 , c.id
	  	 , c.emoji;

";

$stmt01 = $pdo->prepare($sql1);
$stmt01->execute();
$count_cat = $stmt01->rowCount();

$output = "const emojiQuercusData = [";

$output .= "\n    {\n";
$output .= "      id: '0',\n";
$output .= "      name: 'All Emojis',\n";
$output .= "      type: 'all',\n";
$output .= "      selected: 'true',\n";
$output .= "      children: [\n";

// loop through category results
// 1111111111111111111111111111111111111111111111111111111111111111111111111

$counter_cat = 1;

foreach($stmt01 as $row001) {

	if($counter_cat < $count_cat) {
		$comma_cat = ",";
	} else {
		$comma_cat = null;
	}

	$cat_name = $row001['cat_name'];
	$cat_name2 = tidy_name($cat_name);
	$cat_id = $row001['cat_id'];
	$emoji = $row001['emoji'];
	$emoji_count = $row001['emoji_count'];
	
	$output .= "\n    {\n";
	$output .= "      id: '$cat_id',\n";
	$output .= "      parent1_id: 0,\n";
	$output .= "      parent1_name: 'All Emojis',\n";
	$output .= "      name: '$cat_name',\n";
	$output .= "      type: 'category',\n";
	$output .= "      children: [\n";

	// @@@@@@@@@@@@@@@@@
	// >> SUBCAT SQL
	// @@@@@@@@@@@@@@@@@

	$sql2 = "
	
	select c.category cat_name
		 , c.id cat_id
		 , e.subgroup
		 , count(*) emoji_count
	  from emojis e
	  join categories c on c.id = e.category_id
	 where e.status = 'fully-qualified'
	   and e.name not like '%skin tone%'
	   and c.id = :cat_id
  group by c.category
		 , e.subgroup
	";

	$stmt02 = $pdo->prepare($sql2);
	$stmt02->bindParam(':cat_id', $cat_id);
	$stmt02->execute();
	$count_sub = $stmt02->rowCount();
	// dump($count_sub);

	// loop through subcategory results
	// 2222222222222222222222222222222222222222222222222222222222222222222222222

	$counter_sub = 1;

	foreach($stmt02 as $row002) {

		if($counter_sub < $count_sub) {
			$comma_sub = ",";
		} else {
			$comma_sub = null;
		}

		// dump($comma_sub);
		
		$cat_name = $row002['cat_name'];
		$cat_name2 = tidy_name($cat_name);
		$cat_id = $row002['cat_id'];
		$new_id_sub = $cat_id . "." . $counter_sub;
		$subcategory = $row002['subgroup'];
		$subcategory2 = tidy_name($subcategory);
		$emoji_count = $row002['emoji_count'];

		$output .= "        {\n";
		$output .= "          id: '$new_id_sub',\n";
		$output .= "          parent1_id: 0,\n";
		$output .= "          parent1_name: 'All Emojis',\n";
		$output .= "          parent2_id: '$cat_id',\n";
		$output .= "          parent2_name: '$cat_name',\n";
		$output .= "          name: '$subcategory',\n";
		$output .= "          type: 'subcategory',\n";
		$output .= "          children: [";

		// @@@@@@@@@@@@@@@@@
		// >> EMOJI SQL
		// @@@@@@@@@@@@@@@@@

		$sql3 = "
		
		select e.emoji
			 , e.name
			 , e.unicode
			 , e.version
			 , e.id
			 , e.subgroup
			 , c.category cat_name
			 , c.id cat_id
		  from emojis e
		  join categories c on c.id = e.category_id
		 where e.status = 'fully-qualified'
		   and e.name not like '%skin tone%'
		   and e.subgroup = :sub_cat
		";

		$stmt03 = $pdo->prepare($sql3);
		$stmt03->bindParam(':sub_cat', $subcategory);
		$stmt03->execute();
		$count_emj = $stmt03->rowCount();

		// loop through emoji results
		// 3333333333333333333333333333333333333333333333333333333333333333333333333

		$counter_emj = 1;

		foreach($stmt03 as $row003) {

			if($counter_emj < $count_emj) {
				$comma_emj = ",";
			} else {
				$comma_emj = "\n";
			}
			
			$emoji = $row003['emoji'];
			$emojiname = $row003['name'];
			$unicode = $row003['unicode'];
			$version = $row003['version'];
			$cat_name = $row003['cat_name'];
			$subcategory = $row003['subgroup'];
			$id = $row003['id'];
			$new_id_emj = $new_id_sub . "." . $counter_emj;
			$emojiname2 = tidy_name($emojiname);

		$output .= "\n            {\n";
		$output .= "              id: '$new_id_emj',\n";
		$output .= "              parent1_id: 0,\n";
		$output .= "              parent1_name: 'All Emojis',\n";
		$output .= "              parent2_id: '$cat_id',\n";
		$output .= "              parent2_name: '$cat_name',\n";
		$output .= "              parent3_id: '$new_id_sub',\n";
		$output .= "              parent3_name: '$subcategory',\n";
		$output .= "              name: '$emoji $emojiname',\n";
		$output .= "              name_only: '$emojiname',\n";
		$output .= "              emoji: '$emoji',\n";
		$output .= "              type: 'emoji'\n";
		$output .= "            }$comma_emj          ";

		$counter_emj++;

		} // end emoji loop

		$output .= "]\n        }$comma_sub\n";

		$counter_sub++;

	} // end subcat loop

	$output .= "      ]\n    }\n$comma_cat";

	$counter_cat++;

} // end cat loop

$output .= "  ]\n}$comma_cat";

$output .= "
];
";

// -----------------------------------WRITE TO FILE----------------------------

$file_contents = $output;

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

<p>Purpose: Create a JavaScript file with the emoji data in a format that can be used by the quercus browser front-end.</p>

<p>Output: file: <code>$filename</code> (size: " . human_filesize(filesize($filename), 2) . " / last modified: " . date ("F d Y H:i:s.", filemtime($filename)) . ")</p>

<p><a href='" . $filename . "'>View the JavaScript file</a></p>

<hr>
";

?>