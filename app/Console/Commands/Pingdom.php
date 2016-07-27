<?php namespace App\Console\Commands;

use App\Models\WebApp;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Pingdom extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'pings:alert';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{

		$runsExceedingMaxTime = \App\Lib\Pingdom::getRunsExceedingMaxExecutionTime();

		foreach($runsExceedingMaxTime as $run) {
			$run->status = 'exceeded_execution_time';
			$run->save();

			$app = WebApp::find($run->web_app_id);

			if($app)
			{
				\App\Lib\Pingdom::sendAirbrakeAlert($app, $run);

			}

		}

		foreach(WebApp::where('max_execution_time_seconds', '!=', 0)->get() as $app)
		{

			$appHasNotSentStartPing = \App\Lib\Pingdom::hasAppNotSentAPing($app);

			if($appHasNotSentStartPing)
			{
				$lastPing = \App\Lib\Pingdom::getLastStartPing($app);

				\App\Lib\Pingdom::airbrake($app->app_name . ' has failed to send a start ping',
					'App\'s last start ping was sent ' . $lastPing->time_sent . ' Run ID = ' . $lastPing->run_id
				);
			}
		}

	}


}
