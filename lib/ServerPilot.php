<?php

/**
 * PHP library to access ServerPilot.io services
 *
 * @link		<github>	https://github.com/daverogers/serverpilot-php
 * @link		<packagist>	https://packagist.org/packages/daverogers/serverpilot-php
 * @version		1.0.3
 * @author		Dave Rogers <redcore@gmail.com>
 * @contributor m0byd1ck (https://github.com/m0byd1ck)
 */

class ServerPilot {
	// variables
	public $apiID;
	public $apiKey;
	public $decode;

	// constants
	const SP_API_ENDPOINT		= 'https://api.serverpilot.io/v1/';
	const SP_USERAGENT			= 'ServerPilot-PHP/1.0';
	const SP_HTTP_METHOD_POST	= 'post';
	const SP_HTTP_METHOD_GET	= 'get';
	const SP_HTTP_METHOD_DELETE	= 'delete';

	// error constants
	const SP_MISSING_CONFIG	= 'Missing config data';
	const SP_MISSING_API	= 'You must provide API credentials';
	const SP_CURL_ERROR		= 'Curl error code returned ';

	public function __construct( $config = array() ) {
		if( empty($config) ) throw new Exception(ServerPilot::SP_MISSING_CONFIG);
		if( !isset($config['id']) || !isset($config['key']) ) throw new Exception(ServerPilot::SP_MISSING_API);

		$this->apiID	= $config['id'];
		$this->apiKey	= $config['key'];
		$this->decode	= ( isset($config['decode']) ) ? $config['decode'] : true;
	}

	/**
	 * Retrieve list of all servers
	 *
	 */
	public function server_list() {
		return $this->_send_request( 'servers' );
	}

	/**
	 * Create a new server
	 *
	 * @param	string		Nickname of the server. Must be 1 to 255 characters in length, may only contain the characters abcdefghijklmnopqrstuvwxyz0123456789.-
     *
     * @return mixed
     */
	public function server_create( $name ) {
		$params['name'] = $name;

		return $this->_send_request( 'servers', $params, ServerPilot::SP_HTTP_METHOD_POST );
	}

	/**
	 * Retrieve information on an existing server
	 *
	 * @param	string		ID of the server
     *
     * @return mixed
     */
	public function server_info( $id ) {
		return $this->_send_request( "servers/$id" );
	}

	/**
	 * Delete a server
	 *
	 * @param	string		ID of the server
     *
     * @return mixed
     */
	public function server_delete( $id ) {
		return $this->_send_request( "servers/$id", array(), ServerPilot::SP_HTTP_METHOD_DELETE );
	}

	/**
	 * Update a server
	 *
	 * @param	string		ID of the server
	 * @param	bool		"Enabled" state of the Server firewall (False = firewall is not enabled)
	 * @param	bool		"Enabled" state of automatic system updates (False = automatic system updates are not enabled)
     *
     * @return mixed
     */
	public function server_update( $id, $firewall = null, $autoupdates = null ) {
		if( $firewall )
			$params['firewall'] = $firewall;
		if( $autoupdates )
			$params['autoupdates'] = $autoupdates;

		return $this->_send_request( "servers/$id", $params, ServerPilot::SP_HTTP_METHOD_POST );
	}

	/**
	 * Retrieve list of all system users
	 *
	 */
	public function sysuser_list() {
		return $this->_send_request( 'sysusers' );
	}

	/**
	 * Create a new system user
	 *
	 * @param	string		ID of the server
	 * @param	string		Name of the new user. Must be 3 to 32 characters in length, may only contain the characters abcdefghijklmnopqrstuvwxyz0123456789.-
	 * @param	string		Password of the new user. If user has no password, they will not be able to log in. No leading or trailing whitespace is allowed, must be at least 8 characters in length.
     *
     * @return mixed
     */
	public function sysuser_create( $id, $name, $password = NULL ) {
		$params = array(
			'serverid'	=> $id,
			'name'		=> $name);
		if( $password )
			$params['password']	= $password;

		return $this->_send_request( 'sysusers', $params, ServerPilot::SP_HTTP_METHOD_POST );
	}

	/**
	 * Retrieve information on an existing system user
	 *
	 * @param	string		ID of the system user
     *
     * @return mixed
     */
	public function sysuser_info( $id ) {
		return $this->_send_request( "sysusers/$id" );
	}

	/**
	 * Delete a system user
	 *
	 * @param	string		ID of the system user
     *
     * @return mixed
     */
	public function sysuser_delete( $id ) {
		return $this->_send_request( "sysusers/$id", array(), ServerPilot::SP_HTTP_METHOD_DELETE );
	}

	/**
	 * Update a system user
	 *
	 * @param	string		ID of the system user
	 * @param	string		New password of the App user. No leading or trailing whitespace is allowed, must be at least 8 characters in length.
     *
     * @return mixed
     */
	public function sysuser_update( $id, $password ) {
		$params['password'] = $password;

		return $this->_send_request( "sysusers/$id", $params, ServerPilot::SP_HTTP_METHOD_POST );
	}

