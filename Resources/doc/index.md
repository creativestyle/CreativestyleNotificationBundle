Getting Started With CreativestyleNotificationBundle
====================================================

## Installation

1. Download CreativestyleNotificationBundle using composer
2. Enable the Bundle
3. Create your Notification class
4. Configure your application's config.yml
5. Prepare your email template
6. Create your notification build strategy
7. Register your notification build strategy
8. Update your database schema
9. Dispatch your notification

### Step 1: Download CreativestyleNotificationBundle using composer

Add CreativestyleNotificationBundle by running the command:

``` bash
$ php composer.phar require creativestyle/notification-bundle "v1.0"
```

Composer will install the bundle to your project's `vendor/creativestyle` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Creativestyle\Bundle\NotificationBundle\CreativestyleNotificationBundle(),
    );
}
```

### Step 3: Create your Notification class

#### Doctrine ORM Notification class

##### Annotations

``` php
<?php
// src/Acme/AppBundle/Entity/Notification.php

use Doctrine\ORM\Mapping as ORM;
use Creativestyle\Component\Notification\Model\Notification as BaseNotification;

/**
 * @ORM\Entity
 * @ORM\Table(name="app_notification")
 */
class Notification extends BaseNotification
{
}
```

##### xml

If you use xml to configure Doctrine you must add two files. The Entity and the orm.xml:

```php
<?php
// src/Acme/AppBundle/Entity/Notification.php

use Creativestyle\Component\Notification\Model\Notification as BaseNotification;

class Notification extends BaseNotification
{
}
```

```xml
<?xml version="1.0" encoding="UTF-8" ?>

<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:gedmo="http://gediminasm.org/schemas/orm/doctrine-extensions-mapping"
>

    <entity name="Acme/AppBundle/Entity/Notification" table="app_notification">
    </entity>

</doctrine-mapping>
```

### Step 4: Configure your application's config.yml


``` yaml
# app/config/config.yml
creativestyle_notification:
    notification:
        insite:
            enable: false
            prerender: false
            model_class: Creativestyle\Component\Notification\Model\InsiteNotification
            templates: []
    notificator:
        database:
            enable: false
        email:
            enable: true
            templates:
                sth_happens_with_object: "AcmeAppBundle:Notification/Email:sthHappensWithObject.html.twig"
                sth_other_happens_with_object: "AcmeAppBundle:Notification/Email:sthOtherHappensWithObject.html.twig"
```

### Step 5: Prepare your email template

``` html+jinja
{# AcmeAppBundle:Notification/Email:sthHappensWithObject.html.twig #}
{% block subject %}
    {% autoescape false %}
        New notification
    {% endautoescape %}
{% endblock %}

{% block body_text %}
    Hello {{ notification.subscriber.username }}
    Something happens with {{ object.title }}
{% endblock %}

{% block body_html %}
     <h1>Hello {{ notification.subscriber.username }}</h1>
     <p>Something happens with {{ object.title }}</p>
{% endblock %}
```

### Step 6: Create your notification build strategy

``` php
<?php
// src/Acme/AppBundle/Notification/BuildStrategy/MyObjectBuildStrategy.php

namespace Acme\AppBundle\Notification\BuildStrategy;

use Creativestyle\Component\Notification\Builder\Strategy\BuildStrategyInterface;

use Acme\AppBundle\Model\MyObjectInterface;

class MyObjectBuildStrategy implements BuildStrategyInterface
{
    protected $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function createNew($object)
    {
        if (!$object instanceof MyObjectInterface) {
            throw new \InvalidArgumentException('MyObject expected');
        }

        $notification = new $this->class();
        $notification
            ->setObject($object)
            ->setObjectId($object->getId())
            ->setObjectType(get_class($object))
            ->setSubscriber($object->getUser()) // Symfony UserInterface
        ;

        return $notification;
    }
}
```

### Step 7: Register your notification build strategy

Next to do is register your notification build strategy as a service

In YAML:

``` yaml
# src/Acme/AppBundle/Resources/config/services.yml
  notification.build_strategy.thesis:
    class: Acme\AppBundle\Notification\BuildStrategy\MyObjectBuildStrategy
    arguments:
      - "Acme/AppBundle/Entity/Notification"
    tags:
      - {name: creativestyle_notification.build_strategy, type="sth_happens_with_object" }
      - {name: creativestyle_notification.build_strategy, type="sth_other_happens_with_object" }

```

### Step 8: Update your database schema

Now that the bundle is configured, the last thing you need to do is update your
database schema because you have added a new entity which you created in Step 4.

For ORM run the following command.

``` bash
$ php app/console doctrine:schema:update --force
```

### Step 9: Dispatch your notification

Now that you have activated and configured the bundle, all that is left to do is
to dispatch your notification when required event occurs.

``` php
<?php
// src/Acme/AppBundle/Controller/MyObjectController.php

namespace Acme\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MyObjectController extends Controller
{
    public function doSomethingWithObjectAction()
    {
        // ...
        $notificator = $this->get('creativestyle.notification_dispatcher');
        $notificator->notifyAbout($myObject, 'sth_happens_with_object');
        // ...
    }
}


### Next Steps

Now that you have completed the basic installation and configuration of the
Bundle.

The following documents are available:

- [Enable Insite Notification](enable_insite.md)
