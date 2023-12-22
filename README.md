# Notifier Service

Notifier interface for PHP applications using [Symfony Notifier](https://github.com/symfony/notifier) as default implementation.

## Table of Contents

- [Getting started](#getting-started)
    - [Requirements](#requirements)
    - [Highlights](#highlights)
- [Documentation](#documentation)
    - [Basic Usage](#basic-usage)
        - [Creating And Sending Notifications](#creating-and-sending-notifications)
    - [Notifier](#notifier)
        - [Create Notifier](#create-notifier)
    - [Notifications](#notifications)
        - [Notification](#notification)
        - [Abstract Notification](#abstract-notification)
    - [Recipients](#recipients)
        - [Recipient](#recipient)
        - [User Recipient](#user-recipient)
    - [Channel](#channel)
        - [Mail Channel](#mail-channel)
            - [Mail Notification](#mail-notification)
            - [Mail Recipient](#mail-recipient)
            - [Mail Channel Factory](#mail-channel-factory)
        - [Sms Channel](#sms-channel)
            - [Sms Notification](#sms-notification)
            - [Sms Recipient](#sms-recipient)
        - [Chat Channel](#chat-channel)
            - [Chat Notification](#chat-notification)
            - [Chat Recipient](#chat-recipient)
        - [Push Channel](#push-channel)
            - [Push Notification](#push-notification)
            - [Push Recipient](#push-recipient)
        - [Storage Channel](#storage-channel)
            - [Storage Notification](#storage-notification)
            - [Storage Recipient](#storage-recipient)
            - [Accessing Storage Notifications](#accessing-storage-notifications)
    - [Channels](#channels)
        - [Default Channels](#default-channels)
        - [Lazy Channels](#lazy-channels)
    - [Queue](#queue)
    - [Events](#events)
- [Credits](#credits)
___

# Getting started

Add the latest version of the notifier service project running this command.

```
composer require tobento/service-notifier
```

## Requirements

- PHP 8.0 or greater

## Highlights

- Framework-agnostic, will work with any project
- Decoupled design

# Documentation

## Basic Usage

### Creating And Sending Notifications

Once you have [created the notifier](#create-notifier) you can create and send notifications:

```php
use Tobento\Service\Notifier\NotifierInterface;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Recipient;

class SomeService
{
    public function send(NotifierInterface $notifier): void
    {
        // Create a Notification that has to be sent:
        // using the "email" and "sms" channel
        $notification = (new Notification('New Invoice', ['email', 'sms']))
            ->content('You got a new invoice for 15 EUR.');

        // The receiver of the notification:
        $recipient = new Recipient(
            email: 'mail@example.com',
            phone: '15556666666',
        );

        // Send the notification to the recipient:
        $notifier->send($notification, $recipient);
    }
}
```

Check out the [Notifications](#notifications) section to learn more about the available notfications you can create or you might create your own notification class fitting your application.

Check out the [Recipients](#recipients) section to learn more about the available recipients you can create or you might create your own recipient class fitting your application.

## Notifier

### Create Notifier

```php
use Tobento\Service\Notifier\NotifierInterface;
use Tobento\Service\Notifier\Notifier;
use Tobento\Service\Notifier\ChannelsInterface;
use Tobento\Service\Notifier\Channels;
use Tobento\Service\Notifier\QueueHandlerInterface;

$notifier = new Notifier(
    channels: new Channels(), // ChannelsInterface
    
    // you may set a queue handler if you want to support queuing notifications:
    queueHandler: null, // null|QueueHandlerInterface
    
    // you may set an event dispatcher if you want to support events:
    eventDispatcher: null, // null|EventDispatcherInterface
);

var_dump($notifier instanceof NotifierInterface);
// bool(true)
```

Check out the [Channels](#channels) section to learn more about the available channels.

## Notifications

### Notification

The ```Notification::class``` may be used to create simple notification messages supporting all [channels](#channel).

```php
use Tobento\Service\Notifier\Notification;

$notification = new Notification(
    subject: 'New Invoice',
    content: 'You got a new invoice for 15 EUR.',
    channels: ['email', 'sms'],
);
```

If you want to support custom channels you may consider creating a custom notification!

**Available methods**

```php
use Tobento\Service\Notifier\Notification;

$notification = (new Notification())
    // you may prefer using the subject method:
    ->subject('New Invoice')
    // you may prefer using the content method:
    ->content('You got a new invoice for 15 EUR.')
    // you may specify a name for any later usage:
    ->name('New Invoice');
```

In addition, you may add messages for specific channels:

```php
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Message;

$notification = (new Notification(
    subject: 'General subject used if no specific message',
    channels: ['email', 'sms'],
))
->addMessage('sms', new Message\Sms(
    subject: 'Specific sms message',
))
// or specific sms channel:
->addMessage('sms/vonage', new Message\Sms(
    subject: 'Specific sms message',
));
```

### Abstract Notification

Use the ```AbstractNotification::class``` if you want to create specific messages for each channel.

Simply extend from the ```AbstractNotification::class``` and add the message interfaces with its method you want to support.

Furthermore, any ```to``` methods such as ```toSms``` will receive a ```$recipient``` entity, the ```$channel``` name and you may request any service being resolved (autowired) by the container.

```php
use Tobento\Service\Notifier\AbstractNotification;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Message;

class OrderNotification extends AbstractNotification implements Message\ToSms
{
    /**
     * Create an order notification.
     *
     * @param Order $order
     */
    public function __construct(
        protected Order $order,
    ) {}
    
    /**
     * Returns the sms message.
     *
     * @param RecipientInterface $recipient
     * @param string $channel The channel name.
     * @return Message\SmsInterface
     */
    public function toSms(RecipientInterface $recipient, string $channel, SomeService $service): Message\SmsInterface
    {
        return new Message\Sms(
            subject: sprintf('Thanks for your order %s', $this->order->name),
        );
    }
}
```

## Recipients

### Recipient

The ```Recipient::class``` may be used to create a recipient supporting all [channels](#channel).

```php
use Tobento\Service\Notifier\Recipient;

$recipient = new Recipient(
    email: 'mail@example.com', // null|string
    phone: '15556666666', // null|string
    id: 'unique-id', // null|string|int
    type: 'users', // null|string
    locale: 'en', // string (en default)
    // you may set the channels the recipient prefers:
    channels: [],
);

// you may add specific addresses:
$recipient->addAddress(
    channel: 'chat/slack',
    address: ['key' => 'value'] // mixed
);
```

### User Recipient

The ```UserRecipient::class``` may be used if you have installed the [User Service](https://github.com/tobento-ch/service-user).

```php
use Tobento\Service\Notifier\UserRecipient;
use Tobento\Service\User\UserInterface;

$recipient = new UserRecipient(
    user: $user, // UserInterface
    channels: [],
);
```

## Channel

### Mail Channel

The mail channel uses the [Mail Service](https://github.com/tobento-ch/service-mail) to send notifications.

```php
use Tobento\Service\Notifier\Mail;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Mail\MailerInterface;
use Psr\Container\ContainerInterface;

$channel = new Mail\Channel(
    name: 'mail',
    mailer: $mailer, // MailerInterface
    container: $container, // ContainerInterface
);

var_dump($channel instanceof ChannelInterface);
// bool(true)
```

#### Mail Notification

To send mail notifications you have multiple options:

**Using the [Abstract Notification](#abstract-notification)**

Simply extend from the ```AbstractNotification::class``` and implement the ```ToMail``` interface. The interface requires a ```toMailHandler``` method which is already added on the ```AbstractNotification::class``` defining the ```toMail``` method as the message handler. You will just need to add the ```toMail``` method which will receive a ```$recipient``` entity, the ```$channel``` name and you may request any service being resolved (autowired) by the container.

```php
use Tobento\Service\Notifier\AbstractNotification;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Message\ToMail;
use Tobento\Service\Mail\Message;

class SampleNotification extends AbstractNotification implements ToMail
{
    /**
     * Returns the mail message.
     *
     * @param RecipientInterface $recipient
     * @param string $channel The channel name.
     * @return Message
     */
    public function toMail(RecipientInterface $recipient, string $channel, SomeService $service): Message
    {
        return (new Message())
            // not required if none is defined, the address will be added on sending:
            ->to('to@example.com')
            
            ->subject('Subject')
            //->textTemplate('welcome-text')
            //->htmlTemplate('welcome')
            //->text('Lorem Ipsum')
            ->html('<p>Lorem Ipsum</p>');
    }
}
```

If you do not have defined a [default from address](https://github.com/tobento-ch/service-mail#default-addresses-and-parameters), you will need to set it on each message:

```php
use Tobento\Service\Mail\Message;

$message = (new Message())
    ->from('from@example.com');
```

**Using the [Notification](#notification)**

```php
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Mail;

$notification = new Notification(
    subject: 'New Invoice',
    content: 'You got a new invoice for 15 EUR.',
);
    
// with specific mail message:
$notification = (new Notification())
    ->addMessage('mail', (new Mail\Message())
        ->subject('Subject')
        ->html('<p>Lorem Ipsum</p>')
    );
```

Check out the [Mail Message](https://github.com/tobento-ch/service-mail#message) section to learn more about mail messages.

#### Mail Recipient

When sending notifications via the mail channel, the channel will call the ```getAddressForChannel``` method on the recipient entity to get the email address when no were defined on the mail message.

```php
use Tobento\Service\Notifier\Recipient;
use Tobento\Service\Notifier\Address;
use Tobento\Service\Notifier\Notification;

$recipient = new Recipient(
    email: 'mail@example.com',
    // or
    email: new Address\Email('mail@example.com', 'Name'),
);

$address = $recipient->getAddressForChannel('mail', new Notification('subject'));

var_dump($address instanceof Address\EmailInterface);
// bool(true)
```

#### Mail Channel Factory

```php
use Tobento\Service\Notifier\Mail\ChannelFactory;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Mail\MailerInterface;
use Psr\Container\ContainerInterface;

$factory = new ChannelFactory(
    mailer: $mailer, // MailerInterface
    container: $container, // ContainerInterface
);

$channel = $factory->createChannel(name: 'mail');

// using a specific mailer:
$channel = $factory->createChannel(name: 'mail/mailchimp', config: [
    'mailer' => 'mailchimp',
]);

var_dump($channel instanceof ChannelInterface);
// bool(true)
```

### Sms Channel

Use the ```ChannelAdapter::class``` to create a SMS channel using the [Symfony SMS Channel](https://symfony.com/doc/current/notifier.html#sms-channel):

You will need to install any chat service you would like ```composer require symfony/vonage-notifier``` e.g.

```php
use Tobento\Service\Notifier\Symfony\ChannelAdapter;
use Tobento\Service\Notifier\ChannelInterface;
use Psr\Container\ContainerInterface;

$channel = new ChannelAdapter(
    name: 'sms/vonage',
    channel: new \Symfony\Component\Notifier\Channel\SmsChannel(
        transport: new \Symfony\Component\Notifier\Bridge\Vonage\VonageTransport(
            apiKey: '******',
            apiSecret: '******',
            from: 'FROM',
        )
    ),
    container: $container, // ContainerInterface
);

var_dump($channel instanceof ChannelInterface);
// bool(true)
```

#### Sms Notification

To send SMS notifications you have multiple options:

**Using the [Abstract Notification](#abstract-notification)**

Simply extend from the ```AbstractNotification::class``` and implement the ```ToSms``` interface. The interface requires a ```toSmsHandler``` method which is already added on the ```AbstractNotification::class``` defining the ```toSms``` method as the message handler. You will just need to add the ```toSms``` method which will receive a ```$recipient``` entity, the ```$channel``` name and you may request any service being resolved (autowired) by the container.

```php
use Tobento\Service\Notifier\AbstractNotification;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Message;

class SampleNotification extends AbstractNotification implements Message\ToSms
{
    /**
     * Returns the sms message.
     *
     * @param RecipientInterface $recipient
     * @param string $channel The channel name.
     * @return Message\SmsInterface
     */
    public function toSms(RecipientInterface $recipient, string $channel): Message\SmsInterface
    {
        return new Message\Sms(
            subject: 'Sms message',
        );
        
        // you may set a specific to address:
        return new Message\Sms(
            subject: 'Sms message',
            to: $recipient->getAddressForChannel('sms/vonage'),
        );
    }
}
```

**Using the [Notification](#notification)**

```php
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Message;

$notification = new Notification(
    subject: 'New Invoice',
    content: 'You got a new invoice for 15 EUR.',
);
    
// with specific sms message:
$notification = (new Notification())
    ->addMessage('sms', new Message\Sms(
        subject: 'Sms message',
    ));
```

#### Sms Recipient

When sending notifications via the sms channel, the channel will call the ```getAddressForChannel``` method on the recipient entity to get the sms address when no specific were defined.

```php
use Tobento\Service\Notifier\Recipient;
use Tobento\Service\Notifier\Address;
use Tobento\Service\Notifier\Notification;

$recipient = new Recipient(
    phone: '15556666666',
    // or
    phone: new Address\Phone('15556666666', 'Name'),
);

$address = $recipient->getAddressForChannel('sms', new Notification('subject'));

var_dump($address instanceof Address\PhoneInterface);
// bool(true)
```

### Chat Channel

Use the ```ChannelAdapter::class``` to create a chat channel using the [Symfony Chat Channel](https://symfony.com/doc/current/notifier.html#chat-channel):

You will need to install any chat service you would like ```composer require symfony/slack-notifier``` e.g.

```php
use Tobento\Service\Notifier\Symfony\ChannelAdapter;
use Tobento\Service\Notifier\ChannelInterface;
use Psr\Container\ContainerInterface;

$channel = new ChannelAdapter(
    name: 'chat/slack',
    channel: new \Symfony\Component\Notifier\Channel\ChatChannel(
        transport: new \Symfony\Component\Notifier\Bridge\Slack\SlackTransport(
            accessToken: '******',
        )
    ),
    container: $container, // ContainerInterface
);

var_dump($channel instanceof ChannelInterface);
// bool(true)
```

#### Chat Notification

To send chat notifications you have multiple options:

**Using the [Abstract Notification](#abstract-notification)**

Simply extend from the ```AbstractNotification::class``` and implement the ```ToChat``` interface. The interface requires a ```toChatHandler``` method which is already added on the ```AbstractNotification::class``` defining the ```toChat``` method as the message handler. You will just need to add the ```toChat``` method which will receive a ```$recipient``` entity, the ```$channel``` name and you may request any service being resolved (autowired) by the container.

```php
use Tobento\Service\Notifier\AbstractNotification;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Message;
use Tobento\Service\Notifier\Symfony\MessageOptions;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;

class SampleNotification extends AbstractNotification implements Message\ToChat
{
    /**
     * Returns the chat message.
     *
     * @param RecipientInterface $recipient
     * @param string $channel The channel name.
     * @return Message\ChatInterface
     */
    public function toChat(RecipientInterface $recipient, string $channel): Message\ChatInterface
    {
        if ($channel === 'chat/slack') {
            // you may set message options:
            $options = new SlackOptions();

            return new (Message\Chat('Chat message'))
                ->parameter(new MessageOptions($options));
        }
        
        // for any other chat channel:
        return new Message\Chat(
            subject: 'Chat message',
        );
    }
}
```

**Using the [Notification](#notification)**

```php
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Message;

$notification = new Notification(
    subject: 'New Invoice',
    content: 'You got a new invoice for 15 EUR.',
);
    
// with specific chat message:
$notification = (new Notification())
    ->addMessage('chat/slack', new Message\Chat(
        subject: 'Chat message',
    ));
```

#### Chat Recipient

When sending notifications via the chat channel, you may add a specific channel address with parameters for later usage.

```php
use Tobento\Service\Notifier\Recipient;
use Tobento\Service\Notifier\Address;
use Tobento\Service\Notifier\Notification;

$recipient = (new Recipient())
    ->addAddress('chat/slack', ['channel' => 'name']);

$address = $recipient->getAddressForChannel('chat/slack', new Notification('subject'));

var_dump($address);
// array(1) { ["channel"]=> string(4) "name" }
```

**Sample notification with address usage**

```php
use Tobento\Service\Notifier\AbstractNotification;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Message;
use Tobento\Service\Notifier\Symfony\MessageOptions;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;

class SampleNotification extends AbstractNotification implements Message\ToChat
{
    /**
     * Returns the chat message.
     *
     * @param RecipientInterface $recipient
     * @param string $channel The channel name.
     * @return Message\ChatInterface
     */
    public function toChat(RecipientInterface $recipient, string $channel): Message\ChatInterface
    {
        if ($channel === 'chat/slack') {
            $address = $recipient->getAddressForChannel('chat/slack', $this);
            
            $options = new SlackOptions([
                'recipient_id' => $address['channel'] ?? null,
            ]);

            return new (Message\Chat('Chat message'))
                ->parameter(new MessageOptions($options));
        }
        
        // for any other chat channel:
        return new Message\Chat(
            subject: 'Chat message',
        );
    }
}
```

### Push Channel

Use the ```ChannelAdapter::class``` to create a push channel using the [Symfony Push Channel](https://symfony.com/doc/current/notifier.html#push-channel):

You will need to install any push service you would like ```composer require symfony/one-signal-notifier``` e.g.

```php
use Tobento\Service\Notifier\Symfony\ChannelAdapter;
use Tobento\Service\Notifier\ChannelInterface;
use Psr\Container\ContainerInterface;

$channel = new ChannelAdapter(
    name: 'push/one-signal',
    channel: new \Symfony\Component\Notifier\Channel\ChatChannel(
        transport: new \Symfony\Component\Notifier\Bridge\OneSignal\OneSignalTransport(
            appId: '******',
            apiKey: '******',
        )
    ),
    container: $container, // ContainerInterface
);

var_dump($channel instanceof ChannelInterface);
// bool(true)
```

#### Push Notification

To send push notifications you have multiple options:

**Using the [Abstract Notification](#abstract-notification)**

Simply extend from the ```AbstractNotification::class``` and implement the ```ToPush``` interface. The interface requires a ```toPushHandler``` method which is already added on the ```AbstractNotification::class``` defining the ```toPush``` method as the message handler. You will just need to add the ```toPush``` method which will receive a ```$recipient``` entity, the ```$channel``` name and you may request any service being resolved (autowired) by the container.

```php
use Tobento\Service\Notifier\AbstractNotification;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Message;
use Tobento\Service\Notifier\Symfony\MessageOptions;
use Symfony\Component\Notifier\Bridge\OneSignal\OneSignalOptions;

class SampleNotification extends AbstractNotification implements Message\ToPush
{
    /**
     * Returns the push message.
     *
     * @param RecipientInterface $recipient
     * @param string $channel The channel name.
     * @return Message\PushInterface
     */
    public function toPush(RecipientInterface $recipient, string $channel): Message\PushInterface
    {
        if ($channel === 'push/one-signal') {
            // you may set message options:
            $options = new OneSignalOptions([]);

            return new (Message\Push('Push subject'))
                ->content('Push content')
                ->parameter(new MessageOptions($options));
        }
        
        // for any other push channel:
        return new Message\Push(
            subject: 'Push subject',
            content: 'Push content',
        );
    }
}
```

**Using the [Notification](#notification)**

```php
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Message;

$notification = new Notification(
    subject: 'New Invoice',
    content: 'You got a new invoice for 15 EUR.',
);
    
// with specific chat message:
$notification = (new Notification())
    ->addMessage('push/one-signal', new Message\Push(
        subject: 'Push subject',
        content: 'Push content',
    ));
```

#### Push Recipient

When sending notifications via the push channel, you may add a specific channel address with parameters for later usage.

```php
use Tobento\Service\Notifier\Recipient;
use Tobento\Service\Notifier\Address;
use Tobento\Service\Notifier\Notification;

$recipient = (new Recipient())
    ->addAddress('push/one-signal', ['recipient_id' => 'id']);

$address = $recipient->getAddressForChannel('push/one-signal', new Notification('subject'));

var_dump($address);
// array(1) { ["recipient_id"]=> string(2) "id" }
```

**Sample notification with address usage**

```php
use Tobento\Service\Notifier\AbstractNotification;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Message;
use Tobento\Service\Notifier\Symfony\MessageOptions;
use Symfony\Component\Notifier\Bridge\OneSignal\OneSignalOptions;

class SampleNotification extends AbstractNotification implements Message\ToPush
{
    /**
     * Returns the push message.
     *
     * @param RecipientInterface $recipient
     * @param string $channel The channel name.
     * @return Message\PushInterface
     */
    public function toPush(RecipientInterface $recipient, string $channel): Message\PushInterface
    {
        if ($channel === 'push/one-signal') {
            $address = $recipient->getAddressForChannel('push/one-signal', $this);
            
            $options = new OneSignalOptions([
                'recipient_id' => $address['recipient_id'] ?? null,
            ]);

            return new (Message\Push('Push subject'))
                ->content('Push content')
                ->parameter(new MessageOptions($options));
        }
        
        // for any other push channel:
        return new Message\Push(
            subject: 'Push subject',
            content: 'Push content',
        );
    }
}
```

### Storage Channel

The storage channel stores the notification information in the configured storage repository.

```php
use Tobento\Service\Notifier\Storage;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Repository\RepositoryInterface;
use Psr\Container\ContainerInterface;

$channel = new Storage\Channel(
    name: 'storage/database',
    repository: $repository, // RepositoryInterface
    container: $container, // ContainerInterface
);

var_dump($channel instanceof ChannelInterface);
// bool(true)
```

Check out the [Repository Service](https://github.com/tobento-ch/service-repository) to learn more about it.

The storage needs to have the following table columns:

| Column | Type | Description |
| --- | --- | --- |
| ```name``` | varchar(255) | Used to store the notification name |
| ```recipient_id``` | varchar(36) | Used to store the recipient id |
| ```recipient_type``` | varchar(255) | Used to store the recipient type |
| ```data``` | json | Used to store the message data |
| ```read_at``` | datetime | Used to store date read at |
| ```created_at``` | datetime | Used to store date created at |

**Storage Repository**

You may use the provided ```StorageRepository::class``` as repository implementation:

You will need to install the service:

```composer require tobento/service-repository-storage```

```php
use Tobento\Service\Notifier\Storage;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Repository\RepositoryInterface;
use Tobento\Service\Storage\StorageInterface;
use Psr\Container\ContainerInterface;

$channel = new Storage\Channel(
    name: 'storage/database',
    repository: new StorageRepository(
        storage: $storage, // StorageInterface
        table: 'notifications',
    ),
    container: $container, // ContainerInterface
);
```

Check out the [Storage Service - Storages](https://github.com/tobento-ch/service-storage#storages) for the available storages.

Check out the [Repository Storage Service](https://github.com/tobento-ch/service-repository-storage) to learn more about it in general.

#### Storage Notification

To send Storage notifications you have multiple options:

**Using the [Abstract Notification](#abstract-notification)**

Simply extend from the ```AbstractNotification::class``` and implement the ```ToStorage``` interface. The interface requires a ```toStorageHandler``` method which is already added on the ```AbstractNotification::class``` defining the ```toStorage``` method as the message handler. You will just need to add the ```toStorage``` method which will receive a ```$recipient``` entity, the ```$channel``` name and you may request any service being resolved (autowired) by the container.

```php
use Tobento\Service\Notifier\AbstractNotification;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Message;

class SampleNotification extends AbstractNotification implements Message\ToStorage
{
    /**
     * Returns the storage message.
     *
     * @param RecipientInterface $recipient
     * @param string $channel The channel name.
     * @return Message\StorageInterface
     */
    public function toStorage(RecipientInterface $recipient, string $channel): Message\StorageInterface
    {
        return new Message\Storage(data: [
            'order_id' => $this->order->id,
        ]);
    }
}
```

**Using the [Notification](#notification)**

```php
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Message;

$notification = new Notification(
    subject: 'New Invoice',
    content: 'You got a new invoice for 15 EUR.',
);
    
// with specific storage message:
$notification = (new Notification())
    ->addMessage('storage', new Message\Storage([
        'foo' => 'bar',
    ]));
```

#### Storage Recipient

When sending notifications via the storage channel, the channel will store the ```$recipient->getId()``` and ```$recipient->getType()``` values which you can later use to fetch notifications:

```php
// channel will store on sending:
$repository->create([
    'name' => $notification->getName(),
    'recipient_id' => $recipient->getId(),
    'recipient_type' => $recipient->getType(),
    'data' => $message->getData(),
    'read_at' => null,
    'created_at' => null,
]);
```

#### Accessing Storage Notifications

Once notifications are store, you can retrieve the notifications using the repository from the channel:

```php
$channel = $channels->get(name: 'storage/database');

$entities = $channel->repository()->findAll(where: [
    'recipient_id' => $userId,
    //'recipient_type' => 'user',
]);
```

## Channels

### Default Channels

```php
use Tobento\Service\Notifier\Channels;
use Tobento\Service\Notifier\ChannelsInterface;
use Tobento\Service\Notifier\ChannelInterface;

$channels = new Channels(
    $channel, // ChannelInterface
    $anotherChannel, // ChannelInterface
);

var_dump($channels instanceof ChannelsInterface);
// bool(true)
```

### Lazy Channels

The ```LazyChannels::class``` creates the channels only on demand.

```php
use Tobento\Service\Notifier\LazyQueues;
use Tobento\Service\Notifier\ChannelsInterface;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Notifier\ChannelFactoryInterface;
use Tobento\Service\Notifier\Symfony;
use Psr\Container\ContainerInterface;

$channels = new LazyChannels(
    container: $container, // ContainerInterface
    channels: [
        // using a factory:
        'sms' => [
            // factory must implement ChannelFactoryInterface
            'factory' => Symfony\ChannelFactory::class,
            'config' => [
                'dsn' => 'vonage://KEY:SECRET@default?from=FROM',
                'channel' => \Symfony\Component\Notifier\Channel\SmsChannel::class,
            ],
        ],
        
        // using a closure:
        'mail' => static function (string $name, ContainerInterface $c): ChannelInterface {
            // create channel ...
            return $channel;
        },
        
        // or you may sometimes just create the channel (not lazy):
        'sms/null' => new NullChannel(name: 'sms'),
    ],
);

var_dump($channels instanceof ChannelsInterface);
// bool(true)
```

## Queue

You may queue your notification by just adding the ```Queue::class``` parameter: 

```php
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Parameter\Queue;

$notification = (new Notification())
    ->parameter(new Queue(
        // you may specify the queue to be used:
        name: 'secondary',
        
        // you may specify a delay in seconds:
        delay: 30,
        
        // you may specify how many times to retry:
        retry: 3,
        
        // you may specify a priority:
        priority: 100,
        
        // you may specify if you want to encrypt the message:
        encrypt: true,
    ));
```

**Requirements**

To support queuing notifications you will need to pass a queue handler to the notifier.

Consider using the default queue handler using the [Queue Service](https://github.com/tobento-ch/service-queue):

**First, install the queue service:**

```php
composer require tobento/service-queue
```

**Finally, pass the queue handler to the notifier:**

```php
use Tobento\Service\Notifier\NotifierInterface;
use Tobento\Service\Notifier\Notifier;
use Tobento\Service\Notifier\ChannelsInterface;
use Tobento\Service\Notifier\Channels;
use Tobento\Service\Notifier\QueueHandler;
use Tobento\Service\Queue\QueueInterface;

$notifier = new Notifier(
    channels: new Channels(), // ChannelsInterface
    
    // set a queue handler:
    queueHandler: new QueueHandler(
        queue: $queue, // QueueInterface
        // you may define the default queue used if no specific is defined on the notification.
        queueName: 'mails', // null|string
    ),
);
```

## Events

You may listen to the following events if your notifier is configured to support it.

| Event | Description |
| --- | --- |
| ```Tobento\Service\Notifier\Event\NotificationSending::class``` | The Event will be fired **before** sending the notification. |
| ```Tobento\Service\Notifier\Event\NotificationSent::class``` | The Event is fired **after** the notification is sent. |
| ```Tobento\Service\Notifier\Event\NotificationQueued::class``` | The Event will be fired **after** queuing the notification. |

When using the [default notifier](#create-notifier) just pass an event dispatcher.

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)
- [Symfony](https://symfony.com)