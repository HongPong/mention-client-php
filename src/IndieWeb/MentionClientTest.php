<?php
namespace IndieWeb;

/*
 * Make all protected methods public for PHPUnit
 */
class MentionClientTest extends MentionClient {

  public function __call($method, $args) {
    $method = new \ReflectionMethod('IndieWeb\MentionClient', $method);
    $method->setAccessible(true);
    return $method->invokeArgs($this, $args);
  }

  public static function __callStatic($method, $args) {
    $method = new \ReflectionMethod('IndieWeb\MentionClient', $method);
    $method->setAccessible(true);
    return $method->invokeArgs(null, $args);
  }

  protected static function _head($url) {
    $response = self::_read_file($url);
    return [
      'code' => $response['code'],
      'headers' => $response['headers']
    ];
  }

  protected static function _get($url) {
    return self::_read_file($url);
  }

  protected static function _post($url, $body, $headers=array()) {
    return self::_read_file($url);
  }

  private static function _read_file($url) {
    $filename = dirname(__FILE__).'/../../tests/data/'.preg_replace('/https?:\/\//', '', $url);
    if(!file_exists($filename)) {
      $filename = dirname(__FILE__).'/../../tests/data/404.response.txt';
    }
    $response = file_get_contents($filename);

    list($headers, $body) = explode("\r\n\r\n", $response);

    if(preg_match('/HTTP\/1\.1 (\d+)/', $headers, $match)) {
      $code = $match[1];
    }

    $headers = preg_replace('/HTTP\/1\.1 \d+ .+/', '', $headers);

    return [
      'code' => $code,
      'headers' => self::_parse_headers($headers),
      'body' => $body
    ];
  }
}
