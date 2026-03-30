<?php

namespace App\Domain\DvvDataImport\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class PlayerDataImportMessage {}
