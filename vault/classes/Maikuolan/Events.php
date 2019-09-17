<?php
/**
 * Events orchestrator (last modified: 2019.09.17).
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * Source: https://github.com/Maikuolan/Common
 *
 * License: GNU/GPLv2
 * @see LICENSE.txt
 *
 * "COMMON CLASSES PACKAGE", as well as the earliest iteration and deployment
 * of this class, COPYRIGHT 2019 and beyond by Caleb Mazalevskis (Maikuolan).
 */

namespace Maikuolan\Common;

class Events
{

    /** Event handlers. */
    private $Handlers = [];

    /** The status of various events and their handlers. */
    private $Status = [];

    /**
     * Adds a new event handler.
     *
     * @param string $Event The event to fire the handler.
     * @param callable $Handler The handler to add.
     * @param bool $Replace Whether to replace any/all existing handlers.
     * @return bool True on success; False on failure.
     */
    public function addHandler($Event, callable $Handler, $Replace = false)
    {
        if ($Replace || !isset($this->Handlers[$Event])) {
            $this->Handlers[$Event] = [];
            $this->Status[$Event] = ['Protected' => false];
        } elseif (!empty($this->Status[$Event]['Protected'])) {
            return false;
        }
        $this->Handlers[$Event][] = $Handler;
        return true;
    }

    /**
     * Adds a final new event handler.
     *
     * @param string $Event The event to fire the handler.
     * @param callable $Handler The handler to add.
     * @param bool $Replace Whether to replace any/all existing handlers.
     * @return bool True on success; False on failure.
     */
    public function addHandlerFinal($Event, callable $Handler, $Replace = false)
    {
        if ($Replace || !isset($this->Handlers[$Event])) {
            $this->Handlers[$Event] = [];
            $this->Status[$Event] = [];
        } elseif (!empty($this->Status[$Event]['Protected'])) {
            return false;
        }
        $this->Handlers[$Event][] = $Handler;
        $this->Status[$Event]['Protected'] = true;
        return true;
    }

    /**
     * Destroys an event and all associated handlers.
     *
     * @param string $Event The event to destroy.
     * @return bool True on success; False on failure.
     */
    public function destroyEvent($Event)
    {
        if (!isset($this->Handlers[$Event], $this->Status[$Event])) {
            return false;
        }
        unset($this->Handlers[$Event], $this->Status[$Event]);
        return true;
    }

    /**
     * Fire an event.
     *
     * @param string $Event The event to fire.
     * @param string $Data The data to send to the event handlers.
     * @return bool True on success; False on failure.
     */
    public function fireEvent($Event, $Data = '')
    {
        if (!isset($this->Handlers[$Event], $this->Status[$Event])) {
            return false;
        }
        foreach ($this->Handlers[$Event] as $Handler) {
            $Previous = $Handler($Data);
        }
        return true;
    }

}