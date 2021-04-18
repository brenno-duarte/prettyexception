# PHP PrettyException for Whoops

PrettyException is a component that helps to manipulate the Whoops component.

## How to use

```php
use PrettyException\PrettyException;

$exception = new PrettyException();
```

You can return the error using:

- `inPretty`: returns with the Whoops screen
- `inJson`: returns the exception in JSON
- `inText`: returns the exception in text
- `inXml`: returns the exception in XML

Then use the `run` method.

```php
$exception = new PrettyException();
$exception->inPretty()->run();
```

## Customizing the exception screen

It is possible to make some customizations on the exception screen.

`setTitle`: Sets the title for the error page.

```php
$exception->setTitle('New Exception');
```

`table`: Adds a key=>value table of arbitrary data, labeled by $label, to the output. Useful where you want to display contextual data along with the error, about your application or project.

```php
$exception->table('New Exception', [
    'exc' => 'test',
    'exc2' => 10
]);
```

`tableCallback`: Similar to PrettyPageHandler::addDataTable, but accepts a callable that will be called only when rendering an exception. This allows you to gather additional data that may not be available very early in the process.

```php
$exception->tableCallback('New Exception Callback', function(\Whoops\Exception\Inspector $inspector) {
    $data = array();
    $exception = $inspector->getException();
    if ($exception instanceof SomeSpecificException) {
        $data['Important exception data'] = $exception->getSomeSpecificData();
    }
    $data['Exception class'] = get_class($exception);
    $data['Exception code'] = $exception->getCode();
    return $data;
});
```

`closeTableAndRun`: Closes the table and executes the exception. Optionally, you can add a comment.

```php
$exception->closeTableAndRun($comment);
```

## Running exception in other formats

In some cases like AJAX requests or using the command line, it is possible to display exceptions using `runIfAjax` and` runIfCli`.

`runIfAjax` will be executed if there are any AJAX requests and returned in JSON. Otherwise, the standard exception screen will be displayed.

`runIfCli` will only be run from the PHP command line. 

```php
$run->runIfAjax('Exception in AJAX');
$run->runIfCli('Exception in CLI');
```

## License

MIT