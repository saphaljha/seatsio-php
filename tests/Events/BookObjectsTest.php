<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class BookObjectsTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $res = $this->seatsioClient->events->book($event->key, ["A-1", "A-2"]);

        self::assertEquals(ObjectStatus::$BOOKED, $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1")->status);
        self::assertEquals(ObjectStatus::$BOOKED, $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-2")->status);

        self::assertEquals(["A-1", "A-2"], array_keys($res->objects));
    }

    public function testSections()
    {
        $chartKey = $this->createTestChartWithSections();
        $event = $this->seatsioClient->events->create($chartKey);

        $res = $this->seatsioClient->events->book($event->key, ["Section A-A-1", "Section A-A-2"]);

        self::assertEquals(ObjectStatus::$BOOKED, $this->seatsioClient->events->retrieveObjectStatus($event->key, "Section A-A-1")->status);
        self::assertEquals(ObjectStatus::$BOOKED, $this->seatsioClient->events->retrieveObjectStatus($event->key, "Section A-A-2")->status);

        $a1Status = $res->objects["Section A-A-1"];
        self::assertEquals("Section A", $a1Status->section);
        self::assertEquals("Entrance 1", $a1Status->entrance);
        self::assertEquals(someLabels("1", "seat", "A", "row", "Section A"), $a1Status->labels);
    }

    public function testHoldToken()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();
        $this->seatsioClient->events->hold($event->key, "A-1", $holdToken->holdToken);

        $this->seatsioClient->events->book($event->key, "A-1", $holdToken->holdToken);

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals(ObjectStatus::$BOOKED, $objectStatus->status);
        self::assertNull($objectStatus->holdToken);
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->book($event->key, "A-1", null, "order1");

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals("order1", $objectStatus->orderId);
    }

    public function testKeepExtraData()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $extraData = ["foo" => "bar"];
        $this->seatsioClient->events->updateExtraData($event->key, "A-1", $extraData);

        $this->seatsioClient->events->book($event->key, "A-1", null, null, true);

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals((object)$extraData, $objectStatus->extraData);
    }

}
