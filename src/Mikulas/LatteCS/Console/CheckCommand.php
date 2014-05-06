<?php

namespace Mikulas\LatteCS\Console;

use Nette\Neon\Neon;
use Nette\Utils\Finder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
			->addOption(
				'config',
				'c',
				InputOption::VALUE_REQUIRED,
				'Path to config file'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$path = $input->getArgument('path');

		$config = $this->getConfig($input, $output);

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
			$es = array_filter($es, function($e) use ($config) {
				foreach ($config['skip'] as $skip)
				{
					if (strpos($e['code'], $skip) === 0)
					{
						return FALSE;
					}
				}
				return TRUE;
			});
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
					$output->writeLn("  code: $e[code]");
				}
			}
			$pre = "\n";
		}

		if (!$valid)
		{
			exit(1);
		}
	}

	private function getConfig(InputInterface $input, OutputInterface $output)
	{
		$config = [];
		if ($configFile = $input->getOption('config'))
		{
			if (!file_exists($configFile))
			{
				$output->writeLn("<error>Config file '$configFile' does not exist</errro>");
				exit(1);
			}
			$raw = file_get_contents($configFile);
			$config = Neon::decode($raw);
		}
		if (!isset($config['skip']) || !is_array($config['skip']))
		{
			$config['skip'] = [];
		}

		return $config;
	}
}