	/**
	 * Retrieve list of all apps
	 *
	 */
	public function app_list() {
		return $this->_send_request( 'apps' );
	}

	/**
	 * Create a new app
	 *
	 * @param	string		Nickname of the app. Length must be between 3 and 30 characters, may only contain lowercase ascii letters and digits.
	 * @param	string		The System User that will "own" this App. Since every System User is specific to a Server, this implicitly determines on which Server the App will be created.
	 * @param	string		PHP runtime for an App. ["php5.4", "php5.5"]
	 * @param	array		An array of domains that will be used in the webserver's configuration.
	 *						If you set your app's domain name to example.com, Nginx and Apache will be configured to listen for both example.com and www.example.com.
	 *						Note: The complete list of domains must be included in every update to this field.
	 * @param array		If present, installs WordPress on the App. Value is a JSON object containing keys site_title, admin_user, admin_password, and admin_email, each with values that are strings. The admin_password value must be at least 8 characters long.
	 */
	public function app_create( $name, $sysuserid, $runtime, $domains = array(), $wordpress = array() ) {
		$params = array(
			'name'		=> $name,
			'sysuserid'	=> $sysuserid,
			'runtime'	=> $runtime);
		if( $domains )
			$params['domains'] = $domains;
    if( $wordpress )
      $params['wordpress'] = $wordpress;

		return $this->_send_request( 'apps', $params, ServerPilot::SP_HTTP_METHOD_POST );
	}

	/**
	 * Retrieve information on an existing app
	 *
	 * @param	string		ID of the app
     *
     * @return mixed
     */
	public function app_info( $id ) {
		return $this->_send_request( "apps/$id" );
	}

	/**
	 * Delete an app
	 *
	 * @param	string		ID of the app
     *
     * @return mixed
     */
	public function app_delete( $id ) {
		return $this->_send_request( "apps/$id", array(), ServerPilot::SP_HTTP_METHOD_DELETE );
	}

	/**
	 * Update an app
	 *
	 * @param	string		ID of the app
	 * @param	string		PHP runtime for an App. ["php5.4", "php5.5"]
	 * @param	array		An array of domains that will be used in the webserver's configuration.
	 *						If you set your app's domain name to example.com, Nginx and Apache will be configured to listen for both example.com and www.example.com.
	 *						Note: The complete list of domains must be included in every update to this field.
     *
     * @return mixed
     */
	public function app_update( $id, $runtime = NULL, $domains = NULL ) {
		if( $runtime )
			$params['runtime']	= $runtime;
		if( $domains )
			$params['domains']	= $domains;

		return $this->_send_request( "apps/$id", $params, ServerPilot::SP_HTTP_METHOD_POST );
	}

  /**
   * Add an auto SSL cert to app - requires Coach or Business plan.
   * Use ssl_delete to remove cert.
   *
   * @param string  ID of the app
   * @see https://github.com/ServerPilot/API#enable-autossl
   */
  public function ssl_auto( $id ) {
    $params = array(
      'auto'   => true);

    return $this->_send_request( "apps/$id/ssl", $params, ServerPilot::SP_HTTP_METHOD_POST );
  }

	/**
	 * Add an SSL cert to app - requires Coach or Business plan
	 *
	 * @param	string		ID of the app
	 * @param	string		Contents of the private key
	 * @param	string		Contents of the certificate
	 * @param	string		Contents of the CA certificate(s). If none, NULL is acceptable.
     *
     * @return mixed     */
	public function ssl_add( $id, $key, $cert, $cacerts = NULL) {
		$params = array(
			'key'		=> $key,
			'cert'		=> $cert,
			'cacerts'	=> $cacerts);

		return $this->_send_request( "apps/$id/ssl", $params, ServerPilot::SP_HTTP_METHOD_POST );
	}

	/**
	 * Delete an SSL cert for an app - requires Coach or Business plan
	 *
	 * @param	string		ID of the app
     *
     * @return mixed
     */
	public function ssl_delete( $id ) {
		return $this->_send_request( "apps/$id/ssl", array(), ServerPilot::SP_HTTP_METHOD_DELETE );
	}

	/**
	 * Force SSL redirect for an app - requires Coach or Business plan
	 *
	 * @param $id
	 * @param $force
	 * @return mixed
	 */
	public function ssl_force( $id, $force) {
		$params = array(
			'force' => $force,
		);

		return $this->_send_request( "apps/$id/ssl", $params, ServerPilot::SP_HTTP_METHOD_POST );
	}

	/**
	 * Retrieve list of all databases
	 *
	 */
	public function database_list() {
		return $this->_send_request( 'dbs' );
	}

