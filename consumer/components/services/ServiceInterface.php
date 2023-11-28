<?php

namespace consumer\components\services;

interface ServiceInterface {
    /**
     * @param string $consumer
     *
     * @return void
     */
    public function indexing(string $consumer = ''): void;

    /**
     * @return bool
     */
    public function needAuth(): bool;

}