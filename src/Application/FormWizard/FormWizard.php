<?php

namespace App\Application\FormWizard;

/**
 * The Memento pattern is a behavioral design pattern that allows an object to save and restore its state without violating encapsulation.
 * It involves three main roles: the Originator (the object whose state needs saving), the Memento (which stores the state), 
 * and the Caretaker (which manages the saving and restoring process).
 *
 * This pattern is useful when implementing undo/redo mechanisms, checkpoints, or rollback features, especially in workflows 
 * where state evolves step by step. By keeping the internal details hidden, it ensures encapsulation while offering flexibility 
 * in navigating through historical states.
 * 
 * 
 * The Originator in the Memento pattern:
 *
 * This class holds the internal state of the form wizard and is responsible
 * for creating a snapshot (memento) of its current state and restoring it
 * when needed. It does not manage the history of states; that's delegated
 * to the Caretaker (FormHistoryManager).
 */
class FormWizard
{
    private int $currentStep;
    private array $formData;

    public function __construct()
    {
        $this->currentStep = 1;
        $this->formData = [];
    }

    public function setStep(int $step): void
    {
        $this->currentStep = $step;
    }

    public function getStep(): int
    {
        return $this->currentStep;
    }

    public function setData(array $data): void
    {
        $this->formData = $data;
    }

    public function getData(): array
    {
        return $this->formData;
    }

    public function createSnapshot(): FormWizardSnapshot
    {
        return new FormWizardSnapshot($this->currentStep, $this->formData);
    }

    public function restoreSnapshot(FormWizardSnapshot $snapshot): void
    {
        $this->currentStep = $snapshot->getStep();
        $this->formData = $snapshot->getData();
    }
}
