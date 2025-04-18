<?php

namespace App\controllers;

use App\traits\ApiResponse;
use App\traits\Log;
use App\traits\HasPermissions;
use App\models\Terminal;
use App\models\User;

use Flight;
use Exception;

class TerminalController
{
    use ApiResponse;
    use HasPermissions;
    use Log;

    public function index()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TERMINAL.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $terminals = Terminal::getAll();
        $this->success($terminals, 'Terminals list', 200);
    }

    public function show($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TERMINAL.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $terminal = Terminal::get($id);
        if ($terminal) {
            $this->success([$terminal], 'Terminal found', 200);
        } else {
            $this->failed(null, 'Terminal not found', 404);
        }
    }

    public function store()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TERMINAL.CREATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        //Fields ID, terminal_ext
        $data = Flight::request()->data->getData();

        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }
        try {
            $requiredFields = ['terminal_ext'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $this->failed(null, "Field '$field' is required", 400);
                    return;
                }
            }
        } catch (Exception $e) {
            $this->failed(null, "Field '$field' is required", 400);
            return;
        }
        $terminal = new Terminal(null, $data['terminal_ext']);
        $terminal = Terminal::create($terminal);
        $this->saveLog($AuthUser->id, 'TERMINAL_CREATED', 'TERMINAL WAS CREATED SUCCESSFULLY: ' . $terminal->terminal_ext);
        $this->success([$terminal], 'Terminal created', 201);
    }

    public function update($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TERMINAL.UPDATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        $terminal = Terminal::get($id);
        if ($terminal) {
            $terminal->terminal_ext = $data['terminal_ext'] ?? $terminal->terminal_ext;
            $terminal = Terminal::update($terminal);
            $this->saveLog($AuthUser->id, 'TERMINAL_UPDATED', 'TERMINAL WAS UPDATED SUCCESSFULLY: ' . $terminal->terminal_ext);
            $this->success([$terminal], 'Terminal updated', 200);
        } else {
            $this->failed(null, 'Terminal not found', 404);
        }
    }

    public function destroy($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TERMINAL.DELETE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $terminal = Terminal::get($id);
        if ($terminal) {
            Terminal::delete($id);
            $this->saveLog($AuthUser->id, 'TERMINAL_DELETED', 'TERMINAL WAS DELETED SUCCESSFULLY: ' . $terminal->terminal_ext);
            $this->success([null], 'Terminal deleted', 200);
        } else {
            $this->failed(null, 'Terminal not found', 404);
        }
    }

    //func GetAssignements Terminal $terminal::GetAssignments($asesor)
    public function getAssignments($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TERMINAL.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $user = User::get($id);
        if ($user) {
            $assignements = Terminal::GetAssignments($user);
            if ($assignements) {
                $this->success($assignements, 'Terminal assignements', 200);
            } else {
                $this->failed(null, 'This User doesnt have Terminals assigned', 404);
            }
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }

    //Func Asing Terminal $terminal::Assing($terminal, $asesor) 
    public function assing($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TERMINAL.ASSIGN')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }
        try {
            $requiredFields = ['asesor'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $this->failed(null, "Field '$field' is required", 400);
                    return;
                }
            }
        } catch (Exception $e) {
            $this->failed(null, "Field '$field' is required", 400);
            return;
        }
        $terminal = Terminal::get($id);
        $asesor = User::get($data['asesor']);
        if ($terminal && $asesor) {
            try {
                $message = Terminal::Assign($terminal, $asesor);
                $this->saveLog($AuthUser->id, 'TERMINAL_ASSIGNED', 'TERMINAL WAS ASSIGNED SUCCESSFULLY: TERMINAL{' . $terminal->terminal_ext . '} ASESOR{' . $asesor->name . '}');
                $this->success([$message, $terminal], 'Terminal assigned', 200);
            } catch (Exception $e) {
                $this->failed(null, 'Terminal already assigned', 400);
            }
        } else {
            $this->failed(null, 'Terminal or asesor not found', 404);
        }
    }

    //FUncion para desasignar terminal
    public function unassing($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TERMINAL.ASSIGN')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }
        try {
            $requiredFields = ['asesor'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $this->failed(null, "Field '$field' is required", 400);
                    return;
                }
            }
        } catch (Exception $e) {
            $this->failed(null, "Field '$field' is required", 400);
            return;
        }
        $terminal = Terminal::get($id);
        $asesor = User::get($data['asesor']);
        if ($terminal && $asesor) {
            $message = Terminal::Unassign($terminal, $asesor);
            $this->saveLog($AuthUser->id, 'TERMINAL_UNASSIGNED', 'TERMINAL WAS UNASSIGNED SUCCESSFULLY: TERMINAL{' . $terminal->terminal_ext . '} ASESOR{' . $asesor->name . '}');
            $this->success([$message, $terminal], 'Terminal unassing', 200);
        } else {
            $this->failed(null, 'Terminal or asesor not found', 404);
        }
    }
}
