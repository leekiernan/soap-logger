<?php
use Illuminate\Support\Facades\Log;

class ExtendedLogger extends Log {
	public static $redact_fields = ['forename', 'dob', 'surname', 'email', 'telephone', 'postcode'];

	public static function debug($intro, $params) {
		$iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($params));

		foreach($iterator as $key => $value) {
			$nested_keys = array();
			foreach (range(0, $iterator->getDepth()) as $depth) {
				$nested_keys[] = $iterator->getSubIterator($depth)->key();
			}
			$intersect = array_intersect($nested_keys, self::$redact_fields);
			if (count($intersect) > 0) {
				$iterator->getInnerIterator()->offsetSet($key, "[REDACTED]");
			}
		}

		$data = json_encode($params, TRUE);
		parent::debug("$intro : $data");
	}
}

// $json = '{"ClientDetails": {"title": "002","forename": "Donald","surname": "Duck","gender": "m","dob": {"day": "23","month": "06","year": "1978"},"sector": "16","department": "CSMAClub","emp_name": "","promotional_code": "47","hear_about": "10","startdate": "","postcode": "KT6 7AA","addr1": "255a Ewell Road","addr2": "SURBITON","addr3": "Surrey","addr4": "Surrey","country": "GB","countryISO": "","email": {"email": "test@mail.addr","confirm": "test@mail.addr"},"telephone1": "01234567890","telephone2": "01234567890","marketing": {"telephone": "","post": "","email": "","sms": ""},"previous": "","dependant": "y","dependants": [{"title": "002","forename": "test","surname": "dep","gender": "f","calculate": {"relationship": "5","dob": {"day": "23","month": "06","year": "1998"}},"previous": "","include": ""}]},"DoctorDetails": {"gp_title": "001","gp_forename": "","gp_surname": "","gp_address1": "","gp_address2": "","gp_address3": ""},"PaymentDetails": {"payment": null,"cap_ac_name": "","sortcode": {"1": "","2": "","3": ""},"account_no": "","bank_name": "","branch_name": ""},"ConfirmationDetails": {"Med_Rep_Agree": false,"See_Med_Report": false,"termsconditions": false},"CoverDetails": {"policy": "","diag": 1,"heart": 1,"therapy_care": 1,"cash": 1,"contribution_limit": "000","hospital": 1,"uw_basis": 0}}';
// $o = json_decode($json);
// ExtendedLogger::$redact_fields = ['forename', 'dob', 'surname', 'email', 'telephone1', 'telephone2', 'postcode'];
// ExtendedLogger::debug("test", $o);
// [2018-06-27 09:41:07] local.DEBUG: test : {"ClientDetails":{"title":"002", "forename":"[REDACTED]", "surname":"[REDACTED]", "gender":"m", "dob":{"day":"[REDACTED]", "month":"[REDACTED]", "year":"[REDACTED]"}, "sector":"16", "department":"CSMAClub", "emp_name":"", "promotional_code":"47", "hear_about":"10", "startdate":"", "postcode":"[REDACTED]", "addr1":"255a Ewell Road", "addr2":"SURBITON", "addr3":"Surrey", "addr4":"Surrey", "country":"GB", "countryISO":"", "email":{"email":"[REDACTED]", "confirm":"[REDACTED]"}, "telephone1":"[REDACTED]", "telephone2":"01234567890", "marketing":{"telephone":"[REDACTED]", "post":"", "email":"[REDACTED]", "sms":""}, "previous":"", "dependant":"y", "dependants":[{"title":"002", "forename":"[REDACTED]", "surname":"[REDACTED]", "gender":"f", "calculate":{"relationship":"5", "dob":{"day":"[REDACTED]", "month":"[REDACTED]", "year":"[REDACTED]"}}, "previous":"", "include":""}]}, "DoctorDetails":{"gp_title":"001", "gp_forename":"", "gp_surname":"", "gp_address1":"", "gp_address2":"", "gp_address3":""}, "PaymentDetails":{"payment":null,"cap_ac_name":"", "sortcode":{"1":"", "2":"", "3":""}, "account_no":"", "bank_name":"", "branch_name":""}, "ConfirmationDetails":{"Med_Rep_Agree":false,"See_Med_Report":false,"termsconditions":false}, "CoverDetails":{"policy":"", "diag":1,"heart":1,"therapy_care":1,"cash":1,"contribution_limit":"000", "hospital":1,"uw_basis":0}}
