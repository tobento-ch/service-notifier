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

namespace Tobento\Service\Notifier\Mail;

use Tobento\Service\Notifier\ChannelFactoryInterface;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Notifier\Mail\Channel;
use Tobento\Service\Notifier\Exception\ChannelCreateException;
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\MailersInterface;
use Psr\Container\ContainerInterface;

/**
 * ChannelFactory
 */
class ChannelFactory implements ChannelFactoryInterface
{
    /**
     * Create a new ChannelFactory.
     *
     * @param MailerInterface $mailer
     * @param ContainerInterface $container
     */
    public function __construct(
        protected MailerInterface $mailer,
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
        if (
            isset($config['mailer'])
            && $this->mailer instanceof MailersInterface
        ) {
            $mailer = $this->mailer->mailer($config['mailer']);
            
            if (is_null($mailer)) {
                throw new ChannelCreateException(
                    name: $name,
                    message: sprintf('mailer %s does not exist.', $config['mailer'])
                );                
            }
            
            return new Channel(name: $name, mailer: $mailer, container: $this->container);
        }
        
        return new Channel(name: $name, mailer: $this->mailer, container: $this->container);
    }
}