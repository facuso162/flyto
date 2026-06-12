Para ver informacion de un error

```php
echo json_encode([
    'error' => $exception->getMessage(),
    'file' => $exception->getFile(),
    'line' => $exception->getLine(),
    'trace' => $exception->getTraceAsString(),
]);
```
