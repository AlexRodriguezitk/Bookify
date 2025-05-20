<?php

namespace App\controllers;

use App\traits\Log;
use App\traits\ApiResponse;
use App\traits\HasPermissions;
use App\models\Interaction;
use App\models\User;
use Flight;

class InteractionController
{
    use ApiResponse;
    use HasPermissions;
    use Log;


    public function index()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'INTERACTION.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $interactions = Interaction::getAll();
        $this->success($interactions, 'Interactions list', 200);
    }

    public function show($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'INTERACTION.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $interaction = Interaction::get($id);
        if ($interaction) {
            $interaction->user = User::get($interaction->user);
            $this->success([$interaction], 'Interaction found', 200);
        } else {
            $this->failed(null, 'Interaction not found', 404);
        }
    }

    public function store()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'INTERACTION.CREATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();

        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }
        $requiredFields = ['id_ticket', 'id_user', 'message', 'type'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }

        $interaction = new Interaction(null, $data['id_ticket'], $data['id_user'], $data['message'], null, $data['type']);
        $interaction = Interaction::create($interaction);
        $this->saveLog($AuthUser->id, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: ' . $interaction->message);
        $this->success([$interaction], 'Interaction created', 201);
    }

    //update function
    public function update($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'INTERACTION.UPDATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        $interaction = Interaction::get($id);
        if ($interaction) {
            $interaction->id_ticket = $data['id_ticket'] ?? $interaction->id_ticket;
            $interaction->user = $data['id_user'] ?? $interaction->user;
            $interaction->message = $data['message'] ?? $interaction->message;
            $interaction->type = $data['type'] ?? $interaction->type;
            $interaction = Interaction::update($interaction);
            $this->saveLog($AuthUser->id, 'INTERACTION_UPDATED', 'INTERACTION WAS UPDATED SUCCESSFULLY: ' . $interaction->message);
            $this->success([$interaction], 'Interaction updated', 200);
        } else {
            $this->failed(null, 'Interaction not found', 404);
        }
    }

    //delete function
    public function destroy($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'INTERACTION.DELETE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $interaction = Interaction::get($id);
        if ($interaction) {
            Interaction::delete($id);
            $this->saveLog($AuthUser->id, 'INTERACTION_DELETED', 'INTERACTION WAS DELETED SUCCESSFULLY: ' . $interaction->message);
            $this->success([$interaction], 'Interaction deleted', 200);
        } else {
            $this->failed(null, 'Interaction not found', 404);
        }
    }

    public function GetInteractionsByTicket($id_ticket)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'INTERACTION.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $interactions = Interaction::getIntByTicket($id_ticket);
        if ($interactions) {
            foreach ($interactions as $interaction) {
                $interaction->user = User::get($interaction->user);
            }
            $this->success($interactions, 'Interactions found', 200);
        } else {
            $this->failed(null, 'Interactions not found', 404);
        }
    }
}
