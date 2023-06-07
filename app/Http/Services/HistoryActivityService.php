<?php


namespace App\Http\Services;

use App\Repositories\LoggerRepository;

class HistoryActivityService
{
    private $loggerRepository;

    function __construct(LoggerRepository $loggerRepository)
    {
        $this->loggerRepository = $loggerRepository;
    }

    public function logger($data)
    {
        $this->loggerRepository->insert($data);
    }
}
