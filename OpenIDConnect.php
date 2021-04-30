<?php
require __DIR__ . '/vendor/autoload.php';

//Basic Client  From https://github.com/jumbojett/OpenID-Connect-PHP
use Jumbojett\OpenIDConnectClient;


class OpenidConnection
{
    protected $oidc;

    public function __construct($connection)
    {
        $this->oidc = $connection;
    }

    public function Connect($responsetype, $scope, $redirecturi)
    {
        $this->oidc->setResponseTypes($responsetype);
        $this->oidc->setRedirectURL($redirecturi);
        $this->oidc->setAllowImplicitFlow(true);
        $this->oidc->addScope($scope);
        $this->oidc->authenticate();
        
        $claims = $this->oidc->getVerifiedClaims();
        $claims = json_decode(json_encode($claims), true);
    }

    public function UserInfo()
    {
        $UserData = $this->oidc->requestUserInfo();
        return json_decode(json_encode($UserData));
    }

    public function GetToken()
    {
        return $this->oidc->getAccessToken();
    }

    public function requestProtectedApi($eduinfoep, $accesstoken, $rtn_array = true, $gzipenable = false)
    {
        $header  = array("Authorization: Bearer $accesstoken");
        $options = array(
            'http' => array(
                'header'  => $header,
                'method'  => 'GET',
                'content' => '',
            ));
        $context = stream_context_create($options);
        if ($gzipenable) {
            $result = gzdecode(file_get_contents($token_ep, false, $context));
        } else {
            $result = file_get_contents($token_ep, false, $context);
        }
        $u = json_decode($result, $rtn_array);
        return $u;
    }
}

$provideruri = 'https://example.com';
$clientid    = '<ClientId>';
$clientsecret= '<ClientSecret>';

//Openid connection init
$openidconnection = new OpenIDConnectClient($provideruri,$clientid,$clientsecret);

$responsetype = array('<Auth-Method>');
$scope = array('Your-Scope');
$redirecturi = '<RedirectUri>';

//connect
$OpenId = new OpenidConnection($openidconnection);
$OpenId->Connect($responsetype,$scope,$redirecturi);

//userData
$UserInfo = $openid_data->UserInfo();

//get accesstoken
$accesstoken = $OpenId->GetToken();
$eduinfoep = 'https://api.url';

//call API
$edu_info = $OpenId->requestProtectedApi($eduinfoep, $accesstoken, true, false);


