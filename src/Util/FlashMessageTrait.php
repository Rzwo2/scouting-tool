<?php

declare(strict_types=1);

namespace App\Util;

use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

trait FlashMessageTrait
{
    public function addSuccessMessage(string $message): void
    {
        $this->getFlashBag()->add('success', $message);
    }

    public function addWarningMessage(string $message): void
    {
        $this->getFlashBag()->add('warning', $message);
    }

    public function addErrorMessage(string $message): void
    {
        $this->getFlashBag()->add('error', $message);
    }

    private function getFlashBag(): FlashBagInterface
    {
        try {
            return $this->requestStack->getSession()->getFlashBag();
        } catch (SessionNotFoundException $e) {
            throw new \LogicException('You cannot use flash messages if sessions are disabled. Enable them in "config/packages/framework.yaml".', 0, $e);
        }
    }
}
