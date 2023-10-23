<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PasswordUpdateRepository::class)]
class PasswordUpdate
{

    #[Assert\NotBlank(message: "Veuillez renseigner votre ancien mot de passe")]
    private ?string $oldPassword = null;

    #[Assert\NotBlank(message: "Veuillez renseigner votre nouveau mot de passe")]
    private ?string $newPassword = null;

    #[Assert\EqualTo(propertyPath:"newPassword", message:"Vous n'avez pas correctement confirmé le mot de passe")]
    private ?string $confirmPassword = null;


    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): static
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): static
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): static
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
