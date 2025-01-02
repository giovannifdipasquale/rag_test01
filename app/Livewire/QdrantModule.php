<?php

namespace App\Livewire;


use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;
use Qdrant\Qdrant;
use Qdrant\Config;
use Qdrant\Http\Builder;

// creating a collection
use Qdrant\Endpoints\Collections;
use Qdrant\Models\Request\CreateCollection;
use Qdrant\Models\Request\VectorParams;

// adding points inside collection
use Qdrant\Models\PointsStruct;
use Qdrant\Models\PointStruct;
use Qdrant\Models\VectorStruct;

// searching for points inside collection
use Qdrant\Models\Filter\Condition\MatchString;
use Qdrant\Models\Filter\Filter;
use Qdrant\Models\Request\SearchRequest;


class QdrantModule extends Component
{
	public $text;

	public $id = 0;

	private $vectorSize = 1536; // Gli embedding di Open AI restituiscono SEMPRE vettori di 1536 dimensioni
	private $collectionName = "questions";
	private $elementName = "question";

	public $results = [];

	// method that returns the qdrant client
	public function getQdrantClient()
	{
		// set config
		$config = new Config(env('QDRANT_HOST'));
		$config->setApiKey(env('QDRANT_API_KEY'));

		// dd(gettype($config));
		$transport = (new Builder())->build($config);

		return new Qdrant($transport);
	}
	// check if qdrant collection exists

	public function doesCollectionExist()
	{
		$res = $this->getQdrantClient()->collections()->list();
		foreach ($res['result']['collections'] as $collection) {
			if ($collection['name'] === $this->collectionName) {
				// dd('Collection === questions exists');
				return true; // Collection exists
			}
		}
		// dd('Collection === questions does NOT  exist');
		return false; // Collection does not exist
	}
	// creates collection if does not already exist
	public function createQdrantCollection()
	{
		$client = $this->getQdrantClient();
		$createCollection = new CreateCollection();
		$createCollection->addVector(new VectorParams(1536, VectorParams::DISTANCE_COSINE), $this->elementName);
		$client->collections($this->collectionName)->create($createCollection);
	}

	// lists all the points in the collection
	// public function list()
	// {
	// 	$client = $this->getQdrantClient();
	// 	$points = $client->collections($this->collectionName)->points();
	// 	dd($points);
	// }

	// add a point to the collection
	public function add()
	{
		// increment id 
		$this->id++;

		// define client
		$client = $this->getQdrantClient();
		// checks if collection exists
		if (!$this->doesCollectionExist()) {
			// if not, we create it
			$this->createQdrantCollection();
		}


		// create opeai embedding and put it in a variable. OK
		$response = OpenAI::embeddings()->create([
			'model' => 'text-embedding-ada-002',
			'input' => $this->text,
		]);

		// put the embedding in a vector  WORKS
		$vector = array_values($response->embeddings[0]->embedding);

		// and the vector in a point WORKS
		$points = new PointsStruct();
		$points->addPoint(
			new PointStruct(
				(int) $this->id,
				new VectorStruct($vector, $this->elementName),
				[
					$this->elementName => $this->text
				]

			)
		);


		// insert point inside qdrant collection
		$res = $client->collections('questions')->points()->upsert($points);
	}

	public function search()
	{

		$client = $this->getQdrantClient();
		$response = OpenAI::embeddings()->create([
			'model' => 'text-embedding-ada-002',
			'input' => $this->text,
		]);
		$vector = array_values($response->embeddings[0]->embedding);

		$searchRequest = (new SearchRequest(new VectorStruct($vector, $this->elementName)))
			->setLimit(10)
			->setParams([
				'hnsw_ef' => 128,
				'exact' => false,
			])
			->setWithPayload(true);

		$res = $client->collections($this->collectionName)->points()->search($searchRequest);
		$this->results = $res['result'];
	}
	public function render()
	{

		return view('livewire.qdrant-module');
	}
}
