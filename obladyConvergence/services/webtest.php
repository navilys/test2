<?
/*$pfServer = new SoapClient('http://192.168.1.94:8084/typo3conf/ext/pm_webservices/serveur.php?wsdl', array("trace" => 1, "exception" => 0));
try {
  $ret = $pfServer->disableAccount(array('username' => 'gary@colosa.com'));
} catch (Exception $error) {
    header('Content-Type: text/plain;');var_dump($pfServer, $error);
}
echo '<pre>';
var_dump($ret);
echo '</pre>';
die;*/

$client = new SoapClient('http://192.168.1.94/sysCheqLivreApp/en/frclassic/obladyConvergence/services/wsdl2',array("trace" => 1, "exception" => 0));
$params = array('username' => 'gary');
$result = $client->__soapCall("login", array(
array("userid" => "admin",
"password" => "admin"
)));
$sessionId = $result->message;
try {
  $ret = $result = $client->__SoapCall('getParams', $params);
} catch (Exception $error) {
    header('Content-Type: text/plain;');
    var_dump($client, $error); 
    $ret = $error;
}
//G::pr($ret);
die;

//print_r(PATH_PLUGINS . 'externalRegistration' . PATH_SEPARATOR . get_include_path());
/*require_once PATH_PLUGINS.'externalRegistration/classes/model/ErConfiguration.php';
require_once PATH_PLUGINS.'externalRegistration/classes/model/ErRequests.php';
require_once PATH_PLUGINS.'externalRegistration/classes/class.ExternalRegistrationUtils.php';

$erReqUid = G::decrypt('ZJljomimbGOmqWKermZqpGWjZcmRnmOjbGOlp2OeqGo ', URL_KEY);
// Load request
  $erRequestsInstance = new ErRequests();
  $request = $erRequestsInstance->load($erReqUid);
  
  $data = $request['ER_REQ_DATA'];  

$pfServer = new SoapClient('http://172.17.20.29:8081/typo3conf/ext/pm_webservices/serveur.php?wsdl');
$ret = $pfServer->createAccount(array(
    'username' => $data['__USR_USERNAME__'],
    'password' => md5($data['__PASSWORD__']),
    'email' => $data['__USR_EMAIL__'],
    'lastname' => $data['__USR_LASTNAME__'],
    'firstname' => $data['__USR_FIRSTNAME__'],
    'key' => 'AZ320X28PICPONC',
    'pmid' => $data['USR_UID'],
    'usergroup' => '2702543875118dbe80fa706071898640',
    'cHash' => md5($data['__USR_USERNAME__'].'*'.$data['__USR_LASTNAME__'].'*'.$data['__USR_FIRSTNAME__'].'*'.'AZ320X28PICPONC')));
*/
/*G::LoadClass('pmFunctions');
externalRegistrationSendEmail('6364542075118b6dc9786c2001213909', 'garymeyertrigo@gmail.com', $data = array());
$a = getExternalRegistrationLink('6364542075118b6dc9786c2001213909');
print_r($a); die;*/
?>
