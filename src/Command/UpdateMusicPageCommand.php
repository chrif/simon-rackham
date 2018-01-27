<?php

declare(strict_types=1);

namespace Chrif\SimonRackham\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateMusicPageCommand extends Command {

	protected function configure() {
		$this->setName('app:update-music-page')
			->setDescription("Update Simon Rackham music page")
			->setHelp("docker-compose run --rm php bin/console a:u");

	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$html = file_get_contents('http://www.simonrackhamswork.com/music/');

		file_put_contents(__DIR__ . '/../../resource/index.html', $html);

		$output->writeln('Music page updated');

		return 0;
	}
}