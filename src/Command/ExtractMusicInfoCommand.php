<?php

declare(strict_types=1);

namespace Chrif\SimonRackham\Command;

use Chrif\SimonRackham\MusicPageParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExtractMusicInfoCommand extends Command {

	protected function configure() {
		$this->setName('app:extract-music-info')
			->setDescription("Extract Simon Rackham info in csv")
			->setHelp("docker-compose run --rm php bin/console a:e");

	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$parser = new MusicPageParser();

		$output->writeln('Result will be written to parsed.csv');

		$progressBar = new ProgressBar($output, 1);
		$progressBar->setFormat(' %current%/%max% albums [%bar%] %percent:3s%% %elapsed:6s%');
		$progressBar->start();

		$parser->parse(
			__DIR__ . '/../../resource/music-index.html',
			__DIR__ . '/../../parsed.csv',
			$progressBar
		);

		$progressBar->finish();
		$output->writeln('');
		$output->writeln('<info>OK</info>');

		return 0;
	}
}