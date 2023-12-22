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

namespace Tobento\Service\Notifier\Symfony;

use Psr\Container\ContainerInterface;
use Tobento\Service\Notifier\ChannelFactoryInterface;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Notifier\Exception\ChannelCreateException;
use Tobento\Service\Autowire\Autowire;
use Tobento\Service\Autowire\AutowireException;
use Symfony\Component\Notifier\Transport\TransportInterface;
use Symfony\Component\Notifier\Transport\NullTransport;
use Symfony\Component\Notifier\Transport;
use Throwable;

/**
 * ChannelFactory
 */
class ChannelFactory implements ChannelFactoryInterface
{
    /**
     * Create a new ChannelFactory.
     *
     * @param ContainerInterface $container
     */
    public function __construct(
        protected ContainerInterface $container,
    ) {}
    
    /**
     * Create a new channel based on the configuration.
     *
     * @param string $name
     * @param array $config
     * @return ChannelInterface
     * @throws ChannelCreateException
     */
    public function createChannel(string $name, array $config = []): ChannelInterface
    {
        if (empty($config)) {
            $transport = new NullTransport();
        } else {
            try {
                $transport = $this->createTransportFromConfig($name, $config);
            } catch (ChannelCreateException $e) {
                throw $e;
            } catch (Throwable $e) {
                throw new ChannelCreateException(
                    name: $name,
                    message: $e->getMessage(),
                    code: (int)$e->getCode(),
                    previous: $e,
                );
            }
        }
        
        if (!isset($config['channel'])) {
            throw new ChannelCreateException(
                name: $name,
                message: sprintf('Channel config "channel" for name %s does not exist', $name)
            );
        }
        
        try {
            $channel = (new Autowire($this->container))->resolve($config['channel'], ['transport' => $transport]);
        } catch (AutowireException $e) {
            throw new ChannelCreateException(
                name: $name,
                message: $e->getMessage(), 
                code: $e->getCode(),
                previous: $e,
            );
        }
        
        return new ChannelAdapter(
            name: $name,
            channel: $channel,
            container: $this->container,
            config: $config['defaults'] ?? [],
        );
    }
    
    /**
     * Creates the transport from the config.
     *
     * @param string $name The channel name.
     * @param array $config
     * @return TransportInterface
     */
    protected function createTransportFromConfig(string $name, array $config = []): TransportInterface
    {
        if (isset($config['dsn']) && is_string($config['dsn'])) {
            return Transport::fromDsn($config['dsn']);
        }
        
        throw new ChannelCreateException(
            name: $name,
            message: sprintf('Channel config "dsn" for name %s does not exist', $name)
        );
    }
}