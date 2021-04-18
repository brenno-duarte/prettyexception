<?php

namespace PrettyException;

use RuntimeException;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;

class PrettyException
{
    /**
     * @var Run
     */
    private Run $run;

    /**
     * @var string
     */
    private string $comment;

    /**
     * @var PrettyPageHandler
     */
    private PrettyPageHandler $handler;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->run = new Run();
        $this->handler = new PrettyPageHandler();
    }

    /**
     * @param string $label
     * @param array $data
     * 
     * @return PrettyException
     */
    public function table(string $label, array $data): PrettyException
    {
        $this->handler->addDataTable($label, $data);
        $this->handler->setApplicationPaths([__FILE__]);

        return $this;
    }

    /**
     * @param string $label
     * @param callable $callback
     * 
     * @return PrettyException
     */
    public function tableCallback(string $label, callable $callback): PrettyException
    {
        $this->handler->addDataTableCallback($label, $callback);

        return $this;
    }

    /**
     * @param string $title
     * 
     * @return PrettyException
     */
    public function setTitle(string $title): PrettyException
    {
        $this->handler->setPageTitle($title);

        return $this;
    }

    /**
     * @return mixed
     */
    public function closeTableAndRun(string $comment = "")
    {
        $this->comment = $comment;
        $this->run->pushHandler($this->handler);

        $this->run->pushHandler(function ($exception, $inspector, $run, $comment) {
            $inspector->getFrames()->map(function ($frame) {
                if ($function = $frame->getFunction()) {
                    $frame->addComment($this->comment . ": " . $function, 'cpt-obvious');
                }

                return $frame;
            });
        });

        $this->run->register();
    }

    /**
     * @param string $message
     * 
     * @throws RuntimeException 
     */
    public function runIfAjax(string $message)
    {
        $this->run->pushHandler(new PrettyPageHandler());

        if (\Whoops\Util\Misc::isAjaxRequest()) {
            $jsonHandler = new JsonResponseHandler();
            $jsonHandler->setJsonApi(true);

            $this->run->pushHandler($jsonHandler);
        }

        $this->run->register();
        throw new RuntimeException($message);
    }

    /**
     * @param string $message
     * 
     * @throws RuntimeException 
     */
    public function runIfCli(string $message)
    {
        $this->run->pushHandler(new PrettyPageHandler());

        if (\Whoops\Util\Misc::isCommandLine()) {
            $handler = new \Whoops\Handler\PlainTextHandler();
            $this->run->pushHandler($handler);
        }

        $this->run->register();
        throw new RuntimeException($message);
    }

    /**
     * @return PrettyException
     */
    public function inPretty(): PrettyException
    {
        $this->run->prependHandler(new \Whoops\Handler\PrettyPageHandler);

        return $this;
    }

    /**
     * @return PrettyException
     */
    public function inText(): PrettyException
    {
        $this->run->prependHandler(new \Whoops\Handler\PlainTextHandler);

        return $this;
    }

    /**
     * @return PrettyException
     */
    public function inJson(): PrettyException
    {
        $this->run->prependHandler(new \Whoops\Handler\JsonResponseHandler);

        return $this;
    }

    /**
     * @return PrettyException
     */
    public function inXml(): PrettyException
    {
        $this->run->prependHandler(new \Whoops\Handler\XmlResponseHandler);

        return $this;
    }

    /**
     * @param string $handle
     */
    public function run()
    {
        $this->run->register();
    }
}
