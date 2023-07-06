<?php

namespace app\components\services;

interface ServiceInterface {
    /**
     * @return void
     */
    public function indexing(): void;

    /**
     * @return bool
     */
    public function needAuth(): bool;

}