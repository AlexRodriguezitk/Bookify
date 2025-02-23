<?php

namespace App\controllers;

use App\traits\ApiResponse;
use App\traits\Log;
use App\models\Ticket;
use App\models\User;
use App\models\CT_Values;

use Flight;

class TicketController
{
    use ApiResponse;
    use Log;
    
    public function index()
    {
        $tickets = Ticket::getAll();
        $this->success($tickets, 'Tickets list', 200);
    }

    public function show($id)
    {
        $ticket = Ticket::get($id);
        if ($ticket) {
            $custom_values = CT_Values::getByTicket($id);
            $ticket->$custom_values = $custom_values;
            $this->success([$ticket], 'Ticket found', 200);
        } else {
            $this->failed(null, 'Ticket not found', 404);
        }
    }

    public function store()
    {
        $data = Flight::request()->data->getData();
        
        if (empty($data)) {
            $this->failed(null, "No data provided, please use this schema {title: string, client: string, description: string, status: string, priority: string, category: string, asesor: string, custom_values: [{custom_field_id: string, value: string}]}", 400);
            return;
        }

        //ticket Required Fields
        $requiredFields = ['title', 'client', 'description', 'status', 'priority', 'category', 'asesor', 'custom_values'];
        //CT_Values Required Fields
        $requiredFieldsValues = ['custom_field_id','value'];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
            if($field == 'custom_values'){
                if (empty($data[$field])) {
                    $this->failed(null, "Field '$field' is required", 400);
                    return;
                }
                foreach ($data[$field] as $value) {
                    foreach ($requiredFieldsValues as $fieldValues) {
                        if (empty($value[$fieldValues])) {
                            $this->failed(null, "Field '$fieldValues' is required", 400);
                            return;
                        }
                    }
                }

            }
        }

        //$ticket = new Ticket(null, $data['title'], $data['client'], $data['description'], $data['status'], $data['priority'], $data['category'], $data['asesor']);
        //$ticket = Ticket::create($ticket);
        //Creating CT_Values[custom_field_id, ticket_id, value]
        $ticket = new Ticket(null, $data['title'], $data['client'], $data['description'], $data['status'], $data['priority'], $data['category'], $data['asesor']);
        $ticket = Ticket::create($ticket);
        foreach ($data['custom_values'] as $value) {
            $custom_value = new CT_Values(null, $value['custom_field_id'], $ticket->id, $value['value']);
            $custom_value = CT_Values::create($custom_value);
        }
        
        $this->saveLog(null, 'TICKET_CREATED', 'TICKET WAS CREATED SUCCESSFULLY: ' . $ticket->title);
        $this->success([$ticket], 'Ticket created', 201);
    }

    public function update($id)
    {
        $data = Flight::request()->data->getData();
        $ticket = Ticket::get($id);
        if ($ticket) {
            $ticket->title = $data['title'] ?? $ticket->title;
            $ticket->client = $data['client'] ?? $ticket->client;
            $ticket->description = $data['description'] ?? $ticket->description;
            $ticket->status = $data['status'] ?? $ticket->status;
            $ticket->priority = $data['priority'] ?? $ticket->priority;
            $ticket->category = $data['category'] ?? $ticket->category;
            $ticket->asesor = $data['asesor'] ?? $ticket->asesor;

            if($data['custom_values']){
                $requiredFieldsValues = ['custom_field_id', 'value'];
                foreach ($data['custom_values'] as $value) {
                    foreach ($requiredFieldsValues as $fieldValues) {
                        if (empty($value[$fieldValues])) {
                            $this->failed(null, "Field '$fieldValues' is required", 400);
                            return;
                        }
                    }
                }
                //Deleting CT_Values
                CT_Values::deleteByTicket($id);
                //Creating CT_Values
                foreach ($data['custom_values'] as $value) {
                    $custom_value = new CT_Values(null, $value['custom_field_id'], $id, $value['value']);
                    $custom_value = CT_Values::create($custom_value);
                }
            }
            $ticket = Ticket::update($ticket);
            $this->saveLog(null, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: ' . $ticket->title);
            $this->success([$ticket], 'Ticket updated', 200);
        } else {
            $this->failed(null, 'Ticket not found', 404);
        }
    }

    public function destroy($id)
    {
        $ticket = Ticket::get($id);
        if ($ticket) {
            Ticket::delete($ticket);
            CT_Values::deleteByTicket($id);
            $this->saveLog(null, 'TICKET_DELETED', 'TICKET WAS DELETED SUCCESSFULLY: ' . $ticket->title);
            $this->success(null, 'Ticket deleted', 200);
        } else {
            $this->failed(null, 'Ticket not found', 404);
        }
    }

    public function getByCategory($category)
    {
        $tickets = Ticket::getByCategory($category);
        $this->success($tickets, 'Tickets list', 200);
    }

    public function getByStatus($status)
    {
        $tickets = Ticket::getByStatus($status);
        $this->success($tickets, 'Tickets list', 200);
    }

    public function getByPriority($priority)
    {
        $tickets = Ticket::getByPriority($priority);
        $this->success($tickets, 'Tickets list', 200);
    }

    public function getByAsesor($asesor)
    {
        $tickets = Ticket::getByAsesor($asesor);
        $this->success($tickets, 'Tickets list', 200);
    }

    public function getByClient($client)
    {
        $tickets = Ticket::getByClient($client);
        $this->success($tickets, 'Tickets list', 200);
    }

    public function Assign($ticket, $asesor)
    {
        $ticket = Ticket::get($ticket);
        $asesor = User::get($asesor);
        if ($ticket && $asesor) {
            $ticket->asesor = $asesor->id;
            $ticket = Ticket::update($ticket);
            $this->saveLog(null, 'TICKET_ASSIGNED', 'TICKET WAS ASSIGNED SUCCESSFULLY: ' . $ticket->title);
            $this->success([$ticket], 'Ticket assigned', 200);
        } else {
            $this->failed(null, 'Ticket or Asesor not found', 404);
        }
    }
}