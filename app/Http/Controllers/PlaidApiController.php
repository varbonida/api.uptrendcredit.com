<?php

namespace App\Http\Controllers;

use App\Models\ConnectedAccount;

use App\Http\Controllers\Account;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PlaidApiController extends Controller {
  private $client_id;
  private $secret;
  private $url_base;
	private $environment;
	private $access_token;

  public function __construct() {
    $this->client_id = env('PLAID_CLIENT_ID');
    $this->secret = env('PLAID_SECRET');;
		$this->environment = env('PLAID_ENVIRONMENT');
    $this->url_base = 'https://'.$this->environment.'.plaid.com'; //($this->environment == 'sandbox') ? 'https://sandbox.plaid.com' : 'https://production.plaid.com';
		// $model = new AccountToken();
		// $this->access_token = $model::where('user_id', '1')->first()->access_token;
		$this->access_token = 'access-sandbox-a646e96d-9e28-44d8-9bee-165104571db5';
	}
	
	public function createLinkToken(Request $request) {
		$clientId = $this->client_id;
		$secret = $this->secret;
		$environment = $this->environment;
		
		$client = new Client([
			'base_uri' => $this->url_base,
		]);

		$response = $client->post('link/token/create', [
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'json' => [
				'client_id' => $clientId,
				'secret' => $secret,
				'client_name' => 'Uptrend Credit',
				'user' => [
					'client_user_id' => '1',
				],
				'products' => ['auth', 'transactions'],
				'country_codes' => ['US'],
				'language' => 'en',
			],
		]);

		$link_token = json_decode($response->getBody(), true)['link_token'];

		return response()->json([
			'link_token' => $link_token,
		]);

  }

	public function exchangePublicToken(Request $request) {
		$public_token = $request->input('public_token');
		$institution_id = $request->input('institution_id');
		$institution_name = $request->input('institution_name');
		$accounts = $request->input('accounts');

		$client = new Client([
			'base_uri' => $this->url_base,
		]);

		try {
			$response = $client->post('item/public_token/exchange', [
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'json' => [
					'client_id' => $this->client_id,
					'secret' => $this->secret,
					'public_token' => $public_token,
				],
			]);

			$data = json_decode($response->getBody(), true);

			$access_token = $data['access_token'];
			$item_id = $data['item_id'];

			$connected_data = [
				'user_id' => '1',
				'access_token' => $access_token,
				'item_id' => $item_id,
				'institution_id' => $institution_id,
				'institution_name' => $institution_name,
				'accounts' => $accounts,
			];
			$accountController = new Account();
			$accountController->saveConnectedInstitution($connected_data);


			// save to db
			// $model = new AccountToken();
			// $model->user_id = '1';
			// $model->access_token = $access_token;
			// $model->item_id = $item_id;
			// $model->institution_id = 'ins_999';
			// $model->save();

			// get accounts
			$new_request = new Request();
			$new_request->merge(['access_token' => $access_token, 'first_load' => true]);
			$banks = $this->getBankData($new_request);
			
			return response()->json([
				'access_token' => $access_token,
				'item_id' => $item_id,	
			]);

		} catch (\Exception $e) {
			return response()->json(['error' => 'Failed to exchange public token'], 500);
		}
		
	}

	public function getInstitution(Request $request) {

	}

	public function getTransactions(Request $request) {
		$access_token = $request->input('access_token');
		
		$client = new Client([
			'base_uri' => $this->url_base,
		]);

		try {
			$response = $client->post('/transactions/get', [
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'json' => [
					'client_id' => $this->client_id,
					'secret' => $this->secret,
					'access_token' => $this->access_token,
					'start_date' => '2021-01-01',
					'end_date' => '2021-12-10',
				],
			]);

			$data = json_decode($response->getBody(), true);
			
			// save to db

			return response()->json([
				'transactions' => $data['transactions'],
			]);

		} catch (\Exception $e) {
			return response()->json([
				'error' => $e->getMessage(),
			], 500);
		}
	}

	public function getMonthlyTransactions(Request $request) {
		$access_token = $request->input('access_token');
		
		$client = new Client([
			'base_uri' => $this->url_base,
		]);

		try {
			$response = $client->post('/transactions/get', [
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'json' => [
					'client_id' => $this->client_id,
					'secret' => $this->secret,
					'access_token' => $access_token,
					'start_date' => '2021-06-01',#$request->input('start_date'),
					'end_date' => '2021-06-30',#$request->input('end_date'),
					'options' => [
						'account_ids' => [$request->input('account_id')],
					]
				],
			]);

			$data = json_decode($response->getBody(), true);
			
			// save to db

			return response()->json([
				'transactions' => $data['transactions'],
			]);

		} catch (\Exception $e) {
			return response()->json([
				'error' => $e->getMessage(),
			], 500);
		}
	}

	public function getBankData(Request $request) {
		$access_token = $request->input('access_token');

		$client = new Client([
			'base_uri' => $this->url_base,
		]);

		try {
			$response = $client->post('/auth/get', [
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'json' => [
					'client_id' => $this->client_id,
					'secret' => $this->secret,
					'access_token' => $this->access_token,
				],
			]);

			$data = json_decode($response->getBody(), true);
			
			// save to db
			if($request->input('first_load')) {
				foreach ($data['accounts'] as $account) {
					$d = [
						'user_id' => '1',
						'account_id' => $account['account_id'],
						'mask' => $account['mask'],
						'name' => $account['name'],
						'official_name' => $account['official_name'],
						'subtype' => $account['subtype'],
						'type' => $account['type'],
					];
					ConnectedAccount::firstOrCreate($d);
				}
			}

			return response()->json([
				'accounts' => $data['accounts'],
			]);

		} catch (\Exception $e) {
			return response()->json([
				'error' => $e->getMessage(),
			], 500);
		}
	}

	public function getAccountIdentity(Request $request) {
		$access_token = $request->input('access_token');

		$client = new Client([
			'base_uri' => $this->url_base,
		]);

		$req_json = [
			'client_id' => $this->client_id,
			'secret' => $this->secret,
			'access_token' => $access_token,
		];

		if($request->input('account_id')) {
			$req_json['options'] = ['account_ids' => [$request->input('account_id')]];
		}

		try {
			$response = $client->post('/identity/get', [
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'json' => $req_json,
			]);

			$data = json_decode($response->getBody(), true);
			
			// save to db

			return response()->json([
				'accounts' => $data['accounts'],
			]);

		} catch (\Exception $e) {
			return response()->json([
				'error' => $e->getMessage(),
			], 500);
		}
	}

	function getAccountBalance(Request $request) {
		$access_token = $request->input('access_token');

		$client = new Client([
			'base_uri' => $this->url_base,
		]);

		$req_json = [
			'client_id' => $this->client_id,
			'secret' => $this->secret,
			'access_token' => 'access-sandbox-a646e96d-9e28-44d8-9bee-165104571db5',
		];

		if($request->input('account_id')) {
			$req_json['options'] = [
				'account_ids' => [ $request->input('account_id') ],
			];
		}

		try {
			$response = $client->post('/accounts/balance/get', [
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'json' => $req_json,
			]);

			$data = json_decode($response->getBody(), true);
			
			// save to db

			return response()->json([
				'accounts' => $data['accounts'],
			]);

		} catch (\Exception $e) {
			return response()->json([
				'error' => $e->getMessage(),
			], 500);
		}

	}


}
