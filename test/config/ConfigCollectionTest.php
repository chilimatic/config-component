<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 19.07.15
 * Time: 13:34
 */

class ConfigCollectionTest extends PHPUnit_Framework_TestCase {


    /**
     * @test
     */
    public function testConfigCollectionInstanceOf() {
        self::assertInstanceOf('\chilimatic\lib\Config\Collection', new \chilimatic\lib\Config\Collection());

    }

    /**
     * @test
     */
    public function testGraphCollectionInstanceOf() {
        self::assertInstanceOf('\chilimatic\lib\DataStructure\Graph\Collection', new chilimatic\lib\Config\Collection());
    }

    /**
     * @test
     */
    public function testGraphCollectionAddNode() {
        $collection = new chilimatic\lib\Config\Collection();
        $collection->addNode(new \chilimatic\lib\Config\Node(null, '', null));

        self::assertEquals(1, $collection->count());
    }

    /**
     * @test
     * @expectedException TypeError
     */
    public function testGraphCollectionAddNodeWithInvalidKeyParam() {
        $collection = new chilimatic\lib\Config\Collection();
        $collection->addNode(new \chilimatic\lib\Config\Node(null, null, null));

        self::assertEquals(1, $collection->count());
    }

    /**
     * @test
     */
    public function testGraphCollectionAddAndRemoveNode() {
        $node = new \chilimatic\lib\Config\Node(null, '', null);
        $collection = new chilimatic\lib\Config\Collection();
        $collection->addNode($node);

        $collection->removeNode($node);
        self::assertEquals(0, $collection->count());
    }


    /**
     * @test
     */
    public function testGraphCollectionAddAndGetSameNode() {
        $node = new \chilimatic\lib\Config\Node(null, '*', null);
        $collection = new chilimatic\lib\Config\Collection();
        $collection->addNode($node);

        $retNode = $collection->getLastByKey('*');

        self::assertEquals($node, $retNode);
    }

    /**
     * @test
     */
    public function testGraphCollectionAddAndGetObjectStorage() {
        $node = new \chilimatic\lib\Config\Node(null, '*', null);
        $collection = new chilimatic\lib\Config\Collection();
        $collection->addNode($node);

        $retStorage = $collection->getByKey('*');

        self::assertInstanceOf('\SPLObjectStorage', $retStorage);
    }

    /**
     * @test
     */
    public function testGraphCollectionGetUnambigiosSpecificNode() {

        $node1 = new \chilimatic\lib\Config\Node(null, '*', null);
        $node2 = new \chilimatic\lib\Config\Node(null, '.', null);
        $collection = new chilimatic\lib\Config\Collection();
        $collection->addNode($node1);
        $collection->addNode($node2);

        $retNode = $collection->getLastByKey('.');

        self::assertEquals($node2, $retNode);
    }


    /**
     * @test
     */
    public function testGraphCollectionGetAmbigiousSpecificNode() {

        $node1 = new \chilimatic\lib\Config\Node(null, '*', null);
        $node2 = new \chilimatic\lib\Config\Node(null, '*', null);
        $collection = new chilimatic\lib\Config\Collection();
        $collection->addNode($node1);
        $collection->addNode($node2);

        $retNode = $collection->getLastByKey('*');

        self::assertEquals($node2, $retNode);
    }


    /**
     * @test
     */
    public function testGraphCollectionGetSpecificUnambigousChildNode() {

        $node1 = new \chilimatic\lib\Config\Node(null, '*', null);
        $node2 = new \chilimatic\lib\Config\Node(null, '.', null);
        $node1->addChild($node2);

        $collection = new chilimatic\lib\Config\Collection($node1->children->idList, $node1->children->keyList);
        $collection->addNode($node1);
        $retNode = $collection->getLastByKey('.');

        self::assertEquals($node2, $retNode);
    }

}