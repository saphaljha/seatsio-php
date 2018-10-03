<?php

namespace Seatsio\ChartReports;

use Seatsio\SeatsioClientTest;

class ChartReportsTest extends SeatsioClientTest
{

    public function testReportItemProperties()
    {
        $chartKey = $this->createTestChart();

        $report = $this->seatsioClient->chartReports->byLabel($chartKey);

        $reportItem = $report["A-1"][0];
        self::assertEquals("A-1", $reportItem->label);
        self::assertEquals("Cat1", $reportItem->categoryLabel);
        self::assertEquals(9, $reportItem->categoryKey);
        self::assertEquals("seat", $reportItem->objectType);
        self::assertNull($reportItem->section);
        self::assertNull($reportItem->entrance);
    }

    public function testReportItemPropertiesForGA()
    {
        $chartKey = $this->createTestChart();

        $report = $this->seatsioClient->chartReports->byLabel($chartKey);

        $reportItem = $report["GA1"][0];
        self::assertEquals(100, $reportItem->capacity);
        self::assertEquals("generalAdmission", $reportItem->objectType);
    }

    public function testByLabel()
    {
        $chartKey = $this->createTestChart();

        $report = $this->seatsioClient->chartReports->byLabel($chartKey);
        self::assertCount(1, $report["A-1"]);
        self::assertCount(1, $report["A-2"]);
    }

}