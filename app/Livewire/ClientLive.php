<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;

class ClientLive extends Component
{   
    public $clientnum, $name, $location, $contact, $contactper, $payment;
    public $formKey = 0;

    protected $rules = [
        'clientnum'   => 'required|numeric|unique:client_db,client_number',
        'name'        => 'required|string|max:255',
        'location'    => 'required|string|max:255',
         'contact' => [
        'nullable','numeric',
        'regex:/^(09\d{9}|\+639\d{9})$/'],
        'contactper'  => 'nullable|string|max:255',
        'payment'     => 'required|string',
    ];

    public function register()
    {
        $this->validate();

        Client::create([
            'client_number'   => $this->clientnum,
            'client_name'     => $this->name,
            'location'        => $this->location,
            'contact'         => $this->contact,
            'contact_person'  => $this->contactper,
            'payment_type'    => $this->payment,
        ]);

        // Reset values
        $this->reset(['clientnum', 'name', 'location', 'contact', 'contactper', 'payment']);

        $this->resetValidation();

        $this->formKey++;

        session()->flash('status', 'product-added');

    }

    public function render()
    {
        return view('livewire.client-live', [
            'clients' => Client::all()
        ])->layout('layouts.app');
    }
}
