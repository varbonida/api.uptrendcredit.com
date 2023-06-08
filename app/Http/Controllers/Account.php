<?php

namespace App\Http\Controllers;

use App\Models\ConnectedInstitution;
use App\Models\ConnectedAccount;

use Illuminate\Http\Request;

class Account extends Controller
{
	
	public function saveConnectedInstitution($data) {
		
		$institution = ConnectedInstitution::firstOrCreate([
			'user_id' => '1',
			'access_token' => $data['access_token'],
			'item_id' => $data['item_id'],
			'institution_id' => $data['institution_id'],
			'institution_name' => $data['institution_name'],
		]);
		$accounts = $this->saveConnectedAccounts($institution->id, $data['accounts']);

		return true;
	}

	public function saveConnectedAccounts($institution_id, $accounts) {
		$new_accounts = [];
		foreach($accounts as $account) {
			$new_account = ConnectedAccount::firstOrCreate([
				'user_id' => '1',
				'connected_institution_id' => $institution_id,
				'account_id' => $account['id'],
				'mask' => $account['mask'],
				'name' => $account['name'],
				'official_name' => '',
				'subtype' => $account['subtype'],
				'type' => $account['type'],
			]);
			$new_accounts[] = $new_account;
		}
		return $new_accounts;
	}

	public function getConnectedBanks(Request $request) {
		$accounts = ConnectedAccount::where('user_id', '1')->get();
		$arr_accounts = json_decode($accounts, true);
		$ids = array_map(function ($acct) {
			return $acct['connected_institution_id'];
		}, $arr_accounts);
		$uniq_ids = array_unique($ids);

		$institutions = ConnectedInstitution::whereIn('id', $uniq_ids)->get();
		$arr_institutions = json_decode($institutions, true);

		$accounts_by_institution = [];

		foreach ($arr_accounts as $account) {
			$institution_id = $account['connected_institution_id'];
	
			if (!isset($accounts_by_institution[$institution_id])) {
				$accounts_by_institution[$institution_id] = [];
			}
	
			$accounts_by_institution[$institution_id][] = $account;
		}

		foreach ($arr_institutions as &$institution) {
			$institution_id = $institution['id'];
	
			if (isset($accounts_by_institution[$institution_id])) {
				$institution['accounts'] = $accounts_by_institution[$institution_id];
			} else {
				$institution['accounts'] = [];
			}
		}

		return $arr_institutions;
	}
	
	public function getConnectedBankByAccountID(Request $request) {
		$connected_bank_id = $request->input('bank_id');
		$account_id = $request->input('account_id');
		
		$connected_ins = ConnectedInstitution::with('connected_accounts')->find($connected_bank_id)->first();
		$account = $connected_ins->connected_accounts->where('account_id', $account_id)->first();
		$account['access_token'] = $connected_ins['access_token'];
		
		return $account;
	}

}
