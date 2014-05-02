<?php

namespace Mikulas\LatteCS\Rules;

use Latte\Token;


class BlockAnnotation extends Rule
{

	/**
	 * @param Token[] $tokens
	 * @param string $content
	 * @return mixed
	 */
	protected function run(array $tokens, $content)
	{
		$expectingComment = FALSE;
		$block = NULL;
		foreach ($tokens as $pos => $token)
		{
			if ($token->type === $token::MACRO_TAG && $token->name === 'block')
			{
				$block = $token->value;
				$expectingComment = TRUE;

				// if block is closed on same line, do not expect annotation
				for ($k = $pos + 1; $k < count($tokens); ++$k)
				{
					$next = $tokens[$k];
					if ($next->line !== $token->line)
					{
						break;
					}
					if ($next->type === $next::MACRO_TAG && $next->name === '/block')
					{
						$expectingComment = FALSE;
					}
				}
				continue;
			}

			if (!$expectingComment)
			{
				continue;
			}
			else if ($token->type === $token::TEXT)
			{
				if (!preg_match('~^\s*$~s', $token->text))
				{
					$this->error("Block #$block is not properly annotated", $token->line);
					$expectingComment = FALSE;
				}
			}
			else if ($token->type === $token::COMMENT)
			{
				if (strpos($token->text, '{**') === FALSE)
				{
					$this->error('Lattedoc does not start with two stars but it should', $token->line);
				}

				$matches = [];
				if (!preg_match_all('~^\s*[*]\s+(?P<params>@param.*?)$~m', $token->text, $matches))
				{
					$this->error("Block #$block is annotated, but no @param was specified.", $token->line);
				}
				else
				{
					foreach ($matches['params'] as $param)
					{
						$match = [];
						if (!preg_match('~^@param(\s+)(?P<type>\S+?)(\s+)(?P<name>\$?\S+)~ims', $param, $match))
						{
							$this->error("Invalid annotation '$param', expected '@param type \$name'", $token->line);
						}
						else
						{
							if (strpos($match['name'], '$') === FALSE)
							{
								$this->error("Invalid annotation '$param', variable name must start with '\$'", $token->line);
							}
							// TODO should we test if whitespace is just one space?
						}
					}
				}

				$expectingComment = FALSE;
			}
			else
			{
				$this->error("Block #$block is not properly annotated", $token->line);
				$expectingComment = FALSE;
			}
		}
	}

}
