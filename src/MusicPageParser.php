<?php

declare(strict_types=1);

namespace Chrif\SimonRackham;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNameList;
use DOMXPath;
use Exception;
use Symfony\Component\Console\Helper\ProgressBar;

class MusicPageParser {

	public function parse(string $source, string $outputTo, ProgressBar $progressBar) {
		$html = $this->getHtml($source);

		$document = $this->getDocument($html);

		$xPath = new DOMXPath($document);

		$albumNodes = $this->getAlbumNodes($xPath);
		$progressBar->start(count($albumNodes));

		$albumsByYear = $this->parseAlbumsByYear($albumNodes, $xPath, $progressBar);

		$this->writeCsv($albumsByYear, $outputTo);
	}

	/**
	 * @param $filenameOrUrl
	 * @return bool|string
	 */
	private function getHtml($filenameOrUrl) {
		return file_get_contents($filenameOrUrl);
	}

	/**
	 * @param $html
	 * @return DOMDocument
	 */
	private function getDocument($html) {
		$document = new DOMDocument();

		error_reporting(E_ERROR | E_PARSE);
		$document->loadHTML($html);
		error_reporting(E_ALL);

		return $document;
	}

	/**
	 * @param DOMXPath $xPath
	 * @return DOMElement[]|DOMNameList
	 */
	private function getAlbumNodes(DOMXPath $xPath) {
		/** @var DOMNameList|DOMElement[] $albumNodes */
		$albumNodes = $xPath->query("//div[contains(@class, 'album-meta-text')]");

		return $albumNodes;
	}

	/**
	 * @param $albumNodes
	 * @param $xPath
	 * @param ProgressBar $progressBar
	 * @return array
	 */
	private function parseAlbumsByYear($albumNodes, $xPath, ProgressBar $progressBar) {
		$albumsByYear = [];

		foreach ($albumNodes as $i => $albumNode) {
			$title = $this->extractInfo($xPath, "h2[contains(@class, 'album-title')]", $albumNode, "title", $i);

			$href = $this->extractInfo($xPath, "h2[contains(@class, 'album-title')]/a/@href", $albumNode, "href", $i);
			$href = 'http://www.simonrackhamswork.com' . $href;

			$year = $this->extractInfo($xPath, "div[contains(@class, 'album-release-date responsive_show')]", $albumNode, "year", $i);
			$year = preg_replace("#\D+#", "", $year);

			$description = $this->extractInfo($xPath, "div[contains(@class, 'album-description')]", $albumNode, "description", $i);

			if (!isset($albumsByYear[$year])) {
				$albumsByYear[$year] = [];
			}

			$albumsByYear[$year][] = [
				"title" => $title,
				'year' => $year,
				'index' => str_pad((string)(count($albumsByYear[$year]) + 1), 2, '0', STR_PAD_LEFT),
				'description' => $description,
				'href' => $href,
			];

			$progressBar->advance();
		}

		return $albumsByYear;
	}

	/**
	 * @param $albumsByYear
	 * @param $filename
	 * @throws Exception
	 */
	private function writeCsv($albumsByYear, $filename) {
		$file = fopen($filename, 'w');
		foreach ($albumsByYear as $year => $albumsForYear) {
			foreach ($albumsForYear as $album) {
				if (false === fputcsv($file, $album)) {
					throw new Exception('failure to write to csv file');
				}
			}
		}
		fclose($file);
	}

	private function extractInfo(DOMXPath $xmlPath, $expression, $album, $column, $i) {
		/** @var DOMNameList|DOMElement[]|DOMAttr[] $info */
		$info = $xmlPath->query($expression, $album);
		if (!$info) {
			throw new Exception("No {$column} $i");
		}
		if (count($info) > 1) {
			throw new Exception("Too many {$column}s $i");
		}
		if (count($info) === 0) {
			throw new Exception("No {$column} $i");
		}

		$out = $info[0]->textContent;

		if (empty($out)) {
			throw new Exception("There is no {$column} $i");
		}

		$out = trim($out);

		// pattern to match all whitespaces
		// @see https://gist.github.com/chrif/8dffdb1dab7cba848c3f3fb62a17485e
		$out = preg_replace("#[\pZ\pC]+#u", " ", $out);

		return $out;
	}

}