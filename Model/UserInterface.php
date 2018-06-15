<?php

namespace MD\SocomBundle\Model;

interface UserInterface
{
    const COMPTABILITE  = 'comptabilité';
    const TECHNICIEN    = 'technicien';
    const ADMINISTRATIF = 'administratif';
    const MAGASINIER    = 'magasinier';

    /**
     * @param null|string $phone
     * @return UserInterface
     */
    public function setPhone(?string $phone): UserInterface;

    /**
     * @return null|string
     */
    public function getPhone(): ?string;

    /**
     * @param \DateTime $createdAt
     * @return UserInterface
     */
    public function setCreatedAt(\DateTime $createdAt): UserInterface;

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime;

    /**
     * @param \DateTime $updatedAt
     * @return UserInterface
     */
    public function setUpdatedAt(\DateTime $updatedAt): UserInterface;

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime;

    /**
     * @param bool $admin
     * @return UserInterface
     */
    public function setAdmin(bool $admin): UserInterface;

    /**
     * @return bool
     */
    public function getAdmin(): bool;

    /**
     * @param string $type
     * @return UserInterface
     */
    public function setType(string $type): UserInterface;

    /**
     * @return null|string
     */
    public function getType(): ?string;

    /**
     * @param string $lastName
     * @return UserInterface
     */
    public function setLastName(string $lastName): UserInterface;

    /**
     * @return null|string
     */
    public function getLastName(): ?string;

    /**
     * @param string $firstName
     * @return UserInterface
     */
    public function setFirstName(string $firstName): UserInterface;

    /**
     * @return null|string
     */
    public function getFirstName(): ?string;
}
