<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Alert extends Component
{
    /**
     * Type d'alerte : success, error, warning, info
     */
    public string $type;
    
    /**
     * Message à afficher
     */
    public ?string $message;
    
    /**
     * Titre optionnel de l'alerte
     */
    public ?string $title;

    /**
     * Créer une nouvelle instance du composant
     */
    public function __construct(string $type = 'info', ?string $message = null, ?string $title = null)
    {
        $this->type = $type;
        $this->message = $message;
        $this->title = $title;
    }

    /**
     * Obtenir la vue / le contenu du composant
     */
    public function render(): View
    {
        return view('components.alert');
    }
    
    /**
     * Déterminer si le message existe
     */
    public function hasMessage(): bool
    {
        return !empty($this->message);
    }
    
    /**
     * Déterminer les classes CSS en fonction du type
     */
    public function getColorClass(): string
    {
        return match($this->type) {
            'success' => 'bg-green-100 border-green-500 text-green-700',
            'error'   => 'bg-red-100 border-red-500 text-red-700',
            'warning' => 'bg-yellow-100 border-yellow-500 text-yellow-700',
            'info'    => 'bg-blue-100 border-blue-500 text-blue-700',
            default   => 'bg-gray-100 border-gray-500 text-gray-700',
        };
    }
    
    /**
     * Déterminer l'icône en fonction du type
     */
    public function getIcon(): string
    {
        return match($this->type) {
            'success' => '✅',
            'error'   => '❌',
            'warning' => '⚠️',
            'info'    => 'ℹ️',
            default   => '📌',
        };
    }
}