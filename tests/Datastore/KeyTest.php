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

use Google\Cloud\DataStore\Key;

/**
 * @group datastore
 */
class KeyTest extends \PHPUnit_Framework_TestCase
{
    public function testWithInitialPath()
    {
        $key = new Key('foo', [
            'path' => [
                ['kind' => 'Person']
            ]
        ]);

        $this->assertEquals($key->keyObject()['path'][0]['kind'], 'Person');
    }

    public function testKeyNamespaceId()
    {
        $key = new Key('foo', [
            'namespaceId' => 'MyApp'
        ]);

        $this->assertEquals($key->keyObject()['partitionId'], [
            'projectId' => 'foo',
            'namespaceId' => 'MyApp'
        ]);
    }
}
