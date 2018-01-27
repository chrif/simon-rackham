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

		/** @var ProgressBar $progress */
		$progress = new ProgressBar($output, 100);
		$progress->setFormat('verbose');
		$progress->start();

		$context = stream_context_create([], ['notification' => $this->notifier($progress)]);

		$html = file_get_contents('http://www.simonrackhamswork.com/music/', false, $context);

		$progress->finish();

		file_put_contents(__DIR__ . '/../../resource/music-index.html', $html);

		$output->writeln('');
		$output->writeln('Music page updated');

		return 0;
	}

	private function notifier(ProgressBar $progressBar) {
		return (function (
			$notification_code,
			$severity,
			$message,
			$message_code,
			$bytes_transferred,
			$bytes_max
		) use ($progressBar) {
			switch ($notification_code) {
				case STREAM_NOTIFY_FILE_SIZE_IS:
					$progressBar->start($bytes_max);
					break;
				case STREAM_NOTIFY_PROGRESS:
					$progressBar->setProgress($bytes_transferred);
					break;
			}
		})->bindTo($this);
	}

}