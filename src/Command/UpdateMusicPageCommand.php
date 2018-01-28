<?php

declare(strict_types=1);

namespace Chrif\SimonRackham\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateMusicPageCommand extends Command {

	protected function configure() {
		$this->setName('app:update-music-page')
			->setDescription("Update Simon Rackham music page")
			->setHelp("docker-compose run --rm php bin/console a:u");

	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln('Updating music page');

		/** @var ProgressBar $progressBar */
		$progressBar = new ProgressBar($output, 150);
		$progressBar->setFormat('[%bar%] %percent:3s%% %elapsed:6s%');
		$progressBar->start();

		$context = stream_context_create([], ['notification' => $this->notifier($progressBar)]);

		$html = file_get_contents('http://www.simonrackhamswork.com/music/', false, $context);

		if (false === $html) {
			$output->writeln('');
			$output->writeln('<error>Unable to fetch page</error>');

			return 1;
		}

		$progressBar->finish();

		file_put_contents(__DIR__ . '/../../resource/music-index.html', $html);

		$output->writeln('');
		$output->writeln('<info>OK</info>');

		return 0;
	}

	private function notifier(ProgressBar $progressBar) {
		return (function ($notification_code) use ($progressBar) {
			if (STREAM_NOTIFY_PROGRESS === $notification_code) {
				$progressBar->advance();
			}
		})->bindTo($this);
	}

}