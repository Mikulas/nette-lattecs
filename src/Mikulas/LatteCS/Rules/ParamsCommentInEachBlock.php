<?php

namespace Mikulas\LatteCS\Rules;

use Latte\Token;


class ParamsCommentInEachBlock extends Rule
{

	/**
	 * @param Token[] $tokens
	 * @return mixed
	 */
	protected function run(array $tokens)
	{
		$expectingComment = FALSE;
		$block = NULL;
		foreach ($tokens as $token)
		{
			if ($token->type === $token::MACRO_TAG && $token->name === 'block')
			{
				$block = $token->value;
				$expectingComment = TRUE;
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
					$this->error('text, but not just whitespace', $token->line);
				}
			}
			else if ($token->type === $token::COMMENT)
			{
				if (strpos($token->text, '{**') === FALSE)
				{
					$this->error('Lattedoc does not start with two stars but it should', $token->line);
				}

				$matches = [];
				var_dump($token->text);
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
