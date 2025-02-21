<?php

namespace App\controllers;

use App\traits\ApiResponse;
use App\models\Terminal;
use App\models\User;

use Flight;
use Exception;

class TerminalController
{
    use ApiResponse;

    public function index()
    {
        $terminals = Terminal::getAll();
        $this->success($terminals, 'Terminals list', 200);
    }

    public function show($id)
    {
        $terminal = Terminal::get($id);
        if ($terminal) {
            $this->success([$terminal], 'Terminal found', 200);
        } else {
            $this->failed(null, 'Terminal not found', 404);
        }
    }

    public function store()
    {
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
        $this->success([$terminal], 'Terminal created', 201);
    }

    public function update($id)
    {
        $data = Flight::request()->data->getData();
        $terminal = Terminal::get($id);
        if ($terminal) {
            $terminal->terminal_ext = $data['terminal_ext'] ?? $terminal->terminal_ext;
            $terminal = Terminal::update($terminal);
            $this->success([$terminal], 'Terminal updated', 200);
        } else {
            $this->failed(null, 'Terminal not found', 404);
        }
    }

    public function destroy($id)
    {
        $terminal = Terminal::get($id);
        if ($terminal) {
            Terminal::delete($terminal);
            $this->success(null, 'Terminal deleted', 200);
        } else {
            $this->failed(null, 'Terminal not found', 404);
        }
    }

    //func GetAssignements Terminal $terminal::GetAssignments($asesor)
    public function getAssignments($id)
    {
        $user = User::get($id);
        if ($user) {
            $assignements = Terminal::GetAssignments($user);
            $this->success($assignements, 'Terminal assignements', 200);
        } else {
            $this->failed(null, 'Terminal not found', 404);
        }
    }

    //Func Asing Terminal $terminal::Assing($terminal, $asesor) 
    public function assing($id)
    {   
        $data = Flight::request()->data->getData();
        $terminal = Terminal::get($id);
        $asesor = User::get($data['asesor']);
        if ($terminal && $asesor) {
            try {
                $message = Terminal::Assign($terminal, $asesor);
                $this->success([$message, $terminal], 'Terminal asing', 200);
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
        $data = Flight::request()->data->getData();
        $terminal = Terminal::get($id);
        $asesor = User::get($data['asesor']);
        if ($terminal && $asesor) {
            $message = Terminal::Unassign($terminal, $asesor);
            $this->success([$message, $terminal], 'Terminal unassing', 200);
        } else {
            $this->failed(null, 'Terminal or asesor not found', 404);
        }
    }

}