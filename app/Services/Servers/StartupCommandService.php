<?php

namespace App\Services\Servers;

use App\Models\Server;

class StartupCommandService
{
    /**
     * Generates a startup command for a given server instance.
     */
    public function handle(Server $server, bool $hideAllValues = false): string
    {
        $find = ['{{SERVER_MEMORY}}', '{{SERVER_IP}}', '{{SERVER_PORT}}'];
        $replace = [
            (string) $server->memory,
            $server->allocation->ip ?? '127.0.0.1',
            (string) ($server->allocation->port ?? '0'),
        ];

        foreach ($server->variables as $variable) {
            $find[] = '{{' . $variable->env_variable . '}}';
            $serverValue = $variable->serverVariable->first()?->variable_value ?? $variable->default_value;
            $replace[] = ($variable->user_viewable && !$hideAllValues) ? $serverValue : '[hidden]';
        }

        return str_replace($find, $replace, $server->startup);
    }
}
