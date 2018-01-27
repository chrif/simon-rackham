<?php

declare(strict_types=1);

namespace Chrif\SimonRackham;

use DOMDocument;
use DOMElement;
use DOMNameList;
use DOMXPath;
use Exception;

class MusicPageParser {

	public function parse(string $source, string $outputTo) {
		$html = $this->getHtml($source);

		$document = $this->getDocument($html);

		$xPath = new DOMXPath($document);

		$albumNodes = $this->getAlbumNodes($xPath);

		$albumsByYear = $this->parseAlbumsByYear($albumNodes, $xPath);

		$this->writeCsv($albumsByYear, $outputTo);

		return $albumNodes->length  . " albums OK";
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
		$document->preserveWhiteSpace = false;

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
		$albumNodes = $xPath->query("//div[@class='album-meta-text']");

		return $albumNodes;
	}

	/**
	 * @param $albumNodes
	 * @param $xPath
	 * @return array
	 */
	private function parseAlbumsByYear($albumNodes, $xPath) {
		$albumsByYear = [];

		foreach ($albumNodes as $i => $albumNode) {
			$title = $this->extractInfo($xPath, "h2[@class='album-title']", $albumNode, "title", $i);

			$year = $this->extractInfo($xPath, "div[@class='album-release-date responsive_show']", $albumNode, "title", $i);
			$year = preg_replace("#\D+#", "", $year);

			$description = $this->extractInfo($xPath, "div[@class='album-description']", $albumNode, "description", $i);

			if (!isset($albumsByYear[$year])) {
				$albumsByYear[$year] = [];
			}

			$albumsByYear[$year][] = [
				"title" => $title,
				'year' => $year,
				'index' => str_pad((string)(count($albumsByYear[$year]) + 1), 2, '0', STR_PAD_LEFT),
				'description' => $description,
			];
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
		/** @var DOMNameList|DOMElement[] $info */
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
		$info = $info[0]->textContent;
		if (empty($info)) {
			throw new Exception("There is no {$column} $i");
		}
		$info = trim($info);
		$info = preg_replace("#\s{2,}#", "", $info);

		return $info;
	}

}