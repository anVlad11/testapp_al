<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Controller\IndexController;
use Zend\Http\PhpEnvironment\Request;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    protected const TOKEN_READ = "8040c6830d2e14af191feef7eaf";
    protected const TOKEN_WRITE = "akpsdkaosdpoasdkopsadk";
    protected const API_ENDPOINT = "/api/tasks";

    public function setUp()
    {
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();
    }

    public function testGetAction()
    {
        $headers = new \Zend\Http\Headers;
        $headers->addHeaderLine('Authorization', self::TOKEN_READ);

        $this->getRequest()
            ->setMethod('GET')
            ->setHeaders($headers);
        $this->dispatch(self::API_ENDPOINT);

        $this->assertResponseStatusCode(200);
        $this->assertNotEmpty($this->getResponse()->getBody(), "Body is empty");
        $this->assertJson($this->getResponse()->getBody(), "Body is not a valid JSON");

        $json = json_decode($this->getResponse()->getBody());
        $this->assertTrue(is_array($json), "JSON is not an array");
        $taskIds = [];
        foreach($json as $index => $item)
        {
            $this->assertObjectHasAttribute('id', $item, "Task item #$index has no `id` attribute");
            $this->assertObjectHasAttribute('title', $item, "Task item #$index has no `title` attribute");
            $this->assertObjectHasAttribute('description', $item, "Task item #$index has no `description` attribute");
            $this->assertObjectHasAttribute('created_at', $item, "Task item #$index has no `created_at` attribute");
            $taskIds[] = $item->id;
        }
        return $taskIds;

    }

    /**
     * @depends testGetAction
     * @param array $taskIds
     */
    public function testGetActionWithRandomValidId(array $taskIds)
    {
        $id = $taskIds[array_rand($taskIds)];

        $headers = new \Zend\Http\Headers;
        $headers->addHeaderLine('Authorization', self::TOKEN_READ);

        $this->getRequest()
            ->setMethod('GET')
            ->setHeaders($headers);
        $this->dispatch(self::API_ENDPOINT . "/" . $id);

        $this->assertResponseStatusCode(200);
        $this->assertNotEmpty($this->getResponse()->getBody(), "Body is empty");
        $this->assertJson($this->getResponse()->getBody(), "Body is not a valid JSON");

        $json = json_decode($this->getResponse()->getBody());
        $this->assertTrue(is_object($json), "JSON is not an object");

        $this->assertObjectHasAttribute('id', $json, "Task item has no `id` attribute");
        $this->assertObjectHasAttribute('title', $json, "Task item has no `title` attribute");
        $this->assertObjectHasAttribute('description', $json, "Task item has no `description` attribute");
        $this->assertObjectHasAttribute('created_at', $json, "Task item has no `created_at` attribute");
    }

    public function testGetActionWithWrongToken()
    {
        $headers = new \Zend\Http\Headers;
        $headers->addHeaderLine('Authorization', self::TOKEN_WRITE);

        $this->getRequest()
            ->setMethod('GET')
            ->setHeaders($headers);
        $this->dispatch(self::API_ENDPOINT);

        $this->assertResponseStatusCode(401);
    }
}
