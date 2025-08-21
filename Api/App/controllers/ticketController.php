<?php

namespace App\controllers;

use App\models\CT_Fields;
use App\traits\ApiResponse;
use App\traits\Log;
use App\traits\ST_History;
use App\traits\HasPermissions;
use App\models\Ticket;
use App\models\User;
use App\models\CT_Values;

use Flight;

class TicketController
{
    use ApiResponse;
    use HasPermissions;
    use ST_History;
    use Log;

    public function index()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TICKET.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $has_pagination = isset($_GET['page']) && isset($_GET['limit']);


        if ($has_pagination) {
            $page = $_GET['page'];
            $limit = $_GET['limit'];

            $offset = ($page - 1) * $limit;

            $total = Ticket::Count();
            $pages = ceil($total / $limit);
            $tickets = Ticket::GetAll($limit, $offset);

            if (empty($tickets)) {
                $this->failed(null, 'Tickets not found', 404);
                return;
            }
            foreach ($tickets as $ticket) {
                $ticket->asesor = User::get($ticket->asesor);
                $ticket->client = User::get($ticket->client);
            }
        }

        $this->success($tickets, 'Tickets list', 200);
    }

    public function show($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TICKET.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $ticket = Ticket::get($id);
        if ($ticket) {
            $custom_values = CT_Values::getByTicket($id);
            $ticket->custom_values = $custom_values;
            // Fetching the asesor and client details
            $asesor = User::get($ticket->asesor);
            if ($asesor) {
                // Clear sensitive information
                unset($asesor->password, $asesor->phone);
                $ticket->asesor = $asesor;
            } else {
                $ticket->asesor = null;
            }
            $client = User::get($ticket->client);
            if ($client) {
                // Clear sensitive information
                unset($client->password);
                $ticket->client = $client;
            } else {
                $ticket->client = null;
            }

            //fetching Custom Values
            $custom_values = CT_Values::getByTicket($id);
            $ticket->custom_values = $custom_values;
            //fetching Custom Fields by category ticket
            $custom_fields = CT_Fields::getByCategory($ticket->category);
            $ticket->custom_fields = $custom_fields;

            $this->success([$ticket], 'Ticket found', 200);
        } else {
            $this->failed(null, 'Ticket not found', 404);
        }
    }

    public function store()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TICKET.CREATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();

        if (empty($data)) {
            $this->failed(null, "No data provided, please use this schema {title: string, client: string, description: string, status: string, priority: string, category: string, asesor: string, custom_values: [{custom_field_id: string, value: string}]}", 400);
            return;
        }

        $user = User::get($AuthUser->id);
        if (!$user) {
            $this->failed(null, "12", 400);
            return;
        }
        //ticket Required Fields
        $requiredFields = ['title', 'description', 'category', 'custom_values'];
        //CT_Values Required Fields
        $requiredFieldsValues = ['custom_field_id', 'value'];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
            if ($field == 'custom_values') {
                if (empty($data[$field])) {
                    $this->failed(null, "Field '$field' is required", 400);
                    return;
                }
                foreach ($data[$field] as $value) {
                    foreach ($requiredFieldsValues as $fieldValues) {
                        if (empty($value[$fieldValues]) && $value[$fieldValues] !== '0') {
                            $this->failed(null, "Field '$fieldValues' is required", 400);
                            return;
                        }
                    }
                }
            }
        }

        $ticket = new Ticket(
            null, // id
            !empty($data['client']) ? $data['client'] : $AuthUser->id, // client
            $data['title'], // title
            $data['description'], // description
            null, // creation_date (lo generará la DB normalmente con CURRENT_TIMESTAMP)
            !empty($data['status']) ? $data['status'] : 'NEW', // status
            !empty($data['priority']) ? $data['priority'] : 'LOW', // priority
            $data['category'], // category
            !empty($data['asesor']) ? $data['asesor'] : null // asesor
        );
        $ticket = Ticket::create($ticket);
        foreach ($data['custom_values'] as $value) {
            $custom_value = new CT_Values(null, $value['custom_field_id'], $ticket->id, $value['value']);
            $custom_value = CT_Values::create($custom_value);
        }

        $this->saveLog($AuthUser->id, 'TICKET_CREATED', 'TICKET WAS CREATED SUCCESSFULLY: ' . $ticket->title);
        $this->success([$ticket], 'Ticket created', 201);
    }

    public function update($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TICKET.UPDATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        $ticket = Ticket::get($id);
        if ($ticket) {
            $ticket->title = $data['title'] ?? $ticket->title;
            $ticket->client = $data['client'] ?? $ticket->client;
            $ticket->description = $data['description'] ?? $ticket->description;
            if (!empty($data['status']) && $data['status'] != $ticket->status) $this->saveHistory($id, $ticket->status, $data['status'], $AuthUser->id);
            $ticket->status = $data['status'] ?? $ticket->status;
            $ticket->priority = $data['priority'] ?? $ticket->priority;
            $ticket->category = $data['category'] ?? $ticket->category;
            $ticket->asesor = $data['asesor'] ?? $ticket->asesor;

            if (!empty($data['custom_values'])) {
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
            $this->saveLog($AuthUser->id, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: ' . $ticket->title);
            $this->success([$ticket], 'Ticket updated', 200);
        } else {
            $this->failed(null, 'Ticket not found', 404);
        }
    }

    public function transfer($id, $userid = null)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TICKET.TRANSFER')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $ticket = Ticket::get($id);
        if (!$ticket) {
            $this->failed(null, 'Ticket not found', 404);
            return;
        }

        if ($userid) {
            $user = User::get($userid);
            if (!$user) {
                $this->failed(null, 'User not found', 404);
                return;
            }
            $ticket->asesor = $user->id;
        } else {
            $ticket->asesor = null;
        }
        $ticket = Ticket::Update($ticket);
        $this->saveLog($AuthUser->id, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: ' . $ticket->title);
        $this->success([$ticket], 'Ticket updated', 200);
    }

    public function destroy($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TICKET.DELETE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $ticket = Ticket::get($id);
        if ($ticket) {
            Ticket::delete($ticket);
            CT_Values::deleteByTicket($id);
            $this->saveLog($AuthUser->id, 'TICKET_DELETED', 'TICKET WAS DELETED SUCCESSFULLY: ' . $ticket->title);
            $this->success([null], 'Ticket deleted', 200);
        } else {
            $this->failed(null, 'Ticket not found', 404);
        }
    }

    public function getByCategory($category)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TICKET.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $tickets = Ticket::getByCategory($category);
        $this->success($tickets, 'Tickets list', 200);
    }

    public function getByStatus($status)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TICKET.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $tickets = Ticket::getByStatus($status);
        $this->success($tickets, 'Tickets list', 200);
    }

    public function getByPriority($priority)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TICKET.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $tickets = Ticket::getByPriority($priority);
        $this->success($tickets, 'Tickets list', 200);
    }

    public function getByAsesor($asesor)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TICKET.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $tickets = Ticket::getByAsesor($asesor);
        $this->success($tickets, 'Tickets list', 200);
    }

    public function getMyTickets()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TICKET.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $has_pagination = isset($_GET['page']) && isset($_GET['limit']);

        if ($has_pagination) {
            $page = $_GET['page'];
            $limit = $_GET['limit'];
            $offset = ($page - 1) * $limit;
            $tickets = Ticket::getByAsesor($AuthUser->id, $limit, $offset);
            $total = Ticket::CountByAsesor($AuthUser->id);
            $pages = ceil($total / $limit);
            foreach ($tickets as $ticket) {
                $ticket->asesor = User::get($ticket->asesor);
                $ticket->client = User::get($ticket->client);
                //Clear sensitive information
                unset($ticket->asesor->password, $ticket->asesor->phone);
                unset($ticket->client->password);
            }
            $this->success(['tickets' => $tickets, 'pagination' => ['page' => $page, 'limit' => $limit, 'total' => $total, 'total_pages' => $pages]], 'Tickets list', 200);
        } else {
            $tickets = Ticket::getByAsesor($AuthUser->id);
            foreach ($tickets as $ticket) {
                $ticket->asesor = User::get($ticket->asesor);
                $ticket->client = User::get($ticket->client);
                //Clear sensitive information
                unset($ticket->asesor->password, $ticket->asesor->phone);
                unset($ticket->client->password);
            }
            $this->success(['tickets' => $tickets], 'Tickets list', 200);
        }
    }

    public function getByClient($client)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TICKET.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $tickets = Ticket::getByClient($client);
        $this->success($tickets, 'Tickets list', 200);
    }

    public function Assign($ticket, $asesor)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TICKET.ASSIGN')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $ticket = Ticket::get($ticket);
        $asesor = User::get($asesor);
        if ($ticket && $asesor) {
            $ticket->asesor = $asesor->id;
            $ticket = Ticket::update($ticket);
            $this->saveLog($AuthUser->id, 'TICKET_ASSIGNED', 'TICKET WAS ASSIGNED SUCCESSFULLY: ' . $ticket->title);
            $this->success([$ticket], 'Ticket assigned', 200);
        } else {
            $this->failed(null, 'Ticket or Asesor not found', 404);
        }
    }
}
