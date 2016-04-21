<?php

namespace Suru\Docebo\DoceboApiWrapper\Api;

class User extends BaseEndpoint {

    /**
     * Course endpoints.
     *
     * @var array
     */
    protected $endpoints = [
        'list'           => '/api/user/listUsers',
        'logged_in'      => '/api/user/user_logged_in',
        'profile_image'  => '/api/user/profile_image',
        'stats'          => '/api/user/getstats',
        'token'          => '/api/user/getToken',
        'check_username' => '/api/user/checkUsername',
    ];

    /**
     * Retrieve users on the Docebo platform.
     *
     * @param int $from
     * @param int $count
     * @return object
     */
    public function getUserList($from = null, $count = null)
	{
		$headers = $this->master->getAccessTokenHeader();

		$parameters = [
            'from' => (isset($from)) ? $from : null,
            'count' => (isset($count)) ? $count : null,
        ];

		$response = $this->master->call($this->endpoints['list'], $headers, $parameters);

		return $response->body;
	}

    /**
     * Retrieves whether or not the given user is logged in on the Docebo platform.
     *
     * @param int $id
     * @param string $username
     * @param string $email
     * @return object
     */
    public function getUserLoggedIn($id = null, $username = null, $email = null)
    {
		$headers = $this->master->getAccessTokenHeader();

		$parameters = [
            'id_user' => (isset($id)) ? $id : null,
            'userid' => (isset($username)) ? $username : null,
            'email' => (isset($email)) ? $email : null,
        ];

		$response = $this->master->call($this->endpoints['logged_in'], $headers, $parameters);

		return $response->body;
    }

    /**
     * Retrieves the profile image for the given user.
     *
     * @param int $id
     * @return object
     */
    public function getUserProfileImage($id)
	{
		$headers = $this->master->getAccessTokenHeader();

		$parameters = [
			'id_user' => $id,
		];

		$response = $this->master->call($this->endpoints['profile_image'], $headers, $parameters);

		return $response->body;
	}

    /**
     * Return a detailed list of user course subscription grouped by user (default) or course.
     *
     * @param array $user_ids
     * @return object
     */
	public function getUserStats($user_ids)
	{
		$headers = $this->master->getAccessTokenHeader();

		$parameters = [
			'all_courses' => 1,
			'id_list' => implode(',', $user_ids),
		];

		$response = $this->master->call($this->endpoints['stats'], $headers, $parameters);

		return $response->body;
	}

    /**
     * Checks if a user is logged into the platform.
     *
     * @param string $username
     * @return object
     */
    public function getUserToken($username)
    {
        $headers = $this->master->getAccessTokenHeader();

		$parameters = [
            'username' => $username,
        ];

		$response = $this->master->call($this->endpoints['token'], $headers, $parameters);

		return $response->body;
    }

    /**
     * Checks whether the given username has an associated user on the Docebo system.
     *
     * @param string $username
     * @return boolean
     */
    public function usernameExists($username)
    {
        $headers = $this->master->getAccessTokenHeader();

        $parameters = [
            'userid' => $username,
            'also_check_as_email' => false,
        ];

        $response = $this->master->call($this->endpoints['check_username'], $headers, $parameters);

		if ( ! empty($response->body->success) && ! empty($response->body->email))
        {
            return true;
        }

        return false;
    }

    /**
     * Checks whether the given email has an associated user on the Docebo system.
     *
     * @param string $email
     * @return boolean
     */
    public function emailExists($email)
    {
        $headers = $this->master->getAccessTokenHeader();

        $parameters = [
            'userid' => $email,
            'also_check_as_email' => true,
        ];

        $response = $this->master->call($this->endpoints['check_username'], $headers, $parameters);

		if ( ! empty($response->body->success) && ! empty($response->body->email))
        {
            return true;
        }

        return false;
    }

}
