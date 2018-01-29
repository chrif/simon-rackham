<?php

declare(strict_types=1);

namespace Chrif\SimonRackham;

use PHPUnit\Framework\TestCase;

class WhitespaceMatchTest extends TestCase {

	const SLASH_S = "#\s#";
	const SLASH_S_U = "#\s#u";
	const P_Z_U = "#\pZ#u";
	const P_C_U = "#\pC#u";
	const PZ_PC_U = "#[\pZ\pC]#u";

	/**
	 * https://en.wikipedia.org/wiki/Whitespace_character
	 *
	 * @var array
	 */
	public static $whitespaces = array(
		"character tabulation" => "\u{0009}",
		"line feed" => "\u{000A}",
		"line tabulation" => "\u{000B}",
		"form feed" => "\u{000C}",
		"carriage return" => "\u{000D}",
		"space" => "\u{0020}",
		"next line" => "\u{0085}",
		"no-break space" => "\u{00A0}",
		"ogham space mark" => "\u{1680}",
		"en quad" => "\u{2000}",
		"em quad" => "\u{2001}",
		"en space" => "\u{2002}",
		"em space" => "\u{2003}",
		"three-per-em space" => "\u{2004}",
		"four-per-em space" => "\u{2005}",
		"six-per-em space" => "\u{2006}",
		"figure space" => "\u{2007}",
		"punctuation space" => "\u{2008}",
		"thin space" => "\u{2009}",
		"hair space" => "\u{200A}",
		"line separator" => "\u{2028}",
		"paragraph separator" => "\u{2029}",
		"narrow no-break space" => "\u{202F}",
		"medium mathematical space" => "\u{205F}",
		"ideographic space" => "\u{3000}",
		"mongolian vowel separator" => "\u{180E}",
		"zero width space" => "\u{200B}",
		"zero width non-joiner" => "\u{200C}",
		"zero width joiner" => "\u{200D}",
		"word joiner" => "\u{2060}",
		"zero width non-breaking space" => "\u{FEFF}",
	);

	public function test_slash_S() {
		self::assertSame(array(
			'character tabulation' => true,
			'line feed' => true,
			'line tabulation' => true,
			'form feed' => true,
			'carriage return' => true,
			'space' => true,
		), $this->matchPattern(self::SLASH_S));
	}

	public function test_slash_S_with_U_flag() {
		self::assertSame(array(
			'character tabulation' => true,
			'line feed' => true,
			'line tabulation' => true,
			'form feed' => true,
			'carriage return' => true,
			'space' => true,
			'next line' => true,
			'no-break space' => true,
			'ogham space mark' => true,
			'en quad' => true,
			'em quad' => true,
			'en space' => true,
			'em space' => true,
			'three-per-em space' => true,
			'four-per-em space' => true,
			'six-per-em space' => true,
			'figure space' => true,
			'punctuation space' => true,
			'thin space' => true,
			'hair space' => true,
			'line separator' => true,
			'paragraph separator' => true,
			'narrow no-break space' => true,
			'medium mathematical space' => true,
			'ideographic space' => true,
			'mongolian vowel separator' => true,
		), $this->matchPattern(self::SLASH_S_U));
	}

	public function test_Z_class() {
		self::assertSame(array(
			'space' => true,
			'no-break space' => true,
			'ogham space mark' => true,
			'en quad' => true,
			'em quad' => true,
			'en space' => true,
			'em space' => true,
			'three-per-em space' => true,
			'four-per-em space' => true,
			'six-per-em space' => true,
			'figure space' => true,
			'punctuation space' => true,
			'thin space' => true,
			'hair space' => true,
			'line separator' => true,
			'paragraph separator' => true,
			'narrow no-break space' => true,
			'medium mathematical space' => true,
			'ideographic space' => true,
		), $this->matchPattern(self::P_Z_U));
	}

	public function test_C_class() {
		self::assertSame(array(
			'character tabulation' => true,
			'line feed' => true,
			'line tabulation' => true,
			'form feed' => true,
			'carriage return' => true,
			'next line' => true,
			'mongolian vowel separator' => true,
			'zero width space' => true,
			'zero width non-joiner' => true,
			'zero width joiner' => true,
			'word joiner' => true,
			'zero width non-breaking space' => true,
		), $this->matchPattern(self::P_C_U));
	}

	public function test_Z_and_C_class() {
		self::assertSame(array(
			'character tabulation' => true,
			'line feed' => true,
			'line tabulation' => true,
			'form feed' => true,
			'carriage return' => true,
			'space' => true,
			'next line' => true,
			'no-break space' => true,
			'ogham space mark' => true,
			'en quad' => true,
			'em quad' => true,
			'en space' => true,
			'em space' => true,
			'three-per-em space' => true,
			'four-per-em space' => true,
			'six-per-em space' => true,
			'figure space' => true,
			'punctuation space' => true,
			'thin space' => true,
			'hair space' => true,
			'line separator' => true,
			'paragraph separator' => true,
			'narrow no-break space' => true,
			'medium mathematical space' => true,
			'ideographic space' => true,
			'mongolian vowel separator' => true,
			'zero width space' => true,
			'zero width non-joiner' => true,
			'zero width joiner' => true,
			'word joiner' => true,
			'zero width non-breaking space' => true,
		), $this->matchPattern(self::PZ_PC_U));
	}

	public function test_Z_and_C_match_all() {
		$expected = array_map(function () {
			return true;
		}, self::$whitespaces);

		self::assertSame($expected, $this->matchPattern(self::PZ_PC_U));
	}

	public function test_S_and_S_U_are_different() {
		$slash_s = $this->matchPattern(self::SLASH_S);
		$slash_s_u = $this->matchPattern(self::SLASH_S_U);
		self::assertTrue(count($slash_s_u) > count($slash_s));
		self::assertSame(array(
			'next line' => true,
			'no-break space' => true,
			'ogham space mark' => true,
			'en quad' => true,
			'em quad' => true,
			'en space' => true,
			'em space' => true,
			'three-per-em space' => true,
			'four-per-em space' => true,
			'six-per-em space' => true,
			'figure space' => true,
			'punctuation space' => true,
			'thin space' => true,
			'hair space' => true,
			'line separator' => true,
			'paragraph separator' => true,
			'narrow no-break space' => true,
			'medium mathematical space' => true,
			'ideographic space' => true,
			'mongolian vowel separator' => true,
		), array_diff_key($slash_s_u, $slash_s));
	}

	public function test_S_U_and_Z_are_different() {
		$slash_s_u = $this->matchPattern(self::SLASH_S_U);
		$p_z_u = $this->matchPattern(self::P_Z_U);
		self::assertTrue(count($slash_s_u) > count($p_z_u));
		self::assertSame(array(
			'character tabulation' => true,
			'line feed' => true,
			'line tabulation' => true,
			'form feed' => true,
			'carriage return' => true,
			'next line' => true,
			'mongolian vowel separator' => true,
		), array_diff_key($slash_s_u, $p_z_u));
	}

	/**
	 * @param $pattern
	 * @return array
	 */
	private function matchPattern($pattern): array {
		$actual = [];
		foreach (self::$whitespaces as $name => $whitespace) {
			$actual[$name] = 1 === preg_match($pattern, $whitespace);
		}

		$actual = array_filter($actual);

		return $actual;
	}
}