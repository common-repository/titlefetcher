<?php
/*
Plugin Name: TitleFetcher
Plugin URI: http://rewish.org/wp/title_fetcher
Description: &lt;a class="hoge" href=&quot;http://example.com/&quot;&gt;&lt;/a&gt;のように、hrefを入れてテキストを空にすると、保存時に自動でタイトルを取得します。
Author: rew
Version: 0.1.0
Author URI: http://rewish.org/
*/
class TitleFetcher
{
	public static function ready()
	{
		add_filter('content_save_pre', array(new self, 'fetch'), 1, 2);
	}

	public function fetch($content)
	{
		$content = str_replace('\\', '', $content);
		preg_match_all('/<a .*href=[\'"]+([^\'" ]+).*?></', $content, $url);
		foreach ($url[1] as $u) {
			$u = $this->_fetch($u);
			$content = preg_replace('/(<a.+?>)</', "$1$u<", $content, 1);
		}
		return $content;
	}

	private function _fetch($url)
	{
		$c = @file_get_contents($url);
		if (!$c) return '[No Title]';
		$c = mb_convert_encoding($c, mb_internal_encoding(),
			'JIS, eucjp-win, sjis-win, ASCII, UTF-8');
		if (preg_match('_<title>(.+)</title>_i', $c, $title)) {
			return $title[1];
		}
		return '[No Title]';
	}
}

TitleFetcher::ready();
