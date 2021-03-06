<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;
use Carbon\Carbon;

class Whatever extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
	protected $signature = 'promo:create {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle()
    {

	    $file = $this->argument('file');
	    $this->info('Display this on the screen:' . $file);
	    $this->line('Display this on the screen');


	    if (!Storage::disk('local')->exists($file)) {
		    $this->error('File doesn\'t exist' );
	    	return;
	    }

//	    $books = \App\Book::all();
	    $books = DB::table('books')->select(DB::raw('id as nr, title as tl'))->where('id', '>', 1);

	    $anzahl = $books->count();
	    $books = $books->get();


	    // only for raw-output
	    $options = $this->options();
	    if (isset($options['verbose'])) {
		    $books = collect($books)->map(function($x){ return (array) $x; })->toArray();
		    $headers = ['nr', 'tl'];
		    $this->table($headers, $books);
	    }

		// read content
	    $contents = json_decode(Storage::get('file.json'));
	    $this->info('Anzahl:' . $anzahl);
	    $this->info($contents->title);


	    $bar = $this->output->createProgressBar(count($books));
	    $faker = Faker::create();

	    $allBooks = [];
	    foreach ($books as $b) {

		    $bar->advance();

//$data = [
//    ['name' => 'John', 'age' => 25],
//    ['name' => 'Maria', 'age' => 31],
//    ['name' => 'Julia', 'age' => 55],
//];
//Then insert the data using Eloquent model:
//
//Model::insert($data);
//Or using query builder:
//
//DB::table('table_name')->insert($data);

			    $newBook = new \App\Book();
			    $newBook->title = $faker->phoneNumber;
			    $allBooks[] = $newBook->attributesToArray();

//		    usleep(250000);
	    }
	    \App\Book::insert($allBooks);

	    $bar->finish();


    }
}
