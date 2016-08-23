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

namespace Google\Cloud\Tests\Datastore\Query;

use Google\Cloud\Datastore\Query\Query;

/**
 * @group datastore
 */
class QueryTest extends \PHPUnit_Framework_TestCase
{
    private $query;

    public function setUp()
    {
        $this->query = new Query('foo');
    }

    public function testConstructorOptions()
    {
        $query = new Query('foo', [
            'query' => ['foo' => 'bar']
        ]);

        $this->assertEquals($query->queryObject()['query'], ['foo' => 'bar']);
    }

    public function testCanPaginateFlagIsTrue()
    {
        $this->assertTrue($this->query->canPaginate());
    }

    public function testQueryObjectStructure()
    {
        $this->assertTrue(is_array($this->query->queryObject()));

        $this->assertTrue(is_array($this->query->queryObject()['partitionId']));
        $this->assertTrue(is_array($this->query->queryObject()['readOptions']));
        $this->assertTrue(is_array($this->query->queryObject()['query']));
    }

    public function testJsonSerialize()
    {

        $this->assertEquals($this->query->jsonSerialize(), $this->query->queryObject());
    }

    public function testProjection()
    {
        $self = $this->query->projection('propname');
        $this->assertInstanceOf(Query::class, $self);

        $res = $this->query->queryObject();

        $this->assertEquals($res['query']['projection'], ['propname']);
    }

    public function testProjectionWithArrayArgument()
    {
        $this->query->projection(['propname', 'propname2']);

        $res = $this->query->queryObject();

        $this->assertEquals($res['query']['projection'], ['propname', 'propname2']);
    }

    public function testKind()
    {
        $self = $this->query->kind('kindName');
        $this->assertInstanceOf(Query::class, $self);

        $res = $this->query->queryObject();

        $this->assertEquals($res['query']['kind'], [
            ['name' => 'kindName']
        ]);
    }

    public function testKindWithArrayArgument()
    {
        $this->query->kind(['kindName1', 'kindName2']);

        $res = $this->query->queryObject();

        $this->assertEquals($res['query']['kind'], [
            ['name' => 'kindName1'],
            ['name' => 'kindName2'],
        ]);
    }

    public function testFilter()
    {
        $self = $this->query->filter('propname', 'value');
        $this->assertInstanceOf(Query::class, $self);

        $res = $this->query->queryObject();

        $filters = $res['query']['filter']['compositeFilter']['filters'];

        $this->assertEquals($filters[0]['propertyFilter'], [
            'property' => [
                'name' => 'propname'
            ],
            'value' => [
                'stringValue' =>'value'
            ],
            'op' => Query::OP_DEFAULT
        ]);
    }

    public function testFilterCustomOperator()
    {
        $self = $this->query->filter('propname', 12, Query::OP_GREATER_THAN);
        $this->assertInstanceOf(Query::class, $self);

        $res = $this->query->queryObject();

        $filters = $res['query']['filter']['compositeFilter']['filters'];

        $this->assertEquals($filters[0]['propertyFilter'], [
            'property' => [
                'name' => 'propname'
            ],
            'value' => [
                'integerValue' => 12
            ],
            'op' => Query::OP_GREATER_THAN
        ]);
    }

    public function testOrder()
    {
        $self = $this->query->order('propname', Query::ORDER_DESCENDING);
        $this->assertInstanceOf(Query::class, $self);

        $res = $this->query->queryObject();

        $this->assertEquals($res['query']['order'][0], [
            'property' => [
                'name' => 'propname'
            ],
            'direction' => Query::ORDER_DESCENDING
        ]);
    }

    public function testDistinctOn()
    {
        $self = $this->query->distinctOn('propname');
        $this->assertInstanceOf(Query::class, $self);

        $res = $this->query->queryObject();

        $this->assertEquals($res['query']['distinctOn'], [
            ['name' => 'propname']
        ]);
    }

    public function testDistinctOnWithArrayArgument()
    {
        $this->query->distinctOn(['propname1', 'propname2']);

        $res = $this->query->queryObject();

        $this->assertEquals($res['query']['distinctOn'], [
            ['name' => 'propname1'],
            ['name' => 'propname2'],
        ]);
    }

    public function testStart()
    {
        $self = $this->query->start('1234');
        $this->assertInstanceOf(Query::class, $self);

        $res = $this->query->queryObject();

        $this->assertEquals($res['query']['startCursor'], '1234');
    }

    public function testEnd()
    {
        $self = $this->query->end('1234');
        $this->assertInstanceOf(Query::class, $self);

        $res = $this->query->queryObject();

        $this->assertEquals($res['query']['endCursor'], '1234');
    }

    public function testOffset()
    {
        $self = $this->query->offset(2);
        $this->assertInstanceOf(Query::class, $self);

        $res = $this->query->queryObject();

        $this->assertEquals($res['query']['offset'], 2);
    }

    public function testLimit()
    {
        $self = $this->query->limit(2);
        $this->assertInstanceOf(Query::class, $self);

        $res = $this->query->queryObject();

        $this->assertEquals($res['query']['limit'], 2);
    }
}
