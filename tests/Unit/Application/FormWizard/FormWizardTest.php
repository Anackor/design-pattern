<?php

namespace App\Tests\Unit\Application\FormWizard;

use App\Application\FormWizard\FormWizard;
use App\Application\FormWizard\FormHistoryManager;
use PHPUnit\Framework\TestCase;

class FormWizardTest extends TestCase
{
    public function testInitialStepAndData(): void
    {
        $wizard = new FormWizard();

        $this->assertEquals(1, $wizard->getStep());
        $this->assertEquals([], $wizard->getData());
    }

    public function testSavingAndRestoringSnapshot(): void
    {
        $wizard = new FormWizard();
        $wizard->setStep(2);
        $wizard->setData(['name' => 'John Doe']);

        $snapshot = $wizard->createSnapshot();

        $wizard->setStep(3);
        $wizard->setData(['name' => 'Jane Smith']);

        $wizard->restoreSnapshot($snapshot);

        $this->assertEquals(2, $wizard->getStep());
        $this->assertEquals(['name' => 'John Doe'], $wizard->getData());
    }

    public function testHistoryUndo(): void
    {
        $wizard = new FormWizard();
        $history = new FormHistoryManager();

        $wizard->setStep(1);
        $wizard->setData(['step1' => true]);
        $history->saveSnapshot($wizard->createSnapshot());

        $wizard->setStep(2);
        $wizard->setData(['step2' => true]);
        $history->saveSnapshot($wizard->createSnapshot());

        $wizard->setStep(3);
        $wizard->setData(['step3' => true]);

        // Undo last change
        $snapshot = $history->undo();
        $this->assertNotNull($snapshot);
        $wizard->restoreSnapshot($snapshot);

        $this->assertEquals(2, $wizard->getStep());
        $this->assertEquals(['step2' => true], $wizard->getData());

        // Undo again
        $snapshot = $history->undo();
        $this->assertNotNull($snapshot);
        $wizard->restoreSnapshot($snapshot);

        $this->assertEquals(1, $wizard->getStep());
        $this->assertEquals(['step1' => true], $wizard->getData());
    }

    public function testClearHistory(): void
    {
        $history = new FormHistoryManager();
        $wizard = new FormWizard();

        $wizard->setStep(2);
        $wizard->setData(['test' => 123]);
        $history->saveSnapshot($wizard->createSnapshot());

        $history->clear();

        $this->assertNull($history->undo());
    }
}
