<?php

namespace Eccube\EventListener;

use Eccube\Common\EccubeConfig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class IpAddrListener implements EventSubscriberInterface
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $allowHosts = $this->eccubeConfig['eccube_admin_allow_hosts'];

        if (empty($allowHosts)) {
            return;
        }

        if (array_search($event->getRequest()->getClientIp(), $allowHosts) === false) {
            throw new AccessDeniedHttpException();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onKernelRequest', 512],
        ];
    }
}
