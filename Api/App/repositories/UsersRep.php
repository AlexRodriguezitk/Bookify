<?php

namespace App\Repositories;

class UsersRep
{
    public ?int $id = null;
    public string $name;
    public string $email;
    private string $password;
    public ?string $phone = null;
    public ?string $profile_image = null;
    private ?string $totp_secret = null;
    public string $username;
    public ?\DateTime $created_at = null;
    public ?\DateTime $updated_at = null;
    public ?\DateTime $deleted_at = null;
    public ?\DateTime $last_login_at = null;
    public ?string $last_ip = null;
    public bool $is_active = true;

    public function __construct(array $payload = [])
    {
        $this->id = isset($payload['id']) ? (int) $payload['id'] : null;
        $this->name = (string) ($payload['name'] ?? '');
        $this->email = (string) ($payload['email'] ?? '');
        $this->username = (string) ($payload['username'] ?? '');
        $this->password = (string) ($payload['password'] ?? '');
        $this->phone = isset($payload['phone']) ? (string) $payload['phone'] : null;
        $this->profile_image = isset($payload['profile_image']) ? (string) $payload['profile_image'] : null;
        $this->totp_secret = isset($payload['totp_secret']) ? (string) $payload['totp_secret'] : null;
        $this->last_ip = isset($payload['last_ip']) ? (string) $payload['last_ip'] : null;
        $this->is_active = isset($payload['is_active']) ? (bool) $payload['is_active'] : true;

        $this->created_at = $this->parseDate($payload['created_at'] ?? null);
        $this->updated_at = $this->parseDate($payload['updated_at'] ?? null);
        $this->deleted_at = $this->parseDate($payload['deleted_at'] ?? null);
        $this->last_login_at = $this->parseDate($payload['last_login_at'] ?? null);
    }

    private function parseDate(?string $value): ?\DateTime
    {
        if (empty($value) || $value === '0000-00-00 00:00:00') {
            return null;
        }
        return new \DateTime($value);
    }

    // Getters para campos privados
    public function getPassword(): string
    {
        return $this->password;
    }

    public function getTotpSecret(): ?string
    {
        return $this->totp_secret;
    }

    //Setters para campos privados
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setTotpSecret(?string $totp_secret): void
    {
        $this->totp_secret = $totp_secret;
    }

    // Serialización a JSON sin timezone extra
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'type' => 'user',
            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'username' => $this->username,
                'phone' => $this->phone,
                'profile_image' => $this->profile_image,
                'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
                'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
                'last_login_at' => $this->last_login_at?->format('Y-m-d H:i:s'),
                'last_ip' => $this->last_ip,
                'is_active' => $this->is_active,
            ],
        ];
    }
}
