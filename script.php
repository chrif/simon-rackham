<?php

require_once 'MusicPageParser.php';

$parser = new MusicPageParser();

echo $parser->parse('index.html', 'parsed.csv');
//echo $parser->parse('http://www.simonrackhamswork.com/music/', 'parsed.csv');
