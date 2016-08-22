<?php
/**
 * Copyright 2016 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Cloud\Tests\Datastore;

use Google\Cloud\Datastore\Entity;
use Google\Cloud\Datastore\Key;
use GuzzleHttp\Psr7\Stream;

/**
 * @group datastore
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{
    private $key;

    public function setUp()
    {
        $this->key = new Key('foo', ['path' => [
            ['kind' => 'kind', 'name' => 'name']
        ]]);
    }

    public function testCreateEntity()
    {
        $entity = new Entity($this->key, [
            'foo' => "bar"
        ]);

        $this->assertEquals('bar', $entity['foo']);

        $entity['test'] = 'val';

        $this->assertEquals('val', $entity['test']);

        $this->assertNull($entity['doesntExist']);

        $this->assertFalse(isset($entity['doesntExist']));
        $this->assertTrue(isset($entity['test']));

        unset($entity['test']);
        $this->assertFalse(isset($entity['test']));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidKey()
    {
        $key = $this->prophesize(Key::class);
        $key->state()->willReturn(Key::STATE_INCOMPLETE);

        $e = new Entity($key->reveal());
    }

    public function testGet()
    {
        $data = ['foo' => 'bar'];

        $entity = new Entity($this->key, $data);
        $this->assertEquals($data, $entity->get());
    }

    public function testSet()
    {
        $data = ['foo' => 'bar'];

        $entity = new Entity($this->key, []);
        $entity->set($data);
        $this->assertEquals($data, $entity->get());
    }

    public function testKey()
    {
        $entity = new Entity($this->key, []);
        $this->assertEquals($this->key, $entity->key());
    }

    public function testCursor()
    {
        $entity = new Entity($this->key, []);
        $this->assertNull($entity->cursor());

        $entity = new Entity($this->key, [], [
            'cursor' => 'foo'
        ]);

        $this->assertEquals('foo', $entity->cursor());
    }

    public function testJsonSerialize()
    {
        $entity = new Entity($this->key, ['foo' => 'bar']);

        $this->assertEquals($entity->entityObject(), $entity->jsonSerialize());
    }

    public function testEntityObject()
    {
        $entity = new Entity($this->key, ['foo' => 'bar']);
        $res = $entity->entityObject();

        $this->assertEquals($res['properties']['foo']['stringValue'], 'bar');
        $this->assertEquals($res['key'], $this->key);
    }

    public function testEncode()
    {
        $stream = fopen('php://memory','r+');
        fwrite($stream, 'foo');
        rewind($stream);

        $entity = new Entity($this->key, ['foo' => $stream], [
            'encode' => false
        ]);
        $res = $entity->entityObject();

        $this->assertEquals($res['properties']['foo']['blobValue'], 'foo');

        rewind($stream);

        $entity = new Entity($this->key, ['foo' => $stream], [
            'encode' => true
        ]);
        $res = $entity->entityObject();

        $this->assertEquals($res['properties']['foo']['blobValue'], base64_encode('foo'));
    }
}
