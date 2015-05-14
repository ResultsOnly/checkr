<?php

class Checkr {

	private $api_key = '';

	private $api_version = '';

	/**
	 * Selected package type
	 * i.e tasker_standard, tasker_plus, driver_standard, driver_plus
	 * @var string
	 */
	protected static $package = '';

	/**
	 * @param string $package      sets default package
	 * @param string $api_version  version of checkr api
	 */
	public function __construct($api_version = 'v1', $package = 'driver_standard') {

		// Sets api key based on server
		if (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')))
			$this->api_key = getenv('CHECKR_TEST_KEY');
		else
			$this->api_key = getenv('CHECKR_PRODUCTION_KEY');

		$this->api_version = $api_version;
		$this->package = $package;

	}

	/**
	 * Creates a new candidate
	 * @param  Person $p 				 person object
	 * @param  array  $sensitive_params  sensitive information i.e ssn, drivers license #, drivers state
	 * @return object $candidate         object returned from resource
	 */
	public function createCandidate(Person $p, $sensitive_params = []) {

		$default_params = array(
			'first_name' => $p->firstname,
			'last_name' => $p->lastname,
			'dob' => $p->birthday,
			'phone' => $p->phone,
			'email' => $p->email,
			'zipcode' => $p->zip
		);

		$candidate_params = array_merge($default_params, $sensitive_params);

		$candidate = $this->request('candidates', $candidate_params, true);


		return $candidate;

	}

	/**
	 * Create background check report
	 * @param  string $candidate_id  candidate's id
	 * @return object                a report object
	 */
	public function createReport($candidate_id) {

		$report_params = array(
		    "candidate_id" => $candidate_id,
		    "package" => $this->package
		);

		return $this->request('reports', $report_params, true);

	}

	/**
	 * Retrieves a list of candidates
	 * @return array  an array of candidates
	 */
	public function listCandidates() {

		return $this->request('candidates/', [])->data;

	}

	/**
	 * Retrieve a single candidate
	 * @param  string $id  candidate's id
	 * @return object      a candidate object
	 */
	public function getCandidate($id) {

		return $this->request('candidates/'.$id, []);

	}

	/**
	 * Retrieve a single candidate report
	 * @param  string $report_id  id of report
	 * @return object             a report object
	 */
	public function getReport($report_id) {

		return $this->request('reports/' . $report_id, []);

	}

	/**
	 * List all reports
	 * @return array  an array of report objects
	 */
	public function listReports() {

		foreach ($this->listCandidates() as $candidate) {

			foreach ($candidate->report_ids as $report_id) {

				$reports[] = $this->getReport($report_id);
			}
		}

		return $reports;

	}

	/**
	 * Sends curl request to get data
	 * @param  string  $endpoint checkr api endpoint (default candidates will list candidates)
	 * @param  array   $params   array of params for checkr
	 * @param  boolean $post     default request will be POST else GET
	 * @return json            	 returns json object
	 */
	protected function request($endpoint = 'candidates', $params = [], $post = false) {

		$url = "https://api.checkr.io/{$this->api_version}/{$endpoint}";

		if (!$post)
			$url .= "?include=candidate,ssn_trace,county_criminal_searches";

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERPWD, $this->api_key . ":");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, $post);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

		if (count($params))
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

		$json = curl_exec($curl);
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		$response = json_decode($json);

		return $response;

	}

}
