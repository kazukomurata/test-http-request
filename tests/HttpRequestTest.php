<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Provides to output the results of HTTP request to CSV.
 */
class HttpRequestTest extends TestCase {

  public const LIST_DIR = __DIR__ . '/csv/';

  public const LOG_DIR = __DIR__ . '/log/';

  /**
   * Client.
   *
   * @var \Psr\Http\Client\ClientInterface $client
   */
  protected \Psr\Http\Client\ClientInterface $client;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->client = new Client();
  }

  /**
   * Tests that the URL of each row in the CSV is HTTP status 200.
   */
  public function testCsvListPage() {
    $output = self::LOG_DIR . date("Ymd_His") . '.csv';

    foreach (glob(self::LIST_DIR . "*.csv") as $filename) {
      $file = new SplFileObject($filename);
      $file->setFlags(SplFileObject::READ_CSV);

      foreach ($file as $row) {
        if (empty($row[0])) {
          // Skips the current iteration.
          continue;
        }
        try {
          $response = $this->client->get($row[0]);
          $status = $response->getStatusCode();
          $this->assertEquals(200, $response->getStatusCode());
          file_put_contents(
            $output,
            implode(',', [$row[0], $status]) . PHP_EOL,
            FILE_APPEND
          );
        }
        catch (ClientExceptionInterface $e) {
          echo $e->getMessage();
          continue;
        }
      }
    }
  }

}