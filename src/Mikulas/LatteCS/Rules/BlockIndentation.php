<?php

namespace Mikulas\LatteCS\Rules;

use Latte\Token;


class BlockIndentation extends Rule
{

	/**
	 * @param Token[] $tokens
	 * @param string $content
	 * @return mixed
	 */
	protected function run(array $tokens, $content)
	{
		$blockName = NULL;
		foreach (explode("\n", $content) as $i => $line)
		{
			$lineNum = $i + 1;

			$match = [];
			if (!$blockName && preg_match('~\{block\s+#?(?P<name>[^|]*?)(?:\|.*?)?\}((.*?)(?P<closed>\{/block\b|$))?~i', $line, $match))
			{
				if (!isset($match['closed']) || !$match['closed'])
				{
					$blockName = $match['name'];
					continue;
				}
			}
			if ($blockName && preg_match('~\{/block.*?\}((.*?)(?P<opened>\{block\s+#?(?P<name>[^|]*?)(?:\|.*?)?\}))?~i', $line, $match))
			{
				$blockName = NULL;
				if (isset($match['opened']))
				{
					$blockName = $match['name'];
				}
			}

			if ($blockName && !preg_match('~^(\s+|$)~', $line))
			{
				$this->error("Content in block #$blockName must be indented", $lineNum);
			}
		}
	}

}
