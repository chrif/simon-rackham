<?php

$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$html = file_get_contents('http://www.simonrackhamswork.com/music/');

error_reporting(E_ERROR | E_PARSE);
$dom->loadHTML($html);
error_reporting(E_ALL);

$xmlPath = new DOMXPath($dom);
/** @var DOMNameList|DOMElement[] $albums */
$albums = $xmlPath->query("//div[@class='album-meta-text']");

function extractInfo(DOMXPath $xmlPath, $expression, $album, $column, $i) {
	/** @var DOMNameList|DOMElement[] $info */
	$info = $xmlPath->query($expression, $album);
	if (!$info) {
		throw new Error("No {$column} $i");
	}
	if (count($info) > 1) {
		throw new Error("Too many {$column}s $i");
	}
	if (count($info) === 0) {
		throw new Error("No {$column} $i");
	}
	$info = $info[0]->textContent;
	if (empty($info)) {
		throw new Error("There is no {$column} $i");
	}
	$info = trim($info);
	$info = preg_replace("#\s{2,}#", "", $info);

	return $info;
}

$out = [];
foreach ($albums as $i => $album) {
	$title = extractInfo($xmlPath, "h2[@class='album-title']", $album, "title", $i);

	$year = extractInfo($xmlPath, "div[@class='album-release-date responsive_show']", $album, "title", $i);
	$year = preg_replace("#\D+#", "", $year);

	$description = extractInfo($xmlPath, "div[@class='album-description']", $album, "description", $i);

	if (!isset($out[$year])) {
		$out[$year] = [];
	}

	$out[$year][] = [
		"title" => $title,
		'year' => $year,
		'index' => str_pad(count($out[$year]) + 1, 2, 0, STR_PAD_LEFT),
		'description' => $description,
	];
}

$file = fopen('parsed.csv', 'w');
foreach ($out as $year => $all) {
	foreach ($all as $album) {
		if (false === fputcsv($file, $album)) {
			throw new Exception('failure to write to csv file');
		}
	}
}
fclose($file);

var_export($out);