	/**
	 * Retrieve information on an existing database
	 *
	 * @param	string		ID of the database
     *
     * @return mixed
     */
	public function database_info( $id ) {
		return $this->_send_request( "dbs/$id" );
	}

	/**
	 * Create a new database
	 *
	 * @param	string		ID of the app
	 * @param	string		Name of the database. Length must be between 3 and 64 characters, may contain lowercase ascii letters, digits, or a dash.
	 * @param	string		Name of database user
	 * @param	string		Password for database user. Length must be between 1 and 16 characters, may contain lowercase ascii letters, digits, an underscore, or a dash.
     *
     * @return mixed
     */
	public function database_create( $id, $name, $username, $password ) {
		$user = new stdClass();
		$user->name = $username;
		$user->password = $password;

		$params = array(
			'appid'		=> $id,
			'name'		=> $name,
			'user'		=> $user);

		return $this->_send_request( 'dbs', $params, ServerPilot::SP_HTTP_METHOD_POST );
	}

	/**
	 * Delete a database
	 *
	 * @param	string		ID of the database
     *
     * @return mixed
     */
	public function database_delete( $id ) {
		return $this->_send_request( "dbs/$id", array(), ServerPilot::SP_HTTP_METHOD_DELETE );
	}

	/**
	 * Update password for database user
	 *
	 * @param	string		ID of the database
	 * @param	string		ID for the database user being updated
	 * @param	string		New password for this database user. Length must be between 1 and 16 characters, may contain lowercase ascii letters, digits, an underscore, or a dash
     *
     * @return mixed
     */
	public function database_update( $id, $userid, $password ) {
		$user = new stdClass();
		$user->id = $userid;
		$user->password = $password;

		$params['user']	= $user;

		return $this->_send_request( "dbs/$id", $params, ServerPilot::SP_HTTP_METHOD_POST );
	}

	/**
	 * Retrieve information on a particular action
	 *
	 * @param	string		ID of the action
     *
     * @return mixed
     */
	public function action_info( $id ) {
		return $this->_send_request( "actions/$id" );
	}

    /**
     * @param        string     $url_segs
     * @param array  array      $params
     * @param string string     $http_method
     *
     * @return mixed
     * @throws \ServerPilotException
     */
	private function _send_request( $url_segs, $params = array(), $http_method = 'get' )
	{
		// Initialize and configure the request
		$req = curl_init( ServerPilot::SP_API_ENDPOINT.$url_segs );

		curl_setopt( $req, CURLOPT_USERAGENT, ServerPilot::SP_USERAGENT );
		curl_setopt( $req, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		curl_setopt( $req, CURLOPT_USERPWD, $this->apiID.':'.$this->apiKey );
		curl_setopt( $req, CURLOPT_RETURNTRANSFER, TRUE );

		// Are we using POST or DELETE? Adjust the request accordingly
		if( $http_method == ServerPilot::SP_HTTP_METHOD_POST ) {
			curl_setopt( $req, CURLOPT_HTTPHEADER, array('Content-Type: application/json') );
			curl_setopt( $req, CURLOPT_POST, TRUE );
			curl_setopt( $req, CURLOPT_POSTFIELDS, json_encode($params) );
		}
		if( $http_method == ServerPilot::SP_HTTP_METHOD_DELETE ) {
			curl_setopt( $req, CURLOPT_CUSTOMREQUEST, "DELETE" );
		}

		// Get the response, clean the request and return the data
		$response = curl_exec( $req );
		$http_status = curl_getinfo( $req, CURLINFO_HTTP_CODE );

		curl_close( $req );

        // Everything when fine
        if( $http_status == 200 )
        {
            // Decode JSON by default
            if( $this->decode )
                return json_decode( $response );
            else
                return $response;
        }

        // Some error occurred
        $data = json_decode( $response );

        // The error was provided by serverpilot
        if( property_exists( $response, 'error' ) && property_exists( $response, 'message' ) )
            throw new ServerPilotException($data->error->message, $http_status);

        // No error as provided, pick a default
        switch( $http_status )
        {
            case 400:
                throw new ServerPilotException('We couldn\'t understand your request. Typically missing a parameter or header.', $http_status);
            break;
            case 401:
                throw new ServerPilotException('Either no authentication credentials were provided or they are invalid.', $http_status);
            break;
            case 402:
                throw new ServerPilotException('Method is restricted to users on the Coach or Business plan.', $http_status);
            break;
            case 403:
                throw new ServerPilotException('Forbidden.', $http_status);
            break;
            case 404:
                throw new ServerPilotException('You requested a resource that does not exist.', $http_status);
            break;
            case 409:
                throw new ServerPilotException('Typically when trying creating a resource that already exists.', $http_status);
            break;
            case 500:
                throw new ServerPilotException('Something unexpected happened on ServerPilot\'s end.', $http_status);
            break;
            default:
                throw new ServerPilotException('Unknown error.', $http_status);
                break;
        }
	}
}
