<?php

namespace App\Application\FormWizard;

/**
 * The Caretaker in the Memento pattern:
 *
 * This class manages the history of form wizard states by storing and
 * retrieving snapshots. It does not manipulate or inspect the content
 * of the mementos directly.
 */
class FormHistoryManager
{
    /**
     * @var FormWizardSnapshot[]
     */
    private array $history = [];

    public function saveSnapshot(FormWizardSnapshot $snapshot): void
    {
        $this->history[] = $snapshot;
    }

    public function undo(): ?FormWizardSnapshot
    {
        return array_pop($this->history);
    }

    public function clear(): void
    {
        $this->history = [];
    }
}
