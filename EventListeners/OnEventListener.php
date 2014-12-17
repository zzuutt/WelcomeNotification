<?php
/*************************************************************************************/
/*      Module WelcomeNotification pour Thelia                                       */
/*                                                                                   */
/*      Copyright (©)                                                                */
/*      email : zzuutt34@free.fr                                                     */
/*                                                                                   */
/*                                                         test utf-8 ä,ü,ö,ç,é,â,µ  */
/*************************************************************************************/

namespace WelcomeNotification\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\Customer\CustomerEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\ParserInterface;
use Thelia\Log\Tlog;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\ConfigQuery;
use Thelia\Model\MessageQuery;
use Thelia\Model\LangQuery;

class OnEventListener extends BaseAction implements EventSubscriberInterface
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * @var MailerFactory
     */
    protected $mailer;

    /**
    * @param ParserInterface $parser
    */
    public function __construct(ParserInterface $parser, MailerFactory $mailer)
    {
        $this->parser = $parser;
        $this->mailer = $mailer;
    }

    /**
     * @param \Thelia\Core\Event\Order\OrderEvent $event
     */
    public function sendWelcomenotificationEmail(CustomerEvent $event)
    {
        $store_email = ConfigQuery::read('store_email');

        $store_name = ConfigQuery::read('store_name');

        if ($store_email) {

            $message = MessageQuery::create()
                ->filterByName('welcome_notification')
                ->findOne();

            if (false === $message) {
                throw new \Exception("Failed to load message 'welcome_notification'.");
            }

            $customer = $event->getCustomer();
            $this->parser->assign('customer_id', $customer->getId());
            $lang = LangQuery::create()->findPk($customer->getLang())->getLocale();
            $message->setLocale($lang);
                
            $customerEmail = $customer->getEmail();
            $customerFirstname = $customer->getFirstname();
            $customerLastname = $customer->getLastname();
            
            $instance = \Swift_Message::newInstance()
                ->addTo($customerEmail, $customerFirstname." ".$customerLastname)
                ->addFrom($store_email, ConfigQuery::read('store_name'))
            ;

            // Build subject and body

            $message->buildMessage($this->parser, $instance);

            $this->mailer->send($instance);
            
            Tlog::getInstance()->debug("Welcome email sent to customer: " . $customerEmail.", ".$customerFirstname." ".$customerLastname);
        }
    }

    /**
    * Returns an array of event names this subscriber wants to listen to.
    *
    * The array keys are event names and the value can be:
    *
    * * The method name to call (priority defaults to 0)
    * * An array composed of the method name to call and the priority
    * * An array of arrays composed of the method names to call and respective
    * priorities, or 0 if unset
    *
    * For instance:
    *
    * * array('eventName' => 'methodName')
    * * array('eventName' => array('methodName', $priority))
    * * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
    *
    * @return array The event names to listen to
    *
    * @api
    */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::AFTER_CREATECUSTOMER  => ['sendWelcomenotificationEmail', 129]
        ];
    }
}
