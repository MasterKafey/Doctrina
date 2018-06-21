<?php

namespace AppBundle\Service\Util\Console;

use AppBundle\Service\Util\AbstractContainerAware;
use AppBundle\Service\Util\Console\Model\Message;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Console
 * @package AppBundle\Service\Util\Console
 */
class Console extends AbstractContainerAware
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var array
     */
    protected $messages;

    /**
     * @var int
     */
    protected $index;

    /**
     * Console constructor.
     */
    public function __construct()
    {
        $this->index = 0;
    }

    /**
     * @param Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
        $this->session->start();
        $this->messages = $this->session->getFlashBag()->get('console_messages');
        if (null === $this->messages) {
            $this->messages = array();
        }
    }

    /**
     * Add a message to the console
     *
     * @param string $message Content of the message
     * @param string $type Type of the message (SUCCESS|INFO|WARNING|DANGER)
     * @param string $icon Icon of the message (fa awesome)
     * @param array|null $params Parameters for translation
     * @param null $number Number of element for transChoice
     * @param string|null translation domaine
     */
    public function add($message, $type = Message::TYPE_INFO, $icon = 'far fa-envelope', array $params = array(), $number = null, $domaine = null)
    {
        if (null === $number) {
            $text = $this->container->get('translator')->trans($message, $params, $domaine === null ? 'console' : $domaine);
        } else {
            $text = $this->container->get('translator')->transChoice($message, $number, $params, $domaine === null ? 'console' : $domaine);
        }

        $this->messages[] = new Message($text, $type, $icon);
        $this->saveMessages();
    }

    /**
     * @return Message[]
     * @throws \Exception
     */
    public
    function getMessages()
    {
        $messages = $this->messages;
        $this->messages = array();
        $this->saveMessages();

        return $messages;
    }

    /**
     * Save Messages to be retrieved through session
     */
    protected
    function saveMessages()
    {
        $this->session->getFlashBag()->set('console_messages', $this->messages);
    }
}
