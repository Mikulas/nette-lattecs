<?php

namespace Mikulas\LatteCS\Console;

use Nette\Utils\Finder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CheckCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('check')
			->setDescription('Validate one or multiple latte files')
			->addArgument(
				'path',
				InputArgument::REQUIRED,
				'Path to file or directory to check'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$path = $input->getArgument('path');
		$runner = new \Mikulas\LatteCS\Runner(new \Latte\Parser());

		$errors = [];
		if (is_file($path))
		{
			$errors[$path] = $runner->checkFile($path);
		}
		else
		{
			foreach (Finder::findFiles('*.latte')->from($path) as $file => $info)
			{
				$output->write('.');
				$errors[$file] = $runner->checkFile($file);
			}
			$output->write("\n");
		}

		$valid = TRUE;
		$pre = '';
		foreach ($errors as $file => $es)
		{
			if (!$es)
			{
				continue;
			}

			$valid = FALSE;
			$output->writeLn("$pre<info>$file</info>");
			foreach ($es as $e)
			{
				$output->writeLn("  line $e[line]: <comment>$e[message]</comment>");
				if ($output->isVerbose())
				{
					$output->writeLn("  rule: $e[rule]");
				}
			}
			$pre = "\n";
		}

		if (!$valid)
		{
			exit(1);
		}
	}
}
