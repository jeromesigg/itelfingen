<?php

namespace App\Models;

use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Helper\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Event extends Model
{
    use HasFactory, Notifiable;

    //
    protected $fillable = [
        'name', 'event_status_id', 'start_date', 'end_date',
        'firstname', 'group_name', 'email', 'street', 'house_number', 'plz', 'city', 'telephone', 'comment', 'marketing_comment', 'contract_status_id', 'comment_intern',
        'terms', 'total_amount', 'total_days', 'total_people', 'bexio_user_id', 'bexio_invoice_id', 'bexio_file_id',
        'user_id', 'cleaning_mail', 'bexio_offer_id', 'discount', 'last_info', 'code', 'feedback_mail', 'external', 'early_checkin', 'late_checkout', 'foreign_key', 'uuid', 'phonenumber_query', 'glutz_user_id',
    ];

    protected $casts = [
        'contract_status_id' => ContractState::class,
        'event_status_id' => BookingState::class,
        'terms' => 'boolean',
        'cleaning_mail' => 'boolean',
        'last_info' => 'boolean',
        'external' => 'boolean',
        'early_checkin' => 'boolean',
        'late_checkout' => 'boolean',
    ];

    public function event_status()
    {
        return $this->belongsTo(EventStatus::class);
    }

    public function contract_status()
    {
        return $this->belongsTo(ContractStatus::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function event_rooms()
    {
        return $this->hasMany(EventRoom::class);
    }

    public function routeNotificationForSlack($notification)
    {
        // Ohne Webhook false: sonst wählt Laravel die Slack-Bot-API und die Benachrichtigung bricht ab.
        return config('slack.endpoint') ?: false;
    }

    /** Buchungsstatus; leere Spalte wie früher als kleinster Status (neu). */
    public function bookingState(): BookingState
    {
        return $this->event_status_id ?? BookingState::Neu;
    }

    /** Vertragsstatus; leere Spalte wie früher als kleinster Status (offen). */
    public function contractState(): ContractState
    {
        return $this->contract_status_id ?? ContractState::Offen;
    }

    public function number()
    {
        return str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function status()
    {
        $offer = Helper::GetEventOfferStatus($this);
        $invoice = Helper::GetEventInvoiceStatus($this);
        $cleaning_mail = Helper::GetEventCleaningMailCheck($this);
        $code = Helper::GetEventCodeCheck($this);

        return $offer.$invoice.$cleaning_mail.$code;
    }
}
