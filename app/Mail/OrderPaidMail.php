<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPaidMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Створюємо новий екземпляр повідомлення.
     * 
     * @param Order $order
     */
    public function __construct(
        public Order $order
    ) {
        // Завдяки PHP 8.x властивість $order автоматично стає доступною в шаблоні
    }

    /**
     * Отримуємо конверт повідомлення (тема та інше).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Замовлення №{$this->order->id} успішно оплачено!",
        );
    }

    /**
     * Визначаємо вміст повідомлення (шаблон).
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.paid',
        );
    }

    /**
     * Отримуємо вкладення для повідомлення.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}