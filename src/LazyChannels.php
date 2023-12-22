<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Notifier;

use Tobento\Service\Notifier\Exception\ChannelNotFoundException;
use Tobento\Service\Notifier\Exception\ChannelCreateException;
use Tobento\Service\Notifier\Exception\ChannelException;
use Psr\Container\ContainerInterface;
use Tobento\Service\Autowire\Autowire;
use Tobento\Service\Autowire\AutowireException;
use Throwable;

/**
 * LazyChannels
 */
final class LazyChannels implements ChannelsInterface
{
    /**
     * @var Autowire
     */
    protected Autowire $autowire;
    
    /**
     * @var array<string, ChannelInterface>
     */
    protected array $createdChannels = [];
    
    /**
     * Create a new LazyChannels.
     *
     * @param ContainerInterface $container
     * @param array $channels
     */
    public function __construct(
        ContainerInterface $container,
        protected array $channels,
    ) {
        $this->autowire = new Autowire($container);
    }
    
    /**
     * Returns true if channel exists, otherwise false.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->channels);
    }
    
    /**
     * Returns the channel if exists, otherwise null.
     *
     * @param string $name
     * @return ChannelInterface
     * @throws ChannelException
     */
    public function get(string $name): ChannelInterface
    {
        if (isset($this->createdChannels[$name])) {
            return $this->createdChannels[$name];
        }
        
        if (!array_key_exists($name, $this->channels)) {
            throw new ChannelNotFoundException($name);
        }

        if ($this->channels[$name] instanceof ChannelInterface) {
            return $this->channels[$name];
        }
        
        // create channel from callable:
        if (is_callable($this->channels[$name])) {
            try {
                return $this->createdChannels[$name] = $this->autowire->call(
                    $this->channels[$name],
                    ['name' => $name]
                );
            } catch (Throwable $e) {
                throw new ChannelCreateException($name, $e->getMessage(), (int)$e->getCode(), $e);
            }
        }
        
        // create channel from factory:
        if (!isset($this->channels[$name]['factory'])) {
            throw new ChannelCreateException($name, sprintf('Missing channel factory on "%s" channel', $name));
        }
        
        try {
            $factory = $this->autowire->resolve($this->channels[$name]['factory']);
        } catch (AutowireException $e) {
            throw new ChannelCreateException($name, $e->getMessage(), (int)$e->getCode(), $e);
        }
        
        if (! $factory instanceof ChannelFactoryInterface) {
            throw new ChannelCreateException(
                $name,
                sprintf('Factory must be an instance of %s', ChannelFactoryInterface::class)
            );
        }
        
        $config = $this->channels[$name]['config'] ?? [];
        
        return $this->createdChannels[$name] = $factory->createChannel($name, $config);
    }
    
    /**
     * Returns all channel names.
     *
     * @return array
     */
    public function names(): array
    {
        return array_keys($this->channels);
    }
}