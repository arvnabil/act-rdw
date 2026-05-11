<?php
require 'vendor/autoload.php';
$reflection = new ReflectionClass(\Filament\Actions\ImportAction::class);
$methods = [];
foreach ($reflection->getMethods() as $method) {
    $methods[] = $method->getName();
}
file_put_contents('methods.txt', implode("\n", $methods));
