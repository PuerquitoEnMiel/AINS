<?php

namespace App\Mail;

use App\Models\AdminNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notification;

    public function __construct(AdminNotification $notification)
    {
        $this->notification = $notification;
    }

    public function build()
    {
        $actionUrl = match($this->notification->type) {
            'evidence' => route('admin.badge-evidence.index'),
            'suggestion' => route('admin.badge-suggestions.index'),
            default => route('admin.notifications.index'),
        };

        return $this->from('aitaskforce@ans.edu.ni', 'AINS Task Force')
            ->subject('Notificacion de Sistema AINS: ' . $this->notification->title)
            ->html("
                <div style='font-family: Arial, sans-serif; padding: 30px; max-width: 600px; border: 1px solid #e5e7eb; border-radius: 12px; background-color: #ffffff; color: #1f2937;'>
                    <h2 style='color: #1e3a8a; border-bottom: 2px solid #f3f4f6; padding-bottom: 12px; font-size: 20px; font-weight: 600; margin-top: 0;'>Notificacion de Administracion</h2>
                    
                    <table style='width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 20px;'>
                        <tr>
                            <td style='padding: 8px 0; font-weight: bold; color: #4b5563; width: 100px;'>Tipo:</td>
                            <td style='padding: 8px 0;'>" . strtoupper($this->notification->type) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; font-weight: bold; color: #4b5563;'>Titulo:</td>
                            <td style='padding: 8px 0;'>" . htmlspecialchars($this->notification->title) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; font-weight: bold; color: #4b5563; vertical-align: top;'>Detalle:</td>
                            <td style='padding: 8px 0;'>" . htmlspecialchars($this->notification->message) . "</td>
                        </tr>
                    </table>

                    " . ($this->notification->type === 'security' && isset($this->notification->data['prompt']) 
                        ? "<div style='background-color: #fcf8f8; border: 1px solid #f5e3e3; padding: 15px; border-radius: 8px; font-family: monospace; color: #991b1b; margin-bottom: 25px;'>
                            <strong>Prompt bloqueado:</strong><br>
                            \"" . htmlspecialchars($this->notification->data['prompt']) . "\"
                           </div>" 
                        : "") . "

                    <div style='margin-top: 30px; margin-bottom: 30px; text-align: center;'>
                        <a href='" . $actionUrl . "' style='background-color: #1e3a8a; color: #ffffff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);'>
                            Revisar en el Panel de Administracion
                        </a>
                    </div>
                    
                    <hr style='border: 0; border-top: 1px solid #e5e7eb; margin-top: 30px;'>
                    <p style='font-size: 11px; color: #9ca3af; text-align: center; margin-bottom: 0;'>
                        Este es un correo automatico emitido por el sistema AINS. Por favor no responda directamente a esta direccion.
                    </p>
                </div>
            ");
    }
}
