<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Messenger\Handler\Locator;

use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;

/**
 * @author Miha Vrhovnik <miha.vrhovnik@gmail.com>
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
abstract class AbstractHandlerLocator implements HandlerLocatorInterface
{
    public function resolve($message): callable
    {
        $messageClass = \get_class($message);

        if (null === $handler = $this->resolveFromClass($messageClass)) {
            throw new NoHandlerForMessageException(sprintf('No handler for message "%s".', $messageClass));
        }

        return $handler;
    }

    private function resolveFromClass(string $class): ?callable
    {
        if ($handler = $this->getHandler($class)) {
            return $handler;
        }

        foreach (class_implements($class, false) as $interface) {
            if ($handler = $this->getHandler($interface)) {
                return $handler;
            }
        }

        foreach (class_parents($class, false) as $parent) {
            if ($handler = $this->getHandler($parent)) {
                return $handler;
            }
        }

        return null;
    }

    abstract protected function getHandler(string $class);
}
